<?php
session_start();

require_once 'db_connect.php';

// Validate session
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch FAQ tickets
$faq_query = "
    SELECT T.ID, T.subject, T.Status, T.created_at, U.FirstName, U.LastName
    FROM Tickets T
    JOIN Users U ON T.created_by = U.ID
    WHERE T.FAQ = 1
    ORDER BY T.created_at DESC
";

$result = $mysqli->query($faq_query);

// Check for query errors
if (!$result) {
    die("Query failed: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>FAQ Tickets</title>
</head>
<body>
    <h2>Frequently Asked Questions (Tickets)</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($ticket = $result->fetch_assoc()): ?>
            <div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">
                <strong>
                    <a href="ticket.php?ticket_id=<?php echo $ticket['ID']; ?>">
                        <?php echo htmlspecialchars($ticket['subject']); ?>
                    </a>
                </strong>
                <p>Status: <strong><?php echo $ticket['Status']; ?></strong></p>
                <p>Created by: <?php echo htmlspecialchars($ticket['FirstName'] . ' ' . $ticket['LastName']); ?></p>
                <p>Created at: <?php echo $ticket['created_at']; ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No FAQ-marked tickets yet.</p>
    <?php endif; ?>

    <form action="dashboard.php" method="POST">
        <button type="submit">⬅ Back to Dashboard</button>
    </form>
</body>
</html>
