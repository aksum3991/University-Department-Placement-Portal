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

$college_id = $_GET['id'];

// Fetch college details
$stmt = $conn->prepare("SELECT * FROM colleges WHERE college_id = ?");
$stmt->bind_param("i", $college_id);
$stmt->execute();
$college = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $college_name = $_POST['college_name'];
    $min_college_entrance_result = $_POST['min_college_entrance_result'];
    
    // Update college details
    $stmt = $conn->prepare("UPDATE colleges SET college_name = ?, min_college_entrance_result = ? WHERE college_id = ?");
    $stmt->bind_param("sdi", $college_name, $min_college_entrance_result, $college_id);
    
    if ($stmt->execute()) {
        header("Location: registrar.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit College</title>
</head>
<body>
    <h1>Edit College</h1>
    <form method="post">
        <label for="college_name">College Name:</label>
        <input type="text" id="college_name" name="college_name" value="<?php echo htmlspecialchars($college['college_name']); ?>" required>

        <label for="min_college_entrance_result">Min College Entrance Point:</label>
        <input type="number" step="0.01" id="min_college_entrance_result" name="min_college_entrance_result" max="70.00" min="0.00" value="<?php echo htmlspecialchars($college['min_college_entrance_result']); ?>" required>
        
        <button type="submit">Save</button>
    </form>
    <a href="registrar.php">Back to Dashboard</a>
</body>
</html>
