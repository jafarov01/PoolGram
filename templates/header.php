<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['isAdmin'])) {
    $_SESSION['isAdmin'] = 0;
}

if (isset($_GET["login"])) {
    header("Location: ?view=loginregisterpage");
}

if (isset($_GET["logout"])) {
    $_SESSION['loggedin'] = false;
    $_SESSION['isAdmin'] = 0;
    header("Location: ?view=loginregisterpage");
}

if (isset($_POST['adminPage'])) {
    header("Location: ?view=administrator");
}
?>
<header>
    <div class="logo" style="text-decoration: none; cursor: pointer;" onclick="location.href='index.php?view=mainpage'">PollGram</div>

    <div class="login-button <?php if ($_SESSION['loggedin'] || $_SESSION['inloginpage']) {
        echo 'hidden';
    } ?>">
        <form action="" method="get">
            <button type="submit" name="login">Log In</button>
        </form>

    </div>
    <div class="navBar">

        <div class="login-info <?php if (!$_SESSION['loggedin']) {
            echo 'hidden';
        } ?>">
            <h1 class="userNameNav"> <?= $_SESSION["username"] ?></h1>
        </div>
        <div class="logout-button <?php if (!$_SESSION['loggedin']) {
            echo 'hidden';
        } ?>">
            <form action="" method="get">
                <button type="submit" name="logout">Log out</button>
            </form>
        </div>
        <div class="adminPage <?php if (!$_SESSION['isAdmin']) {
            echo 'hidden';
        } ?>">
            <form action="" method="post">
                <button type="submit" name="adminPage">Administrator Page</button>
            </form>
        </div>
    </div>

</header>