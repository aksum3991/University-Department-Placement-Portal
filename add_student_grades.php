<?php
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "your_db_name";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST["student_id"];
    $first_semester_grade = $_POST["first_semester_grade"];
    $grade_12_exam = $_POST["grade_12_exam"];
    $preference_1 = $_POST["preference_1"];
    $preference_2 = $_POST["preference_2"];
    $preference_3 = $_POST["preference_3"];

    $stmt = $conn->prepare("UPDATE users SET first_semester_grade = ?, grade_12_exam = ?, preference_1 = ?, preference_2 = ?, preference_3 = ? WHERE user_id = ?");
    $stmt->bind_param("ddiidi", $first_semester_grade, $grade_12_exam, $preference_1, $preference_2, $preference_3, $student_id);

    if ($stmt->execute()) {
        echo "Student grades and preferences updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
