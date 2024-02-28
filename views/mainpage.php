<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['isAdmin'])) {
    $_SESSION['isAdmin'] = 0;
}

$logged_in_username = "";
$logged_in = false;

//check if a user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    $logged_in = true;
    $logged_in_username = $_SESSION['username'];
} else {
    $_SESSION['loggedin'] = false;
}

//Retrieve active polls and their options
global $conn;

$active_polls = array();
$sql_active_polls = "SELECT * FROM polls WHERE is_active = 1 AND deadline >= CURRENT_TIMESTAMP ORDER BY date_created DESC";
$result_active_polls = $conn->query($sql_active_polls);

if ($result_active_polls->num_rows > 0) {
    while ($row = $result_active_polls->fetch_assoc()) {
        $poll_id = $row['id'];
        $poll = array(
            "id" => $row['id'],
            "question" => $row['question'],
            "min_rating" => $row['min_rating'],
            "max_rating" => $row['max_rating'],
            "date_created" => $row['date_created'],
            "deadline" => $row['deadline'],
            "multiple_options" => $row['multiple_options'],
            "options" => array()
        );

        $options_sql = "SELECT * FROM choices WHERE poll_id = $poll_id";
        $options_result = $conn->query($options_sql);

        if ($options_result->num_rows > 0) {
            while ($option = $options_result->fetch_assoc()) {
                $poll['options'][] = array(
                    "id" => $option['id'],
                    "choice" => $option['choice'],
                    "votes" => $option['votes']
                );
            }
        }

        $active_polls[] = $poll;
    }
}

//Retrieve expired polls and their options
$expired_polls = array();
$sql_expired_polls = "SELECT id, question, date_created, deadline, min_rating, max_rating FROM polls WHERE is_active = 0 OR deadline < CURRENT_TIMESTAMP ORDER BY date_created DESC";
$result_expired_polls = $conn->query($sql_expired_polls);

if ($result_expired_polls->num_rows > 0) {
    while ($row = $result_expired_polls->fetch_assoc()) {
        $poll_id = $row['id'];
        $poll = array(
            "question" => $row['question'],
            "date_created" => $row['date_created'],
            "deadline" => $row['deadline'],
            "min_rating" => $row['min_rating'],
            "max_rating" => $row['max_rating'],
            "options" => array()
        );

        $options_sql = "SELECT * FROM choices WHERE poll_id = $poll_id";
        $options_result = $conn->query($options_sql);

        if ($options_result->num_rows > 0) {
            while ($option = $options_result->fetch_assoc()) {
                $poll['options'][] = [
                    "choice" => $option['choice'],
                    "votes" => $option['votes']
                ];
            }
        }
        $expired_polls[] = $poll;
    }
}

//once submit a vote is clicked
if (isset($_POST['submit_vote'])) {
    if ($logged_in) {
        $_SESSION['poll_id'] = $_POST['poll_id'];
        header("Location: ?view=submitvotepage");
    } else {
        header("Location: ?view=loginregisterpage");
    }
}
if (isset($_POST['update_vote'])) {
    $_SESSION['poll_id'] = $_POST['poll_id'];
    header("Location: ?view=updatevotepage");
}


?>

<div id="main-page">
    <div id="description">
        <h1>Welcome <?= $logged_in_username ?> to PollGram</h1>
        <p>PollGram is a simple and easy-to-use platform for creating and voting on polls. Browse through the active
            polls and cast your vote, or create your own poll and see what others think.</p>
    </div>
    <div id="active-polls-expired-polls">
        <div id="active-polls">
            <h2>Active Polls</h2>
            <?php foreach ($active_polls as $poll): ?>
                <div class="poll-item">

                    <p class="question"><?= $poll['question']; ?></p>

                    <div class="poll-item-options">
                        <?php foreach ($poll['options'] as $option): ?>
                        <p name="<?= $option['id']; ?>"> <?= $option['choice']; ?>
                            <?php endforeach; ?>
                        <form action="" method="post">
                            <input type="hidden" name="poll_id" value="<?= $poll['id']; ?>">
                            <?php
                            $pollID = $poll['id'];
                            $votes_sql = "SELECT * FROM votes WHERE user_id = '$logged_in_username' AND poll_id = $pollID LIMIT 1";
                            $votes_result = $conn->query($votes_sql);
                            if ($votes_result->num_rows > 0) : ?>
                                <input type="submit" name="update_vote" value="Update the vote">
                            <?php else : ?>
                                <input type="submit" name="submit_vote" value="Submit a vote">
                            <?php endif; ?>
                        </form>
                    </div>

                    <div class="poll-item-dates">
                        <p>Created on: <?= $poll['date_created']; ?></p>
                        <p>Deadline: <?= $poll['deadline']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="expired-polls">
            <h2>Expired Polls</h2>
            <?php foreach ($expired_polls as $poll): ?>

                <div class="poll-item">
                    <p class="question"><?= $poll['question']; ?></p>
                    <div class="poll-item-dates">
                        <p>Created on: <?= $poll['date_created']; ?></p>
                        <p>Expired on: <?= $poll['deadline']; ?></p>
                    </div>
                    <div class="poll-item-results">
                        <div class="min-rating">
                            <p><?php echo "Min rating: " . $poll['min_rating']; ?></p>
                        </div>
                        <div class="max-rating">
                            <p><?php echo "Max rating: " . $poll['max_rating']; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>