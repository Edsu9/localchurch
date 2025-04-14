<?php
session_start();
require_once 'config/db.php';

// Set the default timezone
date_default_timezone_set('Asia/Manila'); // Change to your timezone if needed

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if action is set
if (!isset($_POST['action'])) {
    $_SESSION['error_message'] = "Invalid request";
    header("Location: donations.php");
    exit();
}

$action = $_POST['action'];

// Handle add donation
if ($action == 'add') {
    // Get form data
    $member_id = isset($_POST['member_id']) && $_POST['member_id'] ? intval($_POST['member_id']) : NULL;
    $amount = floatval($_POST['amount']);
    $donation_date = mysqli_real_escape_string($conn, $_POST['donation_date']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $campaign = isset($_POST['campaign']) ? mysqli_real_escape_string($conn, $_POST['campaign']) : '';
    $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';
    $is_recurring = isset($_POST['is_recurring']) ? 1 : 0;
    $receipt_number = isset($_POST['receipt_number']) ? mysqli_real_escape_string($conn, $_POST['receipt_number']) : '';
    
    // Handle receipt upload
    $receipt_file = NULL;
    
    if (isset($_FILES['receipt_file']) && $_FILES['receipt_file']['error'] == 0) {
        $upload_dir = 'uploads/receipts/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = time() . '_' . $_FILES['receipt_file']['name'];
        $file_path = $upload_dir . $file_name;
        
        // Check file type
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
        if (in_array($_FILES['receipt_file']['type'], $allowed_types)) {
            if (move_uploaded_file($_FILES['receipt_file']['tmp_name'], $file_path)) {
                $receipt_file = $file_name;
            }
        }
    }
    
    // Insert donation into database
    $current_timestamp = date('Y-m-d H:i:s');
    $query = "INSERT INTO donations (member_id, amount, donation_date, payment_method, category, 
              campaign, notes, is_recurring, receipt_number, receipt_file, created_at, updated_at) 
              VALUES (" . ($member_id ? "$member_id" : "NULL") . ", $amount, '$donation_date', 
              '$payment_method', '$category', '$campaign', '$notes', $is_recurring, '$receipt_number', " . 
              ($receipt_file ? "'$receipt_file'" : "NULL") . ", '$current_timestamp', '$current_timestamp')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Donation added successfully!";
        header("Location: donations.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        header("Location: donations.php?view=add");
        exit();
    }
}

// Handle edit donation
elseif ($action == 'edit') {
    // Get form data
    $donation_id = intval($_POST['donation_id']);
    $member_id = isset($_POST['member_id']) && $_POST['member_id'] ? intval($_POST['member_id']) : NULL;
    $amount = floatval($_POST['amount']);
    $donation_date = mysqli_real_escape_string($conn, $_POST['donation_date']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $campaign = isset($_POST['campaign']) ? mysqli_real_escape_string($conn, $_POST['campaign']) : '';
    $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';
    $is_recurring = isset($_POST['is_recurring']) ? 1 : 0;
    $receipt_number = isset($_POST['receipt_number']) ? mysqli_real_escape_string($conn, $_POST['receipt_number']) : '';
    
    // Get current donation data
    $query = "SELECT receipt_file FROM donations WHERE id = $donation_id";
    $result = mysqli_query($conn, $query);
    $donation = mysqli_fetch_assoc($result);
    $receipt_file = $donation['receipt_file'];
    
    // Handle receipt upload
    if (isset($_FILES['receipt_file']) && $_FILES['receipt_file']['error'] == 0) {
        $upload_dir = 'uploads/receipts/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = time() . '_' . $_FILES['receipt_file']['name'];
        $file_path = $upload_dir . $file_name;
        
        // Check file type
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
        if (in_array($_FILES['receipt_file']['type'], $allowed_types)) {
            if (move_uploaded_file($_FILES['receipt_file']['tmp_name'], $file_path)) {
                // Delete old receipt if exists
                if ($receipt_file && file_exists($upload_dir . $receipt_file)) {
                    unlink($upload_dir . $receipt_file);
                }
                $receipt_file = $file_name;
            }
        }
    }
    
    // Update donation in database
    $current_timestamp = date('Y-m-d H:i:s');
    $query = "UPDATE donations SET 
              member_id = " . ($member_id ? "$member_id" : "NULL") . ", 
              amount = $amount, 
              donation_date = '$donation_date', 
              payment_method = '$payment_method', 
              category = '$category', 
              campaign = '$campaign', 
              notes = '$notes', 
              is_recurring = $is_recurring, 
              receipt_number = '$receipt_number', 
              receipt_file = " . ($receipt_file ? "'$receipt_file'" : "NULL") . ", 
              updated_at = '$current_timestamp' 
              WHERE id = $donation_id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Donation updated successfully!";
        header("Location: donations.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        header("Location: donations.php?view=edit&id=$donation_id");
        exit();
    }
}

// Invalid action
else {
    $_SESSION['error_message'] = "Invalid action";
    header("Location: donations.php");
    exit();
}
?>

