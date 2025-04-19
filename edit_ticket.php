<?php
session_start();

// 1. Check if the user is logged in and has a valid role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // Not logged in, redirect to login
    header("Location: login.php");
    exit();
}

// Only allow roles "Client" or "Support" (or "Manage" if needed) to edit
$validRoles = ["Client", "Support"]; 
if (!in_array($_SESSION['role'], $validRoles)) {
    die("You do not have permission to edit tickets.");
}

// 2. Check for ticket_id in the URL
if (!isset($_GET['ticket_id']) || !is_numeric($_GET['ticket_id'])) {
    die("Invalid ticket ID.");
}
$ticket_id = (int) $_GET['ticket_id'];

require_once 'db_connect.php';

// 4. Handle POST updates to the ticket (subject, description, status, priority)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form values
    $newSubject     = isset($_POST['subject'])     ? trim($_POST['subject'])     : "";
    $newDescription = isset($_POST['description']) ? trim($_POST['description']) : "";
    $newPriority    = isset($_POST['priority'])    ? trim($_POST['priority'])    : "";

    // Update the ticket in the database
    $sql = "UPDATE tickets
                SET subject = ?,
                    description = ?,
                    priority = ?
                WHERE ID = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('sssi', 
        $newSubject,
        $newDescription,
        $newPriority,
        $ticket_id
    );
    if ($stmt->execute()) {
        // Redirect or show success message
        $success_message = "Ticket updated successfully!";
    } else {
        $error_message = "Error updating ticket: " . $stmt->error;
    }
    $stmt->close();
}

// 5. Fetch the existing ticket data to show in the edit form
$sqlTicket = "SELECT * FROM tickets WHERE ID = ?";
$stmt = $mysqli->prepare($sqlTicket);
$stmt->bind_param('i', $ticket_id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();
$stmt->close();

if (!$ticket) {
    die("Ticket not found.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Ticket #<?php echo htmlspecialchars($ticket_id); ?></title>
</head>
<body>

<h1>Edit Ticket #<?php echo htmlspecialchars($ticket_id); ?></h1>

<?php
// Show success or error messages
if (!empty($error_message)) {
    echo "<p style='color: red;'>" . htmlspecialchars($error_message) . "</p>";
}
if (!empty($success_message)) {
    echo "<p style='color: green;'>" . htmlspecialchars($success_message) . "</p>";
}
?>

<form method="POST" action="">
    <label>Subject:</label><br>
    <input type="text" name="subject" 
           value="<?php echo htmlspecialchars($ticket['subject']); ?>" 
           required><br><br>

    <label>Description:</label><br>
    <textarea name="description" rows="5" cols="50" required><?php 
        echo htmlspecialchars($ticket['description']); 
    ?></textarea><br><br>

    <label>Priority:</label><br>
    <select name="priority" required>
        <!-- Example priorities: Low, Medium, High -->
        <option value="Low" 
            <?php if ($ticket['priority'] === 'Low') echo 'selected'; ?>>Low</option>
        <option value="Medium" 
            <?php if ($ticket['priority'] === 'Medium') echo 'selected'; ?>>Medium</option>
        <option value="High" 
            <?php if ($ticket['priority'] === 'High') echo 'selected'; ?>>High</option>
    </select>
    <br><br>

    <button type="submit">Save Changes</button>
</form>

<br>
<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
