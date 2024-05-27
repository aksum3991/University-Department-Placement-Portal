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
    $department_id = $_POST["department_id"];
    $intake_capacity = $_POST["intake_capacity"];

    $stmt = $conn->prepare("UPDATE departments SET intake_capacity = ? WHERE department_id = ?");
    $stmt->bind_param("ii", $intake_capacity, $department_id);

    if ($stmt->execute()) {
        // Intake capacity set successfully
        header("Location: registrar.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    $department_id = $_GET["department_id"];
    $department = $conn->query("SELECT * FROM departments WHERE department_id = $department_id")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Intake Capacity</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Set Intake Capacity for <?php echo $department['department_name']; ?></h1>
    <form action="set_intake_capacity.php" method="POST">
        <input type="hidden" name="department_id" value="<?php echo $department_id; ?>">
        <label for="intake_capacity">Intake Capacity:</label>
        <input type="number" id="intake_capacity" name="intake_capacity" required>
        <button type="submit">Set Intake Capacity</button>
    </form>
</body>
</html>

<?php $conn->close(); ?>
