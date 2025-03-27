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
    <?php
include 'db_connect.php'; // Connects to the database

// Handle contractor addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contractor_name = $_POST['contractor_name'];
    $contractor_phone = $_POST['contractor_phone'];
    $contractor_email = $_POST['contractor_email'];
    $specialization = $_POST['specialization'];

    // Check if the contractor already exists
    $stmt = $conn->prepare("SELECT contractor_id FROM contractors WHERE email = ?");
    $stmt->bind_param("s", $contractor_email);
    $stmt->execute();
    $result = $stmt->get_result();
    $contractor = $result->fetch_assoc();

    if ($contractor) {
        echo "<p style='color: red;'>Contractor already exists.</p>";
    } else {
        // Insert new contractor into contractors table
        $stmt = $conn->prepare("INSERT INTO contractors (name, phone, email, specialization) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $contractor_name, $contractor_phone, $contractor_email, $specialization);
        
        if ($stmt->execute()) {
            echo "<p style='color: green;'>Contractor added successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error adding contractor: " . $stmt->error . "</p>";
        }
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Contractor</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Add Contractor</h1>
    </header>
    <div class="container">
        <section>
            <h2>Contractor Information</h2>
            <form action="" method="post">
                <label for="contractor_name">Name:</label>
                <input type="text" id="contractor_name" name="contractor_name" required>

                <label for="contractor_phone">Phone:</label>
                <input type="text" id="contractor_phone" name="contractor_phone" required>

                <label for="contractor_email">Email:</label>
                <input type="email" id="contractor_email" name="contractor_email" required>

                <label for="specialization">Specialization:</label>
                <input type="text" id="specialization" name="specialization" required>

                <button type="submit">Add Contractor</button>
            </form>
        </section>
    </div>
</body>
</html>
