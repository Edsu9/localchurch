<?php
session_start();
require_once 'config/db.php';
require 'vendor/autoload.php'; // Make sure you have PhpSpreadsheet installed

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Set the default timezone
date_default_timezone_set('Asia/Manila'); // Change to your timezone if needed

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get report type and format
$report_type = isset($_GET['type']) ? $_GET['type'] : 'members';
$format = isset($_GET['format']) ? $_GET['format'] : 'pdf';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get church settings
$query = "SELECT * FROM settings WHERE id = 1";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);
$church_name = isset($settings['church_name']) ? $settings['church_name'] : 'JIA Somal-ot Church';

// Get report data based on type
if ($report_type == 'members') {
    $title = 'Members List Report';
    
    // Get member data
    $query = "SELECT id, first_name, last_name, email, phone, membership_status, join_date
              FROM members 
              ORDER BY last_name, first_name";
    $result = mysqli_query($conn, $query);
    
    if ($format == 'excel') {
        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Church Management System')
            ->setLastModifiedBy('Church Management System')
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription('Generated on ' . date('Y-m-d H:i:s'))
            ->setKeywords('church members report')
            ->setCategory('Reports');
        
        // Set header row
        $sheet->setCellValue('A1', $church_name);
        $sheet->setCellValue('A2', $title);
        $sheet->setCellValue('A3', 'Generated on ' . date('F d, Y h:i A'));
        
        // Style header
        $sheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:F2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3:F3')->getFont()->setItalic(true);
        
        // Merge cells for header
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A3:F3');
        
        // Center align header
        $sheet->getStyle('A1:F3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Set column headers
        $sheet->setCellValue('A5', 'ID');
        $sheet->setCellValue('B5', 'Name');
        $sheet->setCellValue('C5', 'Email');
        $sheet->setCellValue('D5', 'Phone');
        $sheet->setCellValue('E5', 'Status');
        $sheet->setCellValue('F5', 'Join Date');
        
        // Style column headers
        $sheet->getStyle('A5:F5')->getFont()->setBold(true);
        $sheet->getStyle('A5:F5')->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpSpreadsheet\Style\Color('DDDDDD'));
        $sheet->getStyle('A5:F5')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Add data
        $row = 6;
        while ($member = mysqli_fetch_assoc($result)) {
            $sheet->setCellValue('A' . $row, $member['id']);
            $sheet->setCellValue('B' . $row, $member['first_name'] . ' ' . $member['last_name']);
            $sheet->setCellValue('C' . $row, $member['email']);
            $sheet->setCellValue('D' . $row, $member['phone']);
            $sheet->setCellValue('E' . $row, $member['membership_status']);
            $sheet->setCellValue('F' . $row, $member['join_date'] ? date('M d, Y', strtotime($member['join_date'])) : 'N/A');
            
            // Add borders to cells
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            // Add zebra striping
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpSpreadsheet\Style\Color('F9F9F9'));
            }
            
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set filename and headers
        $filename = 'members_report_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Save file to output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
        
    } elseif ($format == 'pdf') {
        // Create PDF using mPDF
        require_once __DIR__ . '/vendor/autoload.php';
        
        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);
        
        // Set document properties
        $mpdf->SetTitle($title);
        
        // Start building HTML content
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; font-size: 18pt; margin-bottom: 5px; }
            h2 { text-align: center; font-size: 14pt; margin-top: 0; margin-bottom: 5px; }
            .period { text-align: center; font-style: italic; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #DDDDDD; font-weight: bold; text-align: left; padding: 8px; }
            td { padding: 8px; border-bottom: 1px solid #DDDDDD; }
            tr:nth-child(even) { background-color: #F9F9F9; }
            .footer { text-align: center; font-size: 9pt; margin-top: 30px; color: #666; }
        </style>
        <body>
            <h1>' . $church_name . '</h1>
            <h2>' . $title . '</h2>
            <div class="period">Generated on ' . date('F d, Y h:i A') . '</div>
            
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
        ';
        
        // Reset result pointer
        mysqli_data_seek($result, 0);
        
        // Add data rows
        while ($member = mysqli_fetch_assoc($result)) {
            $html .= '
                <tr>
                    <td>' . $member['id'] . '</td>
                    <td>' . $member['first_name'] . ' ' . $member['last_name'] . '</td>
                    <td>' . $member['email'] . '</td>
                    <td>' . $member['phone'] . '</td>
                    <td>' . $member['membership_status'] . '</td>
                    <td>' . ($member['join_date'] ? date('M d, Y', strtotime($member['join_date'])) : 'N/A') . '</td>
                </tr>
            ';
        }
        
        // Close table and add footer
        $html .= '
                </tbody>
            </table>
            
            <div class="footer">
                This report was generated by the Church Management System on ' . date('F d, Y h:i:s A') . '<br>
                &copy; ' . date('Y') . ' ' . $church_name . '. All rights reserved.
            </div>
        </body>
        ';
        
        // Write HTML to PDF
        $mpdf->WriteHTML($html);
        
        // Set filename and output
        $filename = 'members_report_' . date('Y-m-d') . '.pdf';
        $mpdf->Output($filename, 'D');
        exit;
    }
    
} elseif ($report_type == 'donations') {
    $title = 'Donations Report';
    
    // Get donation data
    $query = "SELECT d.id, d.amount, d.donation_date, d.payment_method, d.category, 
              CONCAT(m.first_name, ' ', m.last_name) as member_name
              FROM donations d
              LEFT JOIN members m ON d.member_id = m.id
              WHERE d.donation_date BETWEEN '$start_date' AND '$end_date'
              ORDER BY d.donation_date DESC";
    $result = mysqli_query($conn, $query);
    
    if ($format == 'excel') {
        // Create new spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Church Management System')
            ->setLastModifiedBy('Church Management System')
            ->setTitle($title)
            ->setSubject($title)
            ->setDescription('Generated on ' . date('Y-m-d H:i:s'))
            ->setKeywords('church donations report')
            ->setCategory('Reports');
        
        // Set header row
        $sheet->setCellValue('A1', $church_name);
        $sheet->setCellValue('A2', $title);
        $sheet->setCellValue('A3', 'Period: ' . date('M d, Y', strtotime($start_date)) . ' to ' . date('M d, Y', strtotime($end_date)));
        $sheet->setCellValue('A4', 'Generated on ' . date('F d, Y h:i A'));
        
        // Style header
        $sheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A2:F2')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A3:F3')->getFont()->setItalic(true);
        $sheet->getStyle('A4:F4')->getFont()->setItalic(true);
        
        // Merge cells for header
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->mergeCells('A3:F3');
        $sheet->mergeCells('A4:F4');
        
        // Center align header
        $sheet->getStyle('A1:F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Set column headers
        $sheet->setCellValue('A6', 'ID');
        $sheet->setCellValue('B6', 'Member');
        $sheet->setCellValue('C6', 'Amount');
        $sheet->setCellValue('D6', 'Date');
        $sheet->setCellValue('E6', 'Payment Method');
        $sheet->setCellValue('F6', 'Category');
        
        // Style column headers
        $sheet->getStyle('A6:F6')->getFont()->setBold(true);
        $sheet->getStyle('A6:F6')->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpSpreadsheet\Style\Color('DDDDDD'));
        $sheet->getStyle('A6:F6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Add data
        $row = 7;
        $totalAmount = 0;
        
        while ($donation = mysqli_fetch_assoc($result)) {
            $sheet->setCellValue('A' . $row, $donation['id']);
            $sheet->setCellValue('B' . $row, $donation['member_name']);
            $sheet->setCellValue('C' . $row, $donation['amount']);
            $sheet->setCellValue('D' . $row, date('M d, Y', strtotime($donation['donation_date'])));
            $sheet->setCellValue('E' . $row, $donation['payment_method']);
            $sheet->setCellValue('F' . $row, $donation['category']);
            
            // Format currency
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
            
            // Add borders to cells
            $sheet->getStyle('A' . $row . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            // Add zebra striping
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':F' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new \PhpOffice\PhpSpreadsheet\Style\Color('F9F9F9'));
            }
            
            $totalAmount += $donation['amount'];
            $row++;
        }
        
        // Add total row
        $sheet->setCellValue('B' . $row, 'Total:');
        $sheet->setCellValue('C' . $row, $totalAmount);
        $sheet->getStyle('B' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle('B' . $row . ':C' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set filename and headers
        $filename = 'donations_report_' . date('Y-m-d') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        // Save file to output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
        
    } elseif ($format == 'pdf') {
        // Create PDF using mPDF
        require_once __DIR__ . '/vendor/autoload.php';
        
        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);
        
        // Set document properties
        $mpdf->SetTitle($title);
        
        // Start building HTML content
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; font-size: 18pt; margin-bottom: 5px; }
            h2 { text-align: center; font-size: 14pt; margin-top: 0; margin-bottom: 5px; }
            .period { text-align: center; font-style: italic; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th { background-color: #DDDDDD; font-weight: bold; text-align: left; padding: 8px; }
            td { padding: 8px; border-bottom: 1px solid #DDDDDD; }
            tr:nth-child(even) { background-color: #F9F9F9; }
            .total-row { font-weight: bold; background-color: #EEEEEE; }
            .footer { text-align: center; font-size: 9pt; margin-top: 30px; color: #666; }
        </style>
        <body>
            <h1>' . $church_name . '</h1>
            <h2>' . $title . '</h2>
            <div class="period">Period: ' . date('M d, Y', strtotime($start_date)) . ' to ' . date('M d, Y', strtotime($end_date)) . '</div>
            <div class="period">Generated on ' . date('F d, Y h:i A') . '</div>
            
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
        ';
        
        // Reset result pointer
        mysqli_data_seek($result, 0);
        
        // Add data rows
        $totalAmount = 0;
        while ($donation = mysqli_fetch_assoc($result)) {
            $html .= '
                <tr>
                    <td>' . $donation['id'] . '</td>
                    <td>' . $donation['member_name'] . '</td>
                    <td>$' . number_format($donation['amount'], 2) . '</td>
                    <td>' . date('M d, Y', strtotime($donation['donation_date'])) . '</td>
                    <td>' . $donation['payment_method'] . '</td>
                    <td>' . $donation['category'] . '</td>
                </tr>
            ';
            $totalAmount += $donation['amount'];
        }
        
        // Add total row
        $html .= '
                <tr class="total-row">
                    <td colspan="2">Total:</td>
                    <td>$' . number_format($totalAmount, 2) . '</td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>
        
        <div class="footer">
            This report was generated by the Church Management System on ' . date('F d, Y h:i:s A') . '<br>
            &copy; ' . date('Y') . ' ' . $church_name . '. All rights reserved.
        </div>
        </body>
        ';
        
        // Write HTML to PDF
        $mpdf->WriteHTML($html);
        
        // Set filename and output
        $filename = 'donations_report_' . date('Y-m-d') . '.pdf';
        $mpdf->Output($filename, 'D');
        exit;
    }
}

// If we get here, something went wrong
header('Location: reports.php');
exit;

