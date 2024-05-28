<?php
// Database connection
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch students
$students = $conn->query("
    SELECT u.*, r.gpa, r.entrance_exam, a.second_sem_gpa, a.coc_exam_result, ca.college_id, c.college_category
    FROM users u
    LEFT JOIN student_results r ON u.user_id = r.student_id
    LEFT JOIN student_additional_results a ON u.user_id = a.student_id
    LEFT JOIN college_allocations ca ON u.user_id = ca.student_id
    LEFT JOIN colleges c ON ca.college_id = c.college_id
    WHERE u.role_id = 3
");
if (!$students) {
    die("Query failed: " . $conn->error);
}

$student_scores = [];
$errors = [];

while ($student = $students->fetch_assoc()) {
    $gpa_score = ($student['gpa'] / 4.0) * 50;  // Assuming GPA is out of 4.0
    $entrance_exam_score = round(($student['entrance_exam'] / 700) * 20, 2);  // Assuming entrance exam is out of 700
    $total_score = $gpa_score + $entrance_exam_score;
    
    if ($student['college_category'] == 'Natural Science' || $student['college_category'] == 'Engineering and Technology') {
        if (!is_null($student['second_sem_gpa'])) {
            $total_score += ($student['second_sem_gpa'] / 4.0) * 30;
        } else {
            $errors[] = "Error: Missing second semester GPA for student " . $student['full_name'];
        }
    } elseif ($student['college_category'] == 'Health Science') {
        if (!is_null($student['coc_exam_result'])) {
            $total_score += ($student['coc_exam_result'] / 100) * 30;
        } else {
            $errors[] = "Error: Missing CoC exam result for student " . $student['full_name'];
        }
    }
    
    $student_scores[] = [
        'student_id' => $student['user_id'],
        'full_name' => $student['full_name'],
        'college_id' => $student['college_id'],
        'total_score' => $total_score
    ];
}

// If there are any errors, stop the allocation process and display errors
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "<br>";
    }
    exit;
}

// Check if all students have set their department preferences
$preferences = $conn->query("
    SELECT student_id FROM department_preferences
");
$student_preferences = [];
while ($preference = $preferences->fetch_assoc()) {
    $student_preferences[] = $preference['student_id'];
}

foreach ($student_scores as $student) {
    if (!in_array($student['student_id'], $student_preferences)) {
        echo "Error: Student " . $student['full_name'] . " has not set their department preferences.<br>";
        exit;
    }
}

// Function to allocate students to departments
function allocate_departments($student_scores, $conn) {
    // Sort students by total score in descending order
    usort($student_scores, function($a, $b) {
        return $b['total_score'] <=> $a['total_score'];
    });

    // Fetch department intake capacities
    $departments = $conn->query("SELECT department_id, intake_capacity FROM departments");
    if (!$departments) {
        die("Query failed: " . $conn->error);
    }

    $department_capacities = [];
    while ($department = $departments->fetch_assoc()) {
        $department_capacities[$department['department_id']] = $department['intake_capacity'];
    }

    $allocations = [];
    $allocated_students = [];

    foreach ($student_scores as $student) {
        if (in_array($student['student_id'], $allocated_students)) {
            continue;
        }

        $college_id = $student['college_id'];

        // Fetch departments for the college
        $college_departments = $conn->query("
            SELECT d.department_id, d.department_name, d.min_department_entrance_result 
            FROM departments d
            WHERE d.college_id = $college_id
        ");
        if (!$college_departments) {
            die("Query failed: " . $conn->error);
        }

        while ($department = $college_departments->fetch_assoc()) {
            if (isset($department_capacities[$department['department_id']]) 
                && $student['total_score'] >= $department['min_department_entrance_result'] 
                && $department_capacities[$department['department_id']] > 0) {
                    
                $allocations[] = [
                    'student_id' => $student['student_id'],
                    'department_id' => $department['department_id'],
                    'department_name' => $department['department_name'],
                    'full_name' => $student['full_name'],
                    'total_score' => $student['total_score']
                ];

                // Mark student as allocated
                $allocated_students[] = $student['student_id'];

                // Decrease the department capacity
                $department_capacities[$department['department_id']]--;
                break; // Move to the next student after allocation
            }
        }
    }

    return $allocations;
}

// Allocate departments
$allocations = allocate_departments($student_scores, $conn);

// Save allocations to the database
$conn->query("TRUNCATE TABLE department_allocations");

// Prepare statement for inserting allocations
$stmt = $conn->prepare("INSERT INTO department_allocations (student_id, department_id) VALUES (?, ?)");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

foreach ($allocations as $allocation) {
    $stmt->bind_param("ii", $allocation['student_id'], $allocation['department_id']);
    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error . "<br>";
    }
}
$stmt->close();

// Display the list of students with their allocated departments
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Allocation Results</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Department Allocation Results</h1>
    <table>
        <tr>
            <th>Student Name</th>
            <th>Total Score</th>
            <th>Allocated Department</th>
        </tr>
        <?php foreach ($allocations as $allocation): ?>
        <tr>
            <td><?php echo htmlspecialchars($allocation['full_name']); ?></td>
            <td><?php echo htmlspecialchars($allocation['total_score']); ?></td>
            <td><?php echo htmlspecialchars($allocation['department_name']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
<?php $conn->close(); ?>
