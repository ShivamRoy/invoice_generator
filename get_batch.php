<?php
include '../testmysql.php';
include "../Invoice_generator/dbfunctions.php";
require('libs/fpdf.php');

if (isset($_GET['sedi'])) {
    $sedi = $_GET['sedi'];

    // Fetch data related to the selected SEDI
    $query = "SELECT * FROM make_generator WHERE `SEDI` = '$sedi'";
    $query_inv_res = getData($mysqli, $query, "array");
    $query_inv_row = count($query_inv_res);
    $invoice_num = 'INVOICE #2024-AMB-0219';

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(true, 10);
    // Colors and font setup
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(0, 0, 0);
    $pdf->SetFont('Arial', 'B', 14);
    // Company information on the left
    $pdf->Cell(0, 6, "AADDOO Softtech Pvt. Ltd.", 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, 'Plot No. C-5,', 0, 1);
    $pdf->Cell(0, 6, 'Institutional Area, Sector-6, 134109', 0, 1);
    $pdf->Cell(0, 6, 'Panchkula - 134109', 0, 1);
    // Move cursor up to the top right of the page for "INVOICE" information
    $pdf->SetXY(140, 10); // Adjust the X and Y coordinates as needed
    $pdf->SetTextColor(169, 169, 169); // Gray for "INVOICE" label
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 6, 'INVOICE', 0, 1, 'R');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetXY(140, 20); // Adjust the Y position as needed to align
    $pdf->Cell(0, 6, 'INVOICE #2024-AMB-0219', 0, 1, 'R');
    $pdf->Cell(0, 6, 'DATE: ' . date('F d, Y'), 0, 1, 'R');
    $pdf->Ln(5); // Add a line break if needed
    // Client Information (as before)
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 6, 'TO:', 0, 1);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 6, "Ambuja Foundation", 0, 1);
    $pdf->Cell(0, 6, 'SEDI ' . $query_inv_res[0]['SEDI'] . ' - ' . $query_inv_res[0]['PIN'], 0, 1);
    $pdf->Cell(0, 6, $query_inv_res[0]['State']);
    $pdf->Ln(10);

    // Table Header
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(30, 10, 'QUANTITY', 1, 0, 'C', true);
    $pdf->Cell(130, 10, 'DESCRIPTION', 1, 0, 'C', true);
    $pdf->Cell(30, 10, 'TOTAL', 1, 1, 'C', true);

    // Table Content
    $pdf->SetFont('Arial', '', 10);
    $total = 0;

    for ($i = 0; $i < $query_inv_row; $i++) {
        $batch = $query_inv_res[$i]['Batch'];
        $exam = $query_inv_res[$i]['Exam'];
        $userAttempts = $query_inv_res[$i]['User Attempts'];
        $examDate = $query_inv_res[$i]['Exam Date']; // Assuming the date is in a standard format like 'Y-m-d' or 'Y-m-d H:i:s'
        // Create a DateTime object from the date string
        $date = new DateTime($examDate);
        // Format the date in DMY format
        $formattedDate = $date->format('d-m-Y'); // 'd' = Day, 'm' = Month, 'Y' = Year
        echo $formattedDate; // Output: e.g., 12-11-2024

        $amount = $userAttempts * 13;
        $total += $amount;

        $pdf->Cell(30, 10, $userAttempts . ' Users', 1);
        $pdf->Cell(130, 10, "Exam for $batch on $formattedDate [Rs.13 per user] ($userAttempts*13)", 1);
        $pdf->Cell(30, 10, "Rs. " . number_format($amount, 2), 1, 1, 'R');
    }

    // Subtotal, Tax, Total
    $tax = $total * 0.18;
    $totalDue = $total + $tax;

    // Set the font and add the subtotal, tax, and total cells
    $pdf->SetX($pdf->GetX() + 20); // Move right 20mm from current position
    $pdf->Cell(130, 10, 'SUBTOTAL', 1);
    $pdf->Cell(30, 10, "Rs. " . number_format($total, 2), 1, 1, 'R');

    $pdf->Cell(160, 10, 'SALES TAX (18%)', 1);
    $pdf->Cell(30, 10, "Rs. " . number_format($tax, 2), 1, 1, 'R');

    $pdf->Cell(160, 10, 'TOTAL DUE', 1);
    $pdf->Cell(30, 10, "Rs. " . number_format($totalDue, 2), 1, 1, 'R');

    // Footer message
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(0, 5, 'Make all checks payable to AADDOO Softtech Private Limited', 0, 1);

    // Align image and account details in the same row
    $pdf->Ln(10); // Line break for space
    $pdf->SetFont('Arial', '', 10);

    // Set X and Y positions for the image to align it on the same row as the account details
    $pdf->Cell(0, 5, 'Account Details:', 0, 1); // Title for account details
    $pdf->Cell(100, 5, 'Bank: PNB Solan', 0, 0); // Account detail text cell
    $pdf->Image('aaddoo_stamp.jpg', 120, $pdf->GetY() - 5, 30, 30); // Image aligned at Y position of the current line
    $pdf->Ln(5); // Move to next line for subsequent details
    $pdf->Cell(100, 5, 'Account Number: 0433002100076170', 0, 1);
    $pdf->Cell(100, 5, 'IFSC: PUNB0043300', 0, 1);
    $pdf->Cell(100, 5, 'GSTIN: 02AAQCA0032M2ZW', 0, 1);
    $pdf->Cell(100, 5, 'PAN: AAQCA0032M', 0, 1);

    // Additional footer details
    $pdf->Ln(10);
    $pdf->Cell(0, 10, 'Payment is due immediately.', 0, 1);
    $pdf->Ln(5);
    $pdf->Cell(0, 6, 'Thank you for your business!', 0, 1);

    // Centered bottom image
    $pageWidth = $pdf->GetPageWidth();
    $imageWidth = 20;
    $x = ($pageWidth - $imageWidth) / 2;
    $pdf->Image('aaddoo.ai.jpg', $x, null, $imageWidth, 20);

    // Save and open the PDF
    $pdfFile = 'invoice1.pdf';
    $pdf->Output('F', $pdfFile);
    echo "<script>window.open('$pdfFile', '_blank');</script>";
}
