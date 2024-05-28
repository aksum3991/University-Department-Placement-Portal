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
    SELECT u.*, sr.gpa AS first_sem_gpa, sr.entrance_exam, asr.second_sem_gpa, asr.coc_exam_result
    FROM users u 
    LEFT JOIN student_results sr ON u.user_id = sr.student_id 
    LEFT JOIN student_additional_results asr ON u.user_id = asr.student_id 
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

// Check if student has chosen college preferences
$has_college_preferences = count($preferences) > 0;

$allocation = null;
if ($has_college_preferences) {
    // Fetch college allocation
    $allocation_stmt = $conn->prepare("
        SELECT ca.college_id, c.college_name, c.college_category
        FROM college_allocations ca
        JOIN colleges c ON ca.college_id = c.college_id
        WHERE ca.student_id = ?
    ");
    if (!$allocation_stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $allocation_stmt->bind_param("i", $student_id);
    $allocation_stmt->execute();
    $allocation_result = $allocation_stmt->get_result();
    $allocation = $allocation_result->fetch_assoc();
    $allocation_stmt->close();
}

// Fetch department preferences
$department_preferences = null;
if ($allocation) {
    $dept_preferences_stmt = $conn->prepare("
        SELECT dp.preference, d.department_name 
        FROM department_preferences dp
        JOIN departments d ON dp.department_id = d.department_id
        WHERE dp.student_id = ?
        ORDER BY dp.preference
    ");
    if (!$dept_preferences_stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $dept_preferences_stmt->bind_param("i", $student_id);
    $dept_preferences_stmt->execute();
    $dept_preferences_result = $dept_preferences_stmt->get_result();
    $department_preferences = $dept_preferences_result->fetch_all(MYSQLI_ASSOC);
    $dept_preferences_stmt->close();
}

// Fetch department allocation
$department_allocation = null;
if ($allocation) {
    $dept_allocation_stmt = $conn->prepare("
        SELECT da.department_id, d.department_name 
        FROM department_allocations da
        JOIN departments d ON da.department_id = d.department_id
        WHERE da.student_id = ?
    ");
    if (!$dept_allocation_stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $dept_allocation_stmt->bind_param("i", $student_id);
    $dept_allocation_stmt->execute();
    $dept_allocation_result = $dept_allocation_stmt->get_result();
    $department_allocation = $dept_allocation_result->fetch_assoc();
    $dept_allocation_stmt->close();
}

$conn->close();
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
        if ($student['first_sem_gpa'] !== null) {
            echo "Your GPA: " . htmlspecialchars($student['first_sem_gpa']) . "<br>";
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
    
    <?php if ($has_college_preferences): ?>
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

    <?php if ($has_college_preferences && $allocation): ?>
        <h2>Your College Allocation</h2>
        <p>You have been allocated to <?php echo htmlspecialchars($allocation['college_name']); ?></p>

        <?php
        // Calculate and display the final grade based on the college type
        if ($allocation['college_category'] == 'Natural Science and Engineering' || $allocation['college_category'] == 'Technology') {
            if ($student['second_sem_gpa'] !== null) {
                $first_sem_gpa = $student['first_sem_gpa'];
                $second_sem_gpa_30 = ($student['second_sem_gpa'] / 4.0) * 30;
                $first_sem_gpa_70 = ($first_sem_gpa / 4.0) * 70;
                $final_grade = $first_sem_gpa_70 + $second_sem_gpa_30;

                echo "<p>Your final grade is: " . htmlspecialchars($final_grade) . " out of 100</p>";
            } else {
                echo "<p>Second semester GPA not set yet. Final grade calculation is pending.</p>";
            }
        } elseif ($allocation['college_category'] == 'Health Science') {
            if ($student['coc_exam_result'] !== null) {
                $first_sem_gpa = $student['first_sem_gpa'];
                $coc_exam_30 = ($student['coc_exam_result'] / 100) * 30;
                $first_sem_gpa_70 = ($first_sem_gpa / 4.0) * 70;
                $final_grade = $first_sem_gpa_70 + $coc_exam_30;

                echo "<p>Your final grade is: " . htmlspecialchars($final_grade) . " out of 100</p>";
            } else {
                echo "<p>CoC exam score not set yet. Final grade calculation is pending.</p>";
            }
        }
        ?>

        <!-- Conditionally display the link to set department preferences -->
        <?php if (!$department_preferences): ?>
            <h2>Set Department Preferences</h2>
            <form action="department_preferences.php" method="GET">
                <button type="submit">Set Department Preferences</button>
            </form>
        <?php else: ?>
            <!-- Display department preferences if set -->
            <h2>Your Department Preferences</h2>
            <ul>
                <?php foreach ($department_preferences as $dept_preference): ?>
                    <li><?php echo htmlspecialchars($dept_preference['department_name']) . " (Preference: " . htmlspecialchars($dept_preference['preference']) . ")"; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <!-- Check and display the allocated department -->
        <?php if ($department_allocation): ?>
            <h2>Your Department Allocation</h2>
            <p>You have been allocated to the <?php echo htmlspecialchars($department_allocation['department_name']); ?> department.</p>
        <?php else: ?>
            <h2>Department Allocation</h2>
            <p>Oops! Department allocation not set yet! Wait patiently😎</p>
        <?php endif; ?>

    <?php elseif ($has_college_preferences): ?>
        <h2>Your College Allocation</h2>
        <p>Your allocation results are not yet available. Please check back later.</p>
    <?php endif; ?>
</body>
</html>
