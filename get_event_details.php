<?php
// Include database connection
require_once 'config/db.php';

// Check if event_id is provided
if (!isset($_POST['event_id']) || empty($_POST['event_id'])) {
    echo '<div class="error-message">Error: No event ID provided.</div>';
    exit;
}

$event_id = intval($_POST['event_id']);

// Fetch event details
$query = "SELECT * FROM events WHERE id = ? AND (public_display = 1 OR public_display IS NULL) AND status != 'Cancelled'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo '<div class="error-message">Error: Event not found.</div>';
    exit;
}

$event = $result->fetch_assoc();

// Format date and time
$event_date = date('F d, Y', strtotime($event['event_date']));
$start_time = isset($event['start_time']) && !empty($event['start_time']) ? 
    date('h:i A', strtotime($event['start_time'])) : '';
$end_time = isset($event['end_time']) && !empty($event['end_time']) ? 
    date('h:i A', strtotime($event['end_time'])) : '';

// Time display
$time_display = $start_time;
if (!empty($end_time)) {
    $time_display .= ' - ' . $end_time;
}

// Image path
$image_path = 'img/default-event.jpg';
if (isset($event['event_image']) && !empty($event['event_image']) && file_exists('uploads/event_images/' . $event['event_image'])) {
    $image_path = 'uploads/event_images/' . $event['event_image'];
}

// Event description
$description = isset($event['description']) && !empty($event['description']) ? 
    nl2br(htmlspecialchars($event['description'])) : 'No description available.';

// Output HTML
?>
<img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($event['event_name']); ?>" class="event-modal-image">

<h2><?php echo htmlspecialchars($event['event_name']); ?></h2>

<div class="event-modal-details">
    <p><i class="far fa-calendar-alt"></i> <strong>Date:</strong> <?php echo $event_date; ?></p>
    <p><i class="far fa-clock"></i> <strong>Time:</strong> <?php echo $time_display; ?></p>
    <p><i class="fas fa-map-marker-alt"></i> <strong>Location:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
    
    <?php if (isset($event['event_type']) && !empty($event['event_type'])): ?>
    <p><i class="fas fa-tag"></i> <strong>Type:</strong> <?php echo htmlspecialchars($event['event_type']); ?></p>
    <?php endif; ?>
    
    <?php if (isset($event['organizer']) && !empty($event['organizer'])): ?>
    <p><i class="fas fa-user"></i> <strong>Organizer:</strong> <?php echo htmlspecialchars($event['organizer']); ?></p>
    <?php endif; ?>
    
    <?php if (isset($event['contact_person']) && !empty($event['contact_person'])): ?>
    <p><i class="fas fa-address-card"></i> <strong>Contact Person:</strong> <?php echo htmlspecialchars($event['contact_person']); ?></p>
    <?php endif; ?>
    
    <?php if (isset($event['contact_email']) && !empty($event['contact_email'])): ?>
    <p><i class="fas fa-envelope"></i> <strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($event['contact_email']); ?>"><?php echo htmlspecialchars($event['contact_email']); ?></a></p>
    <?php endif; ?>
    
    <?php if (isset($event['contact_phone']) && !empty($event['contact_phone'])): ?>
        <p><i class="fas fa-phone"></i> <strong>Phone:</strong> <a href="tel:<?php echo htmlspecialchars($event['contact_phone']); ?>"><?php echo htmlspecialchars($event['contact_phone']); ?></a></p>
    <?php endif; ?>
    
    <p><i class="fas fa-info-circle"></i> <strong>Status:</strong> 
        <span class="event-status <?php echo strtolower($event['status']); ?>">
            <?php echo htmlspecialchars($event['status']); ?>
        </span>
    </p>
    
    <?php if (isset($event['registration_required']) && $event['registration_required'] == 1): ?>
    <p><i class="fas fa-clipboard-list"></i> <strong>Registration:</strong> Required</p>
    
    <?php if (isset($event['max_attendees']) && $event['max_attendees'] > 0): ?>
    <p><i class="fas fa-users"></i> <strong>Capacity:</strong> Limited to <?php echo $event['max_attendees']; ?> attendees</p>
    <?php endif; ?>
    <?php endif; ?>
</div>

<div class="event-modal-description">
    <h3>Description</h3>
    <p><?php echo $description; ?></p>
</div>

<div class="event-modal-actions">
    <?php if (isset($event['registration_required']) && $event['registration_required'] == 1): ?>
    <a href="contact.php" class="btn btn-primary hover-lift">Register for Event</a>
    <?php endif; ?>
    <a href="#" class="btn btn-outline hover-lift" onclick="window.print()">Print Details</a>
</div>

<style>
.event-status {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.9rem;
    font-weight: 500;
}

.scheduled {
    background-color: #e3f2fd;
    color: #1976d2;
}

.cancelled {
    background-color: #ffebee;
    color: #d32f2f;
}

.completed {
    background-color: #e8f5e9;
    color: #388e3c;
}

.postponed {
    background-color: #fff8e1;
    color: #f57c00;
}
</style>

