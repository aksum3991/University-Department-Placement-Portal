<?php
// Start session
session_start();

// Check if user is logged in as student
if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 3) {
    header("Location: login.php");
    exit();
}

// Database connection
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the student's allocated college
$student_id = $_SESSION["user_id"];
$stmt = $conn->prepare("
    SELECT ca.college_id, c.college_name 
    FROM college_allocations ca
    JOIN colleges c ON ca.college_id = c.college_id
    WHERE ca.student_id = ?
");
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student_college = $result->fetch_assoc();
$stmt->close();

if (!$student_college) {
    die("No college allocated for the student.");
}

// Fetch departments for the student's allocated college
$departments_stmt = $conn->prepare("
    SELECT * 
    FROM departments 
    WHERE college_id = ?
");
if (!$departments_stmt) {
    die("Error preparing statement: " . $conn->error);
}
$departments_stmt->bind_param("i", $student_college['college_id']);
$departments_stmt->execute();
$departments_result = $departments_stmt->get_result();
$departments = $departments_result->fetch_all(MYSQLI_ASSOC);
$departments_stmt->close();

// Get number of departments
$num_departments = count($departments);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Department Preferences</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Set Department Preferences</h1>
    <h2>Allocated College: <?php echo htmlspecialchars($student_college['college_name']); ?></h2>
    <form action="set_department_preferences.php" method="POST">
        <table>
            <thead>
                <tr>
                    <th>Department Name</th>
                    <?php for ($i = 1; $i <= $num_departments; $i++): ?>
                        <th><?php echo $i . ($i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th'))) . ' Choice'; ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($departments as $department): ?>
                <tr>
                    <td><?php echo htmlspecialchars($department['department_name']); ?></td>
                    <?php for ($i = 1; $i <= $num_departments; $i++): ?>
                        <td>
                            <input type="radio" name="department_preference[<?php echo $department['department_id']; ?>]" value="<?php echo $i; ?>" required>
                        </td>
                    <?php endfor; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">Submit Preferences</button>
    </form>
</body>
</html>
<?php $conn->close(); ?>
