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
    </style>
</head>
<body>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="text-primary">Client Dashboard</h1>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <!-- Available Properties -->
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Available Properties</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <?php while ($row = $availableProperties->fetch_assoc()) { ?>
                    <div class="col-md-4">
                        <div class="card">
                            <a href="property_details.php?id=<?php echo $row['id']; ?>" 
                               class="text-decoration-none text-dark">
                                <img src="<?php 
                                    $imagePath = 'uploads/' . htmlspecialchars($row['image_path']);
                                    echo (!empty($row['image_path']) && file_exists($imagePath)) 
                                        ? $imagePath 
                                        : 'placeholder.jpg'; 
                                ?>" class="card-img-top" alt="Property Image">

                                <div class="card-body text-center">
                                    <h5 class="card-title"><?php echo htmlspecialchars($row['property_title']); ?></h5>
                                    <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                                    <p class="card-text"><strong>Price:</strong> 
                                        <span class="text-primary"><?php echo number_format($row['price'], 2); ?> KES</span>
                                    </p>
                                    <p class="card-text"><strong>Status:</strong> 
                                        <span class="badge <?php 
                                            echo ($row['status'] === 'Available') ? 'bg-success' : 
                                                 (($row['status'] === 'Sold') ? 'bg-danger' : 'bg-warning'); 
                                        ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

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
                        <strong>Response:</strong> 
                        <span class="text-<?php echo !empty($row['response']) ? 'success' : 'secondary'; ?>">
                            <?php echo !empty($row['response']) ? htmlspecialchars($row['response']) : 'Pending'; ?>
                        </span><br>
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
