<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['role'] !== 'Manage' && $_SESSION['role'] !== 'Support') {
    die("You do not have permission to view metrics.");
}

require_once 'db_connect.php';

// 1. Get resolution time for last 4 resolved tickets
$resolutionLabels = [];
$resolutionTimes = [];
$resQuery = "SELECT ID, TIMESTAMPDIFF(MINUTE, created_at, resolved_at) AS minutes
             FROM Tickets
             WHERE resolved_at IS NOT NULL
             ORDER BY resolved_at DESC
             LIMIT 4";
$resResult = mysqli_query($mysqli, $resQuery);
if (!$resResult) {
    die("Resolution time query failed: " . mysqli_error($conn));
}
while ($row = mysqli_fetch_assoc($resResult)) {
    $resolutionLabels[] = "Ticket #" . $row['ID'];
    $resolutionTimes[] = (int)$row['minutes'];
}

// 2. Get monthly ticket volume (last 6 months)
$ticketMonths = [];
$ticketVolume = [];
$volQuery = "SELECT DATE_FORMAT(created_at, '%b') AS month, COUNT(*) AS total
             FROM Tickets
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY month
             ORDER BY month";
$volResult = mysqli_query($mysqli, $volQuery);
if (!$volResult) {
    die("Monthly volume query failed: " . mysqli_error($conn));
}
while ($row = mysqli_fetch_assoc($volResult)) {
    $ticketMonths[] = $row['month'];
    $ticketVolume[] = (int)$row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Metrics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .chart-container { width: 500px; margin-bottom: 40px; }
    </style>
</head>
<body>
    <h1>Metrics</h1>

    <div class="chart-container">
        <h2>Resolution Time (Last 4 Tickets)</h2>
        <canvas id="resolutionTimeChart"></canvas>
    </div>

    <div class="chart-container">
        <h2>Monthly Ticket Volume</h2>
        <canvas id="chartTwo"></canvas>
    </div>

    <form action="dashboard.php" method="get" style="display:inline; margin-top: 15px;">
        <button type="submit">⬅ Back to Dashboard</button>
    </form>

    <script>
        const resolutionLabels = <?php echo json_encode($resolutionLabels); ?>;
        const resolutionTimes = <?php echo json_encode($resolutionTimes); ?>;
        const ticketMonths = <?php echo json_encode($ticketMonths); ?>;
        const ticketVolume = <?php echo json_encode($ticketVolume); ?>;

        new Chart(document.getElementById('resolutionTimeChart'), {
            type: 'bar',
            data: {
                labels: resolutionLabels,
                datasets: [{
                    label: 'Resolution Time (Minutes)',
                    data: resolutionTimes,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            }
        });

        new Chart(document.getElementById('chartTwo'), {
            type: 'line',
            data: {
                labels: ticketMonths,
                datasets: [{
                    label: 'Tickets Submitted',
                    data: ticketVolume,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    fill: false
                }]
            }
        });
    </script>
</body>
</html>
