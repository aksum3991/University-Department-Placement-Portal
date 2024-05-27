<?php
// Database connection
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST["full_name"];
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $gender = $_POST["gender"];
    $role_id = 3; // Assuming role ID for students is 3

    // Insert student data into the student_registration table
    $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, gender, role_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $full_name, $username, $email, $password, $gender, $role_id);

    if ($stmt->execute()) {
        // Registration successful
        header("Location: login.html"); // Redirect to login page
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
