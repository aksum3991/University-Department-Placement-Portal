<?php
// Database connection
require_once('config.php');
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start session
session_start();

// Check if user is logged in as registrar
if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 2) {
    header("Location: login.html");
    exit();
}

// Fetch students
$students = $conn->query("
    SELECT u.*, r.gpa, r.entrance_exam, a.second_sem_gpa, a.coc_exam_result, c.college_category, c.college_name
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

// Fetch departments
$departments = $conn->query("
    SELECT d.*, c.college_name, c.college_category
    FROM departments d 
    JOIN colleges c ON d.college_id = c.college_id
");
if (!$departments) {
    die("Query failed: " . $conn->error);
}

// Fetch colleges
$colleges = $conn->query("
    SELECT * FROM colleges
");
if (!$colleges) {
    die("Query failed: " . $conn->error);
}

$studentsArray = [];
while ($student = $students->fetch_assoc()) {
    $studentsArray[] = $student;
}

$show_second_sem_gpa = false;
$show_coc_exam_result = false;

foreach ($studentsArray as $student) {
    if ($student['college_category'] == 'Natural Science' || $student['college_category'] == 'Engineering and Technology') {
        $show_second_sem_gpa = true;
    } elseif ($student['college_category'] == 'Health Science') {
        $show_coc_exam_result = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Registrar Dashboard</h1>
    
    <h2>Students</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Gender</th>
            <th>GPA</th>
            <th>Entrance Exam</th>
            <th>College</th>
            <th>College Category</th>
            <?php if ($show_second_sem_gpa): ?>
                <th>Second Semester GPA</th>
            <?php endif; ?>
            <?php if ($show_coc_exam_result): ?>
                <th>CoC Exam Result</th>
            <?php endif; ?>
            <th>Actions</th>
        </tr>
        <?php foreach ($studentsArray as $student): ?>
        <tr>
            <td><?php echo $student['user_id']; ?></td>
            <td><?php echo htmlspecialchars($student['full_name']); ?></td>
            <td><?php echo htmlspecialchars($student['username']); ?></td>
            <td><?php echo htmlspecialchars($student['email']); ?></td>
            <td><?php echo htmlspecialchars($student['gender']); ?></td>
            <td><?php echo number_format($student['gpa'], 2, '.', ''); ?></td>
            <td><?php echo htmlspecialchars($student['entrance_exam']); ?></td>
            <td><?php echo htmlspecialchars($student['college_name']); ?></td>
            <td><?php echo htmlspecialchars($student['college_category']); ?></td>
            <?php if ($show_second_sem_gpa): ?>
                <td><?php echo is_null($student['second_sem_gpa']) ? 'N/A' : number_format($student['second_sem_gpa'], 2, '.', ''); ?></td>
            <?php endif; ?>
            <?php if ($show_coc_exam_result): ?>
                <td><?php echo is_null($student['coc_exam_result']) ? 'N/A' : number_format($student['coc_exam_result'], 2, '.', ''); ?></td>
            <?php endif; ?>
            <td>
                <a href="edit_student.php?user_id=<?php echo $student['user_id']; ?>">Edit</a>
                <a href="delete_student.php?user_id=<?php echo $student['user_id']; ?>" class="delete-link" data-entity-name="<?php echo htmlspecialchars($student['full_name']); ?>" data-entity-type="student">Delete</a>
                <?php if (is_null($student['gpa']) || is_null($student['entrance_exam'])): ?>
                    <a href="set_gpa_entrance_exam.php?user_id=<?php echo $student['user_id']; ?>">Set GPA & Entrance Exam</a>
                <?php endif; ?>
                <?php if (($student['college_category'] == 'Natural Science' || $student['college_category'] == 'Engineering and Technology') && is_null($student['second_sem_gpa'])): ?>
                    <a href="set_additional_results.php?user_id=<?php echo $student['user_id']; ?>&type=second_sem_gpa">Set Additional Results</a>
                <?php elseif ($student['college_category'] == 'Health Science' && is_null($student['coc_exam_result'])): ?>
                    <a href="set_additional_results.php?user_id=<?php echo $student['user_id']; ?>&type=coc_exam_result">Set Additional Results</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h2>Departments</h2>
    <table>
        <tr>
            <th>College</th>
            <th>College Category</th>
            <th>Department</th>
            <th>Intake Capacity</th>
            <th>Min Department Entrance Point</th>
            <th>Actions</th>
        </tr>
        <?php while ($department = $departments->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($department['college_name']); ?></td>
            <td><?php echo htmlspecialchars($department['college_category']); ?></td>
            <td><?php echo htmlspecialchars($department['department_name']); ?></td>
            <td><?php echo htmlspecialchars($department['intake_capacity']); ?></td>
            <td><?php echo number_format((float)$department['min_department_entrance_result'], 2, '.', ''); ?>%</td>
            <td>
                <a href="edit_department.php?id=<?php echo $department['department_id']; ?>">Edit</a>
                <a href="delete_department.php?id=<?php echo $department['department_id']; ?>" class="delete-link" data-entity-name="<?php echo htmlspecialchars($department['department_name']); ?>" data-entity-type="department">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h2>Colleges</h2>
    <table>
        <tr>
            <th>College</th>
            <th>Intake Capacity</th>
            <th>Min College Entrance Point</th>
            <th>Actions</th>
        </tr>
        <?php while ($college = $colleges->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($college['college_name']); ?></td>
            <td><?php echo htmlspecialchars($college['intake_capacity']); ?></td>
            <td><?php echo number_format((float)$college['min_college_entrance_result'], 2, '.', ''); ?>%</td>
            <td>
                <a href="edit_college.php?id=<?php echo $college['college_id']; ?>">Edit</a>
                <a href="delete_college.php?id=<?php echo $college['college_id']; ?>" class="delete-link" data-entity-name="<?php echo htmlspecialchars($college['college_name']); ?>" data-entity-type="college">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Modal HTML -->
    <div id="deleteModal" class="modal" hidden>
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="modalMessage"></p>
            <button id="confirmDelete">Yes</button>
            <button id="cancelDelete">No</button>
        </div>
    </div>

    <!-- JavaScript for Modal -->
    <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const modal = document.getElementById('deleteModal');
        const modalMessage = document.getElementById('modalMessage');
        const confirmDelete = document.getElementById('confirmDelete');
        const cancelDelete = document.getElementById('cancelDelete');
        const span = document.getElementsByClassName('close')[0];
        let deleteUrl = '';

        document.querySelectorAll('.delete-link').forEach(item => {
            item.addEventListener('click', event => {
                event.preventDefault();
                const entityName = item.getAttribute('data-entity-name');
                const entityType = item.getAttribute('data-entity-type');
                deleteUrl = item.href;
                modalMessage.innerText = `Are you sure you want to delete ${entityType}: ${entityName}?`;
                modal.style.display = 'block';
            });
        });

        span.onclick = function() {
            modal.style.display = 'none';
        }

        cancelDelete.onclick = function() {
            modal.style.display = 'none';
        }

        confirmDelete.onclick = function() {
            window.location.href = deleteUrl;
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    });
    </script>
</body>
</html>
<?php $conn->close(); ?>
