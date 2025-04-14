<?php
session_start();
require_once 'config/config.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if action is set
if (!isset($_POST['action']) && !isset($_GET['action'])) {
    $_SESSION['error_message'] = "Invalid request";
    header("Location: settings.php");
    exit();
}

$action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];

// Handle create backup
if ($action == 'create_backup') {
    // Create backup directory if it doesn't exist
    $backup_dir = 'backups/';
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }
    
    // Generate backup filename
    $backup_file = $backup_dir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    
    // Get database credentials from config
    $db_host = DB_HOST;
    $db_user = DB_USER;
    $db_pass = DB_PASS;
    $db_name = DB_NAME;

    // Define the mysqldump command
    $command = "C:/xampp/mysql/bin/mysqldump --host=$db_host --user=$db_user --password=$db_pass $db_name > $backup_file";

    // Execute backup command using shell_exec to capture more output
    $output = shell_exec($command . " 2>&1"); // Capture both stdout and stderr

    // Log the output for debugging
    error_log("Backup command output: " . $output);
    
    // Check if the output contains any error
    if (strpos($output, 'ERROR') !== false) {
        $_SESSION['error_message'] = "Error creating backup: " . $output;
        header("Location: settings.php");
        exit();
    }

    // If the file was created, proceed with the download
    if (file_exists($backup_file)) {
        // Backup successful
        // Force download the backup file
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($backup_file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($backup_file));
        readfile($backup_file);
        exit;
    } else {
        // Backup failed
        $_SESSION['error_message'] = "Error creating backup: The backup file was not created.";
        header("Location: settings.php");
        exit();
    }
}

// Handle restore backup
elseif ($action == 'restore_backup') {
    // Check if file was uploaded
    if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] != 0) {
        $_SESSION['error_message'] = "Error uploading backup file";
        header("Location: settings.php");
        exit();
    }
    
    // Check file type
    $file_ext = strtolower(pathinfo($_FILES['backup_file']['name'], PATHINFO_EXTENSION));
    if ($file_ext != 'sql') {
        $_SESSION['error_message'] = "Invalid backup file format. Only SQL files are allowed.";
        header("Location: settings.php");
        exit();
    }
    
    // Create temp directory if it doesn't exist
    $temp_dir = 'temp/';
    if (!is_dir($temp_dir)) {
        mkdir($temp_dir, 0755, true);
    }
    
    // Move uploaded file to temp directory
    $temp_file = $temp_dir . 'restore_' . time() . '.sql';
    if (!move_uploaded_file($_FILES['backup_file']['tmp_name'], $temp_file)) {
        $_SESSION['error_message'] = "Error moving uploaded file";
        header("Location: settings.php");
        exit();
    }
    
    // Get database credentials from config
    $db_host = DB_HOST;
    $db_user = DB_USER;
    $db_pass = DB_PASS;
    $db_name = DB_NAME;
    
    // Create restore command
    $command = "mysql --host=$db_host --user=$db_user --password=$db_pass $db_name < $temp_file";
    
    // Execute restore command
    exec($command, $output, $return_var);
    
    // Delete temp file
    unlink($temp_file);
    
    if ($return_var === 0) {
        // Restore successful
        $_SESSION['success_message'] = "Database restored successfully!";
    } else {
        // Restore failed
        $_SESSION['error_message'] = "Error restoring database";
    }
    
    header("Location: settings.php");
    exit();
}

// Handle delete backup
elseif ($action == 'delete_backup') {
    // Check if file parameter is set
    if (!isset($_GET['file'])) {
        $_SESSION['error_message'] = "Invalid request";
        header("Location: settings.php");
        exit();
    }
    
    $file = $_GET['file'];
    
    // Validate filename (only allow alphanumeric, underscore, hyphen, and period)
    if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $file)) {
        $_SESSION['error_message'] = "Invalid filename";
        header("Location: settings.php");
        exit();
    }
    
    $backup_dir = 'backups/';
    $file_path = $backup_dir . $file;
    
    // Check if file exists and is a regular file
    if (file_exists($file_path) && is_file($file_path)) {
        if (unlink($file_path)) {
            $_SESSION['success_message'] = "Backup file deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting backup file";
        }
    } else {
        $_SESSION['error_message'] = "Backup file not found";
    }
    
    header("Location: settings.php");
    exit();
}

// Invalid action
else {
    $_SESSION['error_message'] = "Invalid action";
    header("Location: settings.php");
    exit();
}
?>
