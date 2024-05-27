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

// Handle the deletion
if (isset($_GET["id"])) {
    $department_id = $_GET["id"];

    // Get the college_id before deleting the department
    $result = $conn->query("SELECT college_id FROM departments WHERE department_id = $department_id");
    $college_id = $result->fetch_assoc()["college_id"];

    // Delete the department
    if ($conn->query("DELETE FROM departments WHERE department_id = $department_id") === TRUE) {
        // Update the college's intake capacity
        $update_college_capacity = $conn->prepare("
            UPDATE colleges 
            SET intake_capacity = (SELECT SUM(intake_capacity) FROM departments WHERE college_id = ?)
            WHERE college_id = ?
        ");
        $update_college_capacity->bind_param("ii", $college_id, $college_id);
        $update_college_capacity->execute();
        $update_college_capacity->close();

        header("Location: admin_dashboard.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
