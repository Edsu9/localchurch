<?php
session_start();
require_once 'config/db.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Alignment as PptAlignment;

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Get report parameters
$report_type = isset($_GET['type']) ? $_GET['type'] : 'donations';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$export_format = isset($_GET['format']) ? $_GET['format'] : 'xlsx';
$member_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get report data based on type
if ($report_type == 'donations') {
  $title = 'Donations Report';
  
  // Get donation data
  if ($member_id > 0) {
    $query = "SELECT d.id, d.amount, d.donation_date, d.payment_method, d.category, 
              CONCAT(m.first_name, ' ', m.last_name) as member_name, d.campaign
              FROM donations d
              LEFT JOIN members m ON d.member_id = m.id
              WHERE d.member_id = $member_id AND d.donation_date BETWEEN '$start_date' AND '$end_date'
              ORDER BY d.donation_date DESC";
  } else {
    $query = "SELECT d.id, d.amount, d.donation_date, d.payment_method, d.category, 
              CONCAT(m.first_name, ' ', m.last_name) as member_name, d.campaign
              FROM donations d
              LEFT JOIN members m ON d.member_id = m.id
              WHERE d.donation_date BETWEEN '$start_date' AND '$end_date'
              ORDER BY d.donation_date DESC";
  }
  
  $result = mysqli_query($conn, $query);
  
  // Get donation statistics
  $query = "SELECT 
              COUNT(*) as total_donations,
              SUM(amount) as total_amount,
              AVG(amount) as average_amount,
              MAX(amount) as highest_donation,
              MIN(amount) as lowest_donation
            FROM donations
            WHERE donation_date BETWEEN '$start_date' AND '$end_date'";
  if ($member_id > 0) {
    $query .= " AND member_id = $member_id";
  }
  $stats = mysqli_fetch_assoc(mysqli_query($conn, $query));
  
} elseif ($report_type == 'members') {
  $title = 'Members Report';
  
  // Get member data
  if ($member_id > 0) {
    $query = "SELECT m.id, CONCAT(m.first_name, ' ', m.last_name) as name, 
              m.email, m.phone, m.membership_status, m.join_date, m.ministry,
              m.address, m.city, m.state, m.zip, m.gender, m.date_of_birth, m.notes
              FROM members m
              WHERE m.id = $member_id";
  } else {
    $query = "SELECT m.id, CONCAT(m.first_name, ' ', m.last_name) as name, 
              m.email, m.phone, m.membership_status, m.join_date, m.ministry
              FROM members m
              ORDER BY m.last_name, m.first_name";
  }
  
  $result = mysqli_query($conn, $query);
  
  // Get member statistics
  $query = "SELECT 
              COUNT(*) as total_members,
              SUM(CASE WHEN membership_status = 'Active' THEN 1 ELSE 0 END) as active_members,
              SUM(CASE WHEN membership_status = 'Inactive' THEN 1 ELSE 0 END) as inactive_members,
              SUM(CASE WHEN membership_status = 'Visitor' THEN 1 ELSE 0 END) as visitors,
              SUM(CASE WHEN membership_status = 'New Member' THEN 1 ELSE 0 END) as new_members
            FROM members";
  $stats = mysqli_fetch_assoc(mysqli_query($conn, $query));
  
} elseif ($report_type == 'events') {
  $title = 'Events Report';
  
  // Get event data
  if (isset($_GET['id'])) {
    $event_id = intval($_GET['id']);
    $query = "SELECT id, event_name, event_date, location, event_type, status, 
              description, organizer, contact_person, contact_email, contact_phone
              FROM events
              WHERE id = $event_id";
  } else {
    $query = "SELECT id, event_name, event_date, location, event_type, status
              FROM events
              WHERE event_date BETWEEN '$start_date' AND '$end_date'
              ORDER BY event_date DESC";
  }
  
  $result = mysqli_query($conn, $query);
  
  // Get event statistics
  $query = "SELECT 
              COUNT(*) as total_events,
              SUM(CASE WHEN status = 'Scheduled' THEN 1 ELSE 0 END) as scheduled_events,
              SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_events,
              SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) as cancelled_events,
              SUM(CASE WHEN status = 'Postponed' THEN 1 ELSE 0 END) as postponed_events
            FROM events
            WHERE event_date BETWEEN '$start_date' AND '$end_date'";
  $stats = mysqli_fetch_assoc(mysqli_query($conn, $query));
}

// Generate report based on format
if ($export_format == 'xlsx' || $export_format == 'csv') {
  // Create new spreadsheet
  $spreadsheet = new Spreadsheet();
  $sheet = $spreadsheet->getActiveSheet();
  
  // Set document properties
  $spreadsheet->getProperties()
    ->setCreator('Church Management System')
    ->setLastModifiedBy('Church Management System')
    ->setTitle('Church Report')
    ->setSubject('Church ' . ucfirst($report_type) . ' Report')
    ->setDescription('Report generated on ' . date('Y-m-d H:i:s'))
    ->setKeywords('church report ' . $report_type)
    ->setCategory('Reports');
  
  // Set default font
  $spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
  
  // Set title
  $sheet->setCellValue('A1', 'JIA Somal-ot Church');
  $sheet->setCellValue('A2', $title);
  $sheet->setCellValue('A3', 'Period: ' . date('M d, Y', strtotime($start_date)) . ' to ' . date('M d, Y', strtotime($end_date)));
  
  // Style the title
  $sheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(16);
  $sheet->getStyle('A2:F2')->getFont()->setBold(true)->setSize(14);
  $sheet->getStyle('A3:F3')->getFont()->setItalic(true);
  
  // Merge cells for title
  $sheet->mergeCells('A1:F1');
  $sheet->mergeCells('A2:F2');
  $sheet->mergeCells('A3:F3');
  
  // Center align the title
  $sheet->getStyle('A1:F3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
  
  // Set column headers based on report type
  $row = 5;
  if ($report_type == 'donations') {
    $sheet->setCellValue('A' . $row, 'ID');
    $sheet->setCellValue('B' . $row, 'Member Name');
    $sheet->setCellValue('C' . $row, 'Amount');
    $sheet->setCellValue('D' . $row, 'Date');
    $sheet->setCellValue('E' . $row, 'Payment Method');
    $sheet->setCellValue('F' . $row, 'Category');
    $sheet->setCellValue('G' . $row, 'Campaign');
    $lastColumn = 'G';
  } elseif ($report_type == 'members') {
    if ($member_id > 0) {
      // Single member detailed report
      $sheet->setCellValue('A' . $row, 'Field');
      $sheet->setCellValue('B' . $row, 'Value');
      $lastColumn = 'B';
    } else {
      // All members list
      $sheet->setCellValue('A' . $row, 'ID');
      $sheet->setCellValue('B' . $row, 'Name');
      $sheet->setCellValue('C' . $row, 'Email');
      $sheet->setCellValue('D' . $row, 'Phone');
      $sheet->setCellValue('E' . $row, 'Status');
      $sheet->setCellValue('F' . $row, 'Join Date');
      $sheet->setCellValue('G' . $row, 'Ministry');
      $lastColumn = 'G';
    }
  } elseif ($report_type == 'events') {
    if (isset($_GET['id'])) {
      // Single event detailed report
      $sheet->setCellValue('A' . $row, 'Field');
      $sheet->setCellValue('B' . $row, 'Value');
      $lastColumn = 'B';
    } else {
      // All events list
      $sheet->setCellValue('A' . $row, 'ID');
      $sheet->setCellValue('B' . $row, 'Event Name');
      $sheet->setCellValue('C' . $row, 'Date');
      $sheet->setCellValue('D' . $row, 'Location');
      $sheet->setCellValue('E' . $row, 'Type');
      $sheet->setCellValue('F' . $row, 'Status');
      $lastColumn = 'F';
    }
  }
  
  
  // Style the header row
  $headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4472C4'],
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000'],
        ],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
  ];
  
  $sheet->getStyle('A' . $row . ':' . $lastColumn . $row)->applyFromArray($headerStyle);
  
  // Add data to spreadsheet
  $row++;
  $totalAmount = 0;
  
  if ($report_type == 'donations') {
    while ($data = mysqli_fetch_assoc($result)) {
      $sheet->setCellValue('A' . $row, $data['id']);
      $sheet->setCellValue('B' . $row, $data['member_name']);
      $sheet->setCellValue('C' . $row, $data['amount']);
      $sheet->setCellValue('D' . $row, date('M d, Y', strtotime($data['donation_date'])));
      $sheet->setCellValue('E' . $row, $data['payment_method']);
      $sheet->setCellValue('F' . $row, $data['category']);
      $sheet->setCellValue('G' . $row, $data['campaign'] ? $data['campaign'] : 'N/A');
      
      $totalAmount += $data['amount'];
      $row++;
    }
    
    // Add totals
    $row++;
    $sheet->setCellValue('B' . $row, 'Total:');
    $sheet->setCellValue('C' . $row, $totalAmount);
    $sheet->getStyle('B' . $row . ':C' . $row)->getFont()->setBold(true);
    $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
    
    // Format currency
    $sheet->getStyle('C6:C' . ($row-1))->getNumberFormat()->setFormatCode('$#,##0.00');
    
  } elseif ($report_type == 'members') {
    if ($member_id > 0) {
      // Single member detailed report
      $member = mysqli_fetch_assoc($result);
      
      $sheet->setCellValue('A' . $row, 'ID');
      $sheet->setCellValue('B' . $row, $member['id']);
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Name');
      $sheet->setCellValue('B' . $row, $member['name']);
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Email');
      $sheet->setCellValue('B' . $row, $member['email']);
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Phone');
      $sheet->setCellValue('B' . $row, $member['phone']);
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Membership Status');
      $sheet->setCellValue('B' . $row, $member['membership_status']);
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Join Date');
      $sheet->setCellValue('B' . $row, $member['join_date'] ? date('M d, Y', strtotime($member['join_date'])) : 'N/A');
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Ministry');
      $sheet->setCellValue('B' . $row, $member['ministry'] ? $member['ministry'] : 'N/A');
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Gender');
      $sheet->setCellValue('B' . $row, $member['gender'] ? $member['gender'] : 'N/A');
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Date of Birth');
      $sheet->setCellValue('B' . $row, $member['date_of_birth'] ? date('M d, Y', strtotime($member['date_of_birth'])) : 'N/A');
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Address');
      $address = '';
      if ($member['address']) $address .= $member['address'];
      if ($member['city']) $address .= ($address ? ', ' : '') . $member['city'];
      if ($member['state']) $address .= ($address ? ', ' : '') . $member['state'];
      if ($member['zip']) $address .= ($address ? ' ' : '') . $member['zip'];
      $sheet->setCellValue('B' . $row, $address ? $address : 'N/A');
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Notes');
      $sheet->setCellValue('B' . $row, $member['notes'] ? $member['notes'] : 'N/A');
      
      // Style the field names
      $sheet->getStyle('A6:A' . $row)->getFont()->setBold(true);
      
    } else {
      // All members list
      while ($data = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue('A' . $row, $data['id']);
        $sheet->setCellValue('B' . $row, $data['name']);
        $sheet->setCellValue('C' . $row, $data['email']);
        $sheet->setCellValue('D' . $row, $data['phone']);
        $sheet->setCellValue('E' . $row, $data['membership_status']);
        $sheet->setCellValue('F' . $row, $data['join_date'] ? date('M d, Y', strtotime($data['join_date'])) : 'N/A');
        $sheet->setCellValue('G' . $row, $data['ministry'] ? $data['ministry'] : 'N/A');
        $row++;
      }
    }
    
  } elseif ($report_type == 'events') {
    if (isset($_GET['id'])) {
      // Single event detailed report
      $event = mysqli_fetch_assoc($result);
      
      $sheet->setCellValue('A' . $row, 'ID');
      $sheet->setCellValue('B' . $row, $event['id']);
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Event Name');
      $sheet->setCellValue('B' . $row, $event['event_name']);
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Date');
      $sheet->setCellValue('B' . $row, date('M d, Y', strtotime($event['event_date'])));
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Location');
      $sheet->setCellValue('B' . $row, $event['location']);
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Type');
      $sheet->setCellValue('B' . $row, $event['event_type']);
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Status');
      $sheet->setCellValue('B' . $row, $event['status']);
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Organizer');
      $sheet->setCellValue('B' . $row, $event['organizer'] ? $event['organizer'] : 'N/A');
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Contact Person');
      $sheet->setCellValue('B' . $row, $event['contact_person'] ? $event['contact_person'] : 'N/A');
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Contact Email');
      $sheet->setCellValue('B' . $row, $event['contact_email'] ? $event['contact_email'] : 'N/A');
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Contact Phone');
      $sheet->setCellValue('B' . $row, $event['contact_phone'] ? $event['contact_phone'] : 'N/A');
      $row++;
      
      $sheet->setCellValue('A' . $row, 'Description');
      $sheet->setCellValue('B' . $row, $event['description'] ? $event['description'] : 'N/A');
      
      // Style the field names
      $sheet->getStyle('A6:A' . $row)->getFont()->setBold(true);
      
    } else {
      // All events list
      while ($data = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue('A' . $row, $data['id']);
        $sheet->setCellValue('B' . $row, $data['event_name']);
        $sheet->setCellValue('C' . $row, date('M d, Y', strtotime($data['event_date'])));
        $sheet->setCellValue('D' . $row, $data['location']);
        $sheet->setCellValue('E' . $row, $data['event_type']);
        $sheet->setCellValue('F' . $row, $data['status']);
        $row++;
      }
    }
  }
  
  // Auto-size columns
  foreach (range('A', $lastColumn) as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
  }
  
  // Set borders for data
  $dataBorders = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => 'CCCCCC'],
        ],
    ],
  ];
  $sheet->getStyle('A5:' . $lastColumn . ($row))->applyFromArray($dataBorders);
  
  // Zebra striping for rows
  for ($i = 6; $i <= $row; $i += 2) {
    $sheet->getStyle('A' . $i . ':' . $lastColumn . $i)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->setStartColor(['rgb' => 'F2F2F2']);
  }
  
  // Output based on format
  if ($export_format == 'xlsx') {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $report_type . '_report_' . date('Y-m-d') . '.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
  } else { // csv
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="' . $report_type . '_report_' . date('Y-m-d') . '.csv"');
    header('Cache-Control: max-age=0');
    
    $writer = new Csv($spreadsheet);
    $writer->save('php://output');
  }
  
} elseif ($export_format == 'pdf') {
  // For PDF, we'll use MPDF library
  $mpdf = new \Mpdf\Mpdf([
    'margin_left' => 15,
    'margin_right' => 15,
    'margin_top' => 15,
    'margin_bottom' => 15,
  ]);
  
  // Add title
  $mpdf->SetTitle($title);
  
  // Start building HTML content
  $html = '
  <style>
    body { font-family: Arial, sans-serif; }
    h1 { text-align: center; font-size: 18pt; margin-bottom: 5px; }
    h2 { text-align: center; font-size: 14pt; margin-top: 0; margin-bottom: 5px; }
    .period { text-align: center; font-style: italic; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th { background-color: #4472C4; color: white; font-weight: bold; text-align: center; padding: 8px; }
    td { padding: 8px; border: 1px solid #CCCCCC; }
    tr:nth-child(even) { background-color: #F2F2F2; }
    .total { font-weight: bold; }
    .field-name { font-weight: bold; width: 30%; }
  </style>
  <body>
    <h1>JIA Somal-ot</h1>
    <h2>' . $title . '</h2>
    <div class="period">Period: ' . date('M d, Y', strtotime($start_date)) . ' to ' . date('M d, Y', strtotime($end_date)) . '</div>
  ';
  
  // Add report content based on type
  if ($report_type == 'donations') {
    $html .= '
    <table>
      <tr>
        <th>ID</th>
        <th>Member Name</th>
        <th>Amount</th>
        <th>Date</th>
        <th>Payment Method</th>
        <th>Category</th>
        <th>Campaign</th>
      </tr>
    ';
    
    mysqli_data_seek($result, 0);
    $totalAmount = 0;
    
    while ($data = mysqli_fetch_assoc($result)) {
      $html .= '
      <tr>
        <td>' . $data['id'] . '</td>
        <td>' . $data['member_name'] . '</td>
        <td>$' . number_format($data['amount'], 2) . '</td>
        <td>' . date('M d, Y', strtotime($data['donation_date'])) . '</td>
        <td>' . $data['payment_method'] . '</td>
        <td>' . $data['category'] . '</td>
        <td>' . ($data['campaign'] ? $data['campaign'] : 'N/A') . '</td>
      </tr>
      ';
      
      $totalAmount += $data['amount'];
    }
    
    $html .= '
      <tr>
        <td colspan="2" class="total">Total:</td>
        <td class="total">$' . number_format($totalAmount, 2) . '</td>
        <td colspan="4"></td>
      </tr>
    </table>
    ';
    
  } elseif ($report_type == 'members') {
    if ($member_id > 0) {
      // Single member detailed report
      $member = mysqli_fetch_assoc($result);
      
      $html .= '
      <table>
        <tr>
          <th>Field</th>
          <th>Value</th>
        </tr>
        <tr>
          <td class="field-name">ID</td>
          <td>' . $member['id'] . '</td>
        </tr>
        <tr>
          <td class="field-name">Name</td>
          <td>' . $member['name'] . '</td>
        </tr>
        <tr>
          <td class="field-name">Email</td>
          <td>' . $member['email'] . '</td>
        </tr>
        <tr>
          <td class="field-name">Phone</td>
          <td>' . $member['phone'] . '</td>
        </tr>
        <tr>
          <td class="field-name">Membership Status</td>
          <td>' . $member['membership_status'] . '</td>
        </tr>
        <tr>
          <td class="field-name">Join Date</td>
          <td>' . ($member['join_date'] ? date('M d, Y', strtotime($member['join_date'])) : 'N/A') . '</td>
        </tr>
        <tr>
          <td class="field-name">Ministry</td>
          <td>' . ($member['ministry'] ? $member['ministry'] : 'N/A') . '</td>
        </tr>
        <tr>
          <td class="field-name">Gender</td>
          <td>' . ($member['gender'] ? $member['gender'] : 'N/A') . '</td>
        </tr>
        <tr>
          <td class="field-name">Date of Birth</td>
          <td>' . ($member['date_of_birth'] ? date('M d, Y', strtotime($member['date_of_birth'])) : 'N/A') . '</td>
        </tr>
      ';
      
      // Address
      $address = '';
      if ($member['address']) $address .= $member['address'];
      if ($member['city']) $address .= ($address ? ', ' : '') . $member['city'];
      if ($member['state']) $address .= ($address ? ', ' : '') . $member['state'];
      if ($member['zip']) $address .= ($address ? ' ' : '') . $member['zip'];
      
      $html .= '
        <tr>
          <td class="field-name">Address</td>
          <td>' . ($address ? $address : 'N/A') . '</td>
        </tr>
        <tr>
          <td class="field-name">Notes</td>
          <td>' . ($member['notes'] ? nl2br($member['notes']) : 'N/A') . '</td>
        </tr>
      </table>
      ';
      
    } else {
      // All members list
      $html .= '
      <table>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Status</th>
          <th>Join Date</th>
          <th>Ministry</th>
        </tr>
      ';
      
      mysqli_data_seek($result, 0);
      
      while ($data = mysqli_fetch_assoc($result)) {
        $html .= '
        <tr>
          <td>' . $data['id'] . '</td>
          <td>' . $data['name'] . '</td>
          <td>' . $data['email'] . '</td>
          <td>' . $data['phone'] . '</td>
          <td>' . $data['membership_status'] . '</td>
          <td>' . ($data['join_date'] ? date('M d, Y', strtotime($data['join_date'])) : 'N/A') . '</td>
          <td>' . ($data['ministry'] ? $data['ministry'] : 'N/A') . '</td>
        </tr>
        ';
      }
      
      $html .= '</table>';
    }
    
  } elseif ($report_type == 'events') {
    if (isset($_GET['id'])) {
      // Single event detailed report
      $event = mysqli_fetch_assoc($result);
      
      $html .= '
      <table>
        <tr>
          <th>Field</th>
          <th>Value</th>
        </tr>
        <tr>
          <td class="field-name">ID</td>
          <td>' . $event['id'] . '</td>
        </tr>
        <tr>
          <td class="field-name">Event Name</td>
          <td>' . $event['event_name'] . '</td>
        </tr>
        <tr>
          <td class="field-name">Date</td>
          <td>' . date('M d, Y', strtotime($event['event_date'])) . '</td>
        </tr>
        <tr>
          <td class="field-name">Location</td>
          <td>' . $event['location'] . '</td>
        </tr>
        <tr>
          <td class="field-name">Type</td>
          <td>' . $event['event_type'] . '</td>
        </tr>
        <tr>
          <td class="field-name">Status</td>
          <td>' . $event['status'] . '</td>
        </tr>
        <tr>
          <td class="field-name">Organizer</td>
          <td>' . ($event['organizer'] ? $event['organizer'] : 'N/A') . '</td>
        </tr>
        <tr>
          <td class="field-name">Contact Person</td>
          <td>' . ($event['contact_person'] ? $event['contact_person'] : 'N/A') . '</td>
        </tr>
        <tr>
          <td class="field-name">Contact Email</td>
          <td>' . ($event['contact_email'] ? $event['contact_email'] : 'N/A') . '</td>
        </tr>
        <tr>
          <td class="field-name">Contact Phone</td>
          <td>' . ($event['contact_phone'] ? $event['contact_phone'] : 'N/A') . '</td>
        </tr>
        <tr>
          <td class="field-name">Description</td>
          <td>' . ($event['description'] ? nl2br($event['description']) : 'N/A') . '</td>
        </tr>
      </table>
      ';
      
    } else {
      // All events list
      $html .= '
      <table>
        <tr>
          <th>ID</th>
          <th>Event Name</th>
          <th>Date</th>
          <th>Location</th>
          <th>Type</th>
          <th>Status</th>
        </tr>
      ';
      
      mysqli_data_seek($result, 0);
      
      while ($data = mysqli_fetch_assoc($result)) {
        $html .= '
        <tr>
          <td>' . $data['id'] . '</td>
          <td>' . $data['event_name'] . '</td>
          <td>' . date('M d, Y', strtotime($data['event_date'])) . '</td>
          <td>' . $data['location'] . '</td>
          <td>' . $data['event_type'] . '</td>
          <td>' . $data['status'] . '</td>
        </tr>
        ';
      }
      
      $html .= '</table>';
    }
  }
  
  $html .= '
    <div style="text-align: center; margin-top: 20px; font-size: 10pt;">
      Report generated on ' . date('F d, Y H:i:s') . ' by JIA Somal-ot Church Management System
    </div>
  </body>
  ';
  
  // Write HTML to PDF
  $mpdf->WriteHTML($html);
  
  // Output PDF
  header('Content-Type: application/pdf');
  header('Content-Disposition: attachment;filename="' . $report_type . '_report_' . date('Y-m-d') . '.pdf"');
  $mpdf->Output($report_type . '_report_' . date('Y-m-d') . '.pdf', 'D');
  
} elseif ($export_format == 'docx') {
  // Create new Word document
  $phpWord = new PhpWord();
  
  // Set document properties
  $properties = $phpWord->getDocInfo();
  $properties->setCreator('Church Management System');
  $properties->setCompany('JIA Somal-ot Church');
  $properties->setTitle($title);
  $properties->setSubject('Church ' . ucfirst($report_type) . ' Report');
  $properties->setDescription('Report generated on ' . date('Y-m-d H:i:s'));
  
  // Add styles
  $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 18, 'name' => 'Arial'], ['alignment' => 'center']);
  $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 14, 'name' => 'Arial'], ['alignment' => 'center']);
  
  $tableStyle = ['borderSize' => 6, 'borderColor' => '999999', 'cellMargin' => 80];
  $firstRowStyle = ['bgColor' => '4472C4'];
  $phpWord->addTableStyle('reportTable', $tableStyle, $firstRowStyle);
  
  // Add title section
  $section = $phpWord->addSection();
  $section->addTitle('JIA Somal-ot Church', 1);
  $section->addTitle($title, 2);
  $section->addText('Period: ' . date('M d, Y', strtotime($start_date)) . ' to ' . date('M d, Y', strtotime($end_date)), 
    ['italic' => true, 'size' => 10], ['alignment' => 'center']);
  $section->addTextBreak(1);
  
  // Add report content based on type
  if ($report_type == 'donations') {
    // Create table
    $table = $section->addTable('reportTable');
    
    // Add header row
    $table->addRow();
    $table->addCell(800)->addText('ID', ['bold' => true, 'color' => 'FFFFFF']);
    $table->addCell(2000)->addText('Member Name', ['bold' => true, 'color' => 'FFFFFF']);
    $table->addCell(1200)->addText('Amount', ['bold' => true, 'color' => 'FFFFFF']);
    $table->addCell(1500)->addText('Date', ['bold' => true, 'color' => 'FFFFFF']);
    $table->addCell(1500)->addText('Payment Method', ['bold' => true, 'color' => 'FFFFFF']);
    $table->addCell(1500)->addText('Category', ['bold' => true, 'color' => 'FFFFFF']);
    $table->addCell(1500)->addText('Campaign', ['bold' => true, 'color' => 'FFFFFF']);
    
    // Add data rows
    mysqli_data_seek($result, 0);
    $totalAmount = 0;
    $rowCount = 0;
    
    while ($data = mysqli_fetch_assoc($result)) {
      $rowCount++;
      $cellStyle = [];
      if ($rowCount % 2 == 0) {
        $cellStyle = ['bgColor' => 'F2F2F2'];
      }
      
      $table->addRow();
      $table->addCell(800, $cellStyle)->addText($data['id']);
      $table->addCell(2000, $cellStyle)->addText($data['member_name']);
      $table->addCell(1200, $cellStyle)->addText('$' . number_format($data['amount'], 2));
      $table->addCell(1500, $cellStyle)->addText(date('M d, Y', strtotime($data['donation_date'])));
      $table->addCell(1500, $cellStyle)->addText($data['payment_method']);
      $table->addCell(1500, $cellStyle)->addText($data['category']);
      $table->addCell(1500, $cellStyle)->addText($data['campaign'] ? $data['campaign'] : 'N/A');
      
      $totalAmount += $data['amount'];
    }
    
    // Add total row
    $table->addRow();
    $table->addCell(800)->addText('');
    $table->addCell(2000)->addText('Total:', ['bold' => true]);
    $table->addCell(1200)->addText('$' . number_format($totalAmount, 2), ['bold' => true]);
    $table->addCell(1500, ['gridSpan' => 4])->addText('');
    
  } elseif ($report_type == 'members') {
    if ($member_id > 0) {
      // Single member detailed report
      $member = mysqli_fetch_assoc($result);
      
      // Create table
      $table = $section->addTable('reportTable');
      
      // Add header row
      $table->addRow();
      $table->addCell(3000)->addText('Field', ['bold' => true, 'color' => 'FFFFFF']);
      $table->addCell(7000)->addText('Value', ['bold' => true, 'color' => 'FFFFFF']);
      
      // Add data rows
      $fields = [
        'ID' => $member['id'],
        'Name' => $member['name'],
        'Email' => $member['email'],
        'Phone' => $member['phone'],
        'Membership Status' => $member['membership_status'],
        'Join Date' => $member['join_date'] ? date('M d, Y', strtotime($member['join_date'])) : 'N/A',
        'Ministry' => $member['ministry'] ? $member['ministry'] : 'N/A',
        'Gender' => $member['gender'] ? $member['gender'] : 'N/A',
        'Date of Birth' => $member['date_of_birth'] ? date('M d, Y', strtotime($member['date_of_birth'])) : 'N/A'
      ];
      
      // Address
      $address = '';
      if ($member['address']) $address .= $member['address'];
      if ($member['city']) $address .= ($address ? ', ' : '') . $member['city'];
      if ($member['state']) $address .= ($address ? ', ' : '') . $member['state'];
      if ($member['zip']) $address .= ($address ? ' ' : '') . $member['zip'];
      $fields['Address'] = $address ? $address : 'N/A';
      
      $fields['Notes'] = $member['notes'] ? $member['notes'] : 'N/A';
      
      $rowCount = 0;
      foreach ($fields as $field => $value) {
        $rowCount++;
        $cellStyle = [];
        if ($rowCount % 2 == 0) {
          $cellStyle = ['bgColor' => 'F2F2F2'];
        }
        
        $table->addRow();
        $table->addCell(3000, $cellStyle)->addText($field, ['bold' => true]);
        $table->addCell(7000, $cellStyle)->addText($value);
      }
      
    } else {
      // All members list
      // Create table
      $table = $section->addTable('reportTable');
      
      // Add header row
      $table->addRow();
      $table->addCell(800)->addText('ID', ['bold' => true, 'color' => 'FFFFFF']);
      $table->addCell(2000)->addText('Name', ['bold' => true, 'color' => 'FFFFFF']);
      $table->addCell(2000)->addText('Email', ['bold' => true, 'color' => 'FFFFFF']);
      $table->addCell(1500)->addText('Phone', ['bold' => true, 'color' => 'FFFFFF']);
      $table->addCell(1500)->addText('Status', ['bold' => true, 'color' => 'FFFFFF']);
      $table->addCell(1500)->addText('Join Date', ['bold' => true, 'color' => 'FFFFFF']);
      $table->addCell(1500)->addText('Ministry', ['bold' => true, 'color' => 'FFFFFF']);
      
      // Add data rows
      mysqli_data_seek($result, 0);
      $rowCount = 0;
      
      while ($data = mysqli_fetch_assoc($result)) {
        $rowCount++;
        $cellStyle = [];
        if ($rowCount % 2 == 0) {
          $cellStyle = ['bgColor' => 'F2F2F2'];
        }
        
        $table->addRow();
        $table->addCell(800, $cellStyle)->addText($data['id']);
        $table->addCell(2000, $cellStyle)->addText($data['name']);
        $table->addCell(2000, $cellStyle)->addText($data['email']);
        $table->addCell(1500, $cellStyle)->addText($data['phone']);
        $table->addCell(1500, $cellStyle)->addText($data['membership_status']);
        $table->addCell(1500, $cellStyle)->addText($data['join_date'] ? date('M d, Y', strtotime($data['join_date'])) : 'N/A');
        $table->addCell(1500, $cellStyle)->addText($data['ministry'] ? $data['ministry'] : 'N/A');
      }
    }
    
  } elseif ($report_type == 'events') {
    if (isset($_GET['id'])) {
      // Single event detailed report
      $event = mysqli_fetch_assoc($result);
      
      // Create table
      $table = $section->addTable('reportTable');
      
      // Add header row
      $table->addRow();
      $table->addCell(3000)->addText('Field', ['bold' => true, 'color' => 'FFFFFF']);
      $table->addCell(7000)->addText('Value', ['bold' => true, 'color' => 'FFFFFF']);
      
      // Add data rows
      $fields = [
        'ID' => $event['id'],
        'Event Name' => $event['event_name'],
        'Date' => date('M d, Y', strtotime($event['event_date'])),
        'Location' => $event['location'],
        'Type' => $event['event_type'],
        'Status' => $event['status'],
        'Organizer' => $event['organizer'] ? $event['organizer'] : 'N/A',
        'Contact Person' => $event['contact_person'] ? $event['contact_person'] : 'N/A',
        'Contact Email' => $event['contact_email'] ? $event['contact_email'] : 'N/A',
        'Contact Phone' => $event['contact_phone'] ? $event['contact_phone'] : 'N/A',
        'Description' => $event['description'] ? $event['description'] : 'N/A'
      ];
      
      $rowCount = 0;
      foreach ($fields as $field => $value) {
        $rowCount++;
        $cellStyle = [];
        if ($rowCount % 2 == 0) {
          $cellStyle = ['bgColor' => 'F2F2F2'];
        }
        
        $table->addRow();
        $table->addCell(3000, $cellStyle)->addText($field, ['bold' => true]);
        $table->addCell(7000, $cellStyle)->addText($value);
      }
      
    } else {
      // All events list
      // Create table
      $table = $section->addTable('reportTable');
      
      // Add header row
      $table->addRow();
      $table->addCell(800)->addText('ID', ['bold' => true, 'color' => 'FFFFFF']);
      $table->addCell(3000)->addText('Event Name', ['bold' => true, 'color' => 'FFFFFF']);
      $table->addCell(1500)->addText('Date', ['bold' => true, 'color' => 'FFFFFF']);
      $table->addCell(2000)->addText('Location', ['bold' => true, 'color' => 'FFFFFF']);
      $table->addCell(1500)->addText('Type', ['bold' => true, 'color' => 'FFFFFF']);
      $table->addCell(1500)->addText('Status', ['bold' => true, 'color' => 'FFFFFF']);
      
      // Add data rows
      mysqli_data_seek($result, 0);
      $rowCount = 0;
      
      while ($data = mysqli_fetch_assoc($result)) {
        $rowCount++;
        $cellStyle = [];
        if ($rowCount % 2 == 0) {
          $cellStyle = ['bgColor' => 'F2F2F2'];
        }
        
        $table->addRow();
        $table->addCell(800, $cellStyle)->addText($data['id']);
        $table->addCell(3000, $cellStyle)->addText($data['event_name']);
        $table->addCell(1500, $cellStyle)->addText(date('M d, Y', strtotime($data['event_date'])));
        $table->addCell(2000, $cellStyle)->addText($data['location']);
        $table->addCell(1500, $cellStyle)->addText($data['event_type']);
        $table->addCell(1500, $cellStyle)->addText($data['status']);
      }
    }
  }
  
  // Add footer
  $section->addTextBreak(1);
  $section->addText('Report generated on ' . date('F d, Y H:i:s') . ' by JIA Somal-ot Church Management System', 
    ['size' => 8], ['alignment' => 'center']);
  
  // Save to output
  header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
  header('Content-Disposition: attachment;filename="' . $report_type . '_report_' . date('Y-m-d') . '.docx"');
  header('Cache-Control: max-age=0');
  
  $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
  $objWriter->save('php://output');
  
} elseif ($export_format == 'pptx') {
  // Create new PowerPoint presentation
  $presentation = new PhpPresentation();
  
  // Set document properties
  $properties = $presentation->getDocumentProperties();
  $properties->setCreator('Church Management System');
  $properties->setCompany('JIA Somal-ot Church');
  $properties->setTitle($title);
  $properties->setSubject('Church ' . ucfirst($report_type) . ' Report');
  $properties->setDescription('Report generated on ' . date('Y-m-d H:i:s'));
  
  // Remove default slide
  $presentation->removeSlideByIndex(0);
  
  // Add title slide
  $slide = $presentation->createSlide();
  $slide->addShape(
    new \PhpOffice\PhpPresentation\Shape\RichText(),
    new \PhpOffice\PhpPresentation\Style\Color('#4472C4')
  )
  ->setHeight(300)
  ->setWidth(600)
  ->setOffsetX(150)
  ->setOffsetY(200)
  ->getActiveParagraph()->getAlignment()->setHorizontal(PptAlignment::HORIZONTAL_CENTER);
  
  $textRun = $slide->getShapes()[0]->createTextRun('JIA Somal-ot Church');
  $textRun->getFont()->setBold(true)->setSize(32)->setColor(new Color('#000000'));
  
  $slide->getShapes()[0]->createBreak();
  $textRun = $slide->getShapes()[0]->createTextRun($title);
  $textRun->getFont()->setBold(true)->setSize(24)->setColor(new Color('#000000'));
  
  $slide->getShapes()[0]->createBreak();
  $slide->getShapes()[0]->createBreak();
  $textRun = $slide->getShapes()[0]->createTextRun('Period: ' . date('M d, Y', strtotime($start_date)) . ' to ' . date('M d, Y', strtotime($end_date)));
  $textRun->getFont()->setItalic(true)->setSize(16)->setColor(new Color('#666666'));
  
  // Add content slides based on report type
  if ($report_type == 'donations') {
    // Add summary slide
    $slide = $presentation->createSlide();
    $slide->addShape(
      new \PhpOffice\PhpPresentation\Shape\RichText(),
      new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
    )
    ->setHeight(100)
    ->setWidth(600)
    ->setOffsetX(150)
    ->setOffsetY(50);
    
    $textRun = $slide->getShapes()[0]->createTextRun('Donations Summary');
    $textRun->getFont()->setBold(true)->setSize(24)->setColor(new Color('#000000'));
    
    // Add summary data
    $slide->addShape(
      new \PhpOffice\PhpPresentation\Shape\RichText(),
      new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
    )
    ->setHeight(300)
    ->setWidth(600)
    ->setOffsetX(150)
    ->setOffsetY(150);
    
    $textRun = $slide->getShapes()[1]->createTextRun('Total Donations: ');
    $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
    $textRun = $slide->getShapes()[1]->createTextRun($stats['total_donations']);
    $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
    
    $slide->getShapes()[1]->createBreak();
    $textRun = $slide->getShapes()[1]->createTextRun('Total Amount: ');
    $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
    $textRun = $slide->getShapes()[1]->createTextRun('$' . number_format($stats['total_amount'], 2));
    $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
    
    $slide->getShapes()[1]->createBreak();
    $textRun = $slide->getShapes()[1]->createTextRun('Average Donation: ');
    $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
    $textRun = $slide->getShapes()[1]->createTextRun('$' . number_format($stats['average_amount'], 2));
    $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
    
    $slide->getShapes()[1]->createBreak();
    $textRun = $slide->getShapes()[1]->createTextRun('Highest Donation: ');
    $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
    $textRun = $slide->getShapes()[1]->createTextRun('$' . number_format($stats['highest_donation'], 2));
    $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
    
    // Add data slide
    $slide = $presentation->createSlide();
    $slide->addShape(
      new \PhpOffice\PhpPresentation\Shape\RichText(),
      new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
    )
    ->setHeight(100)
    ->setWidth(600)
    ->setOffsetX(150)
    ->setOffsetY(50);
    
    $textRun = $slide->getShapes()[0]->createTextRun('Recent Donations');
    $textRun->getFont()->setBold(true)->setSize(24)->setColor(new Color('#000000'));
    
    // Add table data
    $shape = $slide->createTableShape(mysqli_num_rows($result) > 10 ? 11 : mysqli_num_rows($result) + 1);
    $shape->setHeight(400);
    $shape->setWidth(720);
    $shape->setOffsetX(100);
    $shape->setOffsetY(120);
    
    // Add header row
    $shape->getRow(0)->setHeight(30);
    $shape->getRow(0)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('#4472C4'));
    
    $shape->getCell(0, 0)->createTextRun('ID')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
    $shape->getCell(0, 1)->createTextRun('Member')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
    $shape->getCell(0, 2)->createTextRun('Amount')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
    $shape->getCell(0, 3)->createTextRun('Date')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
    $shape->getCell(0, 4)->createTextRun('Method')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
    
    // Add data rows
    mysqli_data_seek($result, 0);
    $rowCount = 0;
    
    while ($data = mysqli_fetch_assoc($result) && $rowCount < 10) {
      $rowCount++;
      
      if ($rowCount % 2 == 0) {
        $shape->getRow($rowCount)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('#F2F2F2'));
      }
      
      $shape->getCell($rowCount, 0)->createTextRun($data['id'])->getFont()->setSize(12);
      $shape->getCell($rowCount, 1)->createTextRun($data['member_name'])->getFont()->setSize(12);
      $shape->getCell($rowCount, 2)->createTextRun('$' . number_format($data['amount'], 2))->getFont()->setSize(12);
      $shape->getCell($rowCount, 3)->createTextRun(date('M d, Y', strtotime($data['donation_date'])))->getFont()->setSize(12);
      $shape->getCell($rowCount, 4)->createTextRun($data['payment_method'])->getFont()->setSize(12);
    }
    
  } elseif ($report_type == 'members') {
    // Add summary slide
    $slide = $presentation->createSlide();
    $slide->addShape(
      new \PhpOffice\PhpPresentation\Shape\RichText(),
      new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
    )
    ->setHeight(100)
    ->setWidth(600)
    ->setOffsetX(150)
    ->setOffsetY(50);
    
    $textRun = $slide->getShapes()[0]->createTextRun('Members Summary');
    $textRun->getFont()->setBold(true)->setSize(24)->setColor(new Color('#000000'));
    
    // Add summary data
    $slide->addShape(
      new \PhpOffice\PhpPresentation\Shape\RichText(),
      new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
    )
    ->setHeight(300)
    ->setWidth(600)
    ->setOffsetX(150)
    ->setOffsetY(150);
    
    $textRun = $slide->getShapes()[1]->createTextRun('Total Members: ');
    $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
    $textRun = $slide->getShapes()[1]->createTextRun($stats['total_members']);
    $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
    
    $slide->getShapes()[1]->createBreak();
    $textRun = $slide->getShapes()[1]->createTextRun('Active Members: ');
    $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
    $textRun = $slide->getShapes()[1]->createTextRun($stats['active_members']);
    $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
    
    $slide->getShapes()[1]->createBreak();
    $textRun = $slide->getShapes()[1]->createTextRun('Inactive Members: ');
    $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
    $textRun = $slide->getShapes()[1]->createTextRun($stats['inactive_members']);
    $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
    
    $slide->getShapes()[1]->createBreak();
    $textRun = $slide->getShapes()[1]->createTextRun('New Members: ');
    $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
    $textRun = $slide->getShapes()[1]->createTextRun($stats['new_members']);
    $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
    
    // Add data slide
    if ($member_id > 0) {
      // Single member detailed slide
      $member = mysqli_fetch_assoc($result);
      
      $slide = $presentation->createSlide();
      $slide->addShape(
        new \PhpOffice\PhpPresentation\Shape\RichText(),
        new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
      )
      ->setHeight(100)
      ->setWidth(600)
      ->setOffsetX(150)
      ->setOffsetY(50);
      
      $textRun = $slide->getShapes()[0]->createTextRun('Member Details: ' . $member['name']);
      $textRun->getFont()->setBold(true)->setSize(24)->setColor(new Color('#000000'));
      
      // Add member data
      $slide->addShape(
        new \PhpOffice\PhpPresentation\Shape\RichText(),
        new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
      )
      ->setHeight(400)
      ->setWidth(600)
      ->setOffsetX(150)
      ->setOffsetY(150);
      
      $fields = [
        'ID' => $member['id'],
        'Email' => $member['email'],
        'Phone' => $member['phone'],
        'Status' => $member['membership_status'],
        'Join Date' => $member['join_date'] ? date('M d, Y', strtotime($member['join_date'])) : 'N/A',
        'Ministry' => $member['ministry'] ? $member['ministry'] : 'N/A'
      ];
      
      foreach ($fields as $field => $value) {
        $textRun = $slide->getShapes()[1]->createTextRun($field . ': ');
        $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
        $textRun = $slide->getShapes()[1]->createTextRun($value);
        $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
        $slide->getShapes()[1]->createBreak();
      }
      
    } else {
      // Members list slide
      $slide = $presentation->createSlide();
      $slide->addShape(
        new \PhpOffice\PhpPresentation\Shape\RichText(),
        new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
      )
      ->setHeight(100)
      ->setWidth(600)
      ->setOffsetX(150)
      ->setOffsetY(50);
      
      $textRun = $slide->getShapes()[0]->createTextRun('Members List');
      $textRun->getFont()->setBold(true)->setSize(24)->setColor(new Color('#000000'));
      
      // Add table data
      $shape = $slide->createTableShape(mysqli_num_rows($result) > 10 ? 11 : mysqli_num_rows($result) + 1);
      $shape->setHeight(400);
      $shape->setWidth(720);
      $shape->setOffsetX(100);
      $shape->setOffsetY(120);
      
      // Add header row
      $shape->getRow(0)->setHeight(30);
      $shape->getRow(0)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('#4472C4'));
      
      $shape->getCell(0, 0)->createTextRun('ID')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
      $shape->getCell(0, 1)->createTextRun('Name')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
      $shape->getCell(0, 2)->createTextRun('Email')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
      $shape->getCell(0, 3)->createTextRun('Status')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
      $shape->getCell(0, 4)->createTextRun('Join Date')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
      
      // Add data rows
      mysqli_data_seek($result, 0);
      $rowCount = 0;
      
      while ($data = mysqli_fetch_assoc($result) && $rowCount < 10) {
        $rowCount++;
        
        if ($rowCount % 2 == 0) {
          $shape->getRow($rowCount)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('#F2F2F2'));
        }
        
        $shape->getCell($rowCount, 0)->createTextRun($data['id'])->getFont()->setSize(12);
        $shape->getCell($rowCount, 1)->createTextRun($data['name'])->getFont()->setSize(12);
        $shape->getCell($rowCount, 2)->createTextRun($data['email'])->getFont()->setSize(12);
        $shape->getCell($rowCount, 3)->createTextRun($data['membership_status'])->getFont()->setSize(12);
        $shape->getCell($rowCount, 4)->createTextRun($data['join_date'] ? date('M d, Y', strtotime($data['join_date'])) : 'N/A')->getFont()->setSize(12);
      }
    }
    
  } elseif ($report_type == 'events') {
    // Add summary slide
    $slide = $presentation->createSlide();
    $slide->addShape(
      new \PhpOffice\PhpPresentation\Shape\RichText(),
      new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
    )
    ->setHeight(100)
    ->setWidth(600)
    ->setOffsetX(150)
    ->setOffsetY(50);
    
    $textRun = $slide->getShapes()[0]->createTextRun('Events Summary');
    $textRun->getFont()->setBold(true)->setSize(24)->setColor(new Color('#000000'));
    
    // Add summary data
    $slide->addShape(
      new \PhpOffice\PhpPresentation\Shape\RichText(),
      new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
    )
    ->setHeight(300)
    ->setWidth(600)
    ->setOffsetX(150)
    ->setOffsetY(150);
    
    $textRun = $slide->getShapes()[1]->createTextRun('Total Events: ');
    $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
    $textRun = $slide->getShapes()[1]->createTextRun($stats['total_events']);
    $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
    
    $slide->getShapes()[1]->createBreak();
    $textRun = $slide->getShapes()[1]->createTextRun('Scheduled Events: ');
    $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
    $textRun = $slide->getShapes()[1]->createTextRun($stats['scheduled_events']);
    $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
    
    $slide->getShapes()[1]->createBreak();
    $textRun = $slide->getShapes()[1]->createTextRun('Completed Events: ');
    $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
    $textRun = $slide->getShapes()[1]->createTextRun($stats['completed_events']);
    $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
    
    $slide->getShapes()[1]->createBreak();
    $textRun = $slide->getShapes()[1]->createTextRun('Cancelled Events: ');
    $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
    $textRun = $slide->getShapes()[1]->createTextRun($stats['cancelled_events']);
    $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
    
    // Add data slide
    if (isset($_GET['id'])) {
      // Single event detailed slide
      $event = mysqli_fetch_assoc($result);
      
      $slide = $presentation->createSlide();
      $slide->addShape(
        new \PhpOffice\PhpPresentation\Shape\RichText(),
        new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
      )
      ->setHeight(100)
      ->setWidth(600)
      ->setOffsetX(150)
      ->setOffsetY(50);
    
    $textRun = $slide->getShapes()[0]->createTextRun('Event Details: ' . $event['event_name']);
    $textRun->getFont()->setBold(true)->setSize(24)->setColor(new Color('#000000'));
    
    // Add event data
    $slide->addShape(
      new \PhpOffice\PhpPresentation\Shape\RichText(),
      new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
    )
    ->setHeight(400)
    ->setWidth(600)
    ->setOffsetX(150)
    ->setOffsetY(150);
    
    $fields = [
      'ID' => $event['id'],
      'Date' => date('M d, Y', strtotime($event['event_date'])),
      'Location' => $event['location'],
      'Type' => $event['event_type'],
      'Status' => $event['status'],
      'Organizer' => $event['organizer'] ? $event['organizer'] : 'N/A',
      'Contact Person' => $event['contact_person'] ? $event['contact_person'] : 'N/A'
    ];
    
    foreach ($fields as $field => $value) {
      $textRun = $slide->getShapes()[1]->createTextRun($field . ': ');
      $textRun->getFont()->setBold(true)->setSize(16)->setColor(new Color('#000000'));
      $textRun = $slide->getShapes()[1]->createTextRun($value);
      $textRun->getFont()->setSize(16)->setColor(new Color('#000000'));
      $slide->getShapes()[1]->createBreak();
    }
    
  } else {
    // Events list slide
    $slide = $presentation->createSlide();
    $slide->addShape(
      new \PhpOffice\PhpPresentation\Shape\RichText(),
      new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
    )
    ->setHeight(100)
    ->setWidth(600)
    ->setOffsetX(150)
    ->setOffsetY(50);
    
    $textRun = $slide->getShapes()[0]->createTextRun('Events List');
    $textRun->getFont()->setBold(true)->setSize(24)->setColor(new Color('#000000'));
    
    // Add table data
    $shape = $slide->createTableShape(mysqli_num_rows($result) > 10 ? 11 : mysqli_num_rows($result) + 1);
    $shape->setHeight(400);
    $shape->setWidth(720);
    $shape->setOffsetX(100);
    $shape->setOffsetY(120);
    
    // Add header row
    $shape->getRow(0)->setHeight(30);
    $shape->getRow(0)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('#4472C4'));
    
    $shape->getCell(0, 0)->createTextRun('ID')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
    $shape->getCell(0, 1)->createTextRun('Event Name')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
    $shape->getCell(0, 2)->createTextRun('Date')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
    $shape->getCell(0, 3)->createTextRun('Location')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
    $shape->getCell(0, 4)->createTextRun('Status')->getFont()->setBold(true)->setSize(14)->setColor(new Color('#FFFFFF'));
    
    // Add data rows
    mysqli_data_seek($result, 0);
    $rowCount = 0;
    
    while ($data = mysqli_fetch_assoc($result) && $rowCount < 10) {
      $rowCount++;
      
      if ($rowCount % 2 == 0) {
        $shape->getRow($rowCount)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color('#F2F2F2'));
      }
      
      $shape->getCell($rowCount, 0)->createTextRun($data['id'])->getFont()->setSize(12);
      $shape->getCell($rowCount, 1)->createTextRun($data['event_name'])->getFont()->setSize(12);
      $shape->getCell($rowCount, 2)->createTextRun(date('M d, Y', strtotime($data['event_date'])))->getFont()->setSize(12);
      $shape->getCell($rowCount, 3)->createTextRun($data['location'])->getFont()->setSize(12);
      $shape->getCell($rowCount, 4)->createTextRun($data['status'])->getFont()->setSize(12);
    }
  }
  
  // Add footer slide
  $slide = $presentation->createSlide();
  $slide->addShape(
    new \PhpOffice\PhpPresentation\Shape\RichText(),
    new \PhpOffice\PhpPresentation\Style\Color('#FFFFFF')
  )
  ->setHeight(300)
  ->setWidth(600)
  ->setOffsetX(150)
  ->setOffsetY(200)
  ->getActiveParagraph()->getAlignment()->setHorizontal(PptAlignment::HORIZONTAL_CENTER);
  
  $textRun = $slide->getShapes()[0]->createTextRun('Thank You');
  $textRun->getFont()->setBold(true)->setSize(36)->setColor(new Color('#4472C4'));
  
  $slide->getShapes()[0]->createBreak();
  $slide->getShapes()[0]->createBreak();
  $textRun = $slide->getShapes()[0]->createTextRun('Report generated on ' . date('F d, Y'));
  $textRun->getFont()->setSize(16)->setColor(new Color('#666666'));
  
  $slide->getShapes()[0]->createBreak();
  $textRun = $slide->getShapes()[0]->createTextRun('JIA Somal-ot Church Management System');
  $textRun->getFont()->setItalic(true)->setSize(14)->setColor(new Color('#666666'));
  
  // Save to output
  header('Content-Type: application/vnd.openxmlformats-officedocument.presentationml.presentation');
  header('Content-Disposition: attachment;filename="' . $report_type . '_report_' . date('Y-m-d') . '.pptx"');
  header('Cache-Control: max-age=0');
  
  $oWriter = \PhpOffice\PhpPresentation\IOFactory::createWriter($presentation, 'PowerPoint2007');
  $oWriter->save('php://output');
  
} elseif ($export_format == 'txt') {
  // For TXT, we'll create a simple text representation
  header('Content-Type: text/plain');
  header('Content-Disposition: attachment;filename="' . $report_type . '_report_' . date('Y-m-d') . '.txt"');
  
  // Create text content
  $textContent = "JIA Somal-ot Church\n";
  $textContent .= $title . "\n";
  $textContent .= "Period: " . date('M d, Y', strtotime($start_date)) . " to " . date('M d, Y', strtotime($end_date)) . "\n\n";
  
  // Add report content based on type
  if ($report_type == 'donations') 
    $textContent .= "DONATIONS SUMMARY\n";
    $textContent .= "----------------\n";
    $textContent .= "Total Donations: " . $stats['total_donations'] . "\n";
    $textContent .= "Total Amount: $" . number_format($stats['total_amount'], 2) . "\n";
    $textContent .= "Average Donation: $" . number_format($stats['average_amount'], 2) . "\n";
    $textContent .= "Highest Donation: $" . number_format($stats['highest_donation'], 2) . "\n\n";
    
    $textContent .= "DONATIONS LIST\n";
    $textContent .= "-------------\n";
    $textContent .= "ID\tMember Name\tAmount\tDate\tPayment Method\tCategory\tCampaign\n";
    $textContent .= str_repeat("-", 100) . "\n";
    
    mysqli_data_seek($result, 0);
    $totalAmount = 0;
  
    while ($data = mysqli_fetch_assoc($result)) {
      $textContent .= $data['id'] . "\t" . 
                     $data['member_name'] . "\t" . 
                     "$" . number_format($data['amount'], 2) . "\t" . 
                     date('M d, Y', strtotime($data['donation_date'])) . "\t" . 
                     $data['payment_method'] . "\t" . 
                     $data['category'] . "\t" . 
                     ($data['campaign'] ? $data['campaign'] : 'N/A') . "\n";
      
      $totalAmount += $data['amount'];
    }
    
    
    $textContent .= str_repeat("-", 100) . "\n";
    $textContent .= "Total:\t$" . number_format($totalAmount, 2) . "\n";
    
  } elseif ($report_type == 'members') {
    if ($member_id > 0) {
      // Single member detailed report
      $member = mysqli_fetch_assoc($result);
      
      $textContent .= "MEMBER DETAILS\n";
      $textContent .= "--------------\n";
      $textContent .= "ID: " . $member['id'] . "\n";
      $textContent .= "Name: " . $member['name'] . "\n";
      $textContent .= "Email: " . $member['email'] . "\n";
      $textContent .= "Phone: " . $member['phone'] . "\n";
      $textContent .= "Membership Status: " . $member['membership_status'] . "\n";
      $textContent .= "Join Date: " . ($member['join_date'] ? date('M d, Y', strtotime($member['join_date'])) : 'N/A') . "\n";
      $textContent .= "Ministry: " . ($member['ministry'] ? $member['ministry'] : 'N/A') . "\n";
      $textContent .= "Gender: " . ($member['gender'] ? $member['gender'] : 'N/A') . "\n";
      $textContent .= "Date of Birth: " . ($member['date_of_birth'] ? date('M d, Y', strtotime($member['date_of_birth'])) : 'N/A') . "\n";
      
      // Address
      $address = '';
      if ($member['address']) $address .= $member['address'];
      if ($member['city']) $address .= ($address ? ', ' : '') . $member['city'];
      if ($member['state']) $address .= ($address ? ', ' : '') . $member['state'];
      if ($member['zip']) $address .= ($address ? ' ' : '') . $member['zip'];
      
      $textContent .= "Address: " . ($address ? $address : 'N/A') . "\n";
      $textContent .= "Notes: " . ($member['notes'] ? $member['notes'] : 'N/A') . "\n";
      
    } else {
      // All members list
      $textContent .= "MEMBERS SUMMARY\n";
      $textContent .= "---------------\n";
      $textContent .= "Total Members: " . $stats['total_members'] . "\n";
      $textContent .= "Active Members: " . $stats['active_members'] . "\n";
      $textContent .= "Inactive Members: " . $stats['inactive_members'] . "\n";
      $textContent .= "Visitors: " . $stats['visitors'] . "\n";
      $textContent .= "New Members: " . $stats['new_members'] . "\n\n";
      
      $textContent .= "MEMBERS LIST\n";
      $textContent .= "------------\n";
      $textContent .= "ID\tName\tEmail\tPhone\tStatus\tJoin Date\tMinistry\n";
      $textContent .= str_repeat("-", 100) . "\n";
      
      mysqli_data_seek($result, 0);
      
      while ($data = mysqli_fetch_assoc($result)) {
        $textContent .= $data['id'] . "\t" . 
                       $data['name'] . "\t" . 
                       $data['email'] . "\t" . 
                       $data['phone'] . "\t" . 
                       $data['membership_status'] . "\t" . 
                       ($data['join_date'] ? date('M d, Y', strtotime($data['join_date'])) : 'N/A') . "\t" . 
                       ($data['ministry'] ? $data['ministry'] : 'N/A') . "\n";
      }
    }
    
  } elseif ($report_type == 'events') {
    if (isset($_GET['id'])) {
      // Single event detailed report
      $event = mysqli_fetch_assoc($result);
      
      $textContent .= "EVENT DETAILS\n";
      $textContent .= "-------------\n";
      $textContent .= "ID: " . $event['id'] . "\n";
      $textContent .= "Event Name: " . $event['event_name'] . "\n";
      $textContent .= "Date: " . date('M d, Y', strtotime($event['event_date'])) . "\n";
      $textContent .= "Location: " . $event['location'] . "\n";
      $textContent .= "Type: " . $event['event_type'] . "\n";
      $textContent .= "Status: " . $event['status'] . "\n";
      $textContent .= "Organizer: " . ($event['organizer'] ? $event['organizer'] : 'N/A') . "\n";
      $textContent .= "Contact Person: " . ($event['contact_person'] ? $event['contact_person'] : 'N/A') . "\n";
      $textContent .= "Contact Email: " . ($event['contact_email'] ? $event['contact_email'] : 'N/A') . "\n";
      $textContent .= "Contact Phone: " . ($event['contact_phone'] ? $event['contact_phone'] : 'N/A') . "\n";
      $textContent .= "Description: " . ($event['description'] ? $event['description'] : 'N/A') . "\n";
      
    } else {
      // All events list
      $textContent .= "EVENTS SUMMARY\n";
      $textContent .= "--------------\n";
      $textContent .= "Total Events: " . $stats['total_events'] . "\n";
      $textContent .= "Scheduled Events: " . $stats['scheduled_events'] . "\n";
      $textContent .= "Completed Events: " . $stats['completed_events'] . "\n";
      $textContent .= "Cancelled Events: " . $stats['cancelled_events'] . "\n";
      $textContent .= "Postponed Events: " . $stats['postponed_events'] . "\n\n";
      
      $textContent .= "EVENTS LIST\n";
      $textContent .= "-----------\n";
      $textContent .= "ID\tEvent Name\tDate\tLocation\tType\tStatus\n";
      $textContent .= str_repeat("-", 100) . "\n";
      
      mysqli_data_seek($result, 0);
      
      while ($data = mysqli_fetch_assoc($result)) {
        $textContent .= $data['id'] . "\t" . 
                       $data['event_name'] . "\t" . 
                       date('M d, Y', strtotime($data['event_date'])) . "\t" . 
                       $data['location'] . "\t" . 
                       $data['event_type'] . "\t" . 
                       $data['status'] . "\n";
      }
    }
  }
  
  $textContent .= "\n" . str_repeat("-", 100) . "\n";
  $textContent .= "Report generated on " . date('F d, Y H:i:s') . " by JIA Somal-ot Church Management System\n";
  
  echo $textContent;
}
?>