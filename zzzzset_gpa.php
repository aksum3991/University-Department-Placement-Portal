<?php
// Database connection
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();

// Check if user is logged in as registrar
if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 2) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST["student_id"];
    $gpa = $_POST["gpa"];

    $stmt = $conn->prepare("UPDATE users SET gpa = ? WHERE user_id = ?");
    $stmt->bind_param("di", $gpa, $student_id);

    if ($stmt->execute()) {
        // GPA set successfully
        header("Location: registrar.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    $student_id = $_GET["user_id"];
    $student = $conn->query("SELECT * FROM users WHERE user_id = $student_id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set GPA</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Set GPA for <?php echo $student['full_name']; ?></h1>
    <form action="set_gpa.php" method="POST">
        <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
        <label for="gpa">GPA:</label>
        <input type="number" step="0.01" id="gpa" name="gpa" required>
        <button type="submit">Set GPA</button>
    </form>
</body>
</html>

<?php $conn->close(); ?>
