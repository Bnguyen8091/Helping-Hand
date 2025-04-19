<?php
session_start();

// Database connection
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

// Get list of tickets based on user
$tickets_query = "SELECT * FROM tickets, ticketusers WHERE (tickets.ID = ticketusers.TicketID) AND tickets.Status = 'Open' AND ticketusers.UserID = ?";
$stmt = $mysqli->prepare($tickets_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tickets = $stmt->get_result();


if (isset($_GET['ticket_success']) && $_GET['ticket_success'] == '1') {
    echo "<script>alert('Ticket created successfully!');</script>";
}
?>

<html>
<head><title>Dashboard</title></head>
<body>
    <h2>Hello <?php echo htmlspecialchars($userdata["FirstName"]); ?>!</h2>

    <?php if ($userdata['AccessLevel'] === 'Client' || $userdata['AccessLevel'] === 'Support'): ?>
        <h3>Select a ticket:</h3>
        <?php while ($row = $tickets->fetch_assoc()): ?>
            <div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">
                <strong>
                    <a href="ticket.php?ticket_id=<?php echo $row['ID']; ?>">
                    Ticket - <?php echo $row['subject']; ?> 
                    </a>
                </strong>
            </div>
        <?php endwhile; ?>

        <form action="create_ticket.php" method="get" style="display:inline;">
            <button type="submit">Create New Ticket</button>
        </form>
    <?php endif; ?>

    <form action="faq.php" method="get" style="display:inline;">
        <button type="submit">📚 View FAQs</button>
    </form>

    <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'Manage' || $_SESSION['role'] === 'Support')): ?>
        <form action="metrics.php" method="get" style="display:inline;">
            <button type="submit">View Metrics</button>
        </form>
    <?php endif; ?>

    <?php if ($userdata['AccessLevel'] === 'Manage'): ?>
        <form action="users.php" method="get" style="display:inline;">
            <button type="submit">User Account Managment</button>
        </form>
    <?php endif; ?>

    <?php if ($userdata['AccessLevel'] === 'Support' || $userdata['AccessLevel'] === 'Manage'): ?>
        <form action="reports.php" method="get" style="display:inline;">
            <button type="submit">Generate reports</button>
        </form>
    <?php endif; ?>

    <form action="login.php" method="get" style="display:inline;">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
