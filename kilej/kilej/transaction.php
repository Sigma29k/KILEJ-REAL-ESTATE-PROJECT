<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['client_id'])) {
    die("Error: Client is not logged in. <a href='login.php'>Login here</a>");
}

$client_id = intval($_SESSION['client_id']); // Ensure client_id is an integer


// Fetch client transactions securely
$stmt = $conn->prepare("
    SELECT p.property_title, t.total_amount, t.payment_status 
    FROM transactions t 
    JOIN property_listings p ON t.id = p.id 
    WHERE t.client_id = ? 
    ORDER BY t.transaction_date DESC
");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$clientTransactions = $stmt->get_result();

// Fetch client feedback securely
$stmt = $conn->prepare("
    SELECT message, response, date_submitted 
    FROM feedback 
    WHERE client_id = ? 
    ORDER BY date_submitted DESC
");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$clientFeedback = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background-color: #212529;
        }
        .navbar-brand, .nav-link {
            color: white !important;
        }
        .nav-link.active {
            font-weight: bold;
            background-color: #ffc107; /* Yellow background for active tab */
            color: black !important;
            border-radius: 5px;
        }
        .logout-btn {
            background-color: red;
            border: none;
        }
        .logout-btn a{
            color: #f8f9fa;
        }
        .card {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="client_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="properties.php">Properties</a></li>
                    <li class="nav-item"><a class="nav-link" href="virtual_tour.php">Virtual Tour</a></li>
                    <li class="nav-item"><a class="nav-link active" href="transaction.php">Transactions</a></li>
                    <li class="nav-item"><a class="nav-link" href="feedback.php">Feedback</a></li>
                </ul>
            </div>
            <button class="btn btn-danger logout-btn"><a href="logout.php">Logout</a></button>
        </div>
    </nav>

    <!-- Client Transactions -->
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between">
            <h4 class="mb-0">Your Transactions</h4>
            <input type="text" id="searchTransaction" class="form-control form-control-sm w-25" placeholder="Search...">
        </div>
        <div class="card-body">
            <table class="table table-striped" id="transactionsTable">
                <thead class="table-dark">
                    <tr>
                        <th>Property</th>
                        <th>Amount (KES)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $clientTransactions->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['property_title']); ?></td>
                            <td><?php echo number_format($row['total_amount'], 2); ?></td>
                            <td><span class="badge bg-<?php echo ($row['payment_status'] == 'Paid') ? 'success' : 'warning'; ?>">
                                <?php echo htmlspecialchars($row['payment_status']); ?>
                            </span></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>



<script>
    document.getElementById("searchTransaction").addEventListener("keyup", function () {
    let searchValue = this.value.toLowerCase();
    let table = document.getElementById("transactionsTable");
    let rows = table.getElementsByTagName("tr");
    let found = false;

    for (let i = 1; i < rows.length; i++) {  // Start from index 1 to skip table headers
        let cells = rows[i].getElementsByTagName("td");
        let match = false;

        for (let j = 0; j < cells.length; j++) {
            if (cells[j].textContent.toLowerCase().includes(searchValue)) {
                match = true;
                break;
            }
        }

        if (match) {
            rows[i].style.display = "";
            found = true;
        } else {
            rows[i].style.display = "none";
        }
    }

    let noResultRow = document.getElementById("noTransactionRow");
    if (!found) {
        if (!noResultRow) {
            let newRow = table.insertRow();
            newRow.id = "noTransactionRow";
            let cell = newRow.insertCell(0);
            cell.colSpan = 3;
            cell.className = "text-center text-danger";
            cell.innerHTML = "No transaction found";
        }
    } else {
        if (noResultRow) noResultRow.remove();
    }
});
</script>

</body>
</html>