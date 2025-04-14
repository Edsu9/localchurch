<?php
session_start();
require_once 'config/db.php';

// Add timezone setting at the top of the file, after session_start() and require_once:
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
    header("Location: settings.php");
    exit();
}

$action = $_POST['action'];

// Handle update account
if ($action == 'update_account') {
    $user_id = $_SESSION['user_id'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);

    // Check if username already exists (except current user)
    $query = "SELECT id FROM users WHERE username = '$username' AND id != $user_id";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error_message'] = "Username already exists";
        header("Location: settings.php");
        exit();
    }

    // Check if email already exists (except current user)
    $query = "SELECT id FROM users WHERE email = '$email' AND id != $user_id";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $_SESSION['error_message'] = "Email already exists";
        header("Location: settings.php");
        exit();
    }

    // Replace NOW() with current_timestamp variable in all update sections
    $current_timestamp = date('Y-m-d H:i:s');

    // Update user information
    $query = "UPDATE users SET username = '$username', email = '$email', full_name = '$full_name', updated_at = '$current_timestamp' WHERE id = $user_id";

    if (!mysqli_query($conn, $query)) {
        $_SESSION['error_message'] = "Error updating account: " . mysqli_error($conn);
        header("Location: settings.php");
        exit();
    }

    // Update password if provided
    if (!empty($_POST['current_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        // Get current user data
        $query = "SELECT password FROM users WHERE id = $user_id";
        $result = mysqli_query($conn, $query);
        $user = mysqli_fetch_assoc($result);

        // Verify current password
        if (!password_verify($_POST['current_password'], $user['password'])) {
            $_SESSION['error_message'] = "Current password is incorrect";
            header("Location: settings.php");
            exit();
        }

        // Check if new password and confirm password match
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            $_SESSION['error_message'] = "New password and confirm password do not match";
            header("Location: settings.php");
            exit();
        }

        // Update password
        $new_password_hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $query = "UPDATE users SET password = '$new_password_hash', updated_at = '$current_timestamp' WHERE id = $user_id";

        if (!mysqli_query($conn, $query)) {
            $_SESSION['error_message'] = "Error updating password: " . mysqli_error($conn);
            header("Location: settings.php");
            exit();
        }
    }

    // Update session variables
    $_SESSION['username'] = $username;
    $_SESSION['full_name'] = $full_name;

    $_SESSION['success_message'] = "Account updated successfully!";
    header("Location: settings.php");
    exit();
}

// Handle update church information
elseif ($action == 'update_church') {
    $church_name = mysqli_real_escape_string($conn, $_POST['church_name']);
    $tagline = isset($_POST['tagline']) ? mysqli_real_escape_string($conn, $_POST['tagline']) : '';
    $established_year = isset($_POST['established_year']) ? mysqli_real_escape_string($conn, $_POST['established_year']) : '';
    $address = isset($_POST['address']) ? mysqli_real_escape_string($conn, $_POST['address']) : '';
    $city = isset($_POST['city']) ? mysqli_real_escape_string($conn, $_POST['city']) : '';
    $state = isset($_POST['state']) ? mysqli_real_escape_string($conn, $_POST['state']) : '';
    $zip = isset($_POST['zip']) ? mysqli_real_escape_string($conn, $_POST['zip']) : '';
    $country = isset($_POST['country']) ? mysqli_real_escape_string($conn, $_POST['country']) : '';
    $phone = isset($_POST['phone']) ? mysqli_real_escape_string($conn, $_POST['phone']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $website = isset($_POST['website']) ? mysqli_real_escape_string($conn, $_POST['website']) : '';
    $pastor_name = isset($_POST['pastor_name']) ? mysqli_real_escape_string($conn, $_POST['pastor_name']) : '';
    $pastor_title = isset($_POST['pastor_title']) ? mysqli_real_escape_string($conn, $_POST['pastor_title']) : '';

    // Get current settings
    $query = "SELECT logo FROM settings WHERE id = 1";
    $result = mysqli_query($conn, $query);
    $settings = mysqli_fetch_assoc($result);
    $logo = isset($settings['logo']) ? $settings['logo'] : '';

    // Handle logo upload
    if (isset($_FILES['church_logo']) && $_FILES['church_logo']['error'] == 0) {
        $upload_dir = 'uploads/logo/';

        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = time() . '_' . $_FILES['church_logo']['name'];
        $file_path = $upload_dir . $file_name;

        // Check file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['church_logo']['type'], $allowed_types)) {
            if (move_uploaded_file($_FILES['church_logo']['tmp_name'], $file_path)) {
                // Delete old logo if exists
                if ($logo && file_exists($upload_dir . $logo)) {
                    unlink($upload_dir . $logo);
                }
                $logo = $file_name;
            }
        }
    }

    // Check if settings record exists
    $query = "SELECT COUNT(*) as count FROM settings";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    // Replace NOW() with current_timestamp variable in all update sections
    $current_timestamp = date('Y-m-d H:i:s');

    if ($row['count'] > 0) {
        // Update settings
        $query = "UPDATE settings SET 
                  church_name = '$church_name', 
                  tagline = '$tagline', 
                  established_year = '$established_year', 
                  address = '$address', 
                  city = '$city', 
                  state = '$state', 
                  zip = '$zip', 
                  country = '$country', 
                  phone = '$phone', 
                  email = '$email', 
                  website = '$website', 
                  pastor_name = '$pastor_name', 
                  pastor_title = '$pastor_title', 
                  logo = '$logo', 
                  updated_at = '$current_timestamp' 
                  WHERE id = 1";
    } else {
        // Insert settings
        $query = "INSERT INTO settings (church_name, tagline, established_year, address, city, state, zip, country, 
                  phone, email, website, pastor_name, pastor_title, logo, created_at, updated_at) 
                  VALUES ('$church_name', '$tagline', '$established_year', '$address', '$city', '$state', '$zip', 
                  '$country', '$phone', '$email', '$website', '$pastor_name', '$pastor_title', '$logo', '$current_timestamp', '$current_timestamp')";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Church information updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating church information: " . mysqli_error($conn);
    }

    header("Location: settings.php");
    exit();
}

// Handle update system settings
elseif ($action == 'update_system') {
    $theme_color = mysqli_real_escape_string($conn, $_POST['theme_color']);
    $items_per_page = intval($_POST['items_per_page']);
    $date_format = mysqli_real_escape_string($conn, $_POST['date_format']);
    $time_format = mysqli_real_escape_string($conn, $_POST['time_format']);
    $currency_symbol = mysqli_real_escape_string($conn, $_POST['currency_symbol']);
    $enable_email = isset($_POST['enable_email']) ? 1 : 0;
    $smtp_host = isset($_POST['smtp_host']) ? mysqli_real_escape_string($conn, $_POST['smtp_host']) : '';
    $smtp_port = isset($_POST['smtp_port']) ? mysqli_real_escape_string($conn, $_POST['smtp_port']) : '';
    $smtp_username = isset($_POST['smtp_username']) ? mysqli_real_escape_string($conn, $_POST['smtp_username']) : '';
    $smtp_password = isset($_POST['smtp_password']) ? mysqli_real_escape_string($conn, $_POST['smtp_password']) : '';
    $from_email = isset($_POST['from_email']) ? mysqli_real_escape_string($conn, $_POST['from_email']) : '';
    $from_name = isset($_POST['from_name']) ? mysqli_real_escape_string($conn, $_POST['from_name']) : '';

    // Check if settings record exists
    $query = "SELECT COUNT(*) as count FROM settings";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    // Replace NOW() with current_timestamp variable in all update sections
    $current_timestamp = date('Y-m-d H:i:s');

    if ($row['count'] > 0) {
        // Update settings
        $query = "UPDATE settings SET 
                  theme_color = '$theme_color', 
                  items_per_page = $items_per_page, 
                  date_format = '$date_format', 
                  time_format = '$time_format', 
                  currency_symbol = '$currency_symbol', 
                  enable_email = $enable_email, 
                  smtp_host = '$smtp_host', 
                  smtp_port = '$smtp_port', 
                  smtp_username = '$smtp_username', 
                  smtp_password = '$smtp_password', 
                  from_email = '$from_email', 
                  from_name = '$from_name', 
                  updated_at = '$current_timestamp' 
                  WHERE id = 1";
    } else {
        // Insert settings
        $query = "INSERT INTO settings (theme_color, items_per_page, date_format, time_format, currency_symbol, 
                  enable_email, smtp_host, smtp_port, smtp_username, smtp_password, from_email, from_name, 
                  created_at, updated_at) 
                  VALUES ('$theme_color', $items_per_page, '$date_format', '$time_format', '$currency_symbol', 
                  $enable_email, '$smtp_host', '$smtp_port', '$smtp_username', '$smtp_password', '$from_email', 
                  '$from_name', '$current_timestamp', '$current_timestamp')";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "System settings updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating system settings: " . mysqli_error($conn);
    }

    header("Location: settings.php");
    exit();
}

// Add this case in the switch statement
elseif ($action == 'update_website') {
    // Hero section
    $hero_title = mysqli_real_escape_string($conn, $_POST['hero_title']);
    $hero_subtitle = mysqli_real_escape_string($conn, $_POST['hero_subtitle']);
    $welcome_title = mysqli_real_escape_string($conn, $_POST['welcome_title']);
    $welcome_content = mysqli_real_escape_string($conn, $_POST['welcome_content']);
    
    // Service times
    $sunday_morning_time = mysqli_real_escape_string($conn, $_POST['sunday_morning_time']);
    $sunday_morning_desc = mysqli_real_escape_string($conn, $_POST['sunday_morning_desc']);
    $sunday_afternoon_time = mysqli_real_escape_string($conn, $_POST['sunday_afternoon_time']);
    $sunday_afternoon_desc = mysqli_real_escape_string($conn, $_POST['sunday_afternoon_desc']);
    $midweek_time = mysqli_real_escape_string($conn, $_POST['midweek_time']);
    $midweek_desc = mysqli_real_escape_string($conn, $_POST['midweek_desc']);
    
    // Ministries section
    $ministries_title = mysqli_real_escape_string($conn, $_POST['ministries_title']);
    $ministries_subtitle = mysqli_real_escape_string($conn, $_POST['ministries_subtitle']);
    
    // SEO settings
    $meta_title = mysqli_real_escape_string($conn, $_POST['meta_title']);
    $meta_description = mysqli_real_escape_string($conn, $_POST['meta_description']);
    $meta_keywords = mysqli_real_escape_string($conn, $_POST['meta_keywords']);
    
    // Social media
    $facebook_url = mysqli_real_escape_string($conn, $_POST['facebook_url']);
    $twitter_url = mysqli_real_escape_string($conn, $_POST['twitter_url']);
    $instagram_url = mysqli_real_escape_string($conn, $_POST['instagram_url']);
    $youtube_url = mysqli_real_escape_string($conn, $_POST['youtube_url']);
    
    // Handle hero background upload
    $hero_background = '';
    if(isset($_FILES['hero_background']) && $_FILES['hero_background']['size'] > 0) {
        $upload_dir = 'uploads/website/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['hero_background']['name']);
        $target_file = $upload_dir . $file_name;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES['hero_background']['tmp_name']);
        if($check !== false) {
            // Upload file
            if(move_uploaded_file($_FILES['hero_background']['tmp_name'], $target_file)) {
                $hero_background = $file_name;
                
                // Delete old image if exists
                if(isset($settings['hero_background']) && $settings['hero_background'] && file_exists($upload_dir . $settings['hero_background'])) {
                    unlink($upload_dir . $settings['hero_background']);
                }
            }
        }
    } else {
        // Keep existing image
        $hero_background = isset($settings['hero_background']) ? $settings['hero_background'] : '';
    }
    
    // Update settings in database
    $query = "UPDATE settings SET 
                hero_title = '$hero_title',
                hero_subtitle = '$hero_subtitle',
                hero_background = '$hero_background',
                welcome_title = '$welcome_title',
                welcome_content = '$welcome_content',
                sunday_morning_time = '$sunday_morning_time',
                sunday_morning_desc = '$sunday_morning_desc',
                sunday_afternoon_time = '$sunday_afternoon_time',
                sunday_afternoon_desc = '$sunday_afternoon_desc',
                midweek_time = '$midweek_time',
                midweek_desc = '$midweek_desc',
                ministries_title = '$ministries_title',
                ministries_subtitle = '$ministries_subtitle',
                meta_title = '$meta_title',
                meta_description = '$meta_description',
                meta_keywords = '$meta_keywords',
                facebook_url = '$facebook_url',
                twitter_url = '$twitter_url',
                instagram_url = '$instagram_url',
                youtube_url = '$youtube_url'
              WHERE id = 1";
    
    if(mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Website settings updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating website settings: " . mysqli_error($conn);
    }
    
    header("Location: settings.php?tab=website");
    exit();
}

// Invalid action
else {
    $_SESSION['error_message'] = "Invalid action";
    header("Location: settings.php");
    exit();
}
?>

