<?php
session_start();
require_once 'config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Get church settings
$query = "SELECT * FROM settings WHERE id = 1";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);

// Get success or error messages from session
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Clear messages after displaying them
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Church Management System</title>
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
                    <h1><i class="fas fa-cog"></i> Settings</h1>
                </div>
                
                <?php if($success_message): ?>
                    <div class="success-message"><?php echo $success_message; ?></div>
                <?php endif; ?>
                
                <?php if($error_message): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                <?php endif; ?>
                
                <div class="content-container">
                    <div class="settings-tabs">
                        <div class="tab-buttons">
                            <button class="tab-btn active" data-tab="account"><i class="fas fa-user-circle"></i> Account Settings</button>
                            <button class="tab-btn" data-tab="backup"><i class="fas fa-database"></i> Backup & Restore</button>
                        </div>
                        
                        <div class="tab-content">
                            <!-- Account Settings Tab -->
                            <div class="tab-pane active" id="account">
                                <div class="report-table-container">
                                    <div class="table-header">
                                        <h3><i class="fas fa-user-circle"></i> Account Settings</h3>
                                    </div>
                                    
                                    <div class="report-table-content">
                                        <form action="settings_process.php" method="post" class="plain-form">
                                            <input type="hidden" name="action" value="update_account">
                                            
                                            <table class="report-table">
                                                <tbody>
                                                    <tr>
                                                        <th colspan="2" class="section-header">
                                                            <i class="fas fa-user"></i> User Information
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <td class="detail-label">Username</td>
                                                        <td><input type="text" name="username" value="<?php echo $user['username']; ?>" required></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="detail-label">Email</td>
                                                        <td><input type="email" name="email" value="<?php echo $user['email']; ?>" required></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="detail-label">Full Name</td>
                                                        <td><input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required></td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <th colspan="2" class="section-header">
                                                            <i class="fas fa-lock"></i> Change Password
                                                        </th>
                                                    </tr>
                                                    <tr>
                                                        <td class="detail-label">Current Password</td>
                                                        <td><input type="password" name="current_password"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="detail-label">New Password</td>
                                                        <td><input type="password" name="new_password"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="detail-label">Confirm New Password</td>
                                                        <td><input type="password" name="confirm_password"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            
                                            <div class="form-actions">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Backup & Restore Tab -->
                            <div class="tab-pane" id="backup">
                                <div class="report-table-container">
                                    <div class="table-header">
                                        <h3><i class="fas fa-database"></i> Backup & Restore</h3>
                                    </div>
                                    
                                    <div class="report-table-content">
                                        <div class="backup-section">
                                            <h4><i class="fas fa-download"></i> Create Backup</h4>
                                            <p>Create a backup of your database. This will download a SQL file containing all your data.</p>
                                            <form action="backup_process.php" method="post">
                                                <input type="hidden" name="action" value="create_backup">
                                                <button type="submit" class="btn btn-primary"><i class="fas fa-download"></i> Create Backup</button>
                                            </form>
                                        </div>
                                        
                                        <div class="backup-section">
                                            <h4><i class="fas fa-upload"></i> Restore Backup</h4>
                                            <p>Restore your database from a previous backup. <strong>Warning:</strong> This will overwrite your current data.</p>
                                            <form action="backup_process.php" method="post" enctype="multipart/form-data">
                                                <input type="hidden" name="action" value="restore_backup">
                                                <div class="file-upload">
                                                    <input type="file" name="backup_file" accept=".sql" required>
                                                </div>
                                                <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to restore this backup? This will overwrite your current data.');"><i class="fas fa-upload"></i> Restore Backup</button>
                                            </form>
                                        </div>
                                        
                                        <div class="backup-section">
                                            <h4><i class="fas fa-history"></i> Backup History</h4>
                                            <table class="report-table">
                                                <thead>
                                                    <tr>
                                                        <th>Filename</th>
                                                        <th>Date</th>
                                                        <th>Size</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $backup_dir = 'backups/';
                                                    if (is_dir($backup_dir)) {
                                                        $files = scandir($backup_dir);
                                                        $backup_files = array_filter($files, function($file) {
                                                            return pathinfo($file, PATHINFO_EXTENSION) === 'sql';
                                                        });
                                                        
                                                        if (count($backup_files) > 0) {
                                                            foreach ($backup_files as $file) {
                                                                $file_path = $backup_dir . $file;
                                                                $file_size = filesize($file_path);
                                                                $file_date = date('F d, Y H:i:s', filemtime($file_path));
                                                                
                                                                echo '<tr>';
                                                                echo '<td>' . $file . '</td>';
                                                                echo '<td>' . $file_date . '</td>';
                                                                echo '<td>' . formatFileSize($file_size) . '</td>';
                                                                echo '<td class="actions">';
                                                                echo '<a href="' . $file_path . '" class="download-btn" download><i class="fas fa-download"></i></a>';
                                                                echo '<a href="backup_process.php?action=delete_backup&file=' . $file . '" class="delete-btn" onclick="return confirm(\'Are you sure you want to delete this backup?\');"><i class="fas fa-trash"></i></a>';
                                                                echo '</td>';
                                                                echo '</tr>';
                                                            }
                                                        } else {
                                                            echo '<tr><td colspan="4" class="text-center">No backup files found.</td></tr>';
                                                        }
                                                    } else {
                                                        echo '<tr><td colspan="4" class="text-center">Backup directory not found.</td></tr>';
                                                    }
                                                    
                                                    function formatFileSize($size) {
                                                        $units = array('B', 'KB', 'MB', 'GB', 'TB');
                                                        $i = 0;
                                                        while ($size >= 1024 && $i < count($units) - 1) {
                                                            $size /= 1024;
                                                            $i++;
                                                        }
                                                        return round($size, 2) . ' ' . $units[$i];
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons and panes
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));
                
                // Add active class to clicked button and corresponding pane
                button.classList.add('active');
                const tabId = button.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Image preview functionality
        const fileInput = document.querySelector('input[name="church_logo"]');
        const imagePreview = document.querySelector('.image-preview-container img');
        
        if (fileInput && imagePreview) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
    </script>
    <script src="script.js"></script>
</body>
</html>

