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
    $user_id = $_POST['user_id'];
    $gpa = $_POST['gpa'];
    $entrance_exam = $_POST['entrance_exam'];

    // Check if the user is a student
    $userCheck = $conn->prepare("SELECT role_id FROM users WHERE user_id = ? AND role_id = 3");
    $userCheck->bind_param("i", $user_id);
    $userCheck->execute();
    $userCheck->store_result();

    if ($userCheck->num_rows == 0) {
        die("Error: The specified user is not a student.");
    }

    // Check if the student already has a record
    $checkQuery = $conn->prepare("SELECT * FROM student_results WHERE student_id = ?");
    $checkQuery->bind_param("i", $user_id);
    $checkQuery->execute();
    $result = $checkQuery->get_result();

    if ($result->num_rows > 0) {
        // Update existing record
        $stmt = $conn->prepare("UPDATE student_results SET gpa = ?, entrance_exam = ? WHERE student_id = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("dii", $gpa, $entrance_exam, $user_id);
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO student_results (student_id, gpa, entrance_exam) VALUES (?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("iid", $user_id, $gpa, $entrance_exam);
    }

    if ($stmt->execute()) {
        header("Location: registrar.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $checkQuery->close();
    $userCheck->close();
}

if (!isset($_GET['user_id'])) {
    header("Location: registrar.php");
    exit();
}

$user_id = $_GET['user_id'];
$result = $conn->query("SELECT * FROM users WHERE user_id = $user_id AND role_id = 3");

if ($result->num_rows != 1) {
    header("Location: registrar.php");
    exit();
}

$student = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set GPA and Entrance Exam</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Set GPA and Entrance Exam for <?php echo htmlspecialchars($student['full_name']); ?></h1>
    <form action="set_gpa_entrance_exam.php" method="POST">
        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($student['user_id']); ?>">
        <label for="gpa">GPA:</label>
        <input type="number" step="0.01" id="gpa" name="gpa" required>
        <br>
        <label for="entrance_exam">Entrance Exam:</label>
        <input type="number" id="entrance_exam" name="entrance_exam" required>
        <br>
        <button type="submit">Submit</button>
    </form>
    <a href="registrar.php">Back to Dashboard</a>
</body>
</html>
<?php $conn->close(); ?>
