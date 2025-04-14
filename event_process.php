<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Function to validate and sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    
    // Common fields for both add and edit
    $event_name = sanitize_input($_POST['event_name']);
    $event_type = sanitize_input($_POST['event_type']);
    $event_date = sanitize_input($_POST['event_date']);
    $start_time = sanitize_input($_POST['start_time']);
    $end_time = isset($_POST['end_time']) ? sanitize_input($_POST['end_time']) : null;
    $location = sanitize_input($_POST['location']);
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $organizer = isset($_POST['organizer']) ? sanitize_input($_POST['organizer']) : '';
    $status = sanitize_input($_POST['status']);
    
    // Optional fields
    $contact_person = isset($_POST['contact_person']) ? sanitize_input($_POST['contact_person']) : '';
    $contact_email = isset($_POST['contact_email']) ? sanitize_input($_POST['contact_email']) : '';
    $contact_phone = isset($_POST['contact_phone']) ? sanitize_input($_POST['contact_phone']) : '';
    $registration_required = isset($_POST['registration_required']) ? 1 : 0;
    $max_attendees = isset($_POST['max_attendees']) && !empty($_POST['max_attendees']) ? intval($_POST['max_attendees']) : 0;
    $public_display = isset($_POST['public_display']) ? 1 : 0;
    
    // Image handling
    $event_image = null;
    $upload_success = true;
    $image_error = '';
    
    if (isset($_FILES['event_image']) && $_FILES['event_image']['size'] > 0) {
        $target_dir = "uploads/event_images/";
        
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["event_image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid('event_') . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES["event_image"]["tmp_name"]);
        if ($check === false) {
            $upload_success = false;
            $image_error = "File is not an image.";
        }
        
        // Check file size (limit to 5MB)
        if ($_FILES["event_image"]["size"] > 5000000) {
            $upload_success = false;
            $image_error = "Sorry, your file is too large. Max size is 5MB.";
        }
        
        // Allow certain file formats
        if ($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg" && $file_extension != "gif") {
            $upload_success = false;
            $image_error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
        
        // If everything is ok, try to upload and resize file
        if ($upload_success) {
            // Create image resource
            list($width, $height) = getimagesize($_FILES["event_image"]["tmp_name"]);
            $source = null;
            
            switch(strtolower($file_extension)) {
                case 'jpg':
                case 'jpeg':
                    $source = imagecreatefromjpeg($_FILES["event_image"]["tmp_name"]);
                    break;
                case 'png':
                    $source = imagecreatefrompng($_FILES["event_image"]["tmp_name"]);
                    break;
                case 'gif':
                    $source = imagecreatefromgif($_FILES["event_image"]["tmp_name"]);
                    break;
            }
            
            if ($source) {
                // Calculate new dimensions (max 800px width, 450px height)
                $max_width = 800;
                $max_height = 450;
                
                if ($width > $max_width || $height > $max_height) {
                    $ratio = $width / $height;
                    if ($max_width / $max_height > $ratio) {
                        $new_width = $max_height * $ratio;
                        $new_height = $max_height;
                    } else {
                        $new_width = $max_width;
                        $new_height = $max_width / $ratio;
                    }
                    
                    // Create new image
                    $thumb = imagecreatetruecolor($new_width, $new_height);
                    
                    // Preserve transparency for PNG and GIF
                    if ($file_extension == 'png' || $file_extension == 'gif') {
                        imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
                        imagealphablending($thumb, false);
                        imagesavealpha($thumb, true);
                    }
                    
                    // Resize and save
                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    
                    switch(strtolower($file_extension)) {
                        case 'jpg':
                        case 'jpeg':
                            imagejpeg($thumb, $target_file, 85);
                            break;
                        case 'png':
                            imagepng($thumb, $target_file, 8);
                            break;
                        case 'gif':
                            imagegif($thumb, $target_file);
                            break;
                    }
                    
                    imagedestroy($thumb);
                    $event_image = $new_filename;
                } else {
                    // Image is already small enough, just move it
                    move_uploaded_file($_FILES["event_image"]["tmp_name"], $target_file);
                    $event_image = $new_filename;
                }
                
                imagedestroy($source);
            } else {
                $upload_success = false;
                $image_error = "Sorry, there was an error processing your image.";
            }
        }
    }
    
    // Current timestamp
    $timestamp = date('Y-m-d H:i:s');
    
    // Add new event
    if ($action == 'add') {
        // Prepare SQL statement
        $sql = "INSERT INTO events (
                    event_name, event_type, event_date, start_time, end_time, location, 
                    description, organizer, status, contact_person, contact_email, 
                    contact_phone, registration_required, max_attendees, public_display, 
                    event_image, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssssssiiisss", 
            $event_name, $event_type, $event_date, $start_time, $end_time, $location, 
            $description, $organizer, $status, $contact_person, $contact_email, 
            $contact_phone, $registration_required, $max_attendees, $public_display, 
            $event_image, $timestamp, $timestamp
        );
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Event added successfully!";
            header("Location: events.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error: " . $stmt->error;
            header("Location: events.php?view=add");
            exit();
        }
        
        $stmt->close();
    }
    
    // Edit existing event
    elseif ($action == 'edit') {
        $event_id = $_POST['event_id'];
        
        // Check if we need to update the image
        if ($upload_success && $event_image) {
            // Get current image to delete if exists
            $query = "SELECT event_image FROM events WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $current_event = $result->fetch_assoc();
                if ($current_event['event_image'] && $current_event['event_image'] != 'default-event.jpg') {
                    $old_image_path = 'uploads/event_images/' . $current_event['event_image'];
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }
            }
            
            // Update with new image
            $sql = "UPDATE events SET 
                    event_name = ?, event_type = ?, event_date = ?, start_time = ?, 
                    end_time = ?, location = ?, description = ?, organizer = ?, 
                    status = ?, contact_person = ?, contact_email = ?, contact_phone = ?, 
                    registration_required = ?, max_attendees = ?, public_display = ?, 
                    event_image = ?, updated_at = ? 
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssssssssssiissi", 
                $event_name, $event_type, $event_date, $start_time, $end_time, 
                $location, $description, $organizer, $status, $contact_person, 
                $contact_email, $contact_phone, $registration_required, 
                $max_attendees, $public_display, $event_image, $timestamp, $event_id
            );
        } else {
            // Update without changing the image
            $sql = "UPDATE events SET 
                    event_name = ?, event_type = ?, event_date = ?, start_time = ?, 
                    end_time = ?, location = ?, description = ?, organizer = ?, 
                    status = ?, contact_person = ?, contact_email = ?, contact_phone = ?, 
                    registration_required = ?, max_attendees = ?, public_display = ?, 
                    updated_at = ? 
                    WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "ssssssssssssiiisi", 
                $event_name, $event_type, $event_date, $start_time, $end_time, 
                $location, $description, $organizer, $status, $contact_person, 
                $contact_email, $contact_phone, $registration_required, 
                $max_attendees, $public_display, $timestamp, $event_id
            );
        }
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Event updated successfully!";
            header("Location: events.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error: " . $stmt->error;
            header("Location: events.php?view=edit&id=" . $event_id);
            exit();
        }
        
        $stmt->close();
    }
}

// If we get here, something went wrong
$_SESSION['error_message'] = "Invalid request.";
header("Location: events.php");
exit();
?>

