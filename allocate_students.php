<?php
require_once('config.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch students who have not chosen their preferences
$students_without_preferences = $conn->query("
    SELECT u.user_id, u.full_name 
    FROM users u
    LEFT JOIN college_preferences p ON u.user_id = p.student_id
    WHERE u.role_id = 3 AND p.college_id IS NULL
");

if (!$students_without_preferences) {
    die("Query failed: " . $conn->error);
}

if ($students_without_preferences->num_rows > 0) {
    echo "<h1>Students without Preferences</h1>";
    echo "<p>The following students have not chosen their preferences:</p>";
    echo "<ul>";
    while ($student = $students_without_preferences->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($student['full_name']) . "</li>";
    }
    echo "</ul>";
    echo "<p>Please ensure all students choose their preferences before proceeding with the allocation.</p>";
    exit;
}

// Fetch students and their preferences
$students = $conn->query("
    SELECT u.*, r.gpa, r.entrance_exam, p.college_id, p.preference, c.min_college_entrance_result, c.college_name
    FROM users u
    LEFT JOIN student_results r ON u.user_id = r.student_id
    LEFT JOIN college_preferences p ON u.user_id = p.student_id
    LEFT JOIN colleges c ON p.college_id = c.college_id
    WHERE u.role_id = 3
    ORDER BY r.gpa DESC, p.preference ASC
");

if (!$students) {
    die("Query failed: " . $conn->error);
}

$student_scores = [];

while ($student = $students->fetch_assoc()) {
    $gpa_score = ($student['gpa'] / 4.0) * 50;  // Assuming GPA is out of 4.0
    $entrance_exam_score = round(($student['entrance_exam'] / 700) * 20, 2);  // Assuming entrance exam is out of 700
    $total_score = $gpa_score + $entrance_exam_score;

    if ($student['gender'] == 'female') {
        $total_score += 5;  // Add bonus for female students
    }

    $student_scores[] = [
        'student_id' => $student['user_id'],
        'full_name' => $student['full_name'],
        'college_id' => $student['college_id'],
        'preference' => $student['preference'],
        'total_score' => $total_score,
        'min_college_entrance_result' => $student['min_college_entrance_result'],
        'college_name' => $student['college_name']
    ];
}

// Function to allocate students to colleges
function allocate_students($student_scores, $conn) {
    // Sort students by total score in descending order
    usort($student_scores, function($a, $b) {
        return $b['total_score'] <=> $a['total_score'];
    });

    // Fetch college intake capacities
    $colleges = $conn->query("SELECT college_id, intake_capacity FROM colleges");
    if (!$colleges) {
        die("Query failed: " . $conn->error);
    }

    $college_capacities = [];
    while ($college = $colleges->fetch_assoc()) {
        $college_capacities[$college['college_id']] = $college['intake_capacity'];
    }

    $allocations = [];
    $allocated_students = [];

    foreach ($student_scores as $student) {
        if (in_array($student['student_id'], $allocated_students)) {
            continue;
        }

        $college_id = $student['college_id'];
        $min_result = $student['min_college_entrance_result'];

        // Check if student meets the minimum entrance requirement and college has available capacity
        if (isset($college_capacities[$college_id]) && $student['total_score'] >= $min_result && $college_capacities[$college_id] > 0) {
            $allocations[] = [
                'student_id' => $student['student_id'],
                'college_id' => $college_id,
                'college_name' => $student['college_name'],
                'full_name' => $student['full_name'],
                'total_score' => $student['total_score']
            ];

            // Mark student as allocated
            $allocated_students[] = $student['student_id'];

            // Decrease the college capacity
            $college_capacities[$college_id]--;
        }
    }

    return $allocations;
}

// Allocate students
$allocations = allocate_students($student_scores, $conn);

// Save allocations to the database
$conn->query("TRUNCATE TABLE college_allocations");
foreach ($allocations as $allocation) {
    $stmt = $conn->prepare("INSERT INTO college_allocations (student_id, college_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $allocation['student_id'], $allocation['college_id']);
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Display the list of students with their allocated colleges
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Allocation Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>College Allocation Results</h1>
    <table>
        <tr>
            <th>Student Name</th>
            <th>Total Score</th>
            <th>Allocated College</th>
        </tr>
        <?php foreach ($allocations as $allocation): ?>
        <tr>
            <td><?php echo htmlspecialchars($allocation['full_name']); ?></td>
            <td><?php echo htmlspecialchars($allocation['total_score']); ?></td>
            <td><?php echo htmlspecialchars($allocation['college_name']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>
