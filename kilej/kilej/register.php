<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_name = $_POST['client_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO clients (client_name, email, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $client_name, $email, $phone, $password);

    if ($stmt->execute()) {
        echo "Registration successful. <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #00c6ff, #0072ff);
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
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            border-color: #0072ff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 114, 255, 0.5);
        }

        .btn-register {
            width: 100%;
            padding: 10px;
            background: #0072ff;
            color: white;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-register:hover {
            background: #0057b3;
        }

        .login-link {
            display: block;
            margin-top: 10px;
            font-size: 14px;
            color: #0072ff;
            text-decoration: none;
            transition: 0.3s;
        }

        .login-link:hover {
            color: #0057b3;
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
        <h2>Client Registration</h2>
        <form method="POST">
            <div class="input-group">
                <label>Name:</label>
                <input type="text" name="client_name" required>
            </div>
            <div class="input-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>Phone:</label>
                <input type="text" name="phone" required>
            </div>
            <div class="input-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-register">Register</button>
            <a href="login.php" class="login-link">Already have an account? Login here</a>
        </form>
    </div>
</body>
</html>

