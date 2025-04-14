<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'church_management';

// Create database connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    // If connection fails, log the error but don't expose details
    error_log('Database connection failed: ' . mysqli_connect_error());
    
    // Display a user-friendly message
    echo '<div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">';
    echo '<h1>Website Temporarily Unavailable</h1>';
    echo '<p>We\'re sorry, but the website is currently undergoing maintenance.</p>';
    echo '<p>Please try again later.</p>';
    echo '</div>';
    
    // Stop script execution
    exit;
}

// Set character set
mysqli_set_charset($conn, "utf8");

// Function to handle database errors safely
function handleDatabaseError($query, $error) {
    // Log the error
    error_log('Database query error: ' . $error . ' in query: ' . $query);
    
    // Return a user-friendly message
    return 'An error occurred while processing your request. Please try again later.';
}

// Function to safely execute queries
function safeQuery($conn, $query) {
    $result = mysqli_query($conn, $query);
    if (!$result) {
        handleDatabaseError($query, mysqli_error($conn));
        return false;
    }
    return $result;
}

// Function to safely escape strings
function safeEscape($conn, $string) {
    return mysqli_real_escape_string($conn, $string);
}
?>

