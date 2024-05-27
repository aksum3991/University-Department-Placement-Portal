<?php
require_once('config.php');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $college_id = $_POST["college_id"];
    $intake_capacity = $_POST["intake_capacity"];

    $stmt = $conn->prepare("UPDATE colleges SET intake_capacity = ? WHERE college_id = ?");
    $stmt->bind_param("ii", $intake_capacity, $college_id);

    if ($stmt->execute()) {
        echo "College intake capacity updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
