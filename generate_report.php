<?php
session_start();
require_once 'config/db.php';

// Set the default timezone
date_default_timezone_set('Asia/Manila'); // Change to your timezone if needed

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get report type
$report_type = isset($_GET['type']) ? $_GET['type'] : 'members';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get church settings
$query = "SELECT * FROM settings WHERE id = 1";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);
$church_name = isset($settings['church_name']) ? $settings['church_name'] : 'JIA Somal-ot Church';
$church_address = isset($settings['address']) ? $settings['address'] : '';
$church_city = isset($settings['city']) ? $settings['city'] : '';
$church_state = isset($settings['state']) ? $settings['state'] : '';
$church_zip = isset($settings['zip']) ? $settings['zip'] : '';

// Format church address
$church_full_address = $church_address;
if ($church_city) $church_full_address .= ", " . $church_city;
if ($church_state) $church_full_address .= ", " . $church_state;
if ($church_zip) $church_full_address .= " " . $church_zip;

// Get report data based on type
if ($report_type == 'members') {
    $title = 'Members List Report';
    
    // Get member data
    $query = "SELECT id, first_name, last_name, email, phone, membership_status, join_date, ministry 
              FROM members 
              ORDER BY last_name, first_name";
    $members = mysqli_query($conn, $query);
    
    // Get member statistics
    $query = "SELECT 
              COUNT(*) as total_members,
              SUM(CASE WHEN membership_status = 'Active' THEN 1 ELSE 0 END) as active_members,
              SUM(CASE WHEN membership_status = 'Inactive' THEN 1 ELSE 0 END) as inactive_members,
              SUM(CASE WHEN membership_status = 'Visitor' THEN 1 ELSE 0 END) as visitors,
              SUM(CASE WHEN membership_status = 'New Member' THEN 1 ELSE 0 END) as new_members
            FROM members";
    $member_stats = mysqli_fetch_assoc(mysqli_query($conn, $query));
    
    // Get membership status data for chart
    $membership_labels = ['Active', 'Inactive', 'Visitor', 'New Member'];
    $membership_data = [
        $member_stats['active_members'],
        $member_stats['inactive_members'],
        $member_stats['visitors'],
        $member_stats['new_members']
    ];
    
} elseif ($report_type == 'donations') {
    $title = 'Donations Summary Report';
    
    // Get donation data
    $query = "SELECT d.id, d.amount, d.donation_date, d.payment_method, d.category, 
              CONCAT(m.first_name, ' ', m.last_name) as member_name
              FROM donations d
              LEFT JOIN members m ON d.member_id = m.id
              WHERE d.donation_date BETWEEN '$start_date' AND '$end_date'
              ORDER BY d.donation_date DESC";
    $donations = mysqli_query($conn, $query);
    
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
    
    // Get donation categories for chart
    $query = "SELECT 
              category,
              SUM(amount) as total
            FROM donations
            WHERE donation_date BETWEEN '$start_date' AND '$end_date'
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
    
    // Get monthly donations for chart
    $query = "SELECT 
              DATE_FORMAT(donation_date, '%Y-%m') as month,
              SUM(amount) as total
            FROM donations
            WHERE donation_date >= DATE_SUB('$end_date', INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(donation_date, '%Y-%m')
            ORDER BY month ASC";
    $monthly_result = mysqli_query($conn, $query);
    
    $monthly_labels = [];
    $monthly_data = [];
    
    while ($row = mysqli_fetch_assoc($monthly_result)) {
        $monthly_labels[] = date('M Y', strtotime($row['month'] . '-01'));
        $monthly_data[] = $row['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - <?php echo $church_name; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --accent-color: #e74c3c;
            --text-color: #333;
            --light-text: #7f8c8d;
            --light-bg: #f8f9fa;
            --border-color: #e9ecef;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #3498db;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: var(--text-color);
            background-color: #f5f7fa;
            line-height: 1.6;
        }
        
        .report-container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .report-header {
            background-color: var(--primary-color);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .report-header h1 {
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .report-header h2 {
            font-size: 22px;
            margin-bottom: 10px;
            font-weight: 500;
        }
        
        .report-header p {
            margin: 5px 0;
            opacity: 0.9;
        }
        
        .report-date {
            font-style: italic;
            margin-top: 10px;
            font-size: 14px;
            opacity: 0.8;
        }
        
        .report-content {
            padding: 30px;
        }
        
        .report-section {
            margin-bottom: 40px;
        }
        
        .report-section h2 {
            color: var(--primary-color);
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 22px;
            display: flex;
            align-items: center;
        }
        
        .report-section h2 i {
            margin-right: 10px;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: rgba(52, 152, 219, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .stat-icon i {
            font-size: 24px;
            color: var(--primary-color);
        }
        
        .stat-info {
            flex: 1;
        }
        
        .stat-info h3 {
            font-size: 14px;
            color: var(--light-text);
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-color);
        }
        
        .chart-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .chart-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }
        
        .chart-card h3 {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--text-color);
            display: flex;
            align-items: center;
        }
        
        .chart-card h3 i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .chart-wrapper {
            height: 300px;
            position: relative;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }
        
        th, td {
            padding: 15px;
            text-align: left;
        }
        
        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        
        tr:nth-child(even) {
            background-color: var(--light-bg);
        }
        
        tr:hover {
            background-color: rgba(52, 152, 219, 0.05);
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-badge.active {
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
        }
        
        .status-badge.inactive {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }
        
        .status-badge.visitor {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--info-color);
        }
        
        .status-badge.new-member {
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
        }
        
        .report-footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: var(--light-text);
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
        }
        
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 100;
        }
        
        .action-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .action-button i {
            margin-right: 8px;
        }
        
        .action-button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        
        .action-button.back {
            background-color: #95a5a6;
        }
        
        .action-button.back:hover {
            background-color: #7f8c8d;
        }
        
        .action-button.print {
            background-color: #27ae60;
        }
        
        .action-button.print:hover {
            background-color: #219653;
        }
        
        .action-button.pdf {
            background-color: #e74c3c;
        }
        
        .action-button.pdf:hover {
            background-color: #c0392b;
        }
        
        .action-button.excel {
            background-color: #27ae60;
        }
        
        .action-button.excel:hover {
            background-color: #219653;
        }
        
        @media print {
            .action-buttons {
                display: none;
            }
            
            body {
                background-color: white;
            }
            
            .report-container {
                box-shadow: none;
                margin: 0;
                max-width: 100%;
            }
            
            .chart-container {
                page-break-inside: avoid;
            }
            
            .stat-card {
                box-shadow: none;
                border: 1px solid #eee;
            }
            
            .chart-card {
                box-shadow: none;
                border: 1px solid #eee;
            }
            
            table {
                box-shadow: none;
                border: 1px solid #eee;
            }
        }
        
        @media (max-width: 768px) {
            .report-container {
                margin: 10px;
                border-radius: 5px;
            }
            
            .report-header {
                padding: 20px;
            }
            
            .report-content {
                padding: 15px;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
                top: 10px;
                right: 10px;
            }
            
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <div class="action-buttons">
        <button class="action-button print" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
        <a href="download_report.php?type=<?php echo $report_type; ?>&format=pdf&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="action-button pdf"><i class="fas fa-file-pdf"></i> Download PDF</a>
        <a href="download_report.php?type=<?php echo $report_type; ?>&format=excel&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="action-button excel"><i class="fas fa-file-excel"></i> Download Excel</a>
        <a href="reports.php" class="action-button back"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    
    <div class="report-container">
        <div class="report-header">
            <h1><?php echo $church_name; ?></h1>
            <?php if($church_full_address): ?>
                <p><?php echo $church_full_address; ?></p>
            <?php endif; ?>
            <h2><?php echo $title; ?></h2>
            <p class="report-date">Generated on <?php echo date('F d, Y h:i A'); ?></p>
            <?php if($report_type == 'donations'): ?>
                <p class="report-date">Report Period: <?php echo date('M d, Y', strtotime($start_date)); ?> - <?php echo date('M d, Y', strtotime($end_date)); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="report-content">
            <?php if($report_type == 'members'): ?>
            <div class="report-section">
                <h2><i class="fas fa-chart-pie"></i> Membership Overview</h2>
                
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Members</h3>
                            <div class="stat-value"><?php echo $member_stats['total_members']; ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Active Members</h3>
                            <div class="stat-value"><?php echo $member_stats['active_members']; ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Inactive Members</h3>
                            <div class="stat-value"><?php echo $member_stats['inactive_members']; ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="stat-info">
                            <h3>New Members</h3>
                            <div class="stat-value"><?php echo $member_stats['new_members']; ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="chart-container">
                    <div class="chart-card">
                        <h3><i class="fas fa-chart-pie"></i> Membership Status Distribution</h3>
                        <div class="chart-wrapper">
                            <canvas id="membershipChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="report-section">
                <h2><i class="fas fa-list"></i> Members List</h2>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Join Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        mysqli_data_seek($members, 0);
                        while($member = mysqli_fetch_assoc($members)): 
                        ?>
                            <tr>
                                <td><?php echo $member['id']; ?></td>
                                <td><?php echo $member['first_name'] . ' ' . $member['last_name']; ?></td>
                                <td><?php echo $member['email']; ?></td>
                                <td><?php echo $member['phone']; ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower(str_replace(' ', '-', $member['membership_status'])); ?>">
                                        <?php echo $member['membership_status']; ?>
                                    </span>
                                </td>
                                <td><?php echo $member['join_date'] ? date('M d, Y', strtotime($member['join_date'])) : 'N/A'; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <?php if($report_type == 'donations'): ?>
            <div class="report-section">
                <h2><i class="fas fa-chart-line"></i> Donations Overview</h2>
                
                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Donations</h3>
                            <div class="stat-value"><?php echo $donation_stats['total_donations']; ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Amount</h3>
                            <div class="stat-value">$<?php echo number_format($donation_stats['total_amount'], 2); ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Average Donation</h3>
                            <div class="stat-value">$<?php echo number_format($donation_stats['average_amount'], 2); ?></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-arrow-up"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Highest Donation</h3>
                            <div class="stat-value">$<?php echo number_format($donation_stats['highest_donation'], 2); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="chart-container">
                    <div class="chart-card">
                        <h3><i class="fas fa-chart-bar"></i> Monthly Donations</h3>
                        <div class="chart-wrapper">
                            <canvas id="monthlyDonationsChart"></canvas>
                        </div>
                    </div>
                    
                    <div class="chart-card">
                        <h3><i class="fas fa-chart-pie"></i> Donation Categories</h3>
                        <div class="chart-wrapper">
                            <canvas id="categoriesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="report-section">
                <h2><i class="fas fa-list"></i> Donations List</h2>
                
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Member</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Payment Method</th>
                            <th>Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        mysqli_data_seek($donations, 0);
                        while($donation = mysqli_fetch_assoc($donations)): 
                        ?>
                            <tr>
                                <td><?php echo $donation['id']; ?></td>
                                <td><?php echo $donation['member_name']; ?></td>
                                <td>$<?php echo number_format($donation['amount'], 2); ?></td>
                                <td><?php echo date('M d, Y', strtotime($donation['donation_date'])); ?></td>
                                <td><?php echo $donation['payment_method']; ?></td>
                                <td><?php echo $donation['category']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <div class="report-footer">
                <p>This report was generated by the Church Management System on <?php echo date('F d, Y h:i:s A'); ?></p>
                <p>&copy; <?php echo date('Y'); ?> <?php echo $church_name; ?>. All rights reserved.</p>
            </div>
        </div>
    </div>
    
    <?php if($report_type == 'members'): ?>
    <script>
        // Membership Status Chart
        const membershipCtx = document.getElementById('membershipChart').getContext('2d');
        new Chart(membershipCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($membership_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($membership_data); ?>,
                    backgroundColor: [
                        'rgba(39, 174, 96, 0.7)',
                        'rgba(231, 76, 60, 0.7)',
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(243, 156, 18, 0.7)'
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
    
    <?php if($report_type == 'donations'): ?>
    <script>
        // Monthly Donations Chart
        const monthlyCtx = document.getElementById('monthlyDonationsChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($monthly_labels); ?>,
                datasets: [{
                    label: 'Total Donations',
                    data: <?php echo json_encode($monthly_data); ?>,
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return '$' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        
        // Categories Chart
        const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
        new Chart(categoriesCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($category_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($category_data); ?>,
                    backgroundColor: [
                        'rgba(52, 152, 219, 0.7)',
                        'rgba(46, 204, 113, 0.7)',
                        'rgba(155, 89, 182, 0.7)',
                        'rgba(241, 196, 15, 0.7)',
                        'rgba(231, 76, 60, 0.7)'
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>

