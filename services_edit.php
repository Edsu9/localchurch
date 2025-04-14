<?php
session_start();
require_once 'config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_id'])) {
    // Get form data
    $service_id = mysqli_real_escape_string($conn, $_POST['service_id']);
    $service_title = mysqli_real_escape_string($conn, $_POST['service_title']);
    $service_day = mysqli_real_escape_string($conn, $_POST['service_day']);
    $service_time = mysqli_real_escape_string($conn, $_POST['service_time']);
    $service_location = isset($_POST['service_location']) ? mysqli_real_escape_string($conn, $_POST['service_location']) : '';
    $service_leader = isset($_POST['service_leader']) ? mysqli_real_escape_string($conn, $_POST['service_leader']) : '';
    $service_description = isset($_POST['service_description']) ? mysqli_real_escape_string($conn, $_POST['service_description']) : '';
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Update database
    $query = "UPDATE services SET 
              service_title = '$service_title', 
              service_day = '$service_day', 
              service_time = '$service_time', 
              service_location = '$service_location', 
              service_leader = '$service_leader', 
              service_description = '$service_description', 
              is_active = $is_active, 
              updated_at = NOW() 
              WHERE service_id = $service_id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Service updated successfully!";
        header("Location: services_management.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        header("Location: services_management.php?view=edit&id=$service_id");
        exit();
    }
} else {
    // Redirect if not a POST request or missing service_id
    header("Location: services_management.php");
    exit();
}
?>

