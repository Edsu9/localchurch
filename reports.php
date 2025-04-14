<?php
session_start();
require_once 'config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get success or error messages from session
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Clear messages after displaying them
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Get church settings
$query = "SELECT * FROM settings WHERE id = 1";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);
$church_name = isset($settings['church_name']) ? $settings['church_name'] : 'Church Management System';

// Default report type and date range
$report_type = isset($_GET['type']) ? $_GET['type'] : 'donations';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get donation statistics
$query = "SELECT 
            COUNT(*) as total_donations,
            SUM(amount) as total_amount,
            AVG(amount) as average_amount,
            MAX(amount) as highest_donation,
            MIN(amount) as lowest_donation
          FROM donations
          WHERE donation_date BETWEEN '$start_date' AND '$end_date'";
$donation_stats = mysqli_fetch_assoc(mysqli_query($conn, $query));

// Get member statistics
$query = "SELECT 
            COUNT(*) as total_members,
            SUM(CASE WHEN membership_status = 'Active' THEN 1 ELSE 0 END) as active_members,
            SUM(CASE WHEN membership_status = 'Inactive' THEN 1 ELSE 0 END) as inactive_members,
            SUM(CASE WHEN membership_status = 'Visitor' THEN 1 ELSE 0 END) as visitors,
            SUM(CASE WHEN membership_status = 'New Member' THEN 1 ELSE 0 END) as new_members
          FROM members";
$member_stats = mysqli_fetch_assoc(mysqli_query($conn, $query));

// Get total member count
$query = "SELECT COUNT(*) as total FROM members";
$result = mysqli_query($conn, $query);
$member_count = mysqli_fetch_assoc($result)['total'];

// Get event statistics
$query = "SELECT 
            COUNT(*) as total_events,
            SUM(CASE WHEN status = 'Scheduled' THEN 1 ELSE 0 END) as scheduled_events,
            SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_events,
            SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_events,
            SUM(CASE WHEN status = 'Postponed' THEN 1 ELSE 0 END) as postponed_events
          FROM events
          WHERE event_date BETWEEN '$start_date' AND '$end_date'";
$event_stats = mysqli_fetch_assoc(mysqli_query($conn, $query));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - <?php echo $church_name; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/enhanced-style.css">
    <link rel="stylesheet" href="css/dashboard-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <h1><i class="fas fa-chart-bar"></i> Reports</h1>
                </div>

                <?php if($success_message): ?>
                    <div class="success-message"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <?php if($error_message): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <div class="content-container">
                    <!-- Report Type Selection -->
                    <div class="report-table-container">
                        <div class="table-header">
                            <h3><i class="fas fa-file-alt"></i> Generate Reports</h3>
                        </div>
                        
                        <div class="report-table-content">
                            <div class="date-range">
                                <h4><i class="fas fa-calendar-alt"></i> Select Date Range</h4>
                                <form action="reports.php" method="get">
                                    <input type="hidden" name="type" value="<?php echo $report_type; ?>">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="start_date">Start Date:</label>
                                            <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="end_date">End Date:</label>
                                            <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistics Overview -->
                    <div class="report-table-container">
                        <div class="table-header">
                            <h3><i class="fas fa-chart-line"></i> Statistics Overview</h3>
                        </div>

                        <div class="report-table-content">
                            <div class="stats-container">
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="stat-info">
                                        <h4>Total Members</h4>
                                        <p class="stat-value"><?php echo $member_stats['total_members']; ?></p>
                                    </div>
                                </div>
                                <!-- Removed Active Members Stat Card -->
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="stat-info">
                                        <h4>Total Donations</h4>
                                        <p class="stat-value">$<?php echo number_format($donation_stats['total_amount'], 2); ?></p>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="stat-info">
                                        <h4>Total Events</h4>
                                        <p class="stat-value"><?php echo $event_stats['total_events']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Report Cards Section -->
                    <div class="report-section">
                        <div class="report-cards">
                            <div class="report-card">   
                                <div class="report-info">
                                    <a href="member_list_report.php" class="btn btn-primary">
                                        <i class="fas fa-eye"></i> View Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>

