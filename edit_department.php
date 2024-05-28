<?php
// Start session
session_start();

// Check if user is logged in as registrar
if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] === 3) {
    header("Location: login.html");
    exit();
}

// Database connection
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$department_id = $_GET['id'];

// Fetch department details
$department = $conn->query("
    SELECT * 
    FROM departments 
    WHERE department_id = $department_id
")->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $department_name = $_POST['department_name'];
    $intake_capacity = $_POST['intake_capacity'];
    $min_department_entrance_result = $_POST['min_department_entrance_result'];

    // Update department
    $stmt = $conn->prepare("
        UPDATE departments 
        SET department_name = ?, intake_capacity = ?, min_department_entrance_result = ? 
        WHERE department_id = ?
    ");
    $stmt->bind_param("sidi", $department_name, $intake_capacity, $min_department_entrance_result, $department_id);
    $stmt->execute();
    
    if($_SESSION["role_id"] ===2 ){
        header("Location: registrar.php");
      exit();
    }
    else if($_SESSION["role_id"] === 1){
        header("Location: admin.php"); 
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department</title>
</head>
<body>
    <h1>Edit Department</h1>
    <form action="" method="POST">
        <label for="department_name">Department Name:</label>
        <input type="text" id="department_name" name="department_name" value="<?php echo htmlspecialchars($department['department_name']); ?>" required>

        <label for="intake_capacity">Intake Capacity:</label>
        <input type="number" id="intake_capacity" name="intake_capacity" value="<?php echo htmlspecialchars($department['intake_capacity']); ?>" required>

        <label for="min_department_entrance_result">Min Department Entrance Point:</label>
        <input type="number" step="0.01" id="min_department_entrance_result" name="min_department_entrance_result" value="<?php echo htmlspecialchars($department['min_department_entrance_result']); ?>" required>

        <button type="submit">Save</button>
    </form>
</body>
</html>
<?php $conn->close(); ?>
