<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['client_id'])) {
    die("Error: Client is not logged in. <a href='login.php'>Login here</a>");
}

$client_id = intval($_SESSION['client_id']); // Ensure client_id is an integer

// Fetch available properties using prepared statement
$query = "SELECT id, property_title, location, price, status, image_path FROM property_listings ORDER BY date_added DESC";
$availableProperties = $conn->query($query);

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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Client Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 900px; margin-top: 20px; }
        .card { margin-bottom: 20px; }      
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
                    <li class="nav-item"><a class="nav-link" href="transaction.php">Transactions</a></li>
                    <li class="nav-item"><a class="nav-link active" href="feedback.php">Feedback</a></li>
                </ul>
            </div>
            <button class="btn btn-danger logout-btn"><a href="logout.php">Logout</a></button>
        </div>
    </nav>

    <!-- Client Feedback -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">Your Feedback</h4>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <?php while ($row = $clientFeedback->fetch_assoc()) { ?>
                    <li class="list-group-item">
                        <strong>Your Message:</strong> <?php echo htmlspecialchars($row['message']); ?><br>
                        <em>Submitted on: <?php echo htmlspecialchars($row['date_submitted']); ?></em>
                    </li>
                <?php } ?>
            </ul>

            <!-- Feedback Submission -->
            <h5 class="mt-3">Submit New Feedback</h5>
            <form id="feedbackForm">
                <div class="mb-3">
                    <textarea id="feedbackMessage" class="form-control" rows="3" placeholder="Enter your feedback..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <div id="feedbackAlert" class="alert mt-3 d-none"></div>
        </div>
    </div>

</div>

<script>
$(document).ready(function () {
    // Search Transactions
    $("#searchTransaction").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#transactionsTable tbody tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Feedback Submission
    $("#feedbackForm").on("submit", function (e) {
        e.preventDefault();
        var feedbackMessage = $("#feedbackMessage").val().trim();

        if (feedbackMessage === "") {
            $("#feedbackAlert").removeClass("d-none alert-success").addClass("alert-danger").text("Feedback cannot be empty.");
            return;
        }

        $.post("submit_feedback.php", { message: feedbackMessage }, function (response) {
            $("#feedbackAlert").toggleClass("alert-success alert-danger", response === "success").removeClass("d-none").text(
                response === "success" ? "Feedback submitted successfully!" : "Failed to submit feedback."
            );
        });
    });
});
</script>

</body>
</html>
