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

// Fetch colleges
$colleges = $conn->query("SELECT * FROM colleges");
if (!$colleges) {
    die("Query failed: " . $conn->error);
}

// Get number of colleges
$num_colleges = $colleges->num_rows;

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
    <form action="set_college_preferences.php" method="POST">
        <table>
            <thead>
                <tr>
                    <th>College Name</th>
                    <?php for ($i = 1; $i <= $num_colleges; $i++): ?>
                        <th><?php echo $i . ($i == 1 ? 'st' : ($i == 2 ? 'nd' : ($i == 3 ? 'rd' : 'th'))) . ' Choice'; ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($college = $colleges->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($college['college_name']); ?></td>
                    <?php for ($i = 1; $i <= $num_colleges; $i++): ?>
                        <td>
                            <input type="radio" name="college_preference[<?php echo $college['college_id']; ?>]" value="<?php echo $i; ?>" required>
                        </td>
                    <?php endfor; ?>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button type="submit">Submit Preferences</button>
    </form>
</body>
</html>
<?php $conn->close(); ?>
