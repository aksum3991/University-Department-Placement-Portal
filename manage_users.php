<?php
session_start();
require 'middleware.php';
checkRole(1); // Only allow admins to access this page

// Database connection
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "your_db_name";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// CRUD operations (create, read, update, delete) for users
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create"])) {
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $role_id = $_POST["role"];
        $gender = $_POST["gender"];
        $gpa = $role_id == 3 ? $_POST["gpa"] : NULL;

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role_id, gender, gpa) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $username, $email, $password, $role_id, $gender, $gpa);

        if ($stmt->execute()) {
            echo "User created successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    } elseif (isset($_POST["update"])) {
        // Update user logic
    } elseif (isset($_POST["delete"])) {
        // Delete user logic
    }
}

// Fetch users to display
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Manage Users</h1>
        <table>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Gender</th>
                <th>GPA</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['username'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['role_id'] == 1 ? 'Admin' : ($row['role_id'] == 2 ? 'Registrar' : 'Student') ?></td>
                <td><?= $row['gender'] ?></td>
                <td><?= $row['gpa'] ?></td>
                <td>
                    <!-- Add edit and delete buttons -->
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
