<?php
// Include FPDF library
require('libs/fpdf.php');




// Fetch data from database
$query = "SELECT * FROM make_generator";
$result = $mysqli->query($query);

// Initialize FPDF and create PDF
$pdf = new FPDF();
$pdf->AddPage();

// Set font for the title
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Exam Data Report', 0, 1, 'C');
$pdf->Ln(10); // Line break

// Set column headers
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(15, 10, 'SEDI', 1);
$pdf->Cell(20, 10, 'State', 1);
$pdf->Cell(15, 10, 'PIN', 1);
$pdf->Cell(30, 10, 'Email Name', 1);
$pdf->Cell(30, 10, 'Email', 1);
$pdf->Cell(25, 10, 'CC Name', 1);
$pdf->Cell(30, 10, 'CC Email', 1);
$pdf->Cell(15, 10, 'Batch', 1);
$pdf->Cell(25, 10, 'Funding Resource', 1);
$pdf->Cell(15, 10, 'Exam', 1);
$pdf->Ln();

// Set font for data rows
$pdf->SetFont('Arial', '', 9);

// Populate data rows
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(15, 10, $row['SEDI'], 1);
    $pdf->Cell(20, 10, $row['State'], 1);
    $pdf->Cell(15, 10, $row['PIN'], 1);
    $pdf->Cell(30, 10, $row['Email Name'], 1);
    $pdf->Cell(30, 10, $row['Email'], 1);
    $pdf->Cell(25, 10, $row['CC Name'], 1);
    $pdf->Cell(30, 10, $row['CC Email'], 1);
    $pdf->Cell(15, 10, $row['Batch'], 1);
    $pdf->Cell(25, 10, $row['Funding Resource'], 1);
    $pdf->Cell(15, 10, $row['Exam'], 1);
    $pdf->Ln();
}

// Close the connection
$mysqli->close();

// Output the PDF
$pdf->Output('D', 'exam_data_report.pdf'); // D for download, I for inline display
?>