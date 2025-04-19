<?php
session_start();


require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $pwhash = hash('sha256', $password);

    if ($email !== '' && $password !== '') {
        $user_query = "SELECT * FROM Users WHERE EmailAddress = ?";
        $stmt = $mysqli->prepare($user_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if ($pwhash === $user['Password']) {
                $_SESSION['user_id'] = $user['ID'];
                $_SESSION['role']    = $user['AccessLevel'];
                header('Location: dashboard.php');
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No user found with that email.";
        }
    } else {
        $error = "Please enter both email and password.";
    }
}
?>


<html>
<head><title>Login</title></head>
<body>
<center>
<h2>Login</h2>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <table>
        <tr><td>Email:</td><td><input type="text" name="email"></td></tr>
        <tr><td>Password:</td><td><input type="password" name="password"></td></tr>
        <tr><td colspan="2"><input type="submit" value="Login"></td></tr>
</form>
        <tr><td colspan="2">
            <form action="new_user.php" method="get">
                <input type="submit" value="Create an Account">
            </form>
        </td></tr>

    </table>

</center>
</body>
</html>
