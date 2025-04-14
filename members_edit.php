<?php
session_start();
require_once 'config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: members.php");
    exit();
}

$member_id = $_GET['id'];

// Get member data
$query = "SELECT * FROM members WHERE id = $member_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: members.php");
    exit();
}

$member = mysqli_fetch_assoc($result);

// Get ministries for dropdown
$query = "SELECT DISTINCT ministry FROM members WHERE ministry IS NOT NULL AND ministry != '' ORDER BY ministry";
$ministries_result = mysqli_query($conn, $query);
$ministries = array();
while ($row = mysqli_fetch_assoc($ministries_result)) {
    $ministries[] = $row['ministry'];
}

// Include header
$page_title = "Edit Member";
include 'includes/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-user-edit"></i> Edit Member: <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></h4>
                </div>
                <div class="card-body">
                    <form id="editMemberForm" action="member_process.php" method="post">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Personal Information</h5>
                                
                                <div class="form-group mb-3">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($member['first_name']); ?>" required>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($member['last_name']); ?>" required>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?php echo ($member['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo ($member['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                        <option value="Other" <?php echo ($member['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo $member['date_of_birth']; ?>">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="mb-3">Church Information</h5>
                                
                                <div class="form-group mb-3">
                                    <label for="membership_status">Membership Status <span class="text-danger">*</span></label>
                                    <select class="form-control" id="membership_status" name="membership_status" required>
                                        <option value="">Select Status</option>
                                        <option value="Active" <?php echo ($member['membership_status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="Inactive" <?php echo ($member['membership_status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                        <option value="Visitor" <?php echo ($member['membership_status'] == 'Visitor') ? 'selected' : ''; ?>>Visitor</option>
                                        <option value="New Member" <?php echo ($member['membership_status'] == 'New Member') ? 'selected' : ''; ?>>New Member</option>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="join_date">Join Date</label>
                                    <input type="date" class="form-control" id="join_date" name="join_date" value="<?php echo $member['join_date']; ?>">
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="ministry">Ministry</label>
                                    <input type="text" class="form-control" id="ministry" name="ministry" list="ministryList" value="<?php echo htmlspecialchars($member['ministry']); ?>">
                                    <datalist id="ministryList">
                                        <?php foreach ($ministries as $ministry): ?>
                                            <option value="<?php echo htmlspecialchars($ministry); ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                                
                                <h5 class="mt-4 mb-3">Address Information</h5>
                                
                                <div class="form-group mb-3">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($member['address']); ?>">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="city">City</label>
                                            <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($member['city']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="state">State</label>
                                            <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($member['state']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group mb-3">
                                            <label for="zip">ZIP</label>
                                            <input type="text" class="form-control" id="zip" name="zip" value="<?php echo htmlspecialchars($member['zip']); ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo htmlspecialchars($member['notes']); ?></textarea>
                        </div>
                        
                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Member</button>
                            <a href="members.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

