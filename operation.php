<?php
ob_start(); 
session_start();

// Database connection
$conn = mysqli_connect('localhost', 'root', '', 'student_data_system');

if (!$conn) {
    $_SESSION['message'] = "Database connection failed!";
    $_SESSION['message_type'] = "error";
    header("Location: main.php");
    exit();
}

// ADD STUDENT
if(isset($_POST['add_student'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    
    // Check if email exists
    $checkEmail = mysqli_query($conn, "SELECT id FROM students WHERE email = '$email'");
    
    if(mysqli_num_rows($checkEmail) > 0) {
        $_SESSION['message'] = "Email already exists!";
        $_SESSION['message_type'] = "error";
    } else {
        $sql = "INSERT INTO students (name, email, phone, course) VALUES ('$name', '$email', '$phone', '$course')";
        
        if(mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Student added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error: " . mysqli_error($conn);
            $_SESSION['message_type'] = "error";
        }
    }
}

// UPDATE STUDENT
if(isset($_POST['update_student'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    
    $checkEmail = mysqli_query($conn, "SELECT id FROM students WHERE email = '$email' AND id != $id");
    
    if(mysqli_num_rows($checkEmail) > 0) {
        $_SESSION['message'] = "Email already exists!";
        $_SESSION['message_type'] = "error";
    } else {
        $sql = "UPDATE students SET name='$name', email='$email', phone='$phone', course='$course' WHERE id=$id";
        
        if(mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Student updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error: " . mysqli_error($conn);
            $_SESSION['message_type'] = "error";
        }
    }
}

// DELETE STUDENT
if(isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    $student = mysqli_query($conn, "SELECT name FROM students WHERE id = $id");
    if(mysqli_num_rows($student) > 0) {
        $row = mysqli_fetch_assoc($student);
        $name = $row['name'];
        
        $sql = "DELETE FROM students WHERE id = $id";
        
        if(mysqli_query($conn, $sql)) {
            $_SESSION['message'] = "Student deleted successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error: " . mysqli_error($conn);
            $_SESSION['message_type'] = "error";
        }
    }
}

mysqli_close($conn);
header("Location: main.php");
exit();
ob_end_flush(); 
?>