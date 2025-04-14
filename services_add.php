<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $service_title = mysqli_real_escape_string($conn, $_POST['service_title']);
    $service_day = mysqli_real_escape_string($conn, $_POST['service_day']);
    $service_time = mysqli_real_escape_string($conn, $_POST['service_time']);
    $service_location = isset($_POST['service_location']) ? mysqli_real_escape_string($conn, $_POST['service_location']) : '';
    $service_leader = isset($_POST['service_leader']) ? mysqli_real_escape_string($conn, $_POST['service_leader']) : '';
    $service_description = isset($_POST['service_description']) ? mysqli_real_escape_string($conn, $_POST['service_description']) : '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Insert into database
    $query = "INSERT INTO services (service_title, service_day, service_time, service_location, service_leader, service_description, is_active, created_at, updated_at) 
              VALUES ('$service_title', '$service_day', '$service_time', '$service_location', '$service_leader', '$service_description', $is_active, NOW(), NOW())";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Service added successfully!";
        header("Location: services_management.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        header("Location: services_management.php?view=add");
        exit();
    }
} else {
    // Redirect if not a POST request
    header("Location: services_management.php");
    exit();
}
?>

