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

if (!$_SESSION['loggedin']) {
    header("Location: ?view=loginregisterpage");
}

global $conn;

// Get the number of registered users
$sql = "SELECT COUNT(*) as total FROM Users";
$result = $conn->query($sql);
$total_users = $result->fetch_assoc()['total'];

// Get the number of active polls
$sql = "SELECT COUNT(*) as total FROM polls WHERE is_active = 1";
$result = $conn->query($sql);
$active_polls = $result->fetch_assoc()['total'];

// Get the number of expired polls
$sql = "SELECT COUNT(*) as total FROM polls WHERE is_active = 0";
$result = $conn->query($sql);
$expired_polls = $result->fetch_assoc()['total'];

?>
<div class="adminMainPage">

    <div class="info">
        <h1> <?php echo "Welcome Admin!"; ?> </h1>
        <p>Number of registered users: <?= $total_users ?></p>
        <p>Number of active polls: <?= $active_polls ?></p>
        <p>Number of expired polls: <?= $expired_polls ?></p>
    </div>

    <div class="pollCreate">
        <form method="post">
            <button type="submit" name="pollCreate">Create Poll</button>
        </form>
        <?php
        if (isset($_POST['pollCreate'])) {
            header("Location: index.php?view=pollcreationpage");
        }
        ?>
    </div>
    <div class="pollManage">
        <form method="post">
            <button type="submit" name="pollManage">Manage Polls</button>
        </form>
        <?php
        if (isset($_POST['pollManage'])) {
            header("Location: index.php?view=managepollpage");
        }
        ?>
    </div>
    <div class="creategroups">
        <form method="post">
            <button type="submit" name="createGroups">Create Group</button>
        </form>
        <?php
        if (isset($_POST['createGroups'])) {
            header("Location: index.php?view=creategroupspage");
        }
        ?>
    </div>
</div>
