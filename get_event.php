<?php
session_start();
require_once 'config/db.php';

// Always set content type to JSON for consistent response handling
header('Content-Type: application/json');

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
  echo json_encode(['success' => false, 'message' => 'Authentication required']);
  exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
  echo json_encode(['success' => false, 'message' => 'Event ID is required']);
  exit();
}

// Sanitize input
$id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch event data
$query = "SELECT * FROM events WHERE id = $id";
$result = mysqli_query($conn, $query);

if (!$result) {
  echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
  exit();
}

if (mysqli_num_rows($result) == 0) {
  echo json_encode(['success' => false, 'message' => 'Event not found']);
  exit();
}

$event = mysqli_fetch_assoc($result);
echo json_encode(['success' => true, 'event' => $event]);
exit();
?>

