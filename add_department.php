<?php
// Database connection
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 1) {
    header("Location: login.html");
    exit();
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $college_id = $_POST["college_id"];
    $department_name = $_POST["department_name"];
    $intake_capacity = $_POST["intake_capacity"];

    // Insert the new department
    $stmt = $conn->prepare("INSERT INTO departments (college_id, department_name, intake_capacity) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $college_id, $department_name, $intake_capacity);

    if ($stmt->execute()) {
        // Update the college's intake capacity
        $update_college_capacity = $conn->prepare("
            UPDATE colleges 
            SET intake_capacity = (SELECT SUM(intake_capacity) FROM departments WHERE college_id = ?)
            WHERE college_id = ?
        ");
        $update_college_capacity->bind_param("ii", $college_id, $college_id);
        $update_college_capacity->execute();
        $update_college_capacity->close();

        header("Location: admin.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
