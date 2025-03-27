<?php
include 'db_connect.php'; // Connects to the database


// Fetch summary statistics
$totalTransactions = $conn->query("SELECT COUNT(*) AS count FROM transactions")->fetch_assoc()['count'] ?? 0;

// Revenue Calculation
$totalRevenue = $conn->query("SELECT SUM(total_amount) AS total FROM transactions WHERE payment_status='Completed'")->fetch_assoc()['total'] ?? 0;

// Fetch Pending Transactions
$pendingTransactions = $conn->query("SELECT COUNT(*) AS count FROM transactions WHERE payment_status='Pending'")->fetch_assoc()['count'] ?? 0;

// Fetch Completed Transactions
$completedTransactions = $conn->query("SELECT COUNT(*) AS count FROM transactions WHERE payment_status='Completed'")->fetch_assoc()['count'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real Estate Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        h1 {
            color: #f4f4f4;
        }
        h2 {
            color: #005580;  
        }

        /* Header */
        header {
            background-color: #005580;
            color: white;
            padding: 15px 0;
            text-align: center;
        }

        header h1 {
            margin: 0;
        }

        nav ul {
            list-style: none;
            padding: 0;
            margin: 10px 0;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        nav ul li {
            display: inline;
            background: #3498db;
        }

        nav ul li a {
            text-decoration: none;
            color: white;
            font-weight: bold;
            padding: 10px 15px;
            transition: 0.3s;
        }

        nav ul li a:hover {
            background-color: #003d66;
            border-radius: 5px;
        }

        /* Main Container */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
        }

        /* Sections */
        section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Forms */
        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        form label {
            font-weight: bold;
        }

        form input, form select, form button {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form button {
            background-color: #005580;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        form button:hover {
            background-color: #003d66;
        }

        /* Summary & Insights */
        .summary, .property-insights, .transactions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            text-align: center;
        }

        .card {
            background: #005580; /* Darker background for better contrast */
            color: white; /* White text for visibility */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Stronger shadow for depth */
            font-size: 18px; /* Larger text for readability */
            font-weight: bold;
            text-align: center;
            transition: transform 0.3s ease-in-out, background-color 0.3s;
        }

        .card:hover {
            transform: scale(1.05); /* Slight enlargement on hover */
            background: #003d66; /* Slightly darker background */
        }


        /* Recent Properties */
        #recent-properties ul {
            list-style: none;
            padding: 0;
        }

        #recent-properties li {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 10px;
            padding: 10px;
            background: #3498db;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        #recent-properties img {
            border-radius: 5px;
            width: 100px;
            height: auto;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
                text-align: center;
            }

            .summary, .property-insights, .transactions {
                grid-template-columns: 1fr;
            }

            #recent-properties li {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<header>
        <h1>Real Estate Dashboard</h1>
        <nav>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>    
                <li><a href="add_property.php">Add Property</a></li>
                <li><a href="admin_virtual_tour.php">Add Tour</a></li>
                <li><a href="insights.php">Insights</a></li>
                <li><a href="transactions.php">Transactions</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
</header>
<!-- Transactions Breakdown -->
<h2>Transactions & Payment Insights</h2>
        <div class="transactions">
            <div class="card">Total Revenue: <strong>$<?php echo number_format($totalRevenue, 2); ?></strong></div>
            <div class="card">Pending Transactions: <strong><?php echo $pendingTransactions; ?></strong></div>
            <div class="card">Completed Transactions: <strong><?php echo $completedTransactions; ?></strong></div>
        </div>

</body>
</html>