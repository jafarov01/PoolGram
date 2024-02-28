<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['isAdmin'])) {
    $_SESSION['isAdmin'] = 0;
}

if ($_SESSION['isAdmin'] != 1) {
    header("Location: ?view=mainpage");
    exit;
}

$errors = null;
$is_active = 1;

if (isset($_POST['create_poll'])) {
    //check if the question field is empty
    if (empty($_POST['question'])) {
        $errors['question'] = "Please enter a question";
    } else {
        $question = $_POST['question'];
    }

    //check if the options field is empty
    if (empty($_POST['options'])) {
        $errors['options'] = "Please enter options";
    } else {
        $options = explode(",", $_POST['options']);
        $options = array_map('trim', $options);
    }

    //check if multiple options are allowed or not
    if (isset($_POST['multiple_options'])) {
        $multiple_options = 1;
    } else {
        $multiple_options = 0;
    }

    //check if the expiry date is provided
    if (empty($_POST['deadline'])) {
        $errors['deadline'] = "Please provide an expiry date";
    } else {
        $deadline = $_POST['deadline'];
    }

    if ($errors == null) {
        global $conn;
        $date_created = date("Y-m-d H:i:s");

        //create the poll
        $sql_create_poll = "INSERT INTO polls (question, multiple_options, date_created, deadline, is_active) VALUES ('$question', $multiple_options, '$date_created', '$deadline', '$is_active');";
        $conn->query($sql_create_poll);

        //get the id of the created poll
        $poll_id = $conn->insert_id;

        foreach ($options as $option) {
            $sql_create_options = "INSERT INTO choices (poll_id, choice) VALUES ($poll_id, '$option');";
            $conn->query($sql_create_options);
        }

        //redirect to the main page and show a message that the poll was created successfully
        $_SESSION['message'] = "Poll created successfully!";
        header("Location: ?view=mainpage");
    }
}
?>

<div class="create_poll_section">
    <?php var_dump($_SESSION); ?>
    <h1>CREATE A POLL</h1>
    <form action="" method="post">
        <label for="question">Question:</label>
        <input type="text" id="question" name="question">
        <div class="create_poll_errors">
            <?php if (isset($errors['question'])): ?>
                <p><?= $errors['question']; ?></p>
            <?php endif; ?>
        </div>
        <label for="options">Options (comma separated):</label>
        <textarea id="options" name="options"></textarea>
        <div class="create_poll_errors">
            <?php if (isset($errors['options'])): ?>
                <p><?= $errors['options']; ?></p>
            <?php endif; ?>
        </div>
        <label for="multiple_options">Allow multiple options:
            <input type="checkbox" id="multiple_options" name="multiple_options">
        </label>
        <label for="deadline">Voting deadline:</label>
        <input type="date" id="deadline" name="deadline">
        <div class="create_poll_errors">
            <?php if (isset($errors['deadline'])): ?>
                <p><?= $errors['deadline']; ?></p>
            <?php endif; ?>
        </div>
        <input type="submit" name="create_poll" value="Create Poll">
    </form>

</div>