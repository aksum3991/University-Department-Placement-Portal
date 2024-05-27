<?php
// Database connection
session_start();
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in as student
if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 3) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_SESSION["user_id"];
    $preferences = $_POST["college_preference"];

    foreach ($preferences as $college_id => $preference) {
        $stmt = $conn->prepare("INSERT INTO college_preferences (student_id, college_id, preference) VALUES (?, ?, ?)
                                ON DUPLICATE KEY UPDATE preference = VALUES(preference)");
        $stmt->bind_param("iii", $student_id, $college_id, $preference);

        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
            exit();
        }

        $stmt->close();
    }

    // Preferences set successfully
    header("Location: student.php");
    exit();
}

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
    <title>Set College Preferences</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Set College Preferences</h1>
    <form action="college_preferences.php" method="POST">
        <?php while ($college = $colleges->fetch_assoc()): ?>
            <div>
                <label for="college_<?php echo $college['college_id']; ?>"><?php echo htmlspecialchars($college['college_name']); ?></label>
                <input type="number" id="college_<?php echo $college['college_id']; ?>" name="college_preference[<?php echo $college['college_id']; ?>]" min="1" max="<?php echo $colleges->num_rows; ?>" required>
            </div>
        <?php endwhile; ?>
        <button type="submit">Submit Preferences</button>
    </form>
</body>
</html>
<?php $conn->close(); ?>
