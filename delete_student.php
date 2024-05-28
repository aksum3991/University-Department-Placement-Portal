<?php
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] === 3) {
    header("Location: login.html");
    exit();
}

$student_id = $_GET['user_id'];

$stmt = $conn->prepare("DELETE FROM users WHERE user_id=? AND role_id=3");
$stmt->bind_param("i", $student_id);

if ($stmt->execute()) {
    header("Location: registrar.php");
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
