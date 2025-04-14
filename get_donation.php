<?php
session_start();
require_once 'config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Donation ID is required']);
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch donation data with member name
$query = "SELECT d.*, CONCAT(m.first_name, ' ', m.last_name) as member_name 
          FROM donations d 
          LEFT JOIN members m ON d.member_id = m.id 
          WHERE d.id = $id";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $donation = mysqli_fetch_assoc($result);
    
    // Return donation data as JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'donation' => $donation]);
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Donation not found']);
}
?>

