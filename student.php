<?php
// Start session
session_start();

// Check if user is logged in as student
if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 3) {
    header("Location: login.html");
    exit();
}

// Database connection
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get student information
$student_id = $_SESSION["user_id"];
$stmt = $conn->prepare("
    SELECT u.*, sr.gpa, sr.entrance_exam 
    FROM users u 
    LEFT JOIN student_results sr ON u.user_id = sr.student_id 
    WHERE u.user_id = ? AND u.role_id = 3
");
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $student_id);

// Execute statement
$stmt->execute();

// Get result
$result = $stmt->get_result();
$student = $result->fetch_assoc();
if (!$student) {
    die("Student not found");
}

$stmt->close();

// Fetch student preferences
$preferences_stmt = $conn->prepare("
    SELECT cp.preference, c.college_name 
    FROM college_preferences cp
    JOIN colleges c ON cp.college_id = c.college_id
    WHERE cp.student_id = ?
    ORDER BY cp.preference
");

if (!$preferences_stmt) {
    die("Error preparing statement: " . $conn->error);
}

$preferences_stmt->bind_param("i", $student_id);
$preferences_stmt->execute();
$preferences_result = $preferences_stmt->get_result();
$preferences = $preferences_result->fetch_all(MYSQLI_ASSOC);
$preferences_stmt->close();


// Fetch colleges
$colleges = $conn->query("SELECT * FROM colleges");
if (!$colleges) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Student Dashboard</h1>
    <h2>Welcome, <?php echo htmlspecialchars($student['full_name']); ?></h2>
    <p>
        <?php 
        if ($student['gpa'] !== null) {
            echo "Your GPA: " . htmlspecialchars($student['gpa']) . "<br>";
        } else {
            echo "Oops! GPA not set yet! Wait patiently😎" . "<br>";
        }

        if ($student['entrance_exam'] !== null) {
            echo "Your Entrance Exam Score: " . htmlspecialchars($student['entrance_exam']);
        } else {
            echo "Oops! Entrance Exam Score not set yet! Wait patiently😎";
        }
        ?>
    </p>
    
     <?php if (count($preferences) > 0): ?>
        <h2>Your College Preferences</h2>
        <ul>
            <?php foreach ($preferences as $preference): ?>
                <li><?php echo htmlspecialchars($preference['college_name']) . " (Preference: " . htmlspecialchars($preference['preference']) . ")"; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <h2>College Preferences</h2>
        <form action="college_preferences.php" method="GET">
            <button type="submit">Set College Preferences</button>
        </form>
    <?php endif; ?>
</body>
</html>
<?php $conn->close(); ?>
