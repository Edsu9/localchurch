<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Handle image removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'remove_image') {
    $image_id = isset($_POST['image_id']) ? $_POST['image_id'] : null;
    $entity_type = isset($_POST['entity_type']) ? $_POST['entity_type'] : null;
    $entity_id = isset($_POST['entity_id']) ? $_POST['entity_id'] : null;
    
    if (!$image_id || !$entity_type || !$entity_id) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
        exit();
    }
    
    // Process based on entity type
    switch ($entity_type) {
        case 'event':
            removeEventImage($conn, $entity_id);
            break;
        case 'sermon':
            removeSermonImage($conn, $entity_id);
            break;
        case 'ministry':
            removeMinistryImage($conn, $entity_id);
            break;
        case 'member':
            removeMemberImage($conn, $entity_id);
            break;
        case 'settings':
            removeSettingsImage($conn, $image_id);
            break;
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid entity type']);
            exit();
    }
}

/**
 * Remove event image
 * @param mysqli $conn - Database connection
 * @param int $event_id - Event ID
 */
function removeEventImage($conn, $event_id) {
    // Get current image path
    $query = "SELECT event_image FROM events WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $event_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $image_path);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    
    if ($image_path) {
        // Delete the image file if it exists
        $file_path = 'uploads/event_images/' . $image_path;
        if (file_exists($file_path) && $image_path != 'default-event.jpg') {
            unlink($file_path);
        }
        
        // Update database to remove image reference
        $update_query = "UPDATE events SET event_image = NULL WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "i", $event_id);
        $success = mysqli_stmt_execute($update_stmt);
        mysqli_stmt_close($update_stmt);
        
        if ($success) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Event image removed successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No image found for this event']);
    }
    
    exit();
}

/**
 * Remove sermon image
 * @param mysqli $conn - Database connection
 * @param int $sermon_id - Sermon ID
 */
function removeSermonImage($conn, $sermon_id) {
    // Get current image path
    $query = "SELECT image FROM sermons WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $sermon_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $image_path);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    
    if ($image_path) {
        // Delete the image file if it exists
        $file_path = 'uploads/sermon_images/' . $image_path;
        if (file_exists($file_path) && $image_path != 'default-sermon.jpg') {
            unlink($file_path);
        }
        
        // Update database to remove image reference
        $update_query = "UPDATE sermons SET image = NULL WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "i", $sermon_id);
        $success = mysqli_stmt_execute($update_stmt);
        mysqli_stmt_close($update_stmt);
        
        if ($success) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Sermon image removed successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No image found for this sermon']);
    }
    
    exit();
}

/**
 * Remove ministry image
 * @param mysqli $conn - Database connection
 * @param int $ministry_id - Ministry ID
 */
function removeMinistryImage($conn, $ministry_id) {
    // Get current image path
    $query = "SELECT image FROM ministries WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $ministry_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $image_path);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    
    if ($image_path) {
        // Delete the image file if it exists
        $file_path = 'uploads/ministry_images/' . $image_path;
        if (file_exists($file_path) && $image_path != 'default-ministry.jpg') {
            unlink($file_path);
        }
        
        // Update database to remove image reference
        $update_query = "UPDATE ministries SET image = NULL WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "i", $ministry_id);
        $success = mysqli_stmt_execute($update_stmt);
        mysqli_stmt_close($update_stmt);
        
        if ($success) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Ministry image removed successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No image found for this ministry']);
    }
    
    exit();
}

/**
 * Remove member image
 * @param mysqli $conn - Database connection
 * @param int $member_id - Member ID
 */
function removeMemberImage($conn, $member_id) {
    // Get current image path
    $query = "SELECT profile_photo FROM members WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $member_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $image_path);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    
    if ($image_path) {
        // Delete the image file if it exists
        $file_path = 'uploads/member_photos/' . $image_path;
        if (file_exists($file_path) && $image_path != 'default-profile.jpg') {
            unlink($file_path);
        }
        
        // Update database to remove image reference
        $update_query = "UPDATE members SET profile_photo = NULL WHERE id = ?";
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($update_stmt, "i", $member_id);
        $success = mysqli_stmt_execute($update_stmt);
        mysqli_stmt_close($update_stmt);
        
        if ($success) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Member photo removed successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No photo found for this member']);
    }
    
    exit();
}

/**
 * Remove settings image
 * @param mysqli $conn - Database connection
 * @param string $image_field - Image field name
 */
function removeSettingsImage($conn, $image_field) {
    // Validate image field name to prevent SQL injection
    $allowed_fields = [
        'logo', 'favicon', 'home_slider_1', 'home_slider_2', 'home_slider_3', 
        'home_welcome_image', 'about_us_image', 'pastor_image', 'ministries_image',
        'ministry_image_1', 'ministry_image_2', 'ministry_image_3', 
        'events_image', 'contact_image'
    ];
    
    if (!in_array($image_field, $allowed_fields)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid image field']);
        exit();
    }
    
    // Get current image path
    $query = "SELECT $image_field FROM settings WHERE id = 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $image_path = $row[$image_field];
    
    if ($image_path) {
        // Delete the image file if it exists
        $file_path = $image_path;
        if (file_exists($file_path) && strpos($image_path, 'default') === false) {
            unlink($file_path);
        }
        
        // Update database to remove image reference
        $update_query = "UPDATE settings SET $image_field = NULL WHERE id = 1";
        $success = mysqli_query($conn, $update_query);
        
        if ($success) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Image removed successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to update database']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No image found for this field']);
    }
    
    exit();
}
?>

