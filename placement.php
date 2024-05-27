<?php
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "your_db_name";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function calculateScore($first_sem_grade, $grade_12_exam, $gender) {
    $score = $first_sem_grade * 0.5 + $grade_12_exam * 0.2;
    if ($gender == 'female') {
        $score *= 1.05; // Add 5% bonus for female students
    }
    return $score;
}

// Fetch students with their scores
$result = $conn->query("SELECT user_id, first_semester_grade, grade_12_exam, gender, preference_1, preference_2, preference_3 FROM users WHERE role_id = 3");

$students = [];
while ($row = $result->fetch_assoc()) {
    $row['score'] = calculateScore($row['first_semester_grade'], $row['grade_12_exam'], $row['gender']);
    $students[] = $row;
}

// Sort students by score in descending order
usort($students, function($a, $b) {
    return $b['score'] <=> $a['score'];
});

// Fetch colleges with their intake capacities
$colleges_result = $conn->query("SELECT college_id, intake_capacity FROM colleges");
$colleges = [];
while ($row = $colleges_result->fetch_assoc()) {
    $colleges[$row['college_id']] = $row['intake_capacity'];
}

// Place students in their preferred colleges
foreach ($students as $student) {
    $placed = false;
    foreach (['preference_1', 'preference_2', 'preference_3'] as $preference) {
        $college_id = $student[$preference];
        if ($college_id && $colleges[$college_id] > 0) {
            $stmt = $conn->prepare("INSERT INTO placements (user_id, college_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $student['user_id'], $college_id);
            $stmt->execute();
            $stmt->close();

            $colleges[$college_id]--;
            $placed = true;
            break;
        }
    }

    if (!$placed) {
        // Handle the case where the student cannot be placed in any of their preferred colleges
        echo "Student ID " . $student['user_id'] . " could not be placed in any preferred college.";
    }
}

$conn->close();
?>
