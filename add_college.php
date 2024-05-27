<?php
// Database connection
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $college_name = $_POST["college_name"];

    $stmt = $conn->prepare("INSERT INTO colleges (college_name, intake_capacity) VALUES (?, 0)");
    $stmt->bind_param("s", $college_name);

    if ($stmt->execute()) {
        // College added successfully
        header("Location: admin.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
