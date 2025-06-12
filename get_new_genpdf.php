<?php
include '../testmysql.php';
include "../Invoice_generator/dbfunctions.php";
require('libs/fpdf.php');

if (isset($_GET['batches'])) {
    $batchesData = json_decode($_GET['batches'], true);

    if (!is_array($batchesData)) {
        die("Invalid batch data.");
    }

    // Initialize PDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Title
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Invoice Batches', 0, 1, 'C');
    $pdf->Ln(5);

    // Table Headers
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(60, 10, 'Batch', 1);
    $pdf->Cell(60, 10, 'User Attempts', 1);
    $pdf->Cell(60, 10, 'Exam Date', 1);
    $pdf->Ln();

    // Table Content
    $pdf->SetFont('Arial', '', 12);
    foreach ($batchesData as $batch) {
        $batchNumber = isset($batch['batchNumber']) ? $batch['batchNumber'] : 'N/A';
        $userAttempts = isset($batch['userAttempts']) ? $batch['userAttempts'] : '0';
        $examDate = isset($batch['date']) ? $batch['date'] : 'N/A';

        $pdf->Cell(60, 10, $batchNumber, 1);
        $pdf->Cell(60, 10, $userAttempts, 1);
        $pdf->Cell(60, 10, $examDate, 1);
        $pdf->Ln();
    }

    // Draw outer rectangle
    $startX = 10;
    $startY = 20;
    $sectionWidth = 190;
    $sectionHeight = $pdf->GetY() - $startY;
    $pdf->Rect($startX, $startY, $sectionWidth, $sectionHeight);

    // Add logo/image at bottom center
    $pdf->Ln(10);
    $pageWidth = $pdf->GetPageWidth();
    $imageWidth = 30;
    $imagePath = 'aaddoo.ai.jpg';

    if (file_exists($imagePath)) {
        $x = ($pageWidth - $imageWidth) / 2;
        $pdf->Image($imagePath, $x, $pdf->GetY(), $imageWidth);
    } else {
        $pdf->Cell(0, 10, 'Logo not found.', 0, 1, 'C');
    }

    // Output PDF to browser
    $pdf->Output('I', 'invoice1.pdf');
} else {
    echo "No batch data received.";
}
?>
