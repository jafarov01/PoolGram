<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['isAdmin'])) {
    $_SESSION['isAdmin'] = 0;
}

$logged_in_username = "";
$logged_in = false;
//$error to be null as default
$error = null;

//check if a user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    $logged_in = true;
    $logged_in_username = $_SESSION['username'];
} else {
    $_SESSION['loggedin'] = false;
}

//Retrieve active polls and their options
global $conn;

$selected_poll_id = $_SESSION['poll_id'];
$selected_poll_data = null;

$sql = "SELECT * FROM polls WHERE id = $selected_poll_id";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $selected_poll_data = $result->fetch_assoc();
    $poll_id = $selected_poll_data['id'];
    $options_sql = "SELECT * FROM choices WHERE poll_id = $poll_id";
    $options_result = $conn->query($options_sql);
    if ($options_result->num_rows > 0) {
        while ($option = $options_result->fetch_assoc()) {
            $selected_poll_data['options'][] = array(
                "id" => $option['id'],
                "choice" => $option['choice'],
                "votes" => $option['votes']
            );
        }
    }
}

//wait for the user to submit the vote again and update the votes table accordingly
//once the vote was submitted
if (isset($_POST['vote'])) {
    //get the the most recent choices that the user voted and decrement the values by 1.
    //get the most recent choices that the user voted
    $sql_get_prev_votes = "SELECT choices FROM votes WHERE user_id = '$logged_in_username' AND poll_id = $selected_poll_id";
    $prev_votes_result = $conn->query($sql_get_prev_votes);

    if ($prev_votes_result->num_rows > 0) {
        $prev_votes = $prev_votes_result->fetch_assoc();
        $prev_choices = explode(',', $prev_votes['choices']);
        foreach ($prev_choices as $prev_choice) {
            //decrement the number of votes for the option
            $sql_decrement_votes = "UPDATE choices SET votes = votes - 1 WHERE id = $prev_choice";
            $conn->query($sql_decrement_votes);
        }
    }

    if ($selected_poll_data['multiple_options'] == '0') {
        //check if any option was chosen: if not, show an error .poll-submission-errors div
        if (empty($_POST['selected_option'])) {
            $error = ["no_selection" => "Please select an option to vote"];
        } else {
            $selected_option = $_POST['selected_option'];
        }
    } elseif ($selected_poll_data['multiple_options'] == '1') {
        if (empty($_POST['selected_options'])) {
            $error = ["no_selection" => "Please select an option to vote"];
        } else {
            $selected_options = $_POST['selected_options'];
        }
    }

    //save to the database
    if ($error == null) {
        if ($selected_poll_data['multiple_options'] == '0') {
            $sql_vote = "UPDATE choices SET votes = votes + 1 WHERE id = $selected_option";
            $conn->query($sql_vote);
            $choices = $selected_option;
        } elseif ($selected_poll_data['multiple_options'] == '1') {
            $choices = implode(",", $selected_options);
            foreach ($selected_options as $option) {
                $sql_vote = "UPDATE choices SET votes = votes + 1 WHERE id = $option";
                $conn->query($sql_vote);
            }
        }

        //update the "votes" table to add that $_SESSION['username'] has voted for the polls id $selected_poll_id
        $sql_insert_vote = "INSERT INTO votes (poll_id, user_id, choices) VALUES ($selected_poll_id, '" . $_SESSION['username'] . "', '$choices') ON DUPLICATE KEY UPDATE id = id, choices = '$choices';";
        $conn->query($sql_insert_vote);

        //update min_rating and max_rating
        $sql_ratings = "UPDATE polls SET max_rating = (SELECT id FROM choices WHERE poll_id = polls.id ORDER BY votes DESC LIMIT 1), min_rating = (SELECT id FROM choices WHERE poll_id = polls.id ORDER BY votes ASC LIMIT 1)";
        $conn->query($sql_ratings);

        header("Location: ?view=mainpage");
    }
}
?>

<div class="submit_poll_section">
    <h1>UPDATE THE VOTE</h1>
    <?php if ($selected_poll_data): ?>
        <div class="poll-item">
            <p class="question"><?= $selected_poll_data['question']; ?></p>
            <div class="poll-item-dates">
                <p>Created on: <?= $selected_poll_data['date_created']; ?></p>
                <p>Deadline: <?= $selected_poll_data['deadline']; ?></p>
            </div>
            <div class="poll-item-options">
                <form action="" method="post">
                    <input type="hidden" name="poll_id" value="<?= $selected_poll_data['id']; ?>">
                    <?php if ($selected_poll_data['multiple_options'] == '0'): ?>
                        <?php foreach ($selected_poll_data['options'] as $option): ?>
                            <input type="radio" name="selected_option"
                                   value="<?= $option['id']; ?>"> <?= $option['choice']; ?>
                        <?php endforeach; ?>
                    <?php elseif ($selected_poll_data['multiple_options'] == '1'): ?>
                        <?php foreach ($selected_poll_data['options'] as $option): ?>
                            <input type="checkbox" name="selected_options[]"
                                   value="<?= $option['id']; ?>"> <?= $option['choice']; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <input type="submit" name="vote" value="VOTE">
                </form>
            </div>
            <div class="poll-submission-errors">
                <?php if (isset($error['no_selection'])) : ?>
                    <li style="color: #ec5d5d; font-size: 20px"> <?= $error['no_selection']; ?> </li>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <p>The selected poll could not be found.</p>
    <?php endif; ?>
</div>

