<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT id, password FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $login_error = "Invalid email or password.";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <h1 style="text-align:center;margin-top:30px;">Login</h1>
    <?php if (isset($login_error)) {
        echo "<p>$login_error</p>";
    } ?>
    <form class="mx-auto" style="max-width:50%;" method="post">
        <div class="form-outline mb-4">
            <label class="form-label" for="form2Example1">Email address</label>
            <input type="email" name="email" id="form2Example1" class="form-control" />   
        </div>

        <div class="form-outline mb-4">
            <label class="form-label" for="form2Example2">Password</label>
            <input type="password" name="password" id="form2Example2" class="form-control" />  
        </div>

        <input class="btn btn-primary btn-block mb-4" type="submit" value="Login">

        <div class="text-center">
            <p>Not a member? <a href="register.php">Register</a></p>
        </div>
    </form>
</body>

</html>