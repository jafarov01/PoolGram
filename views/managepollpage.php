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

// get all polls from the database
$sql_get_polls = "SELECT id, question FROM polls";
$result = $conn->query($sql_get_polls);

// handle delete poll request
if (isset($_POST['delete_poll'])) {
    $poll_id = $_POST['poll_id'];
    $sql_delete_poll = "DELETE FROM polls WHERE id = $poll_id";
    $conn->query($sql_delete_poll);
    header("Location: index.php?view=managepollpage");
    exit;
}

// handle edit poll request
if (isset($_POST['edit_poll'])) {
    $_SESSION['edit_poll_id'] = $_POST['poll_id'];
    header("Location: index.php?view=editpollpage");
    exit;
}
?>

<div class="manage_poll_section">
    <h1>MANAGE POLLS</h1>
    <table>
        <tr>
            <th>Question</th>
            <th>Actions</th>
        </tr>
        <?php while ($poll = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $poll['question'] ?></td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="poll_id" value="<?= $poll['id'] ?>">
                        <input type="submit" name="edit_poll" value="Edit">
                    </form>
                    <form action="" method="post">
                        <input type="hidden" name="poll_id" value="<?= $poll['id'] ?>">
                        <input type="submit" name="delete_poll" value="Delete">
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</div>
