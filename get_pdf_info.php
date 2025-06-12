<?php
require('libs/fpdf.php');
$mysqli = new mysqli("localhost", "username", "password", "database");

// Check connection
if ($mysqli->connect_errno) {
    die("Failed to connect: " . $mysqli->connect_error);
}

$query = "SELECT * FROM make_generator";
$result = $mysqli->query($query);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Exam Data Report', 0, 1, 'C');
$pdf->Ln(5);

// Set header font
$pdf->SetFont('Arial', 'B', 9);

// Define column headers and widths
$headers = [
    ['label' => 'SEDI', 'width' => 15],
    ['label' => 'State', 'width' => 20],
    ['label' => 'PIN', 'width' => 15],
    ['label' => 'Email Name', 'width' => 30],
    ['label' => 'Email', 'width' => 35],
    ['label' => 'CC Name', 'width' => 25],
    ['label' => 'CC Email', 'width' => 35],
    ['label' => 'Batch', 'width' => 15],
    ['label' => 'Funding Resource', 'width' => 30],
    ['label' => 'Exam', 'width' => 15],
];

// Print headers
foreach ($headers as $col) {
    $pdf->Cell($col['width'], 10, $col['label'], 1);
}
$pdf->Ln();

// Set data font
$pdf->SetFont('Arial', '', 8);

// Print data rows
while ($row = $result->fetch_assoc()) {
    $pdf->Cell($headers[0]['width'], 10, $row['SEDI'], 1);
    $pdf->Cell($headers[1]['width'], 10, $row['State'], 1);
    $pdf->Cell($headers[2]['width'], 10, $row['PIN'], 1);
    $pdf->Cell($headers[3]['width'], 10, substr($row['Email Name'], 0, 20), 1);
    $pdf->Cell($headers[4]['width'], 10, substr($row['Email'], 0, 25), 1);
    $pdf->Cell($headers[5]['width'], 10, substr($row['CC Name'], 0, 20), 1);
    $pdf->Cell($headers[6]['width'], 10, substr($row['CC Email'], 0, 25), 1);
    $pdf->Cell($headers[7]['width'], 10, $row['Batch'], 1);
    $pdf->Cell($headers[8]['width'], 10, substr($row['Funding Resource'], 0, 20), 1);
    $pdf->Cell($headers[9]['width'], 10, $row['Exam'], 1);
    $pdf->Ln();
}

$mysqli->close();
$pdf->Output('D', 'exam_data_report.pdf');
?>
