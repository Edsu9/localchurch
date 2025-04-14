<?php
session_start();
require_once 'config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
   header("Location: login.php");
   exit();
}

// Handle event deletion
if (isset($_GET['delete'])) {
   $id = mysqli_real_escape_string($conn, $_GET['delete']);
   $query = "DELETE FROM events WHERE id = $id";
   
   if (mysqli_query($conn, $query)) {
       $_SESSION['success_message'] = "Event deleted successfully!";
       header("Location: events.php");
       exit();
   } else {
       $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
   }
}

// Handle image deletion
if (isset($_GET['delete_image']) && isset($_GET['id'])) {
    $event_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Get current image path
    $query = "SELECT event_image FROM events WHERE id = $event_id";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $event = mysqli_fetch_assoc($result);
        $image_path = 'uploads/event_images/' . $event['event_image'];
        
        // Delete the image file if it exists and is not the default
        if ($event['event_image'] && $event['event_image'] != 'default-event.jpg' && file_exists($image_path)) {
            unlink($image_path);
        }
        
        // Update the database to remove the image reference
        $update_query = "UPDATE events SET event_image = NULL WHERE id = $event_id";
        if (mysqli_query($conn, $update_query)) {
            $_SESSION['success_message'] = "Event image deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting image: " . mysqli_error($conn);
        }
        
        // Redirect back to the edit page
        header("Location: events.php?view=edit&id=$event_id");
        exit();
    }
}

// Get success or error messages from session
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Clear messages after displaying them
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Fetch all events
$query = "SELECT * FROM events ORDER BY event_date DESC";
$events = mysqli_query($conn, $query);

// Get event types for dropdown
$event_types = [
   "Worship Service",
   "Bible Study",
   "Prayer Meeting",
   "Youth Event",
   "Children's Ministry",
   "Mission Trip",
   "Fellowship",
   "Special Event",
   "Other"
];

// Default view is the list
$current_view = isset($_GET['view']) ? $_GET['view'] : 'list';
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get event data if viewing or editing
$event_data = null;
if (($current_view == 'view' || $current_view == 'edit') && $event_id > 0) {
   $query = "SELECT * FROM events WHERE id = $event_id";
   $result = mysqli_query($conn, $query);
   if ($result && mysqli_num_rows($result) > 0) {
       $event_data = mysqli_fetch_assoc($result);
   } else {
       // Event not found, redirect to list
       $_SESSION['error_message'] = "Event not found";
       header("Location: events.php");
       exit();
   }
}

// Create upload directories if they don't exist
$upload_dir = 'uploads/event_images';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Events - Church Management System</title>
   <link rel="stylesheet" href="css/style.css">
   <link rel="stylesheet" href="css/enhanced-style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   <link rel="stylesheet" href="css/animations.css">
</head>
<body>
   <div class="container">
       <div class="sidebar">
           <div class="logo">
               <img src="img/jia.png" alt="JIA Somal-ot Logo" style="max-width: 50%;">
               <h2>JIA Somal-ot</h2>
           </div>
           <ul class="nav-links">
               <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
               <li><a href="members.php"><i class="fas fa-users"></i> Members</a></li>
               <li><a href="events.php"><i class="fas fa-calendar"></i> Events</a></li>
               <li><a href="donations.php"><i class="fas fa-hand-holding-heart"></i> Donations</a></li>
               <li><a href="services_management.php"><i class="fas fa-church"></i> Services</a></li>
               <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
               <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
               <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
           </ul>
       </div>
       <div class="main-content">
           <div class="content">
               <div class="page-header">
                   <h1><i class="fas fa-calendar"></i> Events Management</h1>
                   <?php if($current_view == 'list'): ?>
                       <div class="header-buttons">
                           <a href="events.php?view=add" class="add-btn"><i class="fas fa-plus"></i> Add Event</a>
                       </div>
                   <?php endif; ?>
               </div>
               
               <?php if($success_message): ?>
                   <div class="success-message"><i class="fas fa-check-circle"></i> <?php echo $success_message; ?></div>
               <?php endif; ?>
               
               <?php if($error_message): ?>
                   <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?></div>
               <?php endif; ?>
               
               <div class="content-container">
                   <?php if($current_view == 'list'): ?>
                       <!-- Events List View -->
                       <div class="report-table-container">
                           <div class="table-header">
                               <h3><i class="fas fa-list"></i> All Events</h3>
                               <div class="search-container">
                                    <input type="text" id="eventSearch" placeholder="Search events...">
                                    <button type="submit"><i class="fas fa-search"></i></button>
                                </div>
                           </div>
                           
                           <div class="report-table-content">
                               <table class="report-table">
                                   <thead>
                                       <tr>
                                           <th>ID</th>
                                           <th>Event Name</th>
                                           <th>Date</th>
                                           <th>Time</th>
                                           <th>Location</th>
                                           <th>Type</th>
                                           <th>Status</th>
                                           <th>Actions</th>
                                       </tr>
                                   </thead>
                                   <tbody>
                                       <?php if(mysqli_num_rows($events) > 0): ?>
                                           <?php while($event = mysqli_fetch_assoc($events)): ?>
                                               <tr>
                                                   <td><?php echo $event['id']; ?></td>
                                                   <td><?php echo isset($event['event_name']) && !empty($event['event_name']) ? $event['event_name'] : $event['title']; ?></td>
                                                   <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                                   <td>
                                                       <?php 
                                                           $start = isset($event['start_time']) && !empty($event['start_time']) ? $event['start_time'] : $event['event_time'];
                                                           echo date('h:i A', strtotime($start));
                                                           if(isset($event['end_time']) && !empty($event['end_time'])) {
                                                               echo ' - ' . date('h:i A', strtotime($event['end_time']));
                                                           }
                                                       ?>
                                                   </td>
                                                   <td><?php echo $event['location']; ?></td>
                                                   <td><?php echo isset($event['event_type']) ? $event['event_type'] : 'General'; ?></td>
                                                   <td><span class="status-badge <?php echo strtolower($event['status']); ?>"><?php echo $event['status']; ?></span></td>
                                                   <td class="actions">
                                                       <a href="events.php?view=view&id=<?php echo $event['id']; ?>" class="view-btn"><i class="fas fa-eye"></i></a>
                                                       <a href="events.php?view=edit&id=<?php echo $event['id']; ?>" class="edit-btn"><i class="fas fa-edit"></i></a>
                                                       <a href="events.php?delete=<?php echo $event['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this event?');"><i class="fas fa-trash"></i></a>
                                                   </td>
                                               </tr>
                                           <?php endwhile; ?>
                                       <?php else: ?>
                                           <tr>
                                               <td colspan="9" class="text-center">No events found. <a href="events.php?view=add">Add your first event</a>.</td>
                                           </tr>
                                       <?php endif; ?>
                                   </tbody>
                               </table>
                           </div>
                       </div>
                   <?php elseif($current_view == 'add'): ?>
                       <!-- Add Event View -->
                       <div class="report-table-container">
                           <div class="table-header">
                               <h3><i class="fas fa-plus-circle"></i> Add New Event</h3>
                               <a href="events.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to List</a>
                           </div>
                           
                           <div class="report-table-content">
                               <form action="event_process.php" method="post" enctype="multipart/form-data" class="plain-form">
                                   <input type="hidden" name="action" value="add">
                                   
                                   <div class="alert alert-info">
                                       <i class="fas fa-info-circle"></i> Events you add here will automatically appear on the church website's upcoming events section if the event date is in the future.
                                   </div>

                                   <table class="report-table">
                                       <tbody>
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-info-circle"></i> Basic Information
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Event Name</td>
                                               <td><input type="text" id="event_name" name="event_name" required></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Event Type</td>
                                               <td>
                                                   <select id="event_type" name="event_type" required>
                                                       <option value="">Select Event Type</option>
                                                       <?php foreach($event_types as $type): ?>
                                                           <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                                                       <?php endforeach; ?>
                                                   </select>
                                               </td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Date</td>
                                               <td><input type="date" id="event_date" name="event_date" required></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Start Time</td>
                                               <td><input type="time" id="start_time" name="start_time" value="00:00"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">End Time</td>
                                               <td><input type="time" id="end_time" name="end_time" value="23:59"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Location</td>
                                               <td><input type="text" id="location" name="location" required></td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-image"></i> Event Image
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Event Image</td>
                                               <td>
                                                   <div class="file-upload-container">
                                                       <input type="file" id="event_image" name="event_image" accept="image/*">
                                                   </div>
                                                   <div class="image-preview-wrapper">
                                                       <div id="event-image-preview" class="image-preview-container">
                                                           <i class="fas fa-calendar-alt"></i>
                                                       </div>
                                                   </div>
                                               </td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-align-left"></i> Event Details
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Description</td>
                                               <td><textarea id="description" name="description" rows="4"></textarea></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Organizer</td>
                                               <td><input type="text" id="organizer" name="organizer"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Status</td>
                                               <td>
                                                   <select id="status" name="status" required>
                                                       <option value="Scheduled">Scheduled</option>
                                                       <option value="Cancelled">Cancelled</option>
                                                       <option value="Postponed">Postponed</option>
                                                       <option value="Completed">Completed</option>
                                                   </select>
                                               </td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-address-card"></i> Contact Information
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Contact Person</td>
                                               <td><input type="text" id="contact_person" name="contact_person"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Contact Email</td>
                                               <td><input type="email" id="contact_email" name="contact_email"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Contact Phone</td>
                                               <td><input type="text" id="contact_phone" name="contact_phone"></td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-users"></i> Registration
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Registration Required</td>
                                               <td>
                                                   <label class="checkbox-container">
                                                       <input type="checkbox" id="registration_required" name="registration_required" value="1">
                                                       <span class="checkmark"></span>
                                                       Registration Required
                                                   </label>
                                               </td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Maximum Attendees</td>
                                               <td><input type="number" id="max_attendees" name="max_attendees" min="0"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Show on Public Page</td>
                                               <td>
                                                   <label class="checkbox-container">
                                                       <input type="checkbox" id="public_display" name="public_display" value="1" checked>
                                                       <span class="checkmark"></span>
                                                       Display this event on the public events page
                                                   </label>
                                               </td>
                                           </tr>
                                       </tbody>
                                   </table>
                                   
                                   <div class="form-actions">
                                       <a href="events.php" class="btn btn-secondary">Cancel</a>
                                       <button type="submit" class="btn btn-primary">Add Event</button>
                                   </div>
                               </form>
                           </div>
                       </div>
                   <?php elseif($current_view == 'view' && $event_data): ?>
                       <!-- View Event Details -->
                       <div class="report-table-container">
                           <div class="table-header">
                               <h3><i class="fas fa-info-circle"></i> Event Details</h3>
                               <div class="actions">
                                   <a href="events.php?view=edit&id=<?php echo $event_data['id']; ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                   <a href="events.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to List</a>
                               </div>
                           </div>
                           
                           <div class="report-table-content">
                               <?php if(isset($event_data['event_image']) && $event_data['event_image'] && $event_data['event_image'] != 'default-event.jpg'): ?>
                                   <div class="event-image">
                                       <img src="uploads/event_images/<?php echo $event_data['event_image']; ?>" alt="Event Image">
                                   </div>
                               <?php endif; ?>
                               
                               <table class="report-table">
                                   <tbody>
                                       <tr>
                                           <th colspan="2" class="section-header">
                                               <i class="fas fa-info-circle"></i> Basic Information
                                           </th>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Event Name</td>
                                           <td><?php echo isset($event_data['event_name']) && !empty($event_data['event_name']) ? $event_data['event_name'] : $event_data['title']; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Event Type</td>
                                           <td><?php echo isset($event_data['event_type']) ? $event_data['event_type'] : 'General'; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Date</td>
                                           <td><?php echo date('F d, Y', strtotime($event_data['event_date'])); ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Time</td>
                                           <td>
                                               <?php 
                                                   $start = isset($event_data['start_time']) && !empty($event_data['start_time']) ? $event_data['start_time'] : $event_data['event_time'];
                                                   echo date('h:i A', strtotime($start));
                                                   if(isset($event_data['end_time']) && !empty($event_data['end_time'])) {
                                                       echo ' - ' . date('h:i A', strtotime($event_data['end_time']));
                                                   }
                                               ?>
                                           </td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Location</td>
                                           <td><?php echo $event_data['location']; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Status</td>
                                           <td><span class="status-badge <?php echo strtolower($event_data['status']); ?>"><?php echo $event_data['status']; ?></span></td>
                                       </tr>
                                       
                                       <tr>
                                           <th colspan="2" class="section-header">
                                               <i class="fas fa-align-left"></i> Event Details
                                           </th>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Description</td>
                                           <td><?php echo $event_data['description'] ? nl2br($event_data['description']) : 'N/A'; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Organizer</td>
                                           <td><?php echo $event_data['organizer'] ? $event_data['organizer'] : 'N/A'; ?></td>
                                       </tr>
                                       
                                       <tr>
                                           <th colspan="2" class="section-header">
                                               <i class="fas fa-address-card"></i> Contact Information
                                           </th>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Contact Person</td>
                                           <td><?php echo isset($event_data['contact_person']) && $event_data['contact_person'] ? $event_data['contact_person'] : 'N/A'; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Contact Email</td>
                                           <td><?php echo isset($event_data['contact_email']) && $event_data['contact_email'] ? $event_data['contact_email'] : 'N/A'; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Contact Phone</td>
                                           <td><?php echo isset($event_data['contact_phone']) && $event_data['contact_phone'] ? $event_data['contact_phone'] : 'N/A'; ?></td>
                                       </tr>
                                       
                                       <tr>
                                           <th colspan="2" class="section-header">
                                               <i class="fas fa-users"></i> Registration
                                           </th>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Registration Required</td>
                                           <td><?php echo isset($event_data['registration_required']) && $event_data['registration_required'] ? 'Yes' : 'No'; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Maximum Attendees</td>
                                           <td><?php echo isset($event_data['max_attendees']) && $event_data['max_attendees'] ? $event_data['max_attendees'] : 'No limit'; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Public Display</td>
                                           <td><?php echo isset($event_data['public_display']) && $event_data['public_display'] ? 'Yes' : 'No'; ?></td>
                                       </tr>
                                       
                                       <tr>
                                           <th colspan="2" class="section-header">
                                               <i class="fas fa-clock"></i> System Information
                                           </th>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Created At</td>
                                           <td><?php echo date('F d, Y H:i:s', strtotime($event_data['created_at'])); ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Last Updated</td>
                                           <td><?php echo date('F d, Y H:i:s', strtotime($event_data['updated_at'])); ?></td>
                                       </tr>
                                   </tbody>
                               </table>
                           </div>
                       </div>
                   <?php elseif($current_view == 'edit' && $event_data): ?>
                       <!-- Edit Event View -->
                       <div class="report-table-container">
                           <div class="table-header">
                               <h3><i class="fas fa-edit"></i> Edit Event #<?php echo $event_data['id']; ?></h3>
                               <a href="events.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to List</a>
                           </div>
                           
                           <div class="report-table-content">
                               <form action="event_process.php" method="post" enctype="multipart/form-data" class="plain-form">
                                   <input type="hidden" name="action" value="edit">
                                   <input type="hidden" name="event_id" value="<?php echo $event_data['id']; ?>">
                                   
                                   <table class="report-table">
                                       <tbody>
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-info-circle"></i> Basic Information
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Event Name</td>
                                               <td><input type="text" id="event_name" name="event_name" value="<?php echo isset($event_data['event_name']) && !empty($event_data['event_name']) ? $event_data['event_name'] : $event_data['title']; ?>" required></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Event Type</td>
                                               <td>
                                                   <select id="event_type" name="event_type" required>
                                                       <option value="">Select Event Type</option>
                                                       <?php foreach($event_types as $type): ?>
                                                           <option value="<?php echo $type; ?>" <?php echo (isset($event_data['event_type']) && $type == $event_data['event_type']) ? 'selected' : ''; ?>><?php echo $type; ?></option>
                                                       <?php endforeach; ?>
                                                   </select>
                                               </td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Date</td>
                                               <td><input type="date" id="event_date" name="event_date" value="<?php echo $event_data['event_date']; ?>" required></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Start Time</td>
                                               <td><input type="time" id="start_time" name="start_time" value="<?php echo isset($event_data['start_time']) && !empty($event_data['start_time']) ? $event_data['start_time'] : $event_data['event_time']; ?>" required></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">End Time</td>
                                               <td><input type="time" id="end_time" name="end_time" value="<?php echo isset($event_data['end_time']) ? $event_data['end_time'] : ''; ?>"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Location</td>
                                               <td><input type="text" id="location" name="location" value="<?php echo $event_data['location']; ?>" required></td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-image"></i> Event Image
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Event Image</td>
                                               <td>
                                                   <div class="file-upload-container">
                                                       <input type="file" id="event_image" name="event_image" accept="image/*">
                                                   </div>
                                                   <div class="image-preview-wrapper">
                                                       <div id="event-image-preview" class="image-preview-container">
                                                           <?php if(isset($event_data['event_image']) && $event_data['event_image'] && $event_data['event_image'] != 'default-event.jpg'): ?>
                                                               <img src="uploads/event_images/<?php echo $event_data['event_image']; ?>" alt="Event">
                                                               <button type="button" class="delete-image-btn" onclick="if(confirm('Are you sure you want to delete this image?')) window.location.href='events.php?view=edit&id=<?php echo $event_data['id']; ?>&delete_image=1'">
                                                                   <i class="fas fa-trash"></i>
                                                               </button>
                                                           <?php else: ?>
                                                               <i class="fas fa-calendar-alt"></i>
                                                           <?php endif; ?>
                                                       </div>
                                                   </div>
                                               </td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-align-left"></i> Event Details
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Description</td>
                                               <td><textarea id="description" name="description" rows="4"><?php echo $event_data['description']; ?></textarea></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Organizer</td>
                                               <td><input type="text" id="organizer" name="organizer" value="<?php echo $event_data['organizer']; ?>"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Status</td>
                                               <td>
                                                   <select id="status" name="status" required>
                                                       <option value="Scheduled" <?php echo ($event_data['status'] == 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                                       <option value="Cancelled" <?php echo ($event_data['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                                       <option value="Postponed" <?php echo ($event_data['status'] == 'Postponed') ? 'selected' : ''; ?>>Postponed</option>
                                                       <option value="Completed" <?php echo ($event_data['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                   </select>
                                               </td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-address-card"></i> Contact Information
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Contact Person</td>
                                               <td><input type="text" id="contact_person" name="contact_person" value="<?php echo isset($event_data['contact_person']) ? $event_data['contact_person'] : ''; ?>"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Contact Email</td>
                                               <td><input type="email" id="contact_email" name="contact_email" value="<?php echo isset($event_data['contact_email']) ? $event_data['contact_email'] : ''; ?>"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Contact Phone</td>
                                               <td><input type="text" id="contact_phone" name="contact_phone" value="<?php echo isset($event_data['contact_phone']) ? $event_data['contact_phone'] : ''; ?>"></td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-users"></i> Registration
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Registration Required</td>
                                               <td>
                                                   <label class="checkbox-container">
                                                       <input type="checkbox" id="registration_required" name="registration_required" value="1" <?php echo isset($event_data['registration_required']) && $event_data['registration_required'] ? 'checked' : ''; ?>>
                                                       <span class="checkmark"></span>
                                                       Registration Required
                                                   </label>
                                               </td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Maximum Attendees</td>
                                               <td><input type="number" id="max_attendees" name="max_attendees" min="0" value="<?php echo isset($event_data['max_attendees']) ? $event_data['max_attendees'] : '0'; ?>"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Show on Public Page</td>
                                               <td>
                                                   <label class="checkbox-container">
                                                       <input type="checkbox" id="public_display" name="public_display" value="1" <?php echo (isset($event_data['public_display']) && $event_data['public_display'] == 1) ? 'checked' : ''; ?>>
                                                       <span class="checkmark"></span>
                                                       Display this event on the public events page
                                                   </label>
                                               </td>
                                           </tr>
                                       </tbody>
                                   </table>
                                   
                                   <div class="form-actions">
                                       <a href="events.php" class="btn btn-secondary">Cancel</a>
                                       <button type="submit" class="btn btn-primary">Update Event</button>
                                   </div>
                               </form>
                           </div>
                       </div>
                   <?php endif; ?>
               </div>
           </div>
       </div>
   </div>
   <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const eventSearch = document.getElementById("eventSearch");
        if (eventSearch) {
            eventSearch.addEventListener("keyup", function() {
                const searchTerm = this.value.toLowerCase();
                const table = document.querySelector(".report-table");
                
                if (table) {
                    const rows = table.querySelectorAll("tbody tr");
                    
                    rows.forEach((row) => {
                        let found = false;
                        const cells = row.querySelectorAll("td");
                        
                        cells.forEach((cell) => {
                            if (cell.textContent.toLowerCase().includes(searchTerm)) {
                                found = true;
                            }
                        });
                        
                        row.style.display = found ? "" : "none";
                    });
                }
            });
        }
        
        // Image preview functionality
        const eventImageInput = document.getElementById("event_image");
        if (eventImageInput) {
            eventImageInput.addEventListener("change", function() {
                const file = this.files[0];
                const preview = document.getElementById("event-image-preview");
                
                if (file && preview) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `<img src="${e.target.result}" alt="Event Preview" style="width: 100%; height: 100%; object-fit: cover;">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
        
        // Toggle switch functionality
        const publicDisplayToggles = document.querySelectorAll('.public-display-toggle');
        publicDisplayToggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
                const eventId = this.getAttribute('data-id');
                const isPublic = this.checked ? 1 : 0;
                
                // Send AJAX request to update public display status
                fetch('update_event_public.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `event_id=${eventId}&public_display=${isPublic}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        const message = document.createElement('div');
                        message.className = 'success-message';
                        message.innerHTML = `<i class="fas fa-check-circle"></i> ${data.message}`;
                        message.style.position = 'fixed';
                        message.style.top = '20px';
                        message.style.right = '20px';
                        message.style.zIndex = '9999';
                        document.body.appendChild(message);
                        
                        // Remove message after 3 seconds
                        setTimeout(() => {
                            message.style.opacity = '0';
                            setTimeout(() => {
                                document.body.removeChild(message);
                            }, 300);
                        }, 3000);
                    } else {
                        alert('Error: ' + data.message);
                        // Revert toggle if there was an error
                        this.checked = !this.checked;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                    // Revert toggle if there was an error
                    this.checked = !this.checked;
                });
            });
        });
    });
    </script>
</body>
</html>

