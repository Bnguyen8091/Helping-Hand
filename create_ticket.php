<?php
session_start();

require_once 'db_connect.php';


// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role']; // Could be 'client' or 'support' or something else

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $description = $_POST['description'] ?? '';
    $priority = $_POST['priority'] ?? 'High';

    // If user role is 'support' and we want them to create a ticket on behalf of a client
    $createdBy = $userId; // default is current user
    if ($userRole === 'Support' && !empty($_POST['client_id'])) {
        $createdBy = (int)$_POST['client_id'];
    }

    // Optionally assign the ticket to the support user themselves
    $assignedTo = ($userRole === 'Support') ? $userId : null;

    // Connect and insert
    try {

        $stmt = $mysqli->prepare("
            INSERT INTO tickets (created_by, assigned_to, subject, description, priority) 
            VALUES (:created_by, :assigned_to, :subject, :description, :priority)
        ");
        $stmt->execute([
            ':created_by' => $createdBy,
            ':assigned_to' => $assignedTo,
            ':subject'     => $subject,
            ':description' => $description,
            ':priority'    => $priority
        ]);

        $ticketId = $mysqli->lastInsertId();
        $sql2 = "INSERT INTO ticketusers (TicketID, UserID) VALUES (:ticket_id, :user_id)";
        $stmt2 = $mysqli->prepare($sql2);
        $stmt2->execute([
            ':ticket_id' => $ticketId,
            ':user_id'   => $createdBy
        ]);

        if ($assignedTo) {
            $stmt2->execute([
                ':ticket_id' => $ticketId,
                ':user_id'   => $assignedTo
            ]);
        }

        header("Location: dashboard.php?ticket_success=1");
        exit;
    } catch (PDOException $e) {
        echo "Error creating ticket: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Ticket</title>
</head>
<body>
<h1>Create a New Ticket</h1>

<form method="POST">
    <label for="subject">Subject:</label><br>
    <input type="text" name="subject" id="subject" required><br><br>

    <label for="description">Description:</label><br>
    <textarea name="description" id="description" rows="5" cols="40" required></textarea><br><br>

    <label for="priority">Priority:</label><br>
    <select name="priority" id="priority">
        <option value="Low">Low</option>
        <option value="Medium">Medium</option>
        <option value="High" selected>High</option>
    </select><br><br>

    <?php if ($userRole === 'Support'): ?>
        <!-- Let support user create on behalf of a client -->
        <label for="client_id">Client ID (on behalf of):</label><br>
        <input type="number" name="client_id" id="client_id" placeholder="E.g., 123"><br><br>
    <?php endif; ?>

    <button type="submit">Create Ticket</button>
</form>

<form action="dashboard.php" method="GET" style="display:inline;">
        <button type="submit">Back to Dashboard</button>
</form>

</body>
</html>
