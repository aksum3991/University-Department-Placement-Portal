<?php
// Start session
session_start();
// Database connection
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT user_id, username, password, role_id FROM users WHERE username = ?");
    if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
    $stmt->bind_param("s", $username);

    // Execute statement
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row["password"])) {
            // Login successful
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["role_id"] = $row["role_id"];

            // Redirect to appropriate page based on role
            switch ($row["role_id"]) {
                case 1:
                    header("Location: admin.php");
                    break;
                case 2:
                    header("Location: registrar.php");
                    break;
                case 3:
                    header("Location: student.php");
                    break;
                default:
                    header("Location: index.php"); // Default page
            }

            exit();
        } else {
            // Incorrect password
            echo "Invalid username or password.";
        }
    } else {
        // User not found
        echo "Invalid username or password.";
    }

    $stmt->close();
}

$conn->close();
?>