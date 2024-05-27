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

// Fetch students
$students = $conn->query("
    SELECT u.*, r.gpa, r.entrance_exam 
    FROM users u
    LEFT JOIN student_results r ON u.user_id = r.student_id
    WHERE u.role_id = 3
");
if (!$students) {
    die("Query failed: " . $conn->error);
}

// Fetch departments
$departments = $conn->query("
    SELECT d.*, c.college_name 
    FROM departments d 
    JOIN colleges c ON d.college_id = c.college_id
");
if (!$departments) {
    die("Query failed: " . $conn->error);
}

// Fetch colleges
$colleges = $conn->query("
    SELECT * FROM colleges
");
if (!$colleges) {
    die("Query failed: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Registrar Dashboard</h1>
    
    <h2>Students</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Gender</th>
            <th>GPA</th>
            <th>Entrance Exam</th>
            <th>Actions</th>
        </tr>
        <?php while ($student = $students->fetch_assoc()): ?>
        <tr>
            <td><?php echo $student['user_id']; ?></td>
            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
            <td><?php echo htmlspecialchars($student['username']); ?></td>
            <td><?php echo htmlspecialchars($student['email']); ?></td>
            <td><?php echo htmlspecialchars($student['gender']); ?></td>
            <td><?php echo number_format($student['gpa'],2,'.',''); ?></td>
            <td><?php echo htmlspecialchars($student['entrance_exam']); ?></td>
            <td>
                <a href="edit_student.php?user_id=<?php echo $student['user_id']; ?>">Edit</a>
                <a href="delete_student.php?user_id=<?php echo $student['user_id']; ?>">Delete</a>
                <?php if (is_null($student['gpa']) || is_null($student['entrance_exam'])): ?>
                    <a href="set_gpa_entrance_exam.php?user_id=<?php echo $student['user_id']; ?>">Set GPA & Entrance Exam</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Departments</h2>
    <table>
        <tr>
            <th>College</th>
            <th>Department</th>
            <th>Intake Capacity</th>
            <th>Minimum Entrance Exam</th>
            <th>Actions</th>
        </tr>
        <?php while ($department = $departments->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($department['college_name']); ?></td>
            <td><?php echo htmlspecialchars($department['department_name']); ?></td>
            <td><?php echo htmlspecialchars($department['intake_capacity']); ?></td>
            <td><?php echo number_format((float)$department['min_department_entrance_result'], 2, '.', ''); ?>%</td>
            <td>
                <a href="edit_department.php?id=<?php echo $department['department_id']; ?>">Edit</a>
                <a href="delete_department.php?id=<?php echo $department['department_id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Colleges</h2>
    <table>
        <tr>
            <th>College</th>
            <th>Intake Capacity</th>
            <th>Min College Entrance Point </th>
            <th>Actions</th>
        </tr>
        <?php while ($college = $colleges->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($college['college_name']); ?></td>
            <td><?php echo htmlspecialchars($college['intake_capacity']); ?></td>
            <td><?php echo number_format((float)$college['min_college_entrance_result'], 2, '.', ''); ?>%</td>
            <td>
                <a href="edit_college.php?id=<?php echo $college['college_id']; ?>">Edit</a>
                <a href="delete_college.php?id=<?php echo $college['college_id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
<?php $conn->close(); ?>
