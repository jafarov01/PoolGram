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

global $conn;

// Get the ID of the poll to be edited
$edit_poll_id = $_SESSION['edit_poll_id'];

// Get the poll data from the database
$sql_get_poll = "SELECT * FROM polls WHERE id = $edit_poll_id";
$result = $conn->query($sql_get_poll);
$poll = $result->fetch_assoc();

// Get the choices for the poll
$sql_get_choices = "SELECT choice FROM choices WHERE poll_id = $edit_poll_id";
$result = $conn->query($sql_get_choices);
$choices = array();
while ($row = $result->fetch_assoc()) {
    $choices[] = $row['choice'];
}
$choices_str = implode(", ", $choices);

// Handle cancel request
if (isset($_POST['cancel'])) {
    header("Location: index.php?view=managepollpage");
    exit;
}

// Handle save request
if (isset($_POST['save'])) {
    // Get the form data
    $question = $_POST['question'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $date_created = $_POST['date_created'];
    $deadline = $_POST['deadline'];
    $multiple_options = isset($_POST['multiple_options']) ? 1 : 0;

    // Validate the form data
    $errors = array();
    if (empty($question)) {
        $errors['question'] = "Please enter a question";
    }
    if (empty($date_created)) {
        $errors['date_created'] = "Please enter date created";
    }
    if (empty($deadline)) {
        $errors['deadline'] = "Please provide an expiry date";
    }

    if (count($errors) == 0) {
        // Update the poll in the database
        global $conn;
        $sql_update_poll = "UPDATE polls SET question = '$question', is_active = '$is_active', date_created = '$date_created', deadline = '$deadline', multiple_options = '$multiple_options' WHERE id = $edit_poll_id";
        $conn->query($sql_update_poll);
        // Delete the current choices for the poll
        $sql_delete_choices = "DELETE FROM choices WHERE poll_id = $edit_poll_id";
        $conn->query($sql_delete_choices);

        // Add the new choices for the poll
        $options = $_POST['options'];
        $options = explode("\n", $options);
        $options = array_map('trim', $options);
        foreach ($options as $option) {
            $sql_add_choice = "INSERT INTO choices (poll_id, choice) VALUES ($edit_poll_id, '$option')";
            $conn->query($sql_add_choice);
        }
        // Redirect to managepollpage and show a message that the poll was edited successfully
        $_SESSION['message'] = "Poll edited successfully!";
        header("Location: index.php?view=managepollpage");
        exit;
    }
}

$sql_get_choices = "SELECT choice FROM choices WHERE poll_id = $edit_poll_id";
$result = $conn->query($sql_get_choices);
$choices = $result->fetch_all();

?>

<div class="edit_poll_section">
    <h1>EDIT POLL</h1>
    <form action="" method="post">
        <label for="question">Question:</label>
        <input type="text" id="question" name="question" value="<?= $poll['question'] ?>">
        <?php if (isset($errors['question'])): ?>
            <p><?= $errors['question']; ?></p>
        <?php endif; ?>

        <label for="options">Options:</label>
        <textarea id="options" name="options"><?=$choices_str;?></textarea>
        <?php if (isset($errors['options'])): ?>
            <p><?= $errors['options']; ?></p>
        <?php endif; ?>

        <input type="checkbox" id="is_active" name="is_active" <?= $poll['is_active'] == 1 ? 'checked' : '' ?>>
        <label for="is_active">Is Active</label>

        <input type="checkbox" id="multiple_options" name="multiple_options" <?= $poll['multiple_options'] == 1 ? 'checked' : '' ?>>
        <label for="multiple_options">Allow multiple options</label>

        <label for="date_created">Date Created:</label>
        <input type="date" id="date_created" name="date_created" value="<?= $poll['date_created'] ?>">
        <?php if (isset($errors['date_created'])): ?>
            <p><?= $errors['date_created']; ?></p>
        <?php endif; ?>

        <label for="deadline">Deadline:</label>
        <input type="date" id="deadline" name="deadline" value="<?= $poll['deadline'] ?>">
        <?php if (isset($errors['deadline'])): ?>
            <p><?= $errors['deadline']; ?></p>
        <?php endif; ?>

        <input type="submit" name="save" value="Save">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</div>
