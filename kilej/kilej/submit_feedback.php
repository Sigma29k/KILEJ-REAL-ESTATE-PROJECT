<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['client_id'])) {
    die("Error: Unauthorized access.");
}

$client_id = $_SESSION['client_id'];
$message = trim($_POST['message']);

if (empty($message)) {
    echo "error";
    exit;
}

$stmt = $conn->prepare("INSERT INTO feedback (client_id, message, date_submitted) VALUES (?, ?, NOW())");
$stmt->bind_param("is", $client_id, $message);
if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
$stmt->close();
$conn->close();
?>
