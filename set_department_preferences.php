<?php
// Start session
session_start();

// Check if user is logged in as student
if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 3) {
    header("Location: login.php");
    exit();
}

// Database connection
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get student ID from session
$student_id = $_SESSION["user_id"];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Delete existing preferences for the student
    $delete_stmt = $conn->prepare("DELETE FROM department_preferences WHERE student_id = ?");
    $delete_stmt->bind_param("i", $student_id);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Insert new preferences
    $insert_stmt = $conn->prepare("INSERT INTO department_preferences (student_id, department_id, preference) VALUES (?, ?, ?)");
    foreach ($_POST['department_preference'] as $department_id => $preference) {
        $insert_stmt->bind_param("iii", $student_id, $department_id, $preference);
        $insert_stmt->execute();
    }
    $insert_stmt->close();

    // Redirect to student dashboard or confirmation page
    header("Location: student.php");
    exit();
}

// Close connection
$conn->close();
?>
