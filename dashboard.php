<?php
session_start();
require_once 'config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
header("Location: login.php");
exit();
}

// Set the default timezone
date_default_timezone_set('Asia/Manila'); // Change to your timezone if needed

// Get user information
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Get dashboard statistics
// Total members
$query = "SELECT COUNT(*) as total FROM members";
$result = mysqli_query($conn, $query);
$total_members = mysqli_fetch_assoc($result)['total'];

// Active members
$query = "SELECT COUNT(*) as total FROM members WHERE membership_status = 'Active'";
$result = mysqli_query($conn, $query);
$active_members = mysqli_fetch_assoc($result)['total'];

// Inactive members
$query = "SELECT COUNT(*) as total FROM members WHERE membership_status = 'Inactive'";
$result = mysqli_query($conn, $query);
$inactive_members = mysqli_fetch_assoc($result)['total'];

// Visitors
$query = "SELECT COUNT(*) as total FROM members WHERE membership_status = 'Visitor'";
$result = mysqli_query($conn, $query);
$visitors = mysqli_fetch_assoc($result)['total'];

// New Member status count
$query = "SELECT COUNT(*) as total FROM members WHERE membership_status = 'New Member'";
$result = mysqli_query($conn, $query);
$new_member_status = mysqli_fetch_assoc($result)['total'];

// Member status breakdown for chart
$query = "SELECT 
          membership_status, 
          COUNT(*) as count 
        FROM members 
        GROUP BY membership_status";
$member_status_result = mysqli_query($conn, $query);
$member_status_labels = [];
$member_status_data = [];
while ($row = mysqli_fetch_assoc($member_status_result)) {
  $member_status_labels[] = $row['membership_status'];
  $member_status_data[] = $row['count'];
}

// Total donations
$query = "SELECT SUM(amount) as total FROM donations";
$result = mysqli_query($conn, $query);
$total_donations = mysqli_fetch_assoc($result)['total'] ?: 0;

// Monthly donations
$query = "SELECT SUM(amount) as total FROM donations WHERE MONTH(donation_date) = MONTH(CURDATE()) AND YEAR(donation_date) = YEAR(CURDATE())";
$result = mysqli_query($conn, $query);
$monthly_donations = mysqli_fetch_assoc($result)['total'] ?: 0;

// Weekly donations
$query = "SELECT SUM(amount) as total FROM donations WHERE donation_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
$result = mysqli_query($conn, $query);
$weekly_donations = mysqli_fetch_assoc($result)['total'] ?: 0;

// Average donation
$query = "SELECT AVG(amount) as avg FROM donations";
$result = mysqli_query($conn, $query);
$avg_donation = mysqli_fetch_assoc($result)['avg'] ?: 0;

// Get monthly donations for chart (last 6 months)
$query = "SELECT 
          DATE_FORMAT(donation_date, '%b %Y') as month,
          SUM(amount) as total 
        FROM donations 
        WHERE donation_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
        GROUP BY MONTH(donation_date), YEAR(donation_date) 
        ORDER BY YEAR(donation_date), MONTH(donation_date)";
$monthly_chart_result = mysqli_query($conn, $query);
$monthly_chart_labels = [];
$monthly_chart_data = [];
while ($row = mysqli_fetch_assoc($monthly_chart_result)) {
  $monthly_chart_labels[] = $row['month'];
  $monthly_chart_data[] = $row['total'];
}

// Get donation categories for chart
$query = "SELECT 
          category, 
          SUM(amount) as total 
        FROM donations 
        GROUP BY category 
        ORDER BY total DESC 
        LIMIT 5";
$categories_result = mysqli_query($conn, $query);
$category_labels = [];
$category_data = [];
while ($row = mysqli_fetch_assoc($categories_result)) {
  $category_labels[] = $row['category'];
  $category_data[] = $row['total'];
}

// Total events
$query = "SELECT COUNT(*) as total FROM events";
$result = mysqli_query($conn, $query);
$total_events = mysqli_fetch_assoc($result)['total'];

// Upcoming events
$query = "SELECT COUNT(*) as total FROM events WHERE event_date >= CURDATE()";
$result = mysqli_query($conn, $query);
$upcoming_events_count = mysqli_fetch_assoc($result)['total'];

// Monthly events
$query = "SELECT COUNT(*) as total FROM events WHERE MONTH(event_date) = MONTH(CURDATE()) AND YEAR(event_date) = YEAR(CURDATE())";
$result = mysqli_query($conn, $query);
$monthly_events = mysqli_fetch_assoc($result)['total'];

// Total services
$query = "SELECT COUNT(*) as total FROM services";
$result = mysqli_query($conn, $query);
$total_services = mysqli_fetch_assoc($result)['total'];

// Active services
$query = "SELECT COUNT(*) as total FROM services WHERE is_active = 1";
$result = mysqli_query($conn, $query);
$active_services = mysqli_fetch_assoc($result)['total'];

// Get recent donations
$query = "SELECT d.id, d.amount, d.donation_date, CONCAT(m.first_name, ' ', m.last_name) as member_name 
      FROM donations d 
      LEFT JOIN members m ON d.member_id = m.id 
      ORDER BY d.donation_date DESC LIMIT 5";
$recent_donations = mysqli_query($conn, $query);

// Get upcoming events
$query = "SELECT id, event_name, event_date, status 
      FROM events 
      WHERE event_date >= CURDATE() 
      ORDER BY event_date ASC LIMIT 5";
$upcoming_events = mysqli_query($conn, $query);

// Get church settings
$query = "SELECT * FROM settings WHERE id = 1";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);
$church_name = isset($settings['church_name']) ? $settings['church_name'] : 'JIA Somal-ot Church';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Church Management <?php echo $church_name; ?></title>
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
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                <div class="server-time">
                    <i class="fas fa-clock"></i> Server Time: <?php echo date('F d, Y h:i:s A'); ?>
                </div>
            </div>
            
            <div class="content-container">
                <!-- Overview Cards Section -->
                <div class="overview-section">
                    <h2 class="section-title"><i class="fas fa-chart-line"></i> Church Overview</h2>
                    <div class="overview-cards">
                        <div class="overview-card members">
                            <div class="overview-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="overview-details">
                                <h3>Total Members</h3>
                                <div class="overview-number"><?php echo $total_members; ?></div>
                            </div>
                        </div>
                        
                        <div class="overview-card donations">
                            <div class="overview-icon">
                                <i class="fas fa-hand-holding-heart"></i>
                            </div>
                            <div class="overview-details">
                                <h3>Total Donations</h3>
                                <div class="overview-number">$<?php echo number_format($total_donations, 2); ?></div>
                            </div>
                        </div>
                        
                        <div class="overview-card events">
                            <div class="overview-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="overview-details">
                                <h3>Events</h3>
                                <div class="overview-number"><?php echo $total_events; ?></div>
                            </div>
                        </div>
                        
                        <div class="overview-card services">
                            <div class="overview-icon">
                                <i class="fas fa-church"></i>
                            </div>
                            <div class="overview-details">
                                <h3>Services</h3>
                                <div class="overview-number"><?php echo $total_services; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-container">
                    <!-- Members Summary Card with Chart -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3><i class="fas fa-users"></i> Members Overview</h3>
                            <a href="members.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                        </div>
                        <div class="dashboard-card-content">
                            <div class="dashboard-chart-wrapper">
                                <canvas id="memberStatusChart"></canvas>
                            </div>
                            <ul class="dashboard-list mt-3">
                                <li class="dashboard-list-item">
                                    <span class="item-name">Active Members</span>
                                    <span class="item-value"><?php echo $active_members; ?></span>
                                </li>
                                <li class="dashboard-list-item">
                                    <span class="item-name">Inactive Members</span>
                                    <span class="item-value"><?php echo $inactive_members; ?></span>
                                </li>
                                <li class="dashboard-list-item">
                                    <span class="item-name">Visitors</span>
                                    <span class="item-value"><?php echo $visitors; ?></span>
                                </li>
                                <li class="dashboard-list-item">
                                    <span class="item-name">New Member</span>
                                    <span class="item-value"><?php echo $new_member_status; ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Donations Summary Card with Chart -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3><i class="fas fa-hand-holding-heart"></i> Donations Overview</h3>
                            <a href="donations.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                        </div>
                        <div class="dashboard-card-content">
                            <div class="dashboard-chart-wrapper">
                                <canvas id="monthlyDonationsChart"></canvas>
                            </div>
                            <ul class="dashboard-list mt-3">
                                <li class="dashboard-list-item">
                                    <span class="item-name">This Month</span>
                                    <span class="item-value">$<?php echo number_format($monthly_donations, 2); ?></span>
                                </li>
                                <li class="dashboard-list-item">
                                    <span class="item-name">This Week</span>
                                    <span class="item-value">$<?php echo number_format($weekly_donations, 2); ?></span>
                                </li>
                                <li class="dashboard-list-item">
                                    <span class="item-name">Average Donation</span>
                                    <span class="item-value">$<?php echo number_format($avg_donation, 2); ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Donation Categories Chart -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3><i class="fas fa-chart-pie"></i> Donation Categories</h3>
                            <a href="reports.php?type=donations" class="view-all">View Report <i class="fas fa-arrow-right"></i></a>
                        </div>
                        <div class="dashboard-card-content">
                            <div class="dashboard-chart-wrapper">
                                <canvas id="donationCategoriesChart"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Services Summary Card -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3><i class="fas fa-church"></i> Services Overview</h3>
                            <a href="services_management.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                        </div>
                        <div class="dashboard-card-content">
                            <div class="services-summary">
                                <div class="summary-item">
                                    <div class="summary-icon">
                                        <i class="fas fa-list"></i>
                                    </div>
                                    <div class="summary-details">
                                        <h4>Total Services</h4>
                                        <div class="summary-number"><?php echo $total_services; ?></div>
                                    </div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="summary-details">
                                        <h4>Active Services</h4>
                                        <div class="summary-number"><?php echo $active_services; ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <h5>Weekly Schedule</h5>
                                <ul class="dashboard-list">
                                    <?php
                                    // Get active services
                                    $query = "SELECT service_title, service_day, service_time FROM services WHERE is_active = 1 ORDER BY FIELD(service_day, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), service_time LIMIT 5";
                                    $services_result = mysqli_query($conn, $query);
                                    
                                    if (mysqli_num_rows($services_result) > 0) {
                                        while($service = mysqli_fetch_assoc($services_result)): ?>
                                        <li class="dashboard-list-item">
                                            <div>
                                                <div class="item-name"><?php echo $service['service_title']; ?></div>
                                                <div class="item-date"><?php echo $service['service_day'] . ' at ' . $service['service_time']; ?></div>
                                            </div>
                                        </li>
                                        <?php endwhile;
                                    } else { ?>
                                        <li class="dashboard-list-item">
                                            <div class="text-center">No active services</div>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activities Section -->
                <div class="dashboard-container">
                    <!-- Recent Donations -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3><i class="fas fa-money-bill-wave"></i> Recent Donations</h3>
                            <a href="donations.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                        </div>
                        <div class="dashboard-card-content">
                            <ul class="dashboard-list">
                                <?php 
                                mysqli_data_seek($recent_donations, 0);
                                if (mysqli_num_rows($recent_donations) > 0) {
                                    while($donation = mysqli_fetch_assoc($recent_donations)): ?>
                                    <li class="dashboard-list-item">
                                        <div>
                                            <div class="item-name"><?php echo $donation['member_name'] ? $donation['member_name'] : 'Anonymous'; ?></div>
                                            <div class="item-date"><?php echo date('M d, Y', strtotime($donation['donation_date'])); ?></div>
                                        </div>
                                        <span class="item-value">$<?php echo number_format($donation['amount'], 2); ?></span>
                                    </li>
                                <?php endwhile;
                                } else { ?>
                                    <li class="dashboard-list-item">
                                        <div class="text-center">No recent donations</div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Upcoming Events -->
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h3><i class="fas fa-calendar-day"></i> Upcoming Events</h3>
                            <a href="events.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                        </div>
                        <div class="dashboard-card-content">
                            <ul class="dashboard-list">
                                <?php 
                                mysqli_data_seek($upcoming_events, 0);
                                if (mysqli_num_rows($upcoming_events) > 0) {
                                    while($event = mysqli_fetch_assoc($upcoming_events)): ?>
                                    <li class="dashboard-list-item">
                                        <div>
                                            <div class="item-name"><?php echo $event['event_name']; ?></div>
                                            <div class="item-date"><?php echo date('M d, Y', strtotime($event['event_date'])); ?></div>
                                        </div>
                                        <span class="status-badge <?php echo strtolower($event['status']); ?>"><?php echo $event['status']; ?></span>
                                    </li>
                                <?php endwhile;
                                } else { ?>
                                    <li class="dashboard-list-item">
                                        <div class="text-center">No upcoming events</div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script src="script.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Member Status Chart
        var memberCtx = document.getElementById('memberStatusChart').getContext('2d');
        var memberStatusChart = new Chart(memberCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($member_status_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($member_status_data); ?>,
                    backgroundColor: [
                        'rgba(39, 174, 96, 0.7)',   // Green for Active
                        'rgba(231, 76, 60, 0.7)',   // Red for Inactive
                        'rgba(52, 152, 219, 0.7)',  // Blue for Visitor
                        'rgba(243, 156, 18, 0.7)'   // Orange for New Member
                    ],
                    borderColor: [
                        'rgba(39, 174, 96, 1)',
                        'rgba(231, 76, 60, 1)',
                        'rgba(52, 152, 219, 1)',
                        'rgba(243, 156, 18, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed;
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var percentage = Math.round((value / total) * 100);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
        
        // Monthly Donations Chart
        var donationsCtx = document.getElementById('monthlyDonationsChart').getContext('2d');
        var monthlyDonationsChart = new Chart(donationsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($monthly_chart_labels); ?>,
                datasets: [{
                    label: 'Monthly Donations',
                    data: <?php echo json_encode($monthly_chart_data); ?>,
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$ ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$ ' + value;
                            }
                        }
                    }
                }
            }
        });
        
        // Donation Categories Chart
        var categoriesCtx = document.getElementById('donationCategoriesChart').getContext('2d');
        var donationCategoriesChart = new Chart(categoriesCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($category_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($category_data); ?>,
                    backgroundColor: [
                        'rgba(52, 152, 219, 0.7)',  // Blue
                        'rgba(46, 204, 113, 0.7)',  // Green
                        'rgba(155, 89, 182, 0.7)',  // Purple
                        'rgba(241, 196, 15, 0.7)',  // Yellow
                        'rgba(231, 76, 60, 0.7)'    // Red
                    ],
                    borderColor: [
                        'rgba(52, 152, 219, 1)',
                        'rgba(46, 204, 113, 1)',
                        'rgba(155, 89, 182, 1)',
                        'rgba(241, 196, 15, 1)',
                        'rgba(231, 76, 60, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed;
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var percentage = Math.round((value / total) * 100);
                                return label + ': $' + value.toFixed(2) + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
</body>
</html>

