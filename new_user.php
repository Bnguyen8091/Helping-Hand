<?php
session_start();

require_once 'db_connect.php';


//Hash New Password
$pwhash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
	$password = trim($_POST['password']);
	$pwhash = hash('sha256', $password);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && ($_POST['id'] == '0')) {
		//Create New User
		$new_firstname = trim($_POST['firstname']);
		$new_lastname = trim($_POST['lastname']);
		$new_access = "Client";
		$new_email = trim($_POST['email']);
		$stmt = $mysqli->prepare("INSERT INTO Users "
			. "(FirstName, LastName, AccessLevel, EmailAddress, Password) "
			. "VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param("sssss", $new_firstname, $new_lastname, 
			$new_access, $new_email, $pwhash);
		$stmt->execute();
		$stmt->close();
		echo("Welcome ". $new_firstname . "! Your account has been created. </br> <a href='login.php'>Return to login page.</a>");
		exit();

} 

?>

<html>
<head><title>User Accounts</title></head>
<body>
<?php

// Get list of users
$users_query = "SELECT * FROM Users";
$stmt = $mysqli->prepare($users_query);
$stmt->execute();
$users = $stmt->get_result();

//set default empty values for user
$edit_id = "0";
$edit_first = "";
$edit_last = "";
$edit_email = "";
$edit_access = "";
$edit_submit = "Create Account";

?>
<h2> Create an account:</h2>
<form method="POST">
	<input type="hidden" name="id" value="<?php echo $edit_id; ?>">
    <table>
		<tr><td>First Name:</td><td><input type="text" name="firstname"
			value="<?php echo htmlspecialchars($edit_first) ?>" ></td></tr>
		<tr><td>Last Name:</td><td><input type="text" name="lastname"
			value="<?php echo htmlspecialchars($edit_last) ?>" ></td></tr>
		<tr><td>Email Address:</td><td><input type="text" name="email"
			value="<?php echo htmlspecialchars($edit_email) ?>" ></td></tr>
        <tr><td>Password:</td><td><input type="password" name="password"></td><td>
			</td></tr>
        <tr><td colspan="2"><input type="submit" 
			value="<?php echo htmlspecialchars($edit_submit) ?>" ></form></td></tr>
</form>
		<td>
			<form action="login.php" method="get">
        		<button type="submit">Cancel</button>
    		</form>
		</td></tr>

    </table>
</form>

</body>
</html>
