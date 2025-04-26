<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if ticket ID is passed
if (!isset($_GET['ticket_id']) || !is_numeric($_GET['ticket_id'])) {
    die("Invalid ticket ID.");
}

$ticket_id = intval($_GET['ticket_id']); // sanitize it

require_once 'db_connect.php';

$stmt = $mysqli->prepare("SELECT * FROM TicketUsers WHERE TicketID = ? AND UserID = ?");
$stmt->bind_param("ii", $ticket_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();



// Pass variables to comment section
include 'comments_section.php';
?>
