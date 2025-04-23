<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DB connection
require_once 'db_connect.php';

// Validate login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Inputs
$ticket_id = isset($_GET['ticket_id']) ? intval($_GET['ticket_id']) : 0;
$user_id = $_SESSION['user_id'];

if (!$ticket_id || !$user_id) {
    die("Missing ticket or user.");
}

// Get user info
$user_query = "SELECT * FROM Users WHERE ID = ?";
$stmt = $mysqli->prepare($user_query);
if (!$stmt) {
    die("User query prepare failed: " . $mysqli->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$userdata = $user_result->fetch_assoc();
$stmt->close();

// Mark as FAQ logic and redirect to FAQ page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_faq']) && $userdata['AccessLevel'] === 'Support') {
    $check = $mysqli->prepare("SELECT FAQ FROM Tickets WHERE ID = ?");
    if (!$check) {
        die("Check FAQ prepare failed: " . $mysqli->error);
    }
    $check->bind_param("i", $ticket_id);
    $check->execute();
    $res = $check->get_result();
    $ticket = $res->fetch_assoc();
    $check->close();

    if ($ticket && isset($ticket['FAQ']) && $ticket['FAQ'] == 0) {
        $faq_stmt = $mysqli->prepare("UPDATE Tickets SET FAQ = 1 WHERE ID = ?");
        if ($faq_stmt) {
            $faq_stmt->bind_param("i", $ticket_id);
            $faq_stmt->execute();
            $faq_stmt->close();
        } else {
            die("FAQ update prepare failed: " . $mysqli->error);
        }
    }

    header("Location: faq.php");
    exit;
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    if ($comment !== '') {
        $stmt = $mysqli->prepare("INSERT INTO TicketComments (TicketID, UserID, Comment, Time) VALUES (?, ?, ?, NOW())");
        if (!$stmt) {
            die("Comment insert prepare failed: " . $mysqli->error);
        }
        $stmt->bind_param("iis", $ticket_id, $user_id, $comment);
        $stmt->execute();
        $stmt->close();
        header("Location: ?ticket_id=$ticket_id");
        exit;
    }
}

$closeStatus = false;
$closed = 'closed';

// Close the ticket (optional toggle)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['close_ticket']) && $userdata['AccessLevel'] === 'Support') {
    $stmt = $mysqli->prepare("UPDATE Tickets SET Status = 'Closed', resolved_at = NOW() WHERE ID = ?");
    if (!$stmt) {
        die("Close ticket prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $stmt->close();
    header("Location: dashboard.php");
    exit;
}

$sqlTicket = "SELECT subject, description, priority
               FROM tickets
              WHERE ID = ?";
$stmt = $mysqli->prepare($sqlTicket);
$stmt->bind_param('i', $ticket_id);
$stmt->execute();
$resultTicket = $stmt->get_result();
$ticketData = $resultTicket->fetch_assoc();
$stmt->close();

if (!$ticketData) {
    die("Ticket not found.");
}

// Fetch comments
$comment_query = "
    SELECT TC.Comment, TC.Time, U.FirstName, U.LastName
    FROM TicketComments TC
    JOIN Users U ON TC.UserID = U.ID
    WHERE TC.TicketID = ?
    ORDER BY TC.Time ASC
";

$stmt = $mysqli->prepare($comment_query);
if (!$stmt) {
    die("Comment fetch prepare failed: " . $mysqli->error);
}
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$comments = $stmt->get_result();
?>

<!-- COMMENTS SECTION -->
<div>
    <!-- 6. Display the ticket's subject, description, priority -->
    <div class="ticket-info">
        <p><strong>Subject:</strong> <?php echo htmlspecialchars($ticketData['subject']); ?></p>
        <p><strong>Description:</strong><br>
            <?php echo nl2br(htmlspecialchars($ticketData['description'])); ?></p>
        <p><strong>Priority:</strong> <?php echo htmlspecialchars($ticketData['priority']); ?></p>
    </div>
    
    <h3>Comments</h3>

    <?php while ($row = $comments->fetch_assoc()): ?>
        <div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">
            <strong><?php echo htmlspecialchars($row['FirstName'] . ' ' . $row['LastName']); ?></strong>
            <em><?php echo $row['Time']; ?></em>
            <p><?php echo nl2br(htmlspecialchars($row['Comment'])); ?></p>
        </div>
    <?php endwhile; ?>

    <h4>Add a Comment</h4>
    <form method="POST">
        <textarea name="comment" rows="4" cols="50" required placeholder="Write a comment..."></textarea><br>
        <button type="submit">Post Comment</button>
    </form>

    <?php if ($userdata['AccessLevel'] === 'Support'): ?>
        <!-- Close Ticket Button -->
        <form method="POST" style="margin-top: 10px;">
            <button type="submit" name="close_ticket">❌ Close Ticket</button>
            <?php $closeStatus = true; ?>
        </form>

        <!-- Mark as FAQ Button -->
        <form method="POST" style="margin-top: 10px;">
            <button type="submit" name="mark_faq">⭐ Mark Ticket as FAQ</button>
        </form>
    <?php endif; ?>

    <!-- Only show the Edit button if the user is Client or Support -->
    <?php if ($_SESSION['role'] === "Client" || $_SESSION['role'] === "Support"): ?>
        <a href="edit_ticket.php?ticket_id=<?php echo $ticket_id; ?>">
            <button>Edit Ticket</button>
        </a>
    <?php endif; ?>

    <!-- Back to Dashboard Button -->
    <form action="dashboard.php" method="get" style="display:inline; margin-top: 15px;">
        <button type="submit">Back to Dashboard</button>
    </form>
</div>
