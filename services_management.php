<?php
session_start();
require_once 'config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle service deletion
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $query = "DELETE FROM services WHERE service_id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Service deleted successfully!";
        header("Location: services_management.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }
}

// Handle service activation/deactivation
if (isset($_GET['toggle'])) {
    $id = mysqli_real_escape_string($conn, $_GET['toggle']);
    
    // Get current status
    $query = "SELECT is_active FROM services WHERE service_id = $id";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $new_status = $row['is_active'] ? 0 : 1;
        
        // Update status
        $update_query = "UPDATE services SET is_active = $new_status WHERE service_id = $id";
        
        if (mysqli_query($conn, $update_query)) {
            $status_text = $new_status ? "activated" : "deactivated";
            $_SESSION['success_message'] = "Service $status_text successfully!";
            header("Location: services_management.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error updating service status: " . mysqli_error($conn);
        }
    }
}

// Get success or error messages from session
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Clear messages after displaying them
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Fetch all services
$query = "SELECT * FROM services ORDER BY FIELD(service_day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), service_time";
$services = mysqli_query($conn, $query);

// Days of the week for dropdown
$days_of_week = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday"
];

// Default view is the list
$current_view = isset($_GET['view']) ? $_GET['view'] : 'list';
$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get service data if viewing or editing
$service_data = null;
if (($current_view == 'view' || $current_view == 'edit') && $service_id > 0) {
    $query = "SELECT * FROM services WHERE service_id = $service_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $service_data = mysqli_fetch_assoc($result);
    } else {
        // Service not found, redirect to list
        $_SESSION['error_message'] = "Service not found";
        header("Location: services_management.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services Management - Church Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/enhanced-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    <h1><i class="fas fa-church"></i> Weekly Services Management</h1>
                    <?php if($current_view == 'list'): ?>
                        <a href="services_management.php?view=add" class="add-btn"><i class="fas fa-plus"></i> Add Service</a>
                    <?php endif; ?>
                </div>
                
                <?php if($success_message): ?>
                    <div class="success-message"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if($error_message): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="content-container">
                    <?php if($current_view == 'list'): ?>
                        <!-- Services List View -->
                        <div class="report-table-container">
                            <div class="table-header">
                                <h3><i class="fas fa-list"></i> All Weekly Services</h3>
                                <div class="search-container">
                                    <input type="text" id="serviceSearch" placeholder="Search services...">
                                    <button type="submit"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                            
                            <div class="report-table-content">
                                <table class="report-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Day</th>
                                            <th>Time</th>
                                            <th>Location</th>
                                            <th>Leader</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($service = mysqli_fetch_assoc($services)): ?>
                                            <tr>
                                                <td><?php echo $service['service_id']; ?></td>
                                                <td><?php echo htmlspecialchars($service['service_title']); ?></td>
                                                <td><?php echo htmlspecialchars($service['service_day']); ?></td>
                                                <td><?php echo htmlspecialchars($service['service_time']); ?></td>
                                                <td><?php echo htmlspecialchars($service['service_location'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($service['service_leader'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <?php if($service['is_active']): ?>
                                                        <span class="status-active">Active</span>
                                                    <?php else: ?>
                                                        <span class="status-inactive">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="actions">
                                                    <a href="services_management.php?view=view&id=<?php echo $service['service_id']; ?>" class="view-btn"><i class="fas fa-eye"></i></a>
                                                    <a href="services_management.php?view=edit&id=<?php echo $service['service_id']; ?>" class="edit-btn"><i class="fas fa-edit"></i></a>
                                                    <a href="services_management.php?toggle=<?php echo $service['service_id']; ?>" class="toggle-btn">
                                                        <i class="fas <?php echo $service['is_active'] ? 'fa-toggle-on' : 'fa-toggle-off'; ?>"></i>
                                                    </a>
                                                    <a href="services_management.php?delete=<?php echo $service['service_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this service?');"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php elseif($current_view == 'add'): ?>
                        <!-- Add Service View -->
                        <div class="report-table-container">
                            <div class="table-header">
                                <h3><i class="fas fa-plus-circle"></i> Add New Service</h3>
                                <a href="services_management.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to List</a>
                            </div>
                            
                            <div class="report-table-content">
                                <form action="services_add.php" method="post" class="plain-form">
                                    <table class="report-table">
                                        <tbody>
                                            <tr>
                                                <th colspan="2" class="section-header">
                                                    <i class="fas fa-info-circle"></i> Basic Information
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Service Title</td>
                                                <td><input type="text" id="service_title" name="service_title" required></td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Day of Week</td>
                                                <td>
                                                    <select id="service_day" name="service_day" required>
                                                        <option value="">Select Day</option>
                                                        <?php foreach($days_of_week as $day): ?>
                                                            <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Service Time</td>
                                                <td><input type="text" id="service_time" name="service_time" placeholder="e.g. 10:00 AM - 12:00 PM" required></td>
                                            </tr>
                                            
                                            <tr>
                                                <th colspan="2" class="section-header">
                                                    <i class="fas fa-map-marker-alt"></i> Location & Leadership
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Location</td>
                                                <td><input type="text" id="service_location" name="service_location" placeholder="e.g. Main Sanctuary"></td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Service Leader</td>
                                                <td><input type="text" id="service_leader" name="service_leader" placeholder="e.g. Pastor John Doe"></td>
                                            </tr>
                                            
                                            <tr>
                                                <th colspan="2" class="section-header">
                                                    <i class="fas fa-file-alt"></i> Additional Information
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Description</td>
                                                <td><textarea id="service_description" name="service_description" rows="3" placeholder="Add a description of this service"></textarea></td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Status</td>
                                                <td>
                                                    <div class="checkbox-container">
                                                        <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                                                        <label for="is_active">Active (display on website)</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <div class="form-actions">
                                        <a href="services_management.php" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Add Service</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php elseif($current_view == 'view' && $service_data): ?>
                        <!-- View Service Details -->
                        <div class="report-table-container">
                            <div class="table-header">
                                <h3><i class="fas fa-info-circle"></i> Service Details</h3>
                                <div class="actions">
                                    <a href="services_management.php?view=edit&id=<?php echo $service_data['service_id']; ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="services_management.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to List</a>
                                </div>
                            </div>
                            
                            <div class="report-table-content">
                                <table class="report-table">
                                    <tbody>
                                        <tr>
                                            <th colspan="2" class="section-header">
                                                <i class="fas fa-info-circle"></i> Basic Information
                                            </th>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Service ID</td>
                                            <td><?php echo $service_data['service_id']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Title</td>
                                            <td><?php echo htmlspecialchars($service_data['service_title']); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Day of Week</td>
                                            <td><?php echo htmlspecialchars($service_data['service_day']); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Time</td>
                                            <td><?php echo htmlspecialchars($service_data['service_time']); ?></td>
                                        </tr>
                                        
                                        <tr>
                                            <th colspan="2" class="section-header">
                                                <i class="fas fa-map-marker-alt"></i> Location & Leadership
                                            </th>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Location</td>
                                            <td><?php echo htmlspecialchars($service_data['service_location'] ?? 'N/A'); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Service Leader</td>
                                            <td><?php echo htmlspecialchars($service_data['service_leader'] ?? 'N/A'); ?></td>
                                        </tr>
                                        
                                        <tr>
                                            <th colspan="2" class="section-header">
                                                <i class="fas fa-file-alt"></i> Additional Information
                                            </th>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Description</td>
                                            <td><?php echo $service_data['service_description'] ? nl2br(htmlspecialchars($service_data['service_description'])) : 'N/A'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Status</td>
                                            <td>
                                                <?php if($service_data['is_active']): ?>
                                                    <span class="status-active">Active</span>
                                                <?php else: ?>
                                                    <span class="status-inactive">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <th colspan="2" class="section-header">
                                                <i class="fas fa-clock"></i> System Information
                                            </th>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Created At</td>
                                            <td><?php echo date('F d, Y H:i:s', strtotime($service_data['created_at'])); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Last Updated</td>
                                            <td><?php echo date('F d, Y H:i:s', strtotime($service_data['updated_at'])); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php elseif($current_view == 'edit' && $service_data): ?>
                        <!-- Edit Service View -->
                        <div class="report-table-container">
                            <div class="table-header">
                                <h3><i class="fas fa-edit"></i> Edit Service #<?php echo $service_data['service_id']; ?></h3>
                                <a href="services_management.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to List</a>
                            </div>
                            
                            <div class="report-table-content">
                                <form action="services_edit.php" method="post" class="plain-form">
                                    <input type="hidden" name="service_id" value="<?php echo $service_data['service_id']; ?>">
                                    
                                    <table class="report-table">
                                        <tbody>
                                            <tr>
                                                <th colspan="2" class="section-header">
                                                    <i class="fas fa-info-circle"></i> Basic Information
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Service Title</td>
                                                <td><input type="text" id="service_title" name="service_title" value="<?php echo htmlspecialchars($service_data['service_title']); ?>" required></td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Day of Week</td>
                                                <td>
                                                    <select id="service_day" name="service_day" required>
                                                        <option value="">Select Day</option>
                                                        <?php foreach($days_of_week as $day): ?>
                                                            <option value="<?php echo $day; ?>" <?php echo ($day == $service_data['service_day']) ? 'selected' : ''; ?>><?php echo $day; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Service Time</td>
                                                <td><input type="text" id="service_time" name="service_time" value="<?php echo htmlspecialchars($service_data['service_time']); ?>" placeholder="e.g. 10:00 AM - 12:00 PM" required></td>
                                            </tr>
                                            
                                            <tr>
                                                <th colspan="2" class="section-header">
                                                    <i class="fas fa-map-marker-alt"></i> Location & Leadership
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Location</td>
                                                <td><input type="text" id="service_location" name="service_location" value="<?php echo htmlspecialchars($service_data['service_location'] ?? ''); ?>" placeholder="e.g. Main Sanctuary"></td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Service Leader</td>
                                                <td><input type="text" id="service_leader" name="service_leader" value="<?php echo htmlspecialchars($service_data['service_leader'] ?? ''); ?>" placeholder="e.g. Pastor John Doe"></td>
                                            </tr>
                                            
                                            <tr>
                                                <th colspan="2" class="section-header">
                                                    <i class="fas fa-file-alt"></i> Additional Information
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Description</td>
                                                <td><textarea id="service_description" name="service_description" rows="3" placeholder="Add a description of this service"><?php echo htmlspecialchars($service_data['service_description'] ?? ''); ?></textarea></td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Status</td>
                                                <td>
                                                    <div class="checkbox-container">
                                                        <input type="checkbox" id="is_active" name="is_active" value="1" <?php echo $service_data['is_active'] ? 'checked' : ''; ?>>
                                                        <label for="is_active">Active (display on website)</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <div class="form-actions">
                                        <a href="services_management.php" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Update Service</button>
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
        const serviceSearch = document.getElementById("serviceSearch");
        if (serviceSearch) {
            serviceSearch.addEventListener("keyup", function() {
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
    });
    </script>
    <script src="script.js"></script>
</body>
</html>

