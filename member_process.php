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
    header("Location: members.php");
    exit();
}

$action = $_POST['action'];

// Handle add member
if ($action == 'add') {
    // Get form data
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $phone = isset($_POST['phone']) ? mysqli_real_escape_string($conn, $_POST['phone']) : '';
    $membership_status = mysqli_real_escape_string($conn, $_POST['membership_status']);
    $join_date = isset($_POST['join_date']) ? mysqli_real_escape_string($conn, $_POST['join_date']) : NULL;
    $ministry = isset($_POST['ministry']) ? mysqli_real_escape_string($conn, $_POST['ministry']) : '';
    $gender = isset($_POST['gender']) ? mysqli_real_escape_string($conn, $_POST['gender']) : '';
    $date_of_birth = isset($_POST['date_of_birth']) ? mysqli_real_escape_string($conn, $_POST['date_of_birth']) : NULL;
    $address = isset($_POST['address']) ? mysqli_real_escape_string($conn, $_POST['address']) : '';
    $city = isset($_POST['city']) ? mysqli_real_escape_string($conn, $_POST['city']) : '';
    $state = isset($_POST['state']) ? mysqli_real_escape_string($conn, $_POST['state']) : '';
    $zip = isset($_POST['zip']) ? mysqli_real_escape_string($conn, $_POST['zip']) : '';
    $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';
    
    // Handle profile photo upload
    $profile_photo = 'default.jpg'; // Default photo
    
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $upload_dir = 'uploads/profile_photos/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = time() . '_' . $_FILES['profile_photo']['name'];
        $file_path = $upload_dir . $file_name;
        
        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['profile_photo']['type'], $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $file_path)) {
                $profile_photo = $file_name;
            }
        }
    }

    $current_timestamp = date('Y-m-d H:i:s');
    
    // Insert member into database
    $query = "INSERT INTO members (first_name, last_name, email, phone, membership_status, join_date, 
              ministry, gender, date_of_birth, address, city, state, zip, notes, profile_photo, created_at, updated_at) 
              VALUES ('$first_name', '$last_name', '$email', '$phone', '$membership_status', " . 
              ($join_date ? "'$join_date'" : "NULL") . ", '$ministry', '$gender', " . 
              ($date_of_birth ? "'$date_of_birth'" : "NULL") . ", '$address', '$city', '$state', '$zip', 
              '$notes', '$profile_photo', '$current_timestamp', '$current_timestamp')";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Member added successfully!";
        header("Location: members.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        header("Location: members.php?view=add");
        exit();
    }
}

// Handle edit member
elseif ($action == 'edit') {
    // Get form data
    $member_id = intval($_POST['member_id']);
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $phone = isset($_POST['phone']) ? mysqli_real_escape_string($conn, $_POST['phone']) : '';
    $membership_status = mysqli_real_escape_string($conn, $_POST['membership_status']);
    $join_date = isset($_POST['join_date']) ? mysqli_real_escape_string($conn, $_POST['join_date']) : NULL;
    $ministry = isset($_POST['ministry']) ? mysqli_real_escape_string($conn, $_POST['ministry']) : '';
    $gender = isset($_POST['gender']) ? mysqli_real_escape_string($conn, $_POST['gender']) : '';
    $date_of_birth = isset($_POST['date_of_birth']) ? mysqli_real_escape_string($conn, $_POST['date_of_birth']) : NULL;
    $address = isset($_POST['address']) ? mysqli_real_escape_string($conn, $_POST['address']) : '';
    $city = isset($_POST['city']) ? mysqli_real_escape_string($conn, $_POST['city']) : '';
    $state = isset($_POST['state']) ? mysqli_real_escape_string($conn, $_POST['state']) : '';
    $zip = isset($_POST['zip']) ? mysqli_real_escape_string($conn, $_POST['zip']) : '';
    $notes = isset($_POST['notes']) ? mysqli_real_escape_string($conn, $_POST['notes']) : '';
    
    // Get current member data
    $query = "SELECT profile_photo FROM members WHERE id = $member_id";
    $result = mysqli_query($conn, $query);
    $member = mysqli_fetch_assoc($result);
    $profile_photo = $member['profile_photo'];
    
    // Handle profile photo upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $upload_dir = 'uploads/profile_photos/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = time() . '_' . $_FILES['profile_photo']['name'];
        $file_path = $upload_dir . $file_name;
        
        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['profile_photo']['type'], $allowed_types)) {
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $file_path)) {
                // Delete old photo if not default
                if ($profile_photo != 'default.jpg' && file_exists($upload_dir . $profile_photo)) {
                    unlink($upload_dir . $profile_photo);
                }
                $profile_photo = $file_name;
            }
        }
    }

    $current_timestamp = date('Y-m-d H:i:s');
    
    // Update member in database
    $query = "UPDATE members SET 
              first_name = '$first_name', 
              last_name = '$last_name', 
              email = '$email', 
              phone = '$phone', 
              membership_status = '$membership_status', 
              join_date = " . ($join_date ? "'$join_date'" : "NULL") . ", 
              ministry = '$ministry', 
              gender = '$gender', 
              date_of_birth = " . ($date_of_birth ? "'$date_of_birth'" : "NULL") . ", 
              address = '$address', 
              city = '$city', 
              state = '$state', 
              zip = '$zip', 
              notes = '$notes', 
              profile_photo = '$profile_photo', 
              updated_at = '$current_timestamp' 
              WHERE id = $member_id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Member updated successfully!";
        header("Location: members.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
        header("Location: members.php?view=edit&id=$member_id");
        exit();
    }
}

// Invalid action
else {
    $_SESSION['error_message'] = "Invalid action";
    header("Location: members.php");
    exit();
}
?>

