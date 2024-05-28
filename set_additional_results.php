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

// Fetch student details
if (isset($_GET['user_id']) && isset($_GET['type'])) {
    $user_id = $_GET['user_id'];
    $type = $_GET['type'];
    $student = $conn->query("
        SELECT u.*, r.gpa, r.entrance_exam, a.second_sem_gpa, a.coc_exam_result, c.college_category
        FROM users u
        LEFT JOIN student_results r ON u.user_id = r.student_id
        LEFT JOIN student_additional_results a ON u.user_id = a.student_id
        LEFT JOIN college_allocations ca ON u.user_id = ca.student_id
        LEFT JOIN colleges c ON ca.college_id = c.college_id
        WHERE u.user_id = $user_id
    ");
    if (!$student) {
        die("Query failed: " . $conn->error);
    }
    $student = $student->fetch_assoc();
} else {
    die("No student ID or type provided.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $second_sem_gpa = isset($_POST['second_sem_gpa']) ? $_POST['second_sem_gpa'] : null;
    $coc_exam_result = isset($_POST['coc_exam_result']) ? $_POST['coc_exam_result'] : null;

    // Check if record exists
    $result = $conn->query("SELECT * FROM student_additional_results WHERE student_id = $user_id");
    if ($result->num_rows > 0) {
        // Update existing record
        $stmt = $conn->prepare("UPDATE student_additional_results SET second_sem_gpa = ?, coc_exam_result = ? WHERE student_id = ?");
        $stmt->bind_param("ddi", $second_sem_gpa, $coc_exam_result, $user_id);
    } else {
        // Insert new record
        $stmt = $conn->prepare("INSERT INTO student_additional_results (student_id, second_sem_gpa, coc_exam_result) VALUES (?, ?, ?)");
        $stmt->bind_param("idd", $user_id, $second_sem_gpa, $coc_exam_result);
    }

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
    <title>Set Additional Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Set Additional Results</h1>
    <form method="post">
        <?php if ($type == 'second_sem_gpa'): ?>
            <label for="second_sem_gpa">Second Semester GPA</label>
            <input type="number" step="0.01" name="second_sem_gpa" value="<?php echo htmlspecialchars($student['second_sem_gpa']); ?>">
        <?php elseif ($type == 'coc_exam_result'): ?>
            <label for="coc_exam_result">CoC Exam Result</label>
            <input type="number" step="0.01" name="coc_exam_result" value="<?php echo htmlspecialchars($student['coc_exam_result']); ?>">
        <?php endif; ?>
        <button type="submit">Save</button>
    </form>
</body>
</html>
