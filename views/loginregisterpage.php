<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['isAdmin'])) {
    $_SESSION['isAdmin'] = 0;
}


//if the user was logged in and came back to this page somehow (there is a reason for sure just sayin :D)
//set the SESSION field loggedin to be false!
if ($_SESSION['loggedin']) {
    $_SESSION["loggedin"] = false;
}

//variables
$errors = [];
$error_dict = [
    "empty_field" => "All of the fields needs to be filled.",
    "not_enough_username" => "Username length must be at least 5 characters.",
    "not_same_passwords" => "Passwords do not match.",
    "not_enough_password" => "Password length must be at least 10 characters.",
    "present_user" => "The username already exists.",
    "wrong_info" => "Wrong username or password.",
];

// functions
// define the register function
function register(&$errors)
{
    global $conn;

    // access the form data
    $name = trim($_POST['name']);
    $surname = trim($_POST['surname']);
    $birthdate = trim($_POST['birthdate']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // validate the form data
    if (empty($name) || empty($surname) || empty($birthdate) || empty($username) || empty($password) || empty($confirm_password)) {
        $errors[] = "empty_field";
    }
    if (strlen($surname) < 5) {
        $errors[] = "not_enough_username";
    }
    if ($password != $confirm_password) {
        $errors[] = "not_same_passwords";
    }
    if (strlen($password) < 10) {
        $errors[] = "not_enough_password";
    }

    if (!empty($errors)) {
        //show the errors on the page under the Register form with red color!
    }

    if (empty($errors)) {
        // Check if the username already exists in the table
        $sql = "SELECT * FROM Users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // A user with the same username already exists
            $errors[] = "present_user";
        } else {
            // Insert the new user into the table
            $sql = "INSERT INTO Users (surname, name, birthdate, username, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $name, $surname, $birthdate, $username, $password);
            $stmt->execute();

            //free the $errors array
            $errors = [];

            //save into the session that the user is registered/logged in
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $username;

            // redirect the user to the mainpage view
            header("Location: ?view=mainpage");
        }
    }
}

function login(&$errors)
{
    global $conn;
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM Users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // output data of each row
        while ($row = $result->fetch_assoc()) {
            $_SESSION['isAdmin'] = $row["is_admin"];
            if($row["is_admin"] == 1){
                $_SESSION['isAdmin'] = true;
            }
        }

        //free the $errors array
        $errors = [];

        //save into the session that the user is registered/logged in
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;

        if ($_SESSION['isAdmin'] == true) {
            header("Location: ?view=administrator");
        } else {
            header("Location: ?view=mainpage");
        }
    } else if ($result->num_rows == 0) {
        // User with specified username and password does not exist
        $errors[] = "wrong_info";
    }
    if (!empty($errors)) {
        //show the errors on the page under the Login form with red color!
    }

}

// check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Determine which form to process
    if (isset($_POST['form_name'])) {
        if ($_POST['form_name'] == 'login') {
            login($errors);
        } else if ($_POST['form_name'] == 'register') {
            register($errors);
        }

    }
}
?>
<div class="container-login-register">
    <div class="container-login-register1">
        <div class="section-login">
            <h3>Login</h3>
            <form method="POST" novalidate>
                <p>
                    <label for="username">Username:</label>
                    <input name="username" id="username" type="text" placeholder="Username">
                </p>
                <p>
                    <label for="password">Password:</label>
                    <input name="password" id="password" type="password" placeholder="Password">
                </p>
                <p>
                    <input type="submit" value="Login">
                </p>
                <input type="hidden" name="form_name" value="login">
            </form>
        </div>
        <?php if (!empty($errors)) : ?>
            <div class="section-error">
                <h2 style="color: #ff0909;"> ERRORS WITH FILLING THE FORM </h2>
                <ol>
                    <?php foreach ($errors as $error) : ?>
                        <li style="color: #ec5d5d; font-size: 20px"> <?= $error_dict[$error]; ?> </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        <?php endif; ?>
    </div>

    <div class="section-register">
        <h3>Register</h3>
        <form method="POST" novalidate>
            <p>
                <label for="name">Name:</label>
                <input name="name" type="text" id="name" placeholder="Name" value="<?php if (isset($_POST['name'])) {
                    echo htmlspecialchars($_POST['name']);
                } ?>">
            </p>
            <p>
                <label for="surname">Surname:</label>
                <input name="surname" type="text" id="surname" placeholder="Surname"
                       value="<?php if (isset($_POST['surname'])) {
                           echo htmlspecialchars($_POST['surname']);
                       } ?>">
            </p>
            <p>
                <label for="birthdate">Birthdate:</label>
                <input name="birthdate" type="date" id="birthdate" placeholder="Birthdate"
                       value="<?php if (isset($_POST['birthdate'])) {
                           echo htmlspecialchars($_POST['birthdate']);
                       } ?>">
            </p>
            <p>
                <label for="username">Username:</label>
                <input name="username" type="text" id="username" placeholder="Username"
                       value="<?php if (isset($_POST['username'])) {
                           echo htmlspecialchars($_POST['username']);
                       } ?>">
            </p>
            <p>
                <label for="password">Password:</label>
                <input name="password" type="password" id="password" placeholder="Password">
            </p>
            <p>
                <label for="confirm_password">Confirm Password:</label>
                <input name="confirm_password" type="password" id="confirm_password" placeholder="Confirm Password">
            </p>
            <p>
                <input type="submit" value="Register">
            </p>
            <input type="hidden" name="form_name" value="register">
        </form>
    </div>

</div>