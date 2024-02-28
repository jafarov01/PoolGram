<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start a session
if(!isset($_SESSION))
{
    session_start();
    $_SESSION['inloginpage'] = false;
}

if (!isset($_SESSION['isAdmin'])) {
    $_SESSION['isAdmin'] = 0;
}

// Determine which view to display
$view = isset($_GET['view']) ? $_GET['view'] : 'mainpage';

// Set the value of $_SESSION['inloginpage'] based on the view
if ($view === 'loginregisterpage') {
    $_SESSION['inloginpage'] = true;
    $_SESSION['username'] = "";
} else {
    $_SESSION['inloginpage'] = false;
}

include 'config/db_connect.php';

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PollGram</title>
    <link rel="stylesheet" href="assets/css/styles.css?<?php echo time(); ?>">
</head>
<body>

<?php

// Include the header template
include 'templates/header.php';

// Include the appropriate view
switch ($view) {
    case 'loginregisterpage':
        include 'views/loginregisterpage.php';
        break;
    case 'mainpage':
        include 'views/mainpage.php';
        break;
    case 'submitvotepage':
        include 'views/submitvotepage.php';
        break;
    case 'updatevotepage':
        include 'views/updatevotepage.php';
        break;
    case 'pollcreationpage':
        include 'views/pollcreationpage.php';
        break;
    case 'administrator':
        include 'views/administrator.php';
        break;
    case 'managepollpage':
        include 'views/managepollpage.php';
        break;
    case 'editpollpage':
        include 'views/editpollpage.php';
        break;
}

// Include the footer template
include 'templates/footer.php';

?>

</body>
</html>
