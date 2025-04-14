<?php
session_start();
require_once 'config/db.php';

// Redirect if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get church settings
$query = "SELECT * FROM settings WHERE id = 1";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);
$church_name = isset($settings['church_name']) ? $settings['church_name'] : 'Church Management System';

// Get date range
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get events for the selected period
$query = "SELECT id, event_name, event_date, event_time, location, description, status 
          FROM events 
          WHERE event_date BETWEEN '$start_date' AND '$end_date' 
          ORDER BY event_date ASC, event_time ASC";
$events = mysqli_query($conn, $query);

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

// Get events by month for chart
$query = "SELECT 
            MONTH(event_date) as month, 
            COUNT(*) as count 
          FROM events 
          WHERE event_date BETWEEN DATE_SUB('$end_date', INTERVAL 12 MONTH) AND '$end_date' 
          GROUP BY MONTH(event_date), YEAR(event_date) 
          ORDER BY YEAR(event_date), MONTH(event_date)";
$monthly_result = mysqli_query($conn, $query);
$monthly_data = [];
$monthly_labels = [];

while ($row = mysqli_fetch_assoc($monthly_result)) {
    $month_name = date('M', mktime(0, 0, 0, $row['month'], 1));
    $monthly_labels[] = $month_name;
    $monthly_data[] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Report - <?php echo $church_name; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/enhanced-style.css">
    <link rel="stylesheet" href="css/reports-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .report-container {
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .report-header {
            background-color: #3498db;
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
            color: #3498db;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 22px;
            display: flex;
            align-items: center;
        }
        
        .report-section h2 i {
            margin-right: 10px;
        }
        
        .event-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .event-table th, 
        .event-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        
        .event-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .event-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .event-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .event-status.scheduled {
            background-color: #e3f2fd;
            color: #3498db;
        }
        
        .event-status.completed {
            background-color: #e6f7ee;
            color: #27ae60;
        }
        
        .event-status.cancelled {
            background-color: #fbe9e7;
            color: #e74c3c;
        }
        
        .event-status.postponed {
            background-color: #fff8e1;
            color: #f39c12;
        }
        
        .chart-container {
            height: 300px;
            margin-bottom: 30px;
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
            background-color: #3498db;
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
            background-color: #2980b9;
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
        
        .no-events {
            text-align: center;
            padding: 40px 20px;
            color: #7f8c8d;
        }
        
        .no-events i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }
        
        .no-events h3 {
            margin-bottom: 10px;
            font-weight: 500;
        }
        
        .no-events p {
            color: #95a5a6;
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
            
            .action-buttons {
                flex-direction: column;
                top: 10px;
                right: 10px;
            }
            
            .event-table {
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
        <a href="download_report.php?type=events&format=pdf&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="action-button pdf"><i class="fas fa-file-pdf"></i> Download PDF</a>
        <a href="download_report.php?type=events&format=excel&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="action-button excel"><i class="fas fa-file-excel"></i> Download Excel</a>
        <a href="reports.php?type=events&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="action-button back"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
    
    <div class="report-container">
        <div class="report-header">
            <h1><?php echo $church_name; ?></h1>
            <h2>Events Report</h2>
            <p class="report-date">Generated on <?php echo date('F d, Y h:i A'); ?></p>
            <p class="report-date">Report Period: <?php echo date('M d, Y', strtotime($start_date)); ?> - <?php echo date('M d, Y', strtotime($end_date)); ?></p>
        </div>
        
        <div class="report-content">
            <div class="report-section">
                <h2><i class="fas fa-chart-pie"></i> Events Overview</h2>
                
                <div class="report-summary">
                    <div class="summary-item highlight">
                        <div class="summary-label">Total Events</div>
                        <div class="summary-value"><?php echo $event_stats['total_events']; ?></div>
                    </div>
                    
                    <div class="summary-item">
                        <div class="summary-label">Scheduled</div>
                        <div class="summary-value"><?php echo $event_stats['scheduled_events']; ?></div>
                    </div>
                    
                    <div class="summary-item">
                        <div class="summary-label">Completed</div>
                        <div class="summary-value"><?php echo $event_stats['completed_events']; ?></div>
                    </div>
                    
                    <div class="summary-item">
                        <div class="summary-label">Cancelled</div>
                        <div class="summary-value"><?php echo $event_stats['cancelled_events']; ?></div>
                    </div>
                    
                    <div class="summary-item">
                        <div class="summary-label">Postponed</div>
                        <div class="summary-value"><?php echo $event_stats['postponed_events']; ?></div>
                    </div>
                </div>
                
                <div class="chart-container">
                    <canvas id="eventStatusChart"></canvas>
                </div>
                
                <?php if (count($monthly_data) > 0): ?>
                <div class="chart-container">
                    <canvas id="monthlyEventsChart"></canvas>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="report-section">
                <h2><i class="fas fa-list"></i> Events List</h2>
                
                <?php if (mysqli_num_rows($events) > 0): ?>
                <table class="event-table">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($event = mysqli_fetch_assoc($events)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event['event_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($event['event_date'])); ?></td>
                                <td><?php echo $event['event_time'] ? date('h:i A', strtotime($event['event_time'])) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($event['location']); ?></td>
                                <td>
                                    <span class="event-status <?php echo strtolower($event['status']); ?>">
                                        <?php echo $event['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="no-events">
                    <i class="fas fa-calendar-times"></i>
                    <h3>No Events Found</h3>
                    <p>There are no events scheduled for the selected period.</p>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="report-footer">
                <p>This report was generated by the Church Management System on <?php echo date('F d, Y h:i:s A'); ?></p>
                <p>&copy; <?php echo date('Y'); ?> <?php echo $church_name; ?>. All rights reserved.</p>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Event Status Chart
            var eventCtx = document.getElementById('eventStatusChart').getContext('2d');
            var eventStatusChart = new Chart(eventCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Scheduled', 'Completed', 'Cancelled', 'Postponed'],
                    datasets: [{
                        data: [
                            <?php echo $event_stats['scheduled_events']; ?>,
                            <?php echo $event_stats['completed_events']; ?>,
                            <?php echo $event_stats['cancelled_events']; ?>,
                            <?php echo $event_stats['postponed_events']; ?>
                        ],
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.7)',  // Blue for Scheduled
                            'rgba(46, 204, 113, 0.7)',  // Green for Completed
                            'rgba(231, 76, 60, 0.7)',   // Red for Cancelled
                            'rgba(241, 196, 15, 0.7)'   // Yellow for Postponed
                        ],
                        borderColor: [
                            'rgba(52, 152, 219, 1)',
                            'rgba(46, 204, 113, 1)',
                            'rgba(231, 76, 60, 1)',
                            'rgba(241, 196, 15, 1)'
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
                                    var value = context.raw;
                                    var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    var percentage = Math.round((value / total) * 100);
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
            
            <?php if (count($monthly_data) > 0): ?>
            // Monthly Events Chart
            var monthlyCtx = document.getElementById('monthlyEventsChart').getContext('2d');
            var monthlyEventsChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($monthly_labels); ?>,
                    datasets: [{
                        label: 'Events per Month',
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
                                precision: 0
                            }
                        }
                    }
                }
            });
            <?php endif; ?>
        });
    </script>
</body>
</html>

