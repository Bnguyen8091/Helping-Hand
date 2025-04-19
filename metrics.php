<?php
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ensure user is Manager or Support to view metrics
if ($_SESSION['role'] !== 'Manage' && $_SESSION['role'] !== 'Support') {
    die("You do not have permission to view metrics.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Metrics</title>
    <!-- Load Chart.js from a CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .chart-container {
            width: 500px;
            margin-bottom: 40px;
        }
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

    <div class="chart-container">
        <h2>Tickets Per Category</h2>
        <canvas id="chartThree"></canvas>
    </div>

    <p><a href="dashboard.php">Back to Dashboard</a></p>

    <script>
        // 1) RESOLUTION TIME - Bar Chart
        const ctx1 = document.getElementById('resolutionTimeChart').getContext('2d');
        const resolutionTimeChart = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['Ticket #101', 'Ticket #102', 'Ticket #103', 'Ticket #104'],
                datasets: [{
                    label: 'Resolution Time (Hours)',
                    data: [4, 8, 2, 6],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)'
                }]
            }
        });

        // 2) MONTHLY TICKET VOLUME - Line Chart
        const ctx2 = document.getElementById('chartTwo').getContext('2d');
        const chartTwo = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Tickets Submitted',
                    data: [45, 60, 50, 70, 65, 55],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    fill: false
                }]
            }
        });

        // 3) TICKETS PER CATEGORY - Pie Chart
        const ctx3 = document.getElementById('chartThree').getContext('2d');
        const chartThree = new Chart(ctx3, {
            type: 'pie',
            data: {
                labels: ['Software Bug', 'Access Request', 'Hardware Issue', 'Other'],
                datasets: [{
                    label: 'Tickets by Category',
                    data: [35, 25, 20, 20],
                    backgroundColor: [
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
                    ]
                }]
            }
        });
    </script>

</body>
</html>
