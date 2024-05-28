<?php
// Database connection
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in as admin or registrar
session_start();
if (!isset($_SESSION["role_id"]) || ($_SESSION["role_id"] !=1)) {
    header("Location: login.html");
    exit();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $college_name = $conn->real_escape_string($_POST['college_name']);
    $college_category = $conn->real_escape_string($_POST['college_category']);

    // Insert the new college into the database
    $sql = "INSERT INTO colleges (college_name, college_category) VALUES ('$college_name', '$college_category')";
    if ($conn->query($sql) === TRUE) {
        header("Location: admin.php"); // Redirect to the dashboard after successful insertion
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
