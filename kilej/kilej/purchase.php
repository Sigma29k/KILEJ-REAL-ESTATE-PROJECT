<?php
require 'db_connect.php';

if (isset($_GET['id'])) {
    $property_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM property_listings WHERE id = ?");
    $stmt->bind_param("i", $property_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $property = $result->fetch_assoc();
    } else {
        echo "<h3>Property not found</h3>";
        exit();
    }
} else {
    echo "<h3>Invalid Request</h3>";
    exit();
}

// Simulating a purchase form
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Property</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4>Confirm Purchase</h4>
        </div>
        <div class="card-body">
            <h5><?php echo htmlspecialchars($property['property_title']); ?></h5>
            <p><strong>Price:</strong> <?php echo number_format($property['price'], 2); ?> KES</p>

            <form action="process_purchase.php" method="post">
                <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
                <div class="mb-3">
                    <label for="buyer_name" class="form-label">Your Name:</label>
                    <input type="text" name="buyer_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="buyer_email" class="form-label">Your Email:</label>
                    <input type="email" name="buyer_email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Complete Purchase</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
