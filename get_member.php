<?php
// This file handles AJAX requests to get member details
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Member ID is required']);
    exit();
}

$member_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch member details
$query = "SELECT * FROM members WHERE id = '$member_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Member not found']);
    exit();
}

$member = mysqli_fetch_assoc($result);

// Return member data as JSON
header('Content-Type: application/json');
echo json_encode(['success' => true, 'member' => $member]);
?>

