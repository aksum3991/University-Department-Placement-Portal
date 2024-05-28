<?php
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 2) {
    header("Location: login.html");
    exit();
}

$department_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM departments WHERE department_id=?");
$stmt->bind_param("i", $department_id);

if ($stmt->execute()) {
    header("Location: registrar.php");
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
