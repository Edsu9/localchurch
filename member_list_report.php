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

// Pagination setup
$records_per_page = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $records_per_page;

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$search_condition = '';
if (!empty($search)) {
  $search_condition = " WHERE first_name LIKE '%$search%' OR last_name LIKE '%$search%' OR 
                        email LIKE '%$search%' OR phone LIKE '%$search%' OR ministry LIKE '%$search%'";
}

// Sorting
$sort_column = isset($_GET['sort']) ? mysqli_real_escape_string($conn, $_GET['sort']) : 'last_name';
$sort_order = isset($_GET['order']) && $_GET['order'] == 'desc' ? 'DESC' : 'ASC';
$allowed_columns = ['id', 'first_name', 'last_name', 'email', 'phone', 'join_date', 'ministry'];
if (!in_array($sort_column, $allowed_columns)) {
  $sort_column = 'last_name';
}

// Fetch members with pagination, search and sorting
$query = "SELECT id, first_name, last_name, email, phone, join_date, ministry FROM members$search_condition 
        ORDER BY $sort_column $sort_order LIMIT $offset, $records_per_page";
$members = mysqli_query($conn, $query);

// Get all members for print view (no pagination)
$all_members_query = "SELECT id, first_name, last_name, email, phone, join_date, ministry FROM members$search_condition 
                   ORDER BY last_name, first_name";
$all_members = mysqli_query($conn, $all_members_query);

// Get total records for pagination
$count_query = "SELECT COUNT(*) as total FROM members$search_condition";
$count_result = mysqli_query($conn, $count_query);
$count_row = mysqli_fetch_assoc($count_result);
$total_records = $count_row ? $count_row['total'] : 0;
$total_pages = ceil($total_records / $records_per_page);

// Check if download is requested
if (isset($_GET['download']) && $_GET['download'] == 'csv') {
  // Set headers for CSV download
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="member_list_report.csv"');
  
  // Create a file pointer connected to the output stream
  $output = fopen('php://output', 'w');
  
  // Output the column headings
  fputcsv($output, array('Name', 'Email', 'Phone', 'Join Date', 'Ministry'));
  
  // Get all records for CSV (no pagination)
  $csv_query = "SELECT id, first_name, last_name, email, phone, join_date, ministry FROM members$search_condition ORDER BY $sort_column $sort_order";
  $csv_result = mysqli_query($conn, $csv_query);
  
  // Fetch all records from database and write to CSV
  while ($member = mysqli_fetch_assoc($csv_result)) {
      $row = array(
          $member['first_name'] . ' ' . $member['last_name'],
          $member['email'],
          $member['phone'] ? $member['phone'] : '',
          $member['join_date'] ? date('M d, Y', strtotime($member['join_date'])) : '',
          $member['ministry'] ? $member['ministry'] : ''
      );
      fputcsv($output, $row);
  }
  
  // Close the file pointer
  fclose($output);
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Member List - <?php echo $church_name; ?></title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
      body {
          font-family: Arial, sans-serif;
          background-color: #f9f9f9;
          color: #333;
          margin: 0;
          padding: 0;
          line-height: 1.6;
      }
      
      .main-content {
          margin-left: 250px;
          padding: 20px;
          transition: margin-left 0.3s;
      }
      
      @media (max-width: 768px) {
          .sidebar {
              width: 0;
              overflow: hidden;
          }
          
          .main-content {
              margin-left: 0;
          }
          
          .menu-toggle {
              display: block;
          }
      }
      
      .card {
          background: #fff;
          border: 1px solid #ddd;
          border-radius: 4px;
          margin-bottom: 20px;
          overflow: hidden;
      }
      
      .card-header {
          background-color: #f5f5f5;
          padding: 15px 20px;
          border-bottom: 1px solid #ddd;
          display: flex;
          justify-content: space-between;
          align-items: center;
      }
      
      .card-title {
          margin: 0;
          font-size: 1.25rem;
          color: #333;
      }
      
      .card-body {
          padding: 20px;
      }
      
      .table-responsive {
          overflow-x: auto;
          -webkit-overflow-scrolling: touch;
      }
      
      table {
          width: 100%;
          border-collapse: collapse;
          margin-bottom: 1rem;
          background-color: transparent;
      }
      
      table th,
      table td {
          padding: 12px 15px;
          text-align: left;
          border-bottom: 1px solid #ddd;
      }
      
      table th {
          background-color: #f5f5f5;
          font-weight: 600;
          color: #333;
          white-space: nowrap;
          position: relative;
      }
      
      table tbody tr:hover {
          background-color: #f9f9f9;
      }
      
      .sort-icon {
          margin-left: 5px;
          color: #999;
      }
      
      .active-sort {
          color: #333;
      }
      
      .btn {
          display: inline-block;
          font-weight: 400;
          text-align: center;
          white-space: nowrap;
          vertical-align: middle;
          user-select: none;
          border: 1px solid #ddd;
          padding: 0.375rem 0.75rem;
          font-size: 0.9rem;
          line-height: 1.5;
          border-radius: 0.25rem;
          transition: all 0.15s ease-in-out;
          text-decoration: none;
          cursor: pointer;
          background-color: #fff;
          color: #333;
      }
      
      .btn:hover {
          background-color: #f5f5f5;
      }
      
      .btn-sm {
          padding: 0.25rem 0.5rem;
          font-size: 0.875rem;
          line-height: 1.5;
          border-radius: 0.2rem;
      }
      
      .action-bar {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 20px;
          flex-wrap: wrap;
          gap: 10px;
      }
      
      .search-container {
          position: relative;
          max-width: 300px;
          width: 100%;
      }
      
      .search-container input {
          width: 100%;
          padding: 10px 15px;
          padding-right: 40px;
          border: 1px solid #ddd;
          border-radius: 4px;
          font-size: 14px;
      }
      
      .search-container input:focus {
          outline: none;
          border-color: #aaa;
      }
      
      .search-container button {
          position: absolute;
          right: 0;
          top: 0;
          height: 100%;
          width: 40px;
          background: transparent;
          border: none;
          cursor: pointer;
          color: #666;
      }
      
      .pagination {
          display: flex;
          padding-left: 0;
          list-style: none;
          border-radius: 0.25rem;
          justify-content: center;
          margin-top: 20px;
          flex-wrap: wrap;
      }
      
      .page-item {
          margin: 0 2px;
      }
      
      .page-link {
          position: relative;
          display: block;
          padding: 0.5rem 0.75rem;
          margin-left: -1px;
          line-height: 1.25;
          color: #333;
          background-color: #fff;
          border: 1px solid #ddd;
          text-decoration: none;
      }
      
      .page-item.active .page-link {
          z-index: 1;
          color: #333;
          background-color: #f5f5f5;
          border-color: #ddd;
          font-weight: bold;
      }
      
      .page-item.disabled .page-link {
          color: #999;
          pointer-events: none;
          cursor: auto;
          background-color: #fff;
          border-color: #ddd;
      }
      
      .page-link:hover {
          background-color: #f5f5f5;
          border-color: #ddd;
      }
      
      .records-info {
          margin-bottom: 15px;
          color: #666;
          font-size: 0.9rem;
      }
      
      .limit-selector {
          display: flex;
          align-items: center;
          gap: 10px;
          margin-bottom: 15px;
      }
      
      .limit-selector label {
          margin-bottom: 0;
          color: #666;
          font-size: 0.9rem;
      }
      
      .limit-selector select {
          padding: 5px 10px;
          border: 1px solid #ddd;
          border-radius: 4px;
          background-color: #fff;
      }
      
      .text-muted {
          color: #777;
      }
      
      .empty-state {
          text-align: center;
          padding: 40px 20px;
          color: #666;
      }
      
      .empty-state i {
          font-size: 3rem;
          margin-bottom: 15px;
          color: #ddd;
      }
      
      .empty-state h3 {
          margin-bottom: 10px;
          font-weight: 500;
      }
      
      /* Print-only table that matches the image layout */
      .print-only {
          display: none;
      }
      
      /* Print styles */
      @media print {
          .no-print {
              display: none !important;
          }
          
          .print-only {
              display: block;
          }
          
          body {
              margin: 0;
              padding: 0;
              background: white;
              font-family: Arial, sans-serif;
          }
          
          .main-content {
              margin-left: 0;
              padding: 20px;
          }
          
          .card {
              border: none;
              box-shadow: none;
          }
          
          .card-body {
              padding: 0;
          }
          
          /* Hide the regular table */
          .table-responsive {
              display: none;
          }
          
          /* Style for the print table */
          .attendance-header {
              text-align: center;
              margin-bottom: 20px;
          }
          
          .attendance-header h1 {
              margin: 0;
              font-size: 18pt;
              font-weight: bold;
          }
          
          .attendance-header p {
              margin: 10px 0 0 0;
              font-size: 12pt;
          }
          
          .attendance-table {
              width: 100%;
              border-collapse: collapse;
          }
          
          .attendance-table th {
              background-color: #d3d3d3 !important;
              -webkit-print-color-adjust: exact;
              print-color-adjust: exact;
              font-weight: bold;
              text-align: center;
              padding: 8px;
              border: 1px solid #000;
          }
          
          .attendance-table td {
              border: 1px solid #000;
              padding: 8px;
              height: 30px;
          }
          
          .attendance-table .no-column {
              width: 10%;
          }
          
          .attendance-table .name-column {
              width: 90%;
          }
        
        .print-pagination {
            margin-top: 20px;
            text-align: center;
            font-size: 10pt;
            page-break-before: avoid;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .attendance-table th {
            background-color: #d3d3d3 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            border: 1px solid #000;
        }
        
        .attendance-table td {
            border: 1px solid #000;
            padding: 8px;
            height: 30px;
        }
        
        .attendance-table .no-column {
            width: 10%;
        }
        
        .attendance-table .name-column {
            width: 90%;
        }
        
        .attendance-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .attendance-header h1 {
            margin: 0;
            font-size: 18pt;
            font-weight: bold;
        }
        
        .attendance-header p {
            margin: 10px 0 0 0;
            font-size: 12pt;
        }
      }
      
      /* Responsive adjustments */
      @media (max-width: 576px) {
          .action-bar {
              flex-direction: column;
              align-items: stretch;
          }
          
          .search-container {
              max-width: 100%;
          }
          
          .btn-group {
              display: flex;
              flex-wrap: wrap;
              gap: 5px;
          }
          
          table {
              font-size: 0.85rem;
          }
          
          table th, table td {
              padding: 8px 10px;
          }
      }
  </style>
</head>
<body>
  <div class="sidebar no-print">
      <div class="logo">
          <img src="img/jia.png" alt="JIA Somal-ot Logo" style="max-width: 50%;">
          <h2>JIA Somal-ot</h2>
      </div>
      <ul class="nav-links">
          <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
          <li><a href="members.php"><i class="fas fa-users"></i> Members</a></li>
          <li><a href="events.php"><i class="fas fa-calendar"></i> Events</a></li>
          <li><a href="donations.php"><i class="fas fa-hand-holding-heart"></i> Donations</a></li>
          <li><a href="reports.php" class="active"><i class="fas fa-chart-bar"></i> Reports</a></li>
          <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
          <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
      </ul>
  </div>
  
  <div class="main-content">
      <div class="card">
          <div class="card-header">
              <h1 class="card-title">Member List</h1>
              <div class="btn-group no-print">
                  <button onclick="window.print()" class="btn btn-sm">
                      <i class="fas fa-print"></i> Print
                  </button>
                  <a href="member_list_report.php?download=csv<?php echo !empty($search) ? '&search='.$search : ''; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>" class="btn btn-sm">
                      <i class="fas fa-download"></i> Export CSV
                  </a>
                  <a href="reports.php" class="btn btn-sm">
                      <i class="fas fa-arrow-left"></i> Back
                  </a>
              </div>
          </div>
          <div class="card-body">
              <!-- Add print page navigation -->
              <div class="action-bar no-print">
                  <form action="" method="GET" class="search-container">
                      <input type="text" name="search" placeholder="Search members..." value="<?php echo htmlspecialchars($search); ?>">
                      <button type="submit"><i class="fas fa-search"></i></button>
                  </form>
                  
                  <div class="limit-selector">
                      <label for="limit">Show:</label>
                      <select name="limit" id="limit" onchange="changeLimit(this.value)">
                          <option value="10" <?php echo $records_per_page == 10 ? 'selected' : ''; ?>>10</option>
                          <option value="25" <?php echo $records_per_page == 25 ? 'selected' : ''; ?>>25</option>
                          <option value="50" <?php echo $records_per_page == 50 ? 'selected' : ''; ?>>50</option>
                          <option value="100" <?php echo $records_per_page == 100 ? 'selected' : ''; ?>>100</option>
                      </select>
                  </div>
                  
                  <?php
                  // Calculate total print pages
                  $rows_per_print_page = 10;
                  $total_print_pages = ceil($total_records / $rows_per_print_page);
                  if ($total_print_pages > 1):
                  ?>
                  <div class="print-page-selector">
                      <label for="print_page">Print Page:</label>
                      <select name="print_page" id="print_page" onchange="changePrintPage(this.value)">
                          <?php for ($i = 1; $i <= $total_print_pages; $i++): ?>
                              <option value="<?php echo $i; ?>" <?php echo (isset($_GET['print_page']) && $_GET['print_page'] == $i) ? 'selected' : ''; ?>>
                                  Page <?php echo $i; ?> of <?php echo $total_print_pages; ?>
                              </option>
                          <?php endfor; ?>
                      </select>
                  </div>
                  <?php endif; ?>
              </div>
              
              <div class="records-info no-print">
                  <?php
                  $start_record = min(($page - 1) * $records_per_page + 1, $total_records);
                  $end_record = min($start_record + $records_per_page - 1, $total_records);
                  if ($total_records > 0) {
                      echo "Showing $start_record to $end_record of $total_records members";
                      if (!empty($search)) {
                          $total_all_members = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM members"));
                          echo " (filtered from total " . $total_all_members . " records)";
                      }
                  } else {
                      echo "No members found";
                      if (!empty($search)) {
                          echo " matching '$search'";
                      }
                  }
                  ?>
              </div>
              
              <!-- Regular table for screen view -->
              <div class="table-responsive">
                  <table>
                      <thead>
                          <tr>
                              <th>
                                  <a href="?sort=id&order=<?php echo $sort_column == 'id' && $sort_order == 'ASC' ? 'desc' : 'asc'; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>&limit=<?php echo $records_per_page; ?>&page=<?php echo $page; ?>">
                                      ID
                                      <?php if ($sort_column == 'id'): ?>
                                          <i class="fas fa-sort-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?> active-sort"></i>
                                      <?php else: ?>
                                          <i class="fas fa-sort sort-icon"></i>
                                      <?php endif; ?>
                                  </a>
                              </th>
                              <th>
                                  <a href="?sort=last_name&order=<?php echo $sort_column == 'last_name' && $sort_order == 'ASC' ? 'desc' : 'asc'; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>&limit=<?php echo $records_per_page; ?>&page=<?php echo $page; ?>">
                                      Name
                                      <?php if ($sort_column == 'last_name'): ?>
                                          <i class="fas fa-sort-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?> active-sort"></i>
                                      <?php else: ?>
                                          <i class="fas fa-sort sort-icon"></i>
                                      <?php endif; ?>
                                  </a>
                              </th>
                              <th>
                                  <a href="?sort=email&order=<?php echo $sort_column == 'email' && $sort_order == 'ASC' ? 'desc' : 'asc'; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>&limit=<?php echo $records_per_page; ?>&page=<?php echo $page; ?>">
                                      Email
                                      <?php if ($sort_column == 'email'): ?>
                                          <i class="fas fa-sort-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?> active-sort"></i>
                                      <?php else: ?>
                                          <i class="fas fa-sort sort-icon"></i>
                                      <?php endif; ?>
                                  </a>
                              </th>
                              <th>
                                  <a href="?sort=phone&order=<?php echo $sort_column == 'phone' && $sort_order == 'ASC' ? 'desc' : 'asc'; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>&limit=<?php echo $records_per_page; ?>&page=<?php echo $page; ?>">
                                      Phone
                                      <?php if ($sort_column == 'phone'): ?>
                                          <i class="fas fa-sort-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?> active-sort"></i>
                                      <?php else: ?>
                                          <i class="fas fa-sort sort-icon"></i>
                                      <?php endif; ?>
                                  </a>
                              </th>
                              <th>
                                  <a href="?sort=join_date&order=<?php echo $sort_column == 'join_date' && $sort_order == 'ASC' ? 'desc' : 'asc'; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>&limit=<?php echo $records_per_page; ?>&page=<?php echo $page; ?>">
                                      Join Date
                                      <?php if ($sort_column == 'join_date'): ?>
                                          <i class="fas fa-sort-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?> active-sort"></i>
                                      <?php else: ?>
                                          <i class="fas fa-sort sort-icon"></i>
                                      <?php endif; ?>
                                  </a>
                              </th>
                              <th>
                                  <a href="?sort=ministry&order=<?php echo $sort_column == 'ministry' && $sort_order == 'ASC' ? 'desc' : 'asc'; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>&limit=<?php echo $records_per_page; ?>&page=<?php echo $page; ?>">
                                      Ministry
                                      <?php if ($sort_column == 'ministry'): ?>
                                          <i class="fas fa-sort-<?php echo $sort_order == 'ASC' ? 'up' : 'down'; ?> active-sort"></i>
                                      <?php else: ?>
                                          <i class="fas fa-sort sort-icon"></i>
                                      <?php endif; ?>
                                  </a>
                              </th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php if ($members && mysqli_num_rows($members) > 0): ?>
                              <?php while($member = mysqli_fetch_assoc($members)): ?>
                                  <tr>
                                      <td><?php echo $member['id']; ?></td>
                                      <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                                      <td><?php echo htmlspecialchars($member['email']); ?></td>
                                      <td><?php echo $member['phone'] ? htmlspecialchars($member['phone']) : 'N/A'; ?></td>
                                      <td><?php echo $member['join_date'] ? date('M d, Y', strtotime($member['join_date'])) : 'N/A'; ?></td>
                                      <td><?php echo $member['ministry'] ? htmlspecialchars($member['ministry']) : 'N/A'; ?></td>
                                  </tr>
                              <?php endwhile; ?>
                          <?php else: ?>
                              <tr>
                                  <td colspan="6" class="empty-state">
                                      <i class="fas fa-users"></i>
                                      <h3>No members found</h3>
                                      <p>
                                          <?php if (!empty($search)): ?>
                                              No members match your search criteria. Try a different search or <a href="member_list_report.php">view all members</a>.
                                          <?php else: ?>
                                              There are no members in the database yet.
                                          <?php endif; ?>
                                      </p>
                                  </td>
                              </tr>
                          <?php endif; ?>
                      </tbody>
                  </table>
              </div>
              
              <!-- Print-only table that matches the image layout -->
              <div class="print-only">
                  <div class="attendance-header">
                      <h1><?php echo htmlspecialchars($church_name); ?></h1>
                      <p>ATTENDANCE SHEET: _______________</p>
                  </div>
                  <table class="attendance-table">
                      <thead>
                          <tr>
                              <th class="no-column">No.</th>
                              <th class="name-column">FULL NAME</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php 
                          $count = 0;
                          $rows_per_page = 10;
                          $current_page = isset($_GET['print_page']) ? intval($_GET['print_page']) : 1;
                          $start_index = ($current_page - 1) * $rows_per_page;
                          
                          if ($all_members && mysqli_num_rows($all_members) > 0):
                              // Make sure start index is valid
                              $total_members = mysqli_num_rows($all_members);
                              $start_index = min($start_index, max(0, $total_members - 1));
                              
                              // Move the result pointer to the start index
                              if ($start_index > 0 && $total_members > $start_index) {
                                  mysqli_data_seek($all_members, $start_index);
                              }
                              
                              // Display only 10 members per page
                              while($count < $rows_per_page && ($member = mysqli_fetch_assoc($all_members))): 
                                  $count++;
                          ?>
                              <tr>
                                  <td><?php echo $start_index + $count; ?></td>
                                  <td><?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?></td>
                              </tr>
                          <?php 
                              endwhile;
                          endif;
                          
                          // Add empty rows to fill the page (10 rows total)
                          $empty_rows = $rows_per_page - $count;
                          if ($empty_rows > 0):
                              for ($i = 0; $i < $empty_rows; $i++):
                          ?>
                              <tr>
                                  <td><?php echo $start_index + $count + $i + 1; ?></td>
                                  <td></td>
                              </tr>
                          <?php 
                              endfor;
                          endif;
                          ?>
                      </tbody>
                  </table>
                  
                  <?php if ($total_print_pages > 1): ?>
                  <div class="print-pagination">
                      <p>Page <?php echo $current_page; ?> of <?php echo $total_print_pages; ?></p>
                      <?php if ($current_page < $total_print_pages): ?>
                          <p>Please print the next page for additional members.</p>
                      <?php endif; ?>
                  </div>
                  <?php endif; ?>
              </div>
              
              <?php if ($total_pages > 1): ?>
                  <nav class="no-print">
                      <ul class="pagination">
                          <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                              <a class="page-link" href="?page=1<?php echo !empty($search) ? '&search='.$search : ''; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&limit=<?php echo $records_per_page; ?>">
                                  <i class="fas fa-angle-double-left"></i>
                              </a>
                          </li>
                          <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                              <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&limit=<?php echo $records_per_page; ?>">
                                  <i class="fas fa-angle-left"></i>
                              </a>
                          </li>
                          
                          <?php
                          $start_page = max(1, $page - 2);
                          $end_page = min($start_page + 4, $total_pages);
                          if ($end_page - $start_page < 4 && $start_page > 1) {
                              $start_page = max(1, $end_page - 4);
                          }
                          
                          for ($i = $start_page; $i <= $end_page; $i++):
                          ?>
                              <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                  <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&limit=<?php echo $records_per_page; ?>">
                                      <?php echo $i; ?>
                                  </a>
                              </li>
                          <?php endfor; ?>
                          
                          <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                              <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&limit=<?php echo $records_per_page; ?>">
                                  <i class="fas fa-angle-right"></i>
                              </a>
                          </li>
                          <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                              <a class="page-link" href="?page=<?php echo $total_pages; ?><?php echo !empty($search) ? '&search='.$search : ''; ?>&sort=<?php echo $sort_column; ?>&order=<?php echo $sort_order; ?>&limit=<?php echo $records_per_page; ?>">
                                  <i class="fas fa-angle-double-right"></i>
                              </a>
                          </li>
                      </ul>
                  </nav>
              <?php endif; ?>
              
              <div class="records-info">
                  <p>Total Members: <?php echo $total_records; ?></p>
              </div>
          </div>
      </div>
  </div>

  <script>
function changeLimit(limit) {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('limit', limit);
    currentUrl.searchParams.set('page', 1); // Reset to first page when changing limit
    window.location.href = currentUrl.toString();
}

function changePrintPage(page) {
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('print_page', page);
    window.location.href = currentUrl.toString();
}
</script>
</body>
</html>

