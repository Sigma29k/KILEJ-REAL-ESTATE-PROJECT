<?php
session_start();
$conn = new mysqli("localhost", "root", "", "kilej");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if email already exists
    $check_email = $conn->prepare("SELECT admin_id FROM admins WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        $error_message = "Email already exists. Try another.";
    } else {
        // Insert new admin
        $stmt = $conn->prepare("INSERT INTO admins (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        
        if ($stmt->execute()) {
            $success_message = "Admin registered successfully! <a href='admin_login.php'>Login here</a>";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-container {
            background: white;
            padding: 30px;
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .register-container h2 {
            margin-bottom: 20px;
            color: #333;
            font-weight: 600;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            font-weight: 500;
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: 0.3s;
        }

        .input-group input:focus {
            border-color: #2a5298;
            outline: none;
            box-shadow: 0 0 5px rgba(42, 82, 152, 0.5);
        }

        .btn-register {
            width: 100%;
            padding: 10px;
            background: #2a5298;
            color: white;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: #1e3c72;
        }

        .error-message {
            color: red;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .success-message {
            color: green;
            margin-bottom: 10px;
            font-size: 14px;
        }

        @media (max-width: 500px) {
            .register-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Admin Registration</h2>
        <?php 
            if (!empty($error_message)) echo "<p class='error-message'>$error_message</p>"; 
            if (!empty($success_message)) echo "<p class='success-message'>$success_message</p>"; 
        ?>
        <form method="POST">
            <div class="input-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-register">Register</button>
        </form>
    </div>
</body>
</html>
