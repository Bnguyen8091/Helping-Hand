<?php
session_start();

require_once 'db_connect.php';

// Validate session
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user info
$user_query = "SELECT * FROM Users WHERE ID = ?";
$stmt = $mysqli->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$userdata = $user_result->fetch_assoc();


if ($userdata["AccessLevel"] == "Manage") {

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
			$new_access = trim($_POST['access']);
			$new_email = trim($_POST['email']);
			$stmt = $mysqli->prepare("INSERT INTO Users "
				. "(FirstName, LastName, AccessLevel, EmailAddress, Password) "
				. "VALUES (?, ?, ?, ?, ?)");
			$stmt->bind_param("sssss", $new_firstname, $new_lastname, 
				$new_access, $new_email, $pwhash);
			$stmt->execute();
			$stmt->close();
			header("Location: #");
			exit();

	} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
		//Update Existing User
		$new_firstname = trim($_POST['firstname']);
		$new_lastname = trim($_POST['lastname']);
		$new_access = trim($_POST['access']);
		$new_email = trim($_POST['email']);
		$id = trim($_POST['id']);
		$stmt = $mysqli->prepare("UPDATE Users SET "
			. "FirstName = ?, LastName = ?, AccessLevel = ?, EmailAddress = ? "
			. "WHERE ID = ?");
		$stmt->bind_param("ssssi", $new_firstname, $new_lastname, 
			$new_access, $new_email, $id);
		$stmt->execute();
		$stmt->close();

		if ($pwhash != '') {
			$stmt = $mysqli->prepare("UPDATE Users SET "
				. "Password = ? "
				. "WHERE ID = ?");
			$stmt->bind_param("si", $pwhash, $id);
			$stmt->execute();
			$stmt->close();
		}
		header("Location: #");
		exit();
	}
} else {
	header("Location: dashboard.php");
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

?>
<h2>Select a user to edit:</h2>
<table border="1" >
<tr><td>Name</td><td>Email Address</td><td>Access Level</td></tr>
<?php
/* fetch associative array */
while ($row = $users->fetch_assoc()) {
    echo "<tr><td>" . $row["FirstName"] . " " . $row["LastName"] 
		. "</td><td>" . $row["EmailAddress"] . "</td><td>"
		. $row["AccessLevel"] . "</td><td><a href='users.php?id="
		. $row["ID"] . "'>Edit</a></tr>";
}
?>
</table>
</br>
<form action="dashboard.php" method="get">
        		<button type="submit">Return to Dashboard</button>
</form>
<?php
//set default empty values for user
$edit_id = "0";
$edit_first = "";
$edit_last = "";
$edit_email = "";
$edit_access = "";
$edit_submit = "Create Account";


//lookup up requested user data
if (isset($_GET['id'])&& $_GET['id'] != 0) {
   	$edit_id = $_GET['id'];
   	$edit_query = "SELECT * FROM Users WHERE ID = ?";
	$stmt = $mysqli->prepare($edit_query);
	$stmt->bind_param("i", $edit_id);
	$stmt->execute();
	$user_result = $stmt->get_result();
	$editdata = $user_result->fetch_assoc();

	//replace default empty values
	$edit_id = $editdata["ID"];
	$edit_first = $editdata["FirstName"];
	$edit_last = $editdata["LastName"];
	$edit_email = $editdata["EmailAddress"];
	$edit_access = $editdata["AccessLevel"];
	$edit_submit = "Update";
}
?>
</br>
</br>

<form method="POST">
	<input type="hidden" name="id" value="<?php echo $edit_id; ?>">
    <table>
		<tr><td>First Name:</td><td><input type="text" name="firstname"
			value="<?php echo htmlspecialchars($edit_first) ?>" ></td></tr>
		<tr><td>Last Name:</td><td><input type="text" name="lastname"
			value="<?php echo htmlspecialchars($edit_last) ?>" ></td></tr>
		<tr><td>Email Address:</td><td><input type="text" name="email"
			value="<?php echo htmlspecialchars($edit_email) ?>" ></td></tr>
        <tr><td>Access Level:</td><td>
		<select name="access">
   	 		<option value="Client"
				<?php if ($edit_access == "Client") {echo "Selected";} ?>
				>Client</option>
    		<option value="Support"
				<?php if ($edit_access == "Support") {echo "Selected";} ?>
				>Support</option>
    		<option value="Manage"
				<?php if ($edit_access == "Manage") {echo "Selected";} ?>
				>Manage</option>
  		</select></td></tr>
        <tr><td>Password:</td><td><input type="password" name="password"></td><td>
			(Blank Password = No Change)</td></tr>
        <tr><td colspan="2"><input type="submit" 
			value="<?php echo htmlspecialchars($edit_submit) ?>" ></form></td></tr>
</form>
		<td>
			<form method="get">
				<input type="hidden" name="id" value="0">
        		<button type="submit">Cancel</button>
    		</form>
		</td></tr>

    </table>
</form>

</body>
</html>
