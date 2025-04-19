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

// Get list of users
$users_query = "SELECT * FROM Users";
$stmt = $mysqli->prepare($users_query);
$stmt->execute();
$users = $stmt->get_result();

// Get list of all tickets
$tickets_query = "SELECT * FROM tickets";
$stmt = $mysqli->prepare($tickets_query);
$stmt->execute();
$tickets = $stmt->get_result();

// Count Open Low tickets
$countOpenLow_query = "SELECT * FROM tickets WHERE Status = 'Open' AND priority = 'Low'";
$stmt = $mysqli->prepare($countOpenLow_query);
$stmt->execute();
$openLowTickets = $stmt->get_result();
$total_openLowTickets = $openLowTickets->num_rows;

// Count Open Medium tickets
$countOpenMedium_query = "SELECT * FROM tickets WHERE Status = 'Open' AND priority = 'Medium'";
$stmt = $mysqli->prepare($countOpenMedium_query);
$stmt->execute();
$openMediumTickets = $stmt->get_result();
$total_openMediumTickets = $openMediumTickets->num_rows;

// Count Open High tickets
$countOpenHigh_query = "SELECT * FROM tickets WHERE Status = 'Open' AND priority = 'High'";
$stmt = $mysqli->prepare($countOpenHigh_query);
$stmt->execute();
$openHighTickets = $stmt->get_result();
$total_openHighTickets = $openHighTickets->num_rows;

// Count Total Open tickets
$total_openTickets = $total_openLowTickets + $total_openMediumTickets + $total_openHighTickets;

// Count Closed Low tickets
$countClosedLow_query = "SELECT * FROM tickets WHERE Status = 'Closed' AND priority = 'Low'";
$stmt = $mysqli->prepare($countClosedLow_query);
$stmt->execute();
$closedLowTickets = $stmt->get_result();
$total_closedLowTickets = $closedLowTickets->num_rows;

// Count Total Low tickets
$total_lowTickets = $total_openLowTickets + $total_closedLowTickets;

// Count Closed Medium tickets
$countClosedMedium_query = "SELECT * FROM tickets WHERE Status = 'Closed' AND priority = 'Medium'";
$stmt = $mysqli->prepare($countClosedMedium_query);
$stmt->execute();
$closedMediumTickets = $stmt->get_result();
$total_closedMediumTickets = $closedMediumTickets->num_rows;

// Count Total Medium tickets
$total_mediumTickets = $total_openMediumTickets + $total_closedMediumTickets;

// Count Closed High tickets
$countClosedHigh_query = "SELECT * FROM tickets WHERE Status = 'Closed' AND priority = 'High'";
$stmt = $mysqli->prepare($countClosedHigh_query);
$stmt->execute();
$closedHighTickets = $stmt->get_result();
$total_closedHighTickets = $closedHighTickets->num_rows;

// Count Total High tickets
$total_highTickets = $total_openHighTickets + $total_closedHighTickets;

// Count Total Closed tickets
$total_closedTickets = $total_closedLowTickets + $total_closedMediumTickets + $total_closedHighTickets;

// Count Total tickets
$total_tickets = $total_openTickets + $total_closedTickets;
?>

<html>
<head><title>Reports</title></head>
<body>
    <h2>Report Tickets Table</h2>
    <table border="1" >
        <tr>
            <th></th>
            <th>Low</th>
            <th>Medium</th>
            <th>High</th>
            <th>Total</th>
        </tr>
        <tr>
            <th>Open</th>
            <th><?php echo $total_openLowTickets?> <!-- Open Low Tickets -->
            <th><?php echo $total_openMediumTickets?> <!-- Open Medium Tickets -->
            <th><?php echo $total_openHighTickets?> <!-- Open High Tickets -->
            <th><?php echo $total_openTickets?> <!-- Total Open Tickets -->
        </tr>
        <tr>
            <th>Closed</th>
            <th><?php echo $total_closedLowTickets?> <!-- Closed Low Tickets -->
            <th><?php echo $total_closedMediumTickets?> <!-- Closed Medium Tickets -->
            <th><?php echo $total_closedHighTickets?> <!-- Closed High Tickets -->
            <th><?php echo $total_closedTickets?> <!-- Total Closed Tickets -->
        </tr>
        <tr>
            <th>Total</th>
            <th><?php echo $total_lowTickets?> <!-- Total Low Tickets -->
            <th><?php echo $total_mediumTickets?> <!-- Total Medium Tickets -->
            <th><?php echo $total_highTickets?> <!-- Total High Tickets -->
            <th><?php echo $total_tickets?> <!-- Total Tickets -->
    </table>


    <!-- Back to Dashboard Button -->
    <form action="dashboard.php" method="get" style="display:inline; margin-top: 15px;">
        <button type="submit">Back to Dashboard</button>
    </form>
</body>
</html>
