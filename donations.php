<?php
session_start();
require_once 'config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle donation deletion
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    $query = "DELETE FROM donations WHERE id = $id";
    
    if (mysqli_query($conn, $query)) {
        $_SESSION['success_message'] = "Donation deleted successfully!";
        header("Location: donations.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . mysqli_error($conn);
    }
}

// Get success or error messages from session
$success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';
$error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';

// Clear messages after displaying them
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Fetch all donations with member names
$query = "SELECT d.*, CONCAT(m.first_name, ' ', m.last_name) as member_name 
          FROM donations d 
          LEFT JOIN members m ON d.member_id = m.id 
          ORDER BY d.donation_date DESC";
$donations = mysqli_query($conn, $query);

// Fetch all members for the dropdown
$query = "SELECT id, first_name, last_name FROM members ORDER BY last_name ASC";
$members = mysqli_query($conn, $query);
$members_list = [];
while ($member = mysqli_fetch_assoc($members)) {
    $members_list[] = $member;
}

// Get campaigns for dropdown
$campaigns = [
    "General Fund",
    "Building Fund",
    "Mission Trip",
    "Youth Camp",
    "Christmas Offering",
    "Easter Offering",
    "Benevolence Fund",
    "Other"
];

// Default view is the list
$current_view = isset($_GET['view']) ? $_GET['view'] : 'list';
$donation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get donation data if viewing or editing
$donation_data = null;
if (($current_view == 'view' || $current_view == 'edit') && $donation_id > 0) {
    $query = "SELECT d.*, CONCAT(m.first_name, ' ', m.last_name) as member_name 
              FROM donations d 
              LEFT JOIN members m ON d.member_id = m.id 
              WHERE d.id = $donation_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $donation_data = mysqli_fetch_assoc($result);
    } else {
        // Donation not found, redirect to list
        $_SESSION['error_message'] = "Donation not found";
        header("Location: donations.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donations - Church Management System</title>
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
                    <h1><i class="fas fa-hand-holding-heart"></i> Donations Management</h1>
                    <?php if($current_view == 'list'): ?>
                        <a href="donations.php?view=add" class="add-btn"><i class="fas fa-plus"></i> Add Donation</a>
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
                        <!-- Donations List View -->
                        <div class="report-table-container">
                            <div class="table-header">
                                <h3><i class="fas fa-list"></i> All Donations</h3>
                                <div class="search-container">
                                    <input type="text" id="donationSearch" placeholder="Search donations...">
                                    <button type="submit"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                            
                            <div class="report-table-content">
                                <table class="report-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Member</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Payment Method</th>
                                            <th>Category</th>
                                            <th>Campaign</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($donation = mysqli_fetch_assoc($donations)): ?>
                                            <tr>
                                                <td><?php echo $donation['id']; ?></td>
                                                <td><?php echo $donation['member_name']; ?></td>
                                                <td>₱<?php echo number_format($donation['amount'], 2); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($donation['donation_date'])); ?></td>
                                                <td><?php echo $donation['payment_method']; ?></td>
                                                <td><?php echo $donation['category']; ?></td>
                                                <td><?php echo $donation['campaign'] ? $donation['campaign'] : 'N/A'; ?></td>
                                                <td class="actions">
                                                    <a href="donations.php?view=view&id=<?php echo $donation['id']; ?>" class="view-btn"><i class="fas fa-eye"></i></a>
                                                    <a href="donations.php?view=edit&id=<?php echo $donation['id']; ?>" class="edit-btn"><i class="fas fa-edit"></i></a>
                                                    <a href="donations.php?delete=<?php echo $donation['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this donation?');"><i class="fas fa-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php elseif($current_view == 'add'): ?>
                        <!-- Add Donation View -->
                        <div class="report-table-container">
                            <div class="table-header">
                                <h3><i class="fas fa-plus-circle"></i> Add New Donation</h3>
                                <a href="donations.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to List</a>
                            </div>
                            
                            <div class="report-table-content">
                                <form action="donation_process.php" method="post" class="plain-form">
                                    <input type="hidden" name="action" value="add">
                                    
                                    <table class="report-table">
                                        <tbody>
                                            <tr>
                                                <th colspan="2" class="section-header">
                                                    <i class="fas fa-info-circle"></i> Basic Information
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Member</td>
                                                <td>
                                                    <select id="member_id" name="member_id" required>
                                                        <option value="">Select Member</option>
                                                        <?php foreach($members_list as $member): ?>
                                                            <option value="<?php echo $member['id']; ?>"><?php echo $member['first_name'] . ' ' . $member['last_name']; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Amount</td>
                                                <td><input type="number" id="amount" name="amount" step="0.01" min="0" required></td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Date</td>
                                                <td><input type="date" id="donation_date" name="donation_date" value="<?php echo date('Y-m-d'); ?>" required></td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Payment Method</td>
                                                <td>
                                                    <select id="payment_method" name="payment_method" required>
                                                        <option value="">Select Payment Method</option>
                                                        <option value="Cash">Cash</option>
                                                        <option value="Check">Check</option>
                                                        <option value="Credit Card">Credit Card</option>
                                                        <option value="Bank Transfer">Bank Transfer</option>
                                                        <option value="Online Payment">Online Payment</option>
                                                        <option value="Mobile Payment">Mobile Payment</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <th colspan="2" class="section-header">
                                                    <i class="fas fa-tag"></i> Categorization
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Category</td>
                                                <td>
                                                    <select id="category" name="category" required>
                                                        <option value="">Select Category</option>
                                                        <option value="Tithe">Tithe</option>
                                                        <option value="Offering">Offering</option>
                                                        <option value="Building Fund">Building Fund</option>
                                                        <option value="Missions">Missions</option>
                                                        <option value="Youth Ministry">Youth Ministry</option>
                                                        <option value="Special Project">Special Project</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Campaign/Fund</td>
                                                <td>
                                                    <select id="campaign" name="campaign">
                                                        <option value="">Select Campaign</option>
                                                        <?php foreach($campaigns as $campaign): ?>
                                                            <option value="<?php echo $campaign; ?>"><?php echo $campaign; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <th colspan="2" class="section-header">
                                                    <i class="fas fa-file-alt"></i> Additional Information
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Reference Number</td>
                                                <td><input type="text" id="reference_number" name="reference_number" placeholder="Check number, transaction ID, etc."></td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Notes</td>
                                                <td><textarea id="notes" name="notes" rows="3" placeholder="Add any additional information about this donation"></textarea></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <div class="form-actions">
                                        <a href="donations.php" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Add Donation</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php elseif($current_view == 'view' && $donation_data): ?>
                        <!-- View Donation Details -->
                        <div class="report-table-container">
                            <div class="table-header">
                                <h3><i class="fas fa-info-circle"></i> Donation Details</h3>
                                <div class="actions">
                                    <a href="donations.php?view=edit&id=<?php echo $donation_data['id']; ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="donations.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to List</a>
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
                                            <td class="detail-label">Donation ID</td>
                                            <td><?php echo $donation_data['id']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Member</td>
                                            <td><?php echo $donation_data['member_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Amount</td>
                                            <td>$<?php echo number_format($donation_data['amount'], 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Date</td>
                                            <td><?php echo date('F d, Y', strtotime($donation_data['donation_date'])); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Payment Method</td>
                                            <td><?php echo $donation_data['payment_method']; ?></td>
                                        </tr>
                                        
                                        <tr>
                                            <th colspan="2" class="section-header">
                                                <i class="fas fa-tag"></i> Categorization
                                            </th>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Category</td>
                                            <td><?php echo $donation_data['category']; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Campaign/Fund</td>
                                            <td><?php echo $donation_data['campaign'] ? $donation_data['campaign'] : 'N/A'; ?></td>
                                        </tr>
                                        
                                        <tr>
                                            <th colspan="2" class="section-header">
                                                <i class="fas fa-file-alt"></i> Additional Information
                                            </th>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Reference Number</td>
                                            <td><?php echo $donation_data['reference_number'] ? $donation_data['reference_number'] : 'N/A'; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Notes</td>
                                            <td><?php echo $donation_data['notes'] ? nl2br($donation_data['notes']) : 'N/A'; ?></td>
                                        </tr>
                                        
                                        <tr>
                                            <th colspan="2" class="section-header">
                                                <i class="fas fa-clock"></i> System Information
                                            </th>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Created At</td>
                                            <td><?php echo date('F d, Y H:i:s', strtotime($donation_data['created_at'])); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="detail-label">Last Updated</td>
                                            <td><?php echo date('F d, Y H:i:s', strtotime($donation_data['updated_at'])); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php elseif($current_view == 'edit' && $donation_data): ?>
                        <!-- Edit Donation View -->
                        <div class="report-table-container">
                            <div class="table-header">
                                <h3><i class="fas fa-edit"></i> Edit Donation #<?php echo $donation_data['id']; ?></h3>
                                <a href="donations.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to List</a>
                            </div>
                            
                            <div class="report-table-content">
                                <form action="donation_process.php" method="post" class="plain-form">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="donation_id" value="<?php echo $donation_data['id']; ?>">
                                    
                                    <table class="report-table">
                                        <tbody>
                                            <tr>
                                                <th colspan="2" class="section-header">
                                                    <i class="fas fa-info-circle"></i> Basic Information
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Member</td>
                                                <td>
                                                    <select id="member_id" name="member_id" required>
                                                        <option value="">Select Member</option>
                                                        <?php foreach($members_list as $member): ?>
                                                            <option value="<?php echo $member['id']; ?>" <?php echo ($member['id'] == $donation_data['member_id']) ? 'selected' : ''; ?>><?php echo $member['first_name'] . ' ' . $member['last_name']; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Amount ($)</td>
                                                <td><input type="number" id="amount" name="amount" step="0.01" min="0" value="<?php echo $donation_data['amount']; ?>" required></td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Date</td>
                                                <td><input type="date" id="donation_date" name="donation_date" value="<?php echo $donation_data['donation_date']; ?>" required></td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Payment Method</td>
                                                <td>
                                                    <select id="payment_method" name="payment_method" required>
                                                        <option value="">Select Payment Method</option>
                                                        <option value="Cash" <?php echo ($donation_data['payment_method'] == 'Cash') ? 'selected' : ''; ?>>Cash</option>
                                                        <option value="Check" <?php echo ($donation_data['payment_method'] == 'Check') ? 'selected' : ''; ?>>Check</option>
                                                        <option value="Credit Card" <?php echo ($donation_data['payment_method'] == 'Credit Card') ? 'selected' : ''; ?>>Credit Card</option>
                                                        <option value="Bank Transfer" <?php echo ($donation_data['payment_method'] == 'Bank Transfer') ? 'selected' : ''; ?>>Bank Transfer</option>
                                                        <option value="Online Payment" <?php echo ($donation_data['payment_method'] == 'Online Payment') ? 'selected' : ''; ?>>Online Payment</option>
                                                        <option value="Mobile Payment" <?php echo ($donation_data['payment_method'] == 'Mobile Payment') ? 'selected' : ''; ?>>Mobile Payment</option>
                                                        <option value="Other" <?php echo ($donation_data['payment_method'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <th colspan="2" class="section-header">
                                                    <i class="fas fa-tag"></i> Categorization
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Category</td>
                                                <td>
                                                    <select id="category" name="category" required>
                                                        <option value="">Select Category</option>
                                                        <option value="Tithe" <?php echo ($donation_data['category'] == 'Tithe') ? 'selected' : ''; ?>>Tithe</option>
                                                        <option value="Offering" <?php echo ($donation_data['category'] == 'Offering') ? 'selected' : ''; ?>>Offering</option>
                                                        <option value="Building Fund" <?php echo ($donation_data['category'] == 'Building Fund') ? 'selected' : ''; ?>>Building Fund</option>
                                                        <option value="Missions" <?php echo ($donation_data['category'] == 'Missions') ? 'selected' : ''; ?>>Missions</option>
                                                        <option value="Youth Ministry" <?php echo ($donation_data['category'] == 'Youth Ministry') ? 'selected' : ''; ?>>Youth Ministry</option>
                                                        <option value="Special Project" <?php echo ($donation_data['category'] == 'Special Project') ? 'selected' : ''; ?>>Special Project</option>
                                                        <option value="Other" <?php echo ($donation_data['category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Campaign/Fund</td>
                                                <td>
                                                    <select id="campaign" name="campaign">
                                                        <option value="">Select Campaign</option>
                                                        <?php foreach($campaigns as $campaign): ?>
                                                            <option value="<?php echo $campaign; ?>" <?php echo ($campaign == $donation_data['campaign']) ? 'selected' : ''; ?>><?php echo $campaign; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <th colspan="2" class="section-header">
                                                    <i class="fas fa-file-alt"></i> Additional Information
                                                </th>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Reference Number</td>
                                                <td><input type="text" id="reference_number" name="reference_number" value="<?php echo $donation_data['reference_number']; ?>" placeholder="Check number, transaction ID, etc."></td>
                                            </tr>
                                            <tr>
                                                <td class="detail-label">Notes</td>
                                                <td><textarea id="notes" name="notes" rows="3" placeholder="Add any additional information about this donation"><?php echo $donation_data['notes']; ?></textarea></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <div class="form-actions">
                                        <a href="donations.php" class="btn btn-secondary">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Update Donation</button>
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
        const donationSearch = document.getElementById("donationSearch");
        if (donationSearch) {
            donationSearch.addEventListener("keyup", function() {
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

