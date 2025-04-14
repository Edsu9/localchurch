<?php
session_start();
require_once 'config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if action is set
if (!isset($_POST['action'])) {
    $_SESSION['error_message'] = "Invalid request";
    header("Location: services.php");
    exit();
}

$action = $_POST['action'];

// Add new service
if ($action === 'add') {
    // Validate required fields
    if (empty($_POST['service_title']) || empty($_POST['service_day']) || empty($_POST['service_time'])) {
        $_SESSION['error_message'] = "Service title, day, and time are required";
        header("Location: services.php");
        exit();
    }
    
    // Sanitize input
    $service_title = mysqli_real_escape_string($conn, $_POST['service_title']);
    $service_day = mysqli_real_escape_string($conn, $_POST['service_day']);
    $service_time = mysqli_real_escape_string($conn, $_POST['service_time']);
    $service_location = !empty($_POST['service_location']) ? mysqli_real_escape_string($conn, $_POST['service_location']) : '';
    $service_leader = !empty($_POST['service_leader']) ? mysqli_real_escape_string($conn, $_POST['service_leader']) : '';
    $service_description = !empty($_POST['service_description']) ? mysqli_real_escape_string($conn, $_POST['service_description']) : '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Insert into database
    $query = "INSERT INTO services (service_title, service_day, service_time, service_location, service_leader, service_description, is_active, created_at, updated_at) 
              VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssi", $service_title, $service_day, $service_time, $service_location, $service_leader, $service_description, $is_active);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Service added successfully!";
    } else {
        $_SESSION['error_message'] = "Error adding service: " . $conn->error;
    }
    
    $stmt->close();
    header("Location: services.php");
    exit();
}

// Edit existing service
else if ($action === 'edit') {
    // Validate required fields
    if (empty($_POST['service_id']) || empty($_POST['service_title']) || empty($_POST['service_day']) || empty($_POST['service_time'])) {
        $_SESSION['error_message'] = "Service ID, title, day, and time are required";
        header("Location: services.php");
        exit();
    }
    
    // Sanitize input
    $service_id = mysqli_real_escape_string($conn, $_POST['service_id']);
    $service_title = mysqli_real_escape_string($conn, $_POST['service_title']);
    $service_day = mysqli_real_escape_string($conn, $_POST['service_day']);
    $service_time = mysqli_real_escape_string($conn, $_POST['service_time']);
    $service_location = !empty($_POST['service_location']) ? mysqli_real_escape_string($conn, $_POST['service_location']) : '';
    $service_leader = !empty($_POST['service_leader']) ? mysqli_real_escape_string($conn, $_POST['service_leader']) : '';
    $service_description = !empty($_POST['service_description']) ? mysqli_real_escape_string($conn, $_POST['service_description']) : '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Update database
    $query = "UPDATE services 
              SET service_title = ?, service_day = ?, service_time = ?, service_location = ?, 
                  service_leader = ?, service_description = ?, is_active = ?, updated_at = NOW() 
              WHERE service_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssii", $service_title, $service_day, $service_time, $service_location, $service_leader, $service_description, $is_active, $service_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Service updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating service: " . $conn->error;
    }
    
    $stmt->close();
    header("Location: services.php");
    exit();
}

// Invalid action
else {
    $_SESSION['error_message'] = "Invalid action";
    header("Location: services.php");
    exit();
}
?>

