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


$college_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM colleges WHERE college_id=?");
$stmt->bind_param("i", $college_id);

if ($stmt->execute()) {
    header("Location: admin.php");
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
