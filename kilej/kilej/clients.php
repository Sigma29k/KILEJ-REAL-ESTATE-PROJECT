<?php
require 'db_connect.php'; // Database connection file

// Fetch recent clients
$recentClients = $conn->query("SELECT * FROM clients ORDER BY client_id DESC LIMIT 5");

// Fetch client categories
$clientCategoryQuery = "SELECT client_type, COUNT(*) as count FROM clients GROUP BY client_type";
$clientCategoryResult = $conn->query($clientCategoryQuery);

// Fetch client activity status
$clientStatusQuery = "SELECT client_type, COUNT(*) as count FROM clients WHERE client_id IN (SELECT DISTINCT client_id FROM transactions)";
$clientStatusResult = $conn->query($clientStatusQuery);

// Fetch contractors by specialization
$contractorQuery = "SELECT specialization, COUNT(*) as count FROM contractors GROUP BY specialization";
$contractorResult = $conn->query($contractorQuery);

// Fetch top contractors by completed projects
$topContractorsQuery = "SELECT c.contractor_name, COUNT(hc.project_id) as completed_projects FROM contractors c
LEFT JOIN house_construction hc ON c.contractor_id = hc.contractor_id
WHERE hc.house_status = 'Completed'
GROUP BY c.contractor_id ORDER BY completed_projects DESC LIMIT 5";
$topContractorsResult = $conn->query($topContractorsQuery);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client & Contractor Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Client & Contractor Dashboard</h2>
        
        <!-- Recent Clients -->
        <h4>Recent Clients Added</h4>
        <table class="table table-striped">
            <tr><th>Name</th><th>Phone</th><th>Email</th><th>Type</th></tr>
            <?php while ($row = $recentClients->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['client_name']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['client_type']; ?></td>
                </tr>
            <?php } ?>
        </table>

        <!-- Client Categories -->
        <h4>Client Categories</h4>
        <table class="table table-bordered">
            <tr><th>Category</th><th>Count</th></tr>
            <?php while ($row = $clientCategoryResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['client_type']; ?></td>
                    <td><?php echo $row['count']; ?></td>
                </tr>
            <?php } ?>
        </table>

        <!-- Client Activity Status -->
        <h4>Client Activity Status</h4>
        <table class="table table-bordered">
            <tr><th>Client Type</th><th>Active Clients</th></tr>
            <?php while ($row = $clientStatusResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['client_type']; ?></td>
                    <td><?php echo $row['count']; ?></td>
                </tr>
            <?php } ?>
        </table>

        <!-- Contractors by Specialization -->
        <h4>Contractors by Specialization</h4>
        <table class="table table-bordered">
            <tr><th>Specialization</th><th>Count</th></tr>
            <?php while ($row = $contractorResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['specialization']; ?></td>
                    <td><?php echo $row['count']; ?></td>
                </tr>
            <?php } ?>
        </table>

        <!-- Top Contractors -->
        <h4>Top Contractors</h4>
        <table class="table table-striped">
            <tr><th>Name</th><th>Completed Projects</th></tr>
            <?php while ($row = $topContractorsResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['contractor_name']; ?></td>
                    <td><?php echo $row['completed_projects']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
