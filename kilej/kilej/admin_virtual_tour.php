<?php
session_start();
include 'db_connect.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $media_type = $_POST['media_type'];
    $description = $_POST['description'];

    // File Upload Handling
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["media"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file type
    if ($media_type == "video" && !in_array($fileType, ["mp4", "avi", "mov"])) {
        $uploadOk = 0;
        $error = "Only MP4, AVI, and MOV video files are allowed.";
    } elseif ($media_type == "image" && !in_array($fileType, ["jpg", "png", "jpeg", "gif"])) {
        $uploadOk = 0;
        $error = "Only JPG, PNG, JPEG, and GIF image files are allowed.";
    }

    if ($uploadOk && move_uploaded_file($_FILES["media"]["tmp_name"], $target_file)) {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO virtual_tour (media_type, media_url, description) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $media_type, $target_file, $description);
        
        if ($stmt->execute()) {
            $success = "Virtual tour uploaded successfully!";
        } else {
            $error = "Database error: " . $conn->error;
        }
    } else {
        $error = $error ?? "File upload failed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kilej - Add Virtual Tour</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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

        h1, h3 {
            color: #f4f4f4;
        }
        h2{
            color: #003d66
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
        <h1>Kilej Dashboard</h1>
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

<div class="container mt-4">
    <h2 class="text-center">Add Virtual Tour</h2>

    <!-- Display messages -->
    <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form action="admin_virtual_tour.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="media_type" class="form-label">Media Type</label>
            <select name="media_type" class="form-control" required>
                <option value="image">Image</option>
                <option value="video">Video</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="media" class="form-label">Upload File</label>
            <input type="file" name="media" class="form-control" accept="image/*,video/*" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Upload Virtual Tour</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
