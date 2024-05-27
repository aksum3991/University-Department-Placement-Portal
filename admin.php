<?php
// Database connection
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 1) {
    header("Location: login.html");
    exit();
}

// Fetch students
$students = $conn->query("SELECT * FROM users WHERE role_id = 3");
if (!$students) {
    die("Query failed: " . $conn->error);
}

// Fetch registrars
$registrars = $conn->query("SELECT * FROM users WHERE role_id = 2");
if (!$registrars) {
    die("Query failed: " . $conn->error);
}

// Fetch colleges
$colleges = $conn->query("SELECT * FROM colleges");
if (!$colleges) {
    die("Query failed: " . $conn->error);
}

// Fetch departments with college names
$departments = $conn->query("
    SELECT d.*, c.college_name 
    FROM departments d 
    JOIN colleges c ON d.college_id = c.college_id
");
if (!$departments) {
    die("Query failed: " . $conn->error);
}

// Fetch colleges again for the form
$collegesResult = $conn->query("SELECT * FROM colleges");
if (!$collegesResult) {
    die("Query failed: " . $conn->error);
}

// Check if there are any colleges
$hasColleges = $collegesResult->num_rows > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    <h2>Students</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Actions</th>
        </tr>
        <?php while ($student = $students->fetch_assoc()): ?>
        <tr>
            <td><?php echo $student['user_id']; ?></td>
            <td><?php echo $student['full_name']; ?></td>
            <td><?php echo $student['username']; ?></td>
            <td><?php echo $student['email']; ?></td>
            <td><?php echo $student['gender']; ?></td>
            <td>
                <a href="edit_student.php?id=<?php echo $student['user_id']; ?>">Edit</a>
                <a href="delete_student.php?id=<?php echo $student['user_id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Registrars</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
        <?php while ($registrar = $registrars->fetch_assoc()): ?>
        <tr>
            <td><?php echo $registrar['user_id']; ?></td>
            <td><?php echo $registrar['username']; ?></td>
            <td><?php echo $registrar['email']; ?></td>
            <td>
                <a href="edit_registrar.php?id=<?php echo $registrar['user_id']; ?>">Edit</a>
                <a href="delete_registrar.php?id=<?php echo $registrar['user_id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Colleges</h2>
    <table>
        <tr>
            <th>College Name</th>
            <th>Intake Capacity</th>
            <th>Actions</th>
        </tr>
        <?php while ($college = $colleges->fetch_assoc()): ?>
        <tr>
            <td><?php echo $college['college_name']; ?></td>
            <td><?php echo $college['intake_capacity']; ?></td>
            <td>
                <a href="edit_college.php?id=<?php echo $college['college_id']; ?>">Edit</a>
                <a href="delete_college.php?id=<?php echo $college['college_id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Departments</h2>
    <table>
        <tr>
            <th>College</th>
            <th>Department Name</th>
            <th>Intake Capacity</th>
            <th>Actions</th>
        </tr>
        <?php while ($department = $departments->fetch_assoc()): ?>
        <tr>
            <td><?php echo $department['college_name']; ?></td>
            <td><?php echo $department['department_name']; ?></td>
            <td><?php echo $department['intake_capacity']; ?></td>
            <td>
                <a href="edit_department.php?id=<?php echo $department['department_id']; ?>">Edit</a>
                <a href="delete_department.php?id=<?php echo $department['department_id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Add College</h2>
    <form action="add_college.php" method="POST">
        <label for="college_name">College Name:</label>
        <input type="text" id="college_name" name="college_name" required>
        <button type="submit">Add College</button>
    </form>

    <?php if ($hasColleges): ?>
    <h2>Add Department</h2>
    <form action="add_department.php" method="POST">
        <label for="college_id">College:</label>
        <select id="college_id" name="college_id" required>
            <option value=""></option>
            <?php while ($college = $collegesResult->fetch_assoc()): ?>
            <option value="<?php echo $college['college_id']; ?>"><?php echo $college['college_name']; ?></option>
            <?php endwhile; ?>
        </select>
        
        <label for="department_name">Department Name:</label>
        <input type="text" id="department_name" name="department_name" required>
        
        <label for="intake_capacity">Intake Capacity:</label>
        <input type="number" id="intake_capacity" name="intake_capacity" required>
        
        <button type="submit">Add Department</button>
    </form>
    <?php endif; ?>
</body>
</html>
<?php $conn->close(); ?>
