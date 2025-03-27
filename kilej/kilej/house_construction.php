<?php
require 'db_connect.php'; // Database connection file

// Fetch ongoing projects
$ongoingProjects = $conn->query("SELECT hc.project_id, c.client_name, p.property_title, ctr.contractor_name FROM house_construction hc
JOIN clients c ON hc.client_id = c.client_id
JOIN property_listings p ON hc.property_id = p.property_id
JOIN contractors ctr ON hc.contractor_id = ctr.contractor_id
WHERE hc.house_status != 'Completed'");


// Fetch project status breakdown
$statusQuery = "SELECT house_status, COUNT(*) as count FROM house_construction GROUP BY house_status";
$statusResult = $conn->query($statusQuery);

// Fetch estimated completion dates
$completionDates = $conn->query("SELECT hc.project_id, c.client_name, p.property_title, hc.completion_date FROM house_construction hc
JOIN clients c ON hc.client_id = c.client_id
JOIN property_listings p ON hc.property_id = p.property_id");

// Fetch construction cost distribution (dummy data for now)
$costDistribution = [
    'Materials' => 40,
    'Labor' => 35,
    'Miscellaneous' => 25
];

// Fetch reported delays and issues
$delays = $conn->query("SELECT hc.project_id, c.client_name, p.property_title, hc.house_status FROM house_construction hc
JOIN clients c ON hc.client_id = c.client_id
JOIN property_listings p ON hc.property_id = p.property_id
WHERE hc.house_status = 'Delayed'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Construction Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">House Construction Management</h2>
        
        <!-- Ongoing Projects -->
        <h4>Ongoing Projects</h4>
        <table class="table table-striped">
            <tr><th>Client</th><th>Property</th><th>Contractor</th></tr>
            <?php while ($row = $ongoingProjects->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['client_name']; ?></td>
                    <td><?php echo $row['property_title']; ?></td>
                    <td><?php echo $row['contractor_name']; ?></td>
                </tr>
            <?php } ?>
        </table>

        <!-- Project Status Breakdown -->
        <h4>Project Status Breakdown</h4>
        <canvas id="statusChart"></canvas>
        <script>
            const ctx = document.getElementById('statusChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: [<?php while ($row = $statusResult->fetch_assoc()) echo "'" . $row['house_status'] . "',"; ?>],
                    datasets: [{
                        data: [<?php $statusResult->data_seek(0); while ($row = $statusResult->fetch_assoc()) echo $row['count'] . ","; ?>],
                        backgroundColor: ['#ff6384', '#36a2eb', '#ffce56']
                    }]
                }
            });
        </script>

        <!-- Estimated Completion Dates -->
        <h4>Estimated Completion Dates</h4>
        <table class="table table-bordered">
            <tr><th>Client</th><th>Property</th><th>Completion Date</th></tr>
            <?php while ($row = $completionDates->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['client_name']; ?></td>
                    <td><?php echo $row['property_title']; ?></td>
                    <td><?php echo $row['completion_date']; ?></td>
                </tr>
            <?php } ?>
        </table>

        <!-- Construction Cost Distribution -->
        <h4>Construction Cost Distribution</h4>
        <canvas id="costChart"></canvas>
        <script>
            const costCtx = document.getElementById('costChart').getContext('2d');
            new Chart(costCtx, {
                type: 'bar',
                data: {
                    labels: ['Materials', 'Labor', 'Miscellaneous'],
                    datasets: [{
                        data: [40, 35, 25],
                        backgroundColor: ['#ff6384', '#36a2eb', '#ffce56']
                    }]
                }
            });
        </script>

        <!-- Delays & Issues Reported -->
        <h4>Delays & Issues Reported</h4>
        <table class="table table-danger">
            <tr><th>Client</th><th>Property</th><th>Status</th></tr>
            <?php while ($row = $delays->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['client_name']; ?></td>
                    <td><?php echo $row['property_title']; ?></td>
                    <td><?php echo $row['house_status']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
