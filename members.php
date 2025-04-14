<?php
session_start();
require_once 'config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
   header("Location: login.php");
   exit();
}

// Handle member deletion
if (isset($_GET['delete'])) {
   $id = mysqli_real_escape_string($conn, $_GET['delete']);
   $query = "DELETE FROM members WHERE id = $id";
   
   if (mysqli_query($conn, $query)) {
       $_SESSION['success_message'] = "Member deleted successfully!";
       header("Location: members.php");
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

// Fetch all members
$query = "SELECT * FROM members ORDER BY last_name, first_name";
$members = mysqli_query($conn, $query);

// Default view is the list
$current_view = isset($_GET['view']) ? $_GET['view'] : 'list';
$member_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get member data if viewing or editing
$member_data = null;
if (($current_view == 'view' || $current_view == 'edit') && $member_id > 0) {
   $query = "SELECT * FROM members WHERE id = $member_id";
   $result = mysqli_query($conn, $query);
   if ($result && mysqli_num_rows($result) > 0) {
       $member_data = mysqli_fetch_assoc($result);
   } else {
       // Member not found, redirect to list
       $_SESSION['error_message'] = "Member not found";
       header("Location: members.php");
       exit();
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Members - Church Management System</title>
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
                   <h1><i class="fas fa-users"></i> Members Management</h1>
                   <?php if($current_view == 'list'): ?>
                       <a href="members.php?view=add" class="add-btn"><i class="fas fa-plus"></i> Add Member</a>
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
                       <!-- Members List View -->
                       <div class="report-table-container">
                           <div class="table-header">
                                <h3><i class="fas fa-list"></i> All Members</h3>
                                <div class="header-actions">
                                    <div class="search-container">
                                        <input type="text" id="memberSearch" placeholder="Search members...">
                                        <button type="submit"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                           
                           <div class="report-table-content">
                               <table class="report-table">
                                   <thead>
                                       <tr>
                                           <th>ID</th>
                                           <th>Name</th>
                                           <th>Email</th>
                                           <th>Phone</th>
                                           <th>Status</th>
                                           <th>Join Date</th>
                                           <th>Actions</th>
                                       </tr>
                                   </thead>
                                   <tbody>
                                       <?php while($member = mysqli_fetch_assoc($members)): ?>
                                           <tr>
                                               <td><?php echo $member['id']; ?></td>
                                               <td><?php echo $member['first_name'] . ' ' . $member['last_name']; ?></td>
                                               <td><?php echo $member['email']; ?></td>
                                               <td><?php echo $member['phone']; ?></td>
                                               <td><span class="status-badge <?php echo strtolower(str_replace(' ', '-', $member['membership_status'])); ?>"><?php echo $member['membership_status']; ?></span></td>
                                               <td><?php echo $member['join_date'] ? date('M d, Y', strtotime($member['join_date'])) : 'N/A'; ?></td>
                                               <td class="actions">
                                                   <a href="members.php?view=view&id=<?php echo $member['id']; ?>" class="view-btn" data-id="<?php echo $member['id']; ?>"><i class="fas fa-eye"></i></a>
                                                   <a href="members.php?view=edit&id=<?php echo $member['id']; ?>" class="edit-btn" data-id="<?php echo $member['id']; ?>"><i class="fas fa-edit"></i></a>
                                                   <a href="members.php?delete=<?php echo $member['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this member?');"><i class="fas fa-trash"></i></a>
                                               </td>
                                           </tr>
                                       <?php endwhile; ?>
                                   </tbody>
                               </table>
                           </div>
                       </div>
                   <?php elseif($current_view == 'add'): ?>
                       <!-- Add Member View -->
                       <div class="report-table-container">
                           <div class="table-header">
                               <h3><i class="fas fa-user-plus"></i> Add New Member</h3>
                               <a href="members.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to List</a>
                           </div>
                           
                           <div class="report-table-content">
                               <form action="member_process.php" method="post" enctype="multipart/form-data" class="plain-form">
                                   <input type="hidden" name="action" value="add">
                                   
                                   <table class="report-table">
                                       <tbody>
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-user"></i> Personal Information
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">First Name</td>
                                               <td><input type="text" id="first_name" name="first_name" required></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Last Name</td>
                                               <td><input type="text" id="last_name" name="last_name" required></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Date of Birth</td>
                                               <td><input type="date" id="date_of_birth" name="date_of_birth"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Gender</td>
                                               <td>
                                                   <select id="gender" name="gender">
                                                       <option value="">Select Gender</option>
                                                       <option value="Male">Male</option>
                                                       <option value="Female">Female</option>
                                                       <option value="Other">Other</option>
                                                   </select>
                                               </td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-address-card"></i> Contact Information
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Email</td>
                                               <td><input type="email" id="email" name="email"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Phone</td>
                                               <td><input type="text" id="phone" name="phone"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Address</td>
                                               <td><textarea id="address" name="address" rows="2"></textarea></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">City</td>
                                               <td><input type="text" id="city" name="city"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">State/Province</td>
                                               <td><input type="text" id="state" name="state"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">ZIP/Postal Code</td>
                                               <td><input type="text" id="zip" name="zip"></td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-church"></i> Church Information
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Membership Status</td>
                                               <td>
                                                   <select id="membership_status" name="membership_status" required>
                                                       <option value="">Select Status</option>
                                                       <option value="Active">Active</option>
                                                       <option value="Inactive">Inactive</option>
                                                       <option value="Visitor">Visitor</option>
                                                       <option value="New Member">New Member</option>
                                                   </select>
                                               </td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Join Date</td>
                                               <td><input type="date" id="join_date" name="join_date"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Ministry/Department</td>
                                               <td><input type="text" id="ministry" name="ministry"></td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-image"></i> Profile Photo
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Profile Photo</td>
                                               <td>
                                                   <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                                                   <div id="profile-photo-preview" class="image-preview-container">
                                                       <i class="fas fa-user"></i>
                                                   </div>
                                               </td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-sticky-note"></i> Additional Notes
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Notes</td>
                                               <td><textarea id="notes" name="notes" rows="3"></textarea></td>
                                           </tr>
                                       </tbody>
                                   </table>
                                   
                                   <div class="form-actions">
                                       <a href="members.php" class="btn btn-secondary">Cancel</a>
                                       <button type="submit" class="btn btn-primary">Add Member</button>
                                   </div>
                               </form>
                           </div>
                       </div>
                   <?php elseif($current_view == 'view' && $member_data): ?>
                       <!-- View Member Details -->
                       <div class="report-table-container">
                           <div class="table-header">
                               <h3><i class="fas fa-id-card"></i> Member Details</h3>
                               <div class="actions">
                                   <a href="members.php?view=edit&id=<?php echo $member_data['id']; ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                                   <a href="members.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to List</a>
                               </div>
                           </div>
                           
                           <div class="report-table-content">
                               <?php if($member_data['profile_photo'] && $member_data['profile_photo'] != 'default.jpg'): ?>
                                   <div class="member-photo">
                                       <img src="uploads/profile_photos/<?php echo $member_data['profile_photo']; ?>" alt="<?php echo $member_data['first_name'] . ' ' . $member_data['last_name']; ?>">
                                   </div>
                               <?php else: ?>
                                   <div class="member-photo">
                                       <img src="img/default-user.png" alt="Default Profile">
                                   </div>
                               <?php endif; ?>
                               
                               <table class="report-table">
                                   <tbody>
                                       <tr>
                                           <th colspan="2" class="section-header">
                                               <i class="fas fa-user"></i> Personal Information
                                           </th>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Full Name</td>
                                           <td><?php echo $member_data['first_name'] . ' ' . $member_data['last_name']; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Date of Birth</td>
                                           <td><?php echo $member_data['date_of_birth'] ? date('F d, Y', strtotime($member_data['date_of_birth'])) : 'N/A'; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Gender</td>
                                           <td><?php echo $member_data['gender'] ? $member_data['gender'] : 'N/A'; ?></td>
                                       </tr>
                                       
                                       <tr>
                                           <th colspan="2" class="section-header">
                                               <i class="fas fa-address-book"></i> Contact Information
                                           </th>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Email</td>
                                           <td><?php echo $member_data['email'] ? $member_data['email'] : 'N/A'; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Phone</td>
                                           <td><?php echo $member_data['phone'] ? $member_data['phone'] : 'N/A'; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Address</td>
                                           <td>
                                               <?php 
                                               $address_parts = array();
                                               if ($member_data['address']) $address_parts[] = $member_data['address'];
                                               if ($member_data['city']) $address_parts[] = $member_data['city'];
                                               if ($member_data['state']) $address_parts[] = $member_data['state'];
                                               if ($member_data['zip']) $address_parts[] = $member_data['zip'];
                                               
                                               echo count($address_parts) > 0 ? implode(', ', $address_parts) : 'N/A';
                                               ?>
                                           </td>
                                       </tr>
                                       
                                       <tr>
                                           <th colspan="2" class="section-header">
                                               <i class="fas fa-church"></i> Church Information
                                           </th>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Membership Status</td>
                                           <td><span class="status-badge <?php echo strtolower(str_replace(' ', '-', $member_data['membership_status'])); ?>"><?php echo $member_data['membership_status']; ?></span></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Join Date</td>
                                           <td><?php echo $member_data['join_date'] ? date('F d, Y', strtotime($member_data['join_date'])) : 'N/A'; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Ministry</td>
                                           <td><?php echo $member_data['ministry'] ? $member_data['ministry'] : 'N/A'; ?></td>
                                       </tr>
                                       
                                       <?php if ($member_data['notes']): ?>
                                       <tr>
                                           <th colspan="2" class="section-header">
                                               <i class="fas fa-sticky-note"></i> Notes
                                           </th>
                                       </tr>
                                       <tr>
                                           <td colspan="2"><?php echo nl2br($member_data['notes']); ?></td>
                                       </tr>
                                       <?php endif; ?>
                                       
                                       <tr>
                                           <th colspan="2" class="section-header">
                                               <i class="fas fa-clock"></i> System Information
                                           </th>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Member ID</td>
                                           <td><?php echo $member_data['id']; ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Created At</td>
                                           <td><?php echo date('F d, Y H:i:s', strtotime($member_data['created_at'])); ?></td>
                                       </tr>
                                       <tr>
                                           <td class="detail-label">Last Updated</td>
                                           <td><?php echo date('F d, Y H:i:s', strtotime($member_data['updated_at'])); ?></td>
                                       </tr>
                                   </tbody>
                               </table>
                           </div>
                       </div>
                   <?php elseif($current_view == 'edit' && $member_data): ?>
                       <!-- Edit Member View -->
                       <div class="report-table-container">
                           <div class="table-header">
                               <h3><i class="fas fa-edit"></i> Edit Member #<?php echo $member_data['id']; ?></h3>
                               <a href="members.php" class="back-button"><i class="fas fa-arrow-left"></i> Back to List</a>
                           </div>
                           
                           <div class="report-table-content">
                               <form action="member_process.php" method="post" enctype="multipart/form-data" class="plain-form">
                                   <input type="hidden" name="action" value="edit">
                                   <input type="hidden" name="member_id" value="<?php echo $member_data['id']; ?>">
                                   
                                   <table class="report-table">
                                       <tbody>
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-user"></i> Personal Information
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">First Name</td>
                                               <td><input type="text" id="first_name" name="first_name" value="<?php echo $member_data['first_name']; ?>" required></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Last Name</td>
                                               <td><input type="text" id="last_name" name="last_name" value="<?php echo $member_data['last_name']; ?>" required></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Date of Birth</td>
                                               <td><input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo $member_data['date_of_birth']; ?>"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Gender</td>
                                               <td>
                                                   <select id="gender" name="gender">
                                                       <option value="">Select Gender</option>
                                                       <option value="Male" <?php echo ($member_data['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                                       <option value="Female" <?php echo ($member_data['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                                       <option value="Other" <?php echo ($member_data['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                                   </select>
                                               </td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-address-card"></i> Contact Information
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Email</td>
                                               <td><input type="email" id="email" name="email" value="<?php echo $member_data['email']; ?>"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Phone</td>
                                               <td><input type="text" id="phone" name="phone" value="<?php echo $member_data['phone']; ?>"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Address</td>
                                               <td><textarea id="address" name="address" rows="2"><?php echo $member_data['address']; ?></textarea></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">City</td>
                                               <td><input type="text" id="city" name="city" value="<?php echo $member_data['city']; ?>"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">State/Province</td>
                                               <td><input type="text" id="state" name="state" value="<?php echo $member_data['state']; ?>"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">ZIP/Postal Code</td>
                                               <td><input type="text" id="zip" name="zip" value="<?php echo $member_data['zip']; ?>"></td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-church"></i> Church Information
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Membership Status</td>
                                               <td>
                                                   <select id="membership_status" name="membership_status" required>
                                                       <option value="">Select Status</option>
                                                       <option value="Active" <?php echo ($member_data['membership_status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                                       <option value="Inactive" <?php echo ($member_data['membership_status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                                       <option value="Visitor" <?php echo ($member_data['membership_status'] == 'Visitor') ? 'selected' : ''; ?>>Visitor</option>
                                                       <option value="New Member" <?php echo ($member_data['membership_status'] == 'New Member') ? 'selected' : ''; ?>>New Member</option>
                                                   </select>
                                               </td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Join Date</td>
                                               <td><input type="date" id="join_date" name="join_date" value="<?php echo $member_data['join_date']; ?>"></td>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Ministry/Department</td>
                                               <td><input type="text" id="ministry" name="ministry" value="<?php echo $member_data['ministry']; ?>"></td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-image"></i> Profile Photo
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Profile Photo</td>
                                               <td>
                                                   <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                                                   <div id="profile-photo-preview" class="image-preview-container">
                                                       <?php if($member_data['profile_photo'] && $member_data['profile_photo'] != 'default.jpg'): ?>
                                                           <img src="uploads/profile_photos/<?php echo $member_data['profile_photo']; ?>" alt="Profile">
                                                       <?php else: ?>
                                                           <i class="fas fa-user"></i>
                                                       <?php endif; ?>
                                                   </div>
                                               </td>
                                           </tr>
                                           
                                           <tr>
                                               <th colspan="2" class="section-header">
                                                   <i class="fas fa-sticky-note"></i> Additional Notes
                                               </th>
                                           </tr>
                                           <tr>
                                               <td class="detail-label">Notes</td>
                                               <td><textarea id="notes" name="notes" rows="3"><?php echo $member_data['notes']; ?></textarea></td>
                                           </tr>
                                       </tbody>
                                   </table>
                                   
                                   <div class="form-actions">
                                       <a href="members.php" class="btn btn-secondary">Cancel</a>
                                       <button type="submit" class="btn btn-primary">Update Member</button>
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
        const memberSearch = document.getElementById("memberSearch");
        if (memberSearch) {
            memberSearch.addEventListener("keyup", function() {
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

