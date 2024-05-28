<?php
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] === 3) {
    header("Location: login.html");
    exit();
}

$student_id = $_GET['user_id'];
$student = null; // Initialize $student variable

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $gpa = isset($_POST['gpa']) ? $_POST['gpa'] : null;
    $entrance_exam = isset($_POST['entrance_exam']) ? $_POST['entrance_exam'] : null;

    $stmt = $conn->prepare("UPDATE users SET full_name=?, username=?, email=?, gender=? WHERE user_id=? AND role_id=3");
    $stmt->bind_param("ssssi", $full_name, $username, $email, $gender, $student_id);

    if ($stmt->execute()) {
        if ($gpa !== null && $entrance_exam !== null) {
            $stmt = $conn->prepare("UPDATE student_results SET gpa=?, entrance_exam=? WHERE student_id=?");
            $stmt->bind_param("dsi", $gpa, $entrance_exam, $student_id);
            $stmt->execute();
            $stmt->close();
        }

        if ($_SESSION["role_id"] === 2) {
            header("Location: registrar.php");
        } else if ($_SESSION["role_id"] === 1) {
            header("Location: admin.php");
        }
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    $stmt = $conn->prepare("
        SELECT u.*, r.gpa, r.entrance_exam
        FROM users u
        LEFT JOIN student_results r ON u.user_id = r.student_id
        WHERE u.user_id = ? AND u.role_id = 3
    ");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
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
    <title>Edit Student</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Edit Student</h1>
    <?php if ($student): ?>
    <form action="edit_student.php?user_id=<?php echo $student_id; ?>" method="post">
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($student['full_name']); ?>" required>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($student['username']); ?>" required>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="male" <?php if ($student['gender'] == 'male') echo 'selected'; ?>>Male</option>
            <option value="female" <?php if ($student['gender'] == 'female') echo 'selected'; ?>>Female</option>
        </select>
        
        <?php if ($student['gpa'] !== null): ?>
        <label for="gpa">GPA:</label>
        <input type="number" id="gpa" name="gpa"  value="<?php echo htmlspecialchars($student['gpa']); ?>" step="0.01" min="0" max="4">
        <?php endif; ?>

        <?php if ($student['entrance_exam'] !== null): ?>
        <label for="entrance_exam">Entrance Exam:</label>
        <input type="number" id="entrance_exam" name="entrance_exam" value="<?php echo htmlspecialchars($student['entrance_exam']); ?>" step="0.01" min="0" max="700">
        <?php endif; ?>

        <button type="submit">Save</button>
    </form>
    <?php else: ?>
        <p>Student not found.</p>
    <?php endif; ?>
</body>
</html>
