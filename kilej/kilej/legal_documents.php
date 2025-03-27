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

// Fetch legal documents
$legalDocsQuery = "SELECT document_type, COUNT(*) as count FROM legal_documents GROUP BY document_type";
$legalDocsResult = $conn->query($legalDocsQuery);

// Fetch transactions missing legal documents
$missingDocsQuery = "SELECT COUNT(*) as count FROM transactions t LEFT JOIN legal_documents l ON t.transaction_id = l.transaction_id WHERE l.transaction_id IS NULL";
$missingDocsResult = $conn->query($missingDocsQuery);
$missingDocsCount = $missingDocsResult->fetch_assoc()['count'];

// Fetch recent feedback
$feedbackQuery = "SELECT c.client_name, f.message FROM feedback f JOIN clients c ON f.client_id = c.client_id ORDER BY f.feedback_id DESC LIMIT 5";
$feedbackResult = $conn->query($feedbackQuery);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Estate Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Real Estate Dashboard</h2>
        
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

        <!-- Legal & Compliance -->
        <h4>Legal & Compliance</h4>
        <table class="table table-bordered">
            <tr><th>Document Type</th><th>Count</th></tr>
            <?php while ($row = $legalDocsResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['document_type']; ?></td>
                    <td><?php echo $row['count']; ?></td>
                </tr>
            <?php } ?>
        </table>
        <p><strong>Missing Documents Alert:</strong> <?php echo $missingDocsCount; ?> transactions have no legal documents uploaded.</p>

        <!-- Feedback & Customer Engagement -->
        <h4>Recent Client Feedback</h4>
        <table class="table table-striped">
            <tr><th>Client</th><th>Feedback</th></tr>
            <?php while ($row = $feedbackResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['client_name']; ?></td>
                    <td><?php echo $row['message']; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
