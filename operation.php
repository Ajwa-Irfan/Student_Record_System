<?php
session_start();

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'student_data_system');
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create table if not exists
$createTable = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    course VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $createTable);

//Add Student
if (isset($_POST['add_student'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);

    $insert = "INSERT INTO students (name, email, phone, course) VALUES ('$name', '$email', '$phone', '$course')";
    if (mysqli_query($conn, $insert)) {
        $_SESSION['message'] = "Student added successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error adding student: " . mysqli_error($conn);
        $_SESSION['message_type'] = "error";
    }
    
    header("Location: main.php");
    exit();
}

//Update Student
if (isset($_POST['update_student'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);

    $update = "UPDATE students SET name='$name', email='$email', phone='$phone', course='$course' WHERE id=$id";
    if (mysqli_query($conn, $update)) {
        $_SESSION['message'] = "Student updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating student: " . mysqli_error($conn);
        $_SESSION['message_type'] = "error";
    }
    
    // Preserve search parameter if it exists
    $redirect = "main.php";
    if (isset($_GET['search'])) {
        $redirect .= "?search=" . urlencode($_GET['search']);
    }
    header("Location: $redirect");
    exit();
}

mysqli_close($conn);
?>