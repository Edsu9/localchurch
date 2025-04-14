<?php
session_start();
require_once 'config/db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

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

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$ministry_filter = isset($_GET['ministry']) ? $_GET['ministry'] : '';

// Build query based on filters
$query = "SELECT id, first_name, last_name, email, phone, membership_status, join_date, ministry 
          FROM members 
          WHERE 1=1";

if ($ministry_filter) {
  $query .= " AND ministry = '" . mysqli_real_escape_string($conn, $ministry_filter) . "'";
}

$query .= " ORDER BY last_name, first_name";
$members = mysqli_query($conn, $query);

// Get all available membership statuses
$status_query = "SELECT DISTINCT membership_status FROM members ORDER BY membership_status";
$status_result = mysqli_query($conn, $status_query);

// Get all available ministries
$ministry_query = "SELECT DISTINCT ministry FROM members WHERE ministry != '' ORDER BY ministry";
$ministry_result = mysqli_query($conn, $ministry_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Member List Report - <?php echo $church_name; ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
      body {
          font-family: Arial, sans-serif;
          margin: 0;
          padding: 20px;
          color: #333;
          background-color: #f8f9fa;
      }
      
      .report-container {
          max-width: 1200px;
          margin: 0 auto;
          background-color: #fff;
          border-radius: 5px;
          box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
          overflow: hidden;
      }
      
      .report-header {
          background-color: #f8f9fa;
          padding: 20px;
          text-align: center;
          border-bottom: 1px solid #e9ecef;
      }
      
      .report-header h1 {
          font-size: 24px;
          margin: 0 0 5px;
          color: #333;
      }
      
      .report-header h2 {
          font-size: 18px;
          margin: 0 0 10px;
          color: #333;
      }
      
      .report-header p {
          margin: 5px 0;
          color: #666;
      }
      
      .report-date {
          font-style: italic;
          margin-top: 10px;
          font-size: 14px;
          color: #666;
      }
      
      .report-content {
          padding: 20px;
      }
      
      .filter-container {
          margin-bottom: 20px;
          padding: 15px;
          background-color: #f8f9fa;
          border-radius: 5px;
          border: 1px solid #e9ecef;
      }
      
      .filter-form {
          display: flex;
          flex-wrap: wrap;
          gap: 15px;
      }
      
      .filter-group {
          display: flex;
          align-items: center;
          gap: 10px;
      }
      
      .filter-label {
          font-weight: 500;
          color: #333;
      }
      
      .filter-select {
          padding: 8px 12px;
          border: 1px solid #ddd;
          border-radius: 4px;
          background-color: #fff;
      }
      
      .filter-button {
          padding: 8px 15px;
          background-color: #3498db;
          color: #fff;
          border: none;
          border-radius: 4px;
          cursor: pointer;
      }
      
      .filter-button:hover {
          background-color: #2980b9;
      }
      
      .member-table {
          width: 100%;
          border-collapse: collapse;
      }
      
      .member-table th {
          background-color: #f5f5f5;
          color: #333;
          font-weight: 600;
          text-align: left;
          padding: 12px 15px;
          border: 1px solid #ddd;
      }
      
      .member-table td {
          padding: 10px 15px;
          border: 1px solid #ddd;
      }
      
      .member-table tr:nth-child(even) {
          background-color: #f9f9f9;
      }
      
      .status-badge {
          display: inline-block;
          padding: 3px 8px;
          border-radius: 3px;
          font-size: 12px;
          font-weight: 500;
      }
      
      .status-badge.active {
          background-color: #e6f7ee;
          color: #27ae60;
      }
      
      .status-badge.inactive {
          background-color: #fbe9e7;
          color: #e74c3c;
      }
      
      .status-badge.visitor {
          background-color: #e3f2fd;
          color: #3498db;
      }
      
      .status-badge.new-member {
          background-color: #fff8e1;
          color: #f39c12;
      }
      
      .report-footer {
          margin-top: 30px;
          text-align: center;
          font-size: 12px;
          color: #666;
          padding-top: 15px;
          border-top: 1px solid #e9ecef;
      }
      
      .action-buttons {
          margin-bottom: 20px;
          display: flex;
          gap: 10px;
          justify-content: flex-end;
      }
      
      .action-button {
          padding: 8px 15px;
          background-color: #3498db;
          color: #fff;
          border: none;
          border-radius: 4px;
          cursor: pointer;
          text-decoration: none;
          display: inline-flex;
          align-items: center;
          font-size: 14px;
      }
      
      .action-button i {
          margin-right: 5px;
      }
      
      .action-button.print {
          background-color: #27ae60;
      }
      
      .action-button.back {
          background-color: #95a5a6;
      }
      
      @media print {
          body {
              background-color: #fff;
              padding: 0;
          }
          
          .report-container {
              box-shadow: none;
              border: none;
          }
          
          .filter-container,
          .action-buttons {
              display: none;
          }
          
          .member-table th {
              background-color: #f5f5f5 !important;
              color: #000 !important;
          }
      }
      
      @media (max-width: 768px) {
          .filter-form {
              flex-direction: column;
          }
          
          .filter-group {
              width: 100%;
          }
          
          .member-table {
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
      <a href="reports.php" class="action-button back"><i class="fas fa-arrow-left"></i> Back</a>
  </div>
  
  <div class="report-container">
      <div class="report-header">
          <h1><?php echo $church_name; ?></h1>
          <?php if($church_full_address): ?>
              <p><?php echo $church_full_address; ?></p>
          <?php endif; ?>
          <h2>Member List Report</h2>
          <p class="report-date">Generated on <?php echo date('F d, Y h:i A'); ?></p>
      </div>
      
      <div class="report-content">
          
<div class="filter-container">
    <form action="generate_member_list.php" method="get" class="filter-form">
        <div class="filter-group">
            <label class="filter-label">Ministry:</label>
            <select name="ministry" class="filter-select">
                <option value="">All Ministries</option>
                <?php while($ministry = mysqli_fetch_assoc($ministry_result)): ?>
                    <option value="<?php echo $ministry['ministry']; ?>" <?php echo ($ministry_filter == $ministry['ministry']) ? 'selected' : ''; ?>>
                        <?php echo $ministry['ministry']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <button type="submit" class="filter-button"><i class="fas fa-filter"></i> Apply Filter</button>
    </form>
</div>
          
          <table class="member-table">
              <thead>
                  <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Status</th>
                      <th>Join Date</th>
                      <th>Ministry</th>
                  </tr>
              </thead>
              <tbody>
                  <?php 
                  if (mysqli_num_rows($members) > 0) {
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
                          <td><?php echo $member['ministry'] ? $member['ministry'] : 'N/A'; ?></td>
                      </tr>
                  <?php 
                      endwhile;
                  } else {
                  ?>
                      <tr>
                          <td colspan="7" style="text-align: center;">No members found matching the selected criteria.</td>
                      </tr>
                  <?php
                  }
                  ?>
              </tbody>
          </table>
          
          <div class="report-footer">
              <p>This report was generated by the Church Management System on <?php echo date('F d, Y h:i:s A'); ?></p>
              <p>&copy; <?php echo date('Y'); ?> <?php echo $church_name; ?>. All rights reserved.</p>
          </div>
      </div>
  </div>
</body>
</html>

