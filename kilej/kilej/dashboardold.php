<?php
include 'db_connect.php'; // Connects to the database

// Handle property addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_title = $_POST['property_title'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $status = $_POST['status'];
    $seller_name = $_POST['seller_name'];
    $seller_phone = $_POST['seller_phone'];
    $seller_email = $_POST['seller_email'];

    // Image upload handling
    $target_dir = "uploads/";
    $target_file = "";

    if (!empty($_FILES['image']['name'])) {
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $target_file = $image_name; // Store only filename in DB
        } else {
            $target_file = ""; // Failed to upload
        }
    }

    // Check if the seller already exists
    $stmt = $conn->prepare("SELECT seller_id FROM sellers WHERE email = ?");
    $stmt->bind_param("s", $seller_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $seller = $result->fetch_assoc();

    if ($seller) {
        $seller_id = $seller['seller_id'];
    } else {
        // Insert new seller into sellers table
        $stmt = $conn->prepare("INSERT INTO sellers (name, phone, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $seller_name, $seller_phone, $seller_email);
        if ($stmt->execute()) {
            $seller_id = $stmt->insert_id;
        } else {
            echo "<p style='color: red;'>Error adding seller: " . $stmt->error . "</p>";
            exit;
        }
    }

    // Insert property listing
    $stmt = $conn->prepare("INSERT INTO property_listings (property_title, location, price, status, image_path, seller_id, date_added) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssssi", $property_title, $location, $price, $status, $target_file, $seller_id);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Property added successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error adding property: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// Fetch summary statistics
$totalProperties = $conn->query("SELECT COUNT(*) AS count FROM property_listings")->fetch_assoc()['count'] ?? 0;
$totalClients = $conn->query("SELECT COUNT(*) AS count FROM clients")->fetch_assoc()['count'] ?? 0;
$totalContractors = $conn->query("SELECT COUNT(*) AS count FROM contractors")->fetch_assoc()['count'] ?? 0;
$totalTransactions = $conn->query("SELECT COUNT(*) AS count FROM transactions")->fetch_assoc()['count'] ?? 0;
$totalFeedback = $conn->query("SELECT COUNT(*) AS count FROM feedback")->fetch_assoc()['count'] ?? 0;
$totalLegalDocs = $conn->query("SELECT COUNT(*) AS count FROM legal_documents")->fetch_assoc()['count'] ?? 0;

// Property Listings Insights
$availableProperties = $conn->query("SELECT COUNT(*) AS count FROM property_listings WHERE status='Available'")->fetch_assoc()['count'] ?? 0;
$soldProperties = $conn->query("SELECT COUNT(*) AS count FROM property_listings WHERE status='Sold'")->fetch_assoc()['count'] ?? 0;
$underContractProperties = $conn->query("SELECT COUNT(*) AS count FROM property_listings WHERE status='Under Contract'")->fetch_assoc()['count'] ?? 0;

// Fetch recent properties
$recentProperties = $conn->query("SELECT property_title, location, image_path FROM property_listings ORDER BY date_added DESC LIMIT 5");

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
    
</head>
<body>
    <div class="container">
        <h1>Real Estate Dashboard</h1>

        <!-- Property Addition Form -->
        <h2>Add a New Property</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Property Title:</label>
            <input type="text" name="property_title" required>
            
            <label>Location:</label>
            <input type="text" name="location" required>

            <label>Price:</label>
            <input type="number" name="price" required>

            <label>Status:</label>
            <select name="status">
                <option value="Available">Available</option>
                <option value="Sold">Sold</option>
                <option value="Under Contract">Under Contract</option>
            </select>

            <h3>Seller Details</h3>
            <div class="form-group">
                <label>Seller Name:</label>
                <input type="text" name="seller_name" required>
            </div>

            <div class="form-group">
                <label>Seller Phone:</label>
                <input type="text" name="seller_phone" required>
            </div>

            <div class="form-group">
                <label>Seller Email:</label>
                <input type="email" name="seller_email" required>
            </div>

            <label>Upload Image:</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit">Add Property</button>
        </form>

        <!-- Summary Section -->
        <div class="summary">
            <div class="card">Total Properties: <strong><?php echo $totalProperties; ?></strong></div>
            <div class="card">Total Clients: <strong><?php echo $totalClients; ?></strong></div>
            <div class="card">Total Contractors: <strong><?php echo $totalContractors; ?></strong></div>
            <div class="card">Total Transactions: <strong><?php echo $totalTransactions; ?></strong></div>
            <div class="card">Total Feedback: <strong><?php echo $totalFeedback; ?></strong></div>
            <div class="card">Legal Documents: <strong><?php echo $totalLegalDocs; ?></strong></div>
        </div>

        <!-- Property Listings Insights -->
        <h2>Property Listings Insights</h2>
        <div class="property-insights">
            <div class="card">Available: <strong><?php echo $availableProperties; ?></strong></div>
            <div class="card">Sold: <strong><?php echo $soldProperties; ?></strong></div>
            <div class="card">Under Contract: <strong><?php echo $underContractProperties; ?></strong></div>
        </div>

        <!-- Recent Properties Listed -->
        <h3>Recent Properties Listed</h3>
        <ul>
            <?php while ($row = $recentProperties->fetch_assoc()) { ?>
                <li>
                    <strong><?php echo htmlspecialchars($row['property_title'] . " - " . $row['location']); ?></strong><br>
                    <img src="uploads/<?php echo htmlspecialchars($row['image_path']); ?>" width="150" alt="Property Image">
                </li>
            <?php } ?>
        </ul>

        <!-- Transactions Breakdown -->
        <h2>Transactions & Payment Insights</h2>
        <div class="transactions">
            <div class="card">Total Revenue: <strong>$<?php echo number_format($totalRevenue, 2); ?></strong></div>
            <div class="card">Pending Transactions: <strong><?php echo $pendingTransactions; ?></strong></div>
            <div class="card">Completed Transactions: <strong><?php echo $completedTransactions; ?></strong></div>
        </div>

    </div>
</body>
</html>
