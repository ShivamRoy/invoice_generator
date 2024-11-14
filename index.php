<?php
include "../testmysql.php";

// File upload handling
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csvFile"])) {
    $file = $_FILES["csvFile"]["tmp_name"];

    if (($handle = fopen($file, "r")) !== FALSE) {
        // Skip the header row if present
        fgetcsv($handle, 1000, ",");

        // Read each row of the CSV
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Assign data to variables as per the CSV column order
            $sedi = $data[1];
            $state = $data[2];
            $pin = $data[3];
            $email_name = $data[4];
            $email = $data[5];
            $cc_name = $data[6];
            $cc_email = $data[7];
            $batch = $data[8];
            $funding_resource = $data[9];
            $exam = $data[10];
            $requested_users = $data[11];
            $user_attempts = $data[12];
            $exam_date = $data[13];
            $exam_status = $data[14];
            $invoice_sent = $data[15];
            $invoice_date = $data[16];
            $remarks = $data[17];
            $invoices = $data[18];

            // Insert data into database
            $sql = "INSERT INTO make_generator (SEDI, `State`, PIN, `Email Name`, Email, `CC Name`, `CC Email`, Batch, `Funding Resource`, Exam, `Requested Users`, `User Attempts`, `Exam Date`, `Exam Status`, `Invoice Sent`, `Invoice Date`, Remarks, Invoices) 
                    VALUES ('$sedi', '$state', '$pin', '$email_name', '$email', '$cc_name', '$cc_email', '$batch', '$funding_resource', '$exam', '$requested_users', '$user_attempts', '$exam_date', '$exam_status', '$invoice_sent', '$invoice_date', '$remarks', '$invoices')";

            $mysqli->query($sql);
        }
        
        fclose($handle);
        echo "CSV data uploaded successfully!";
    } else {
        echo "Error opening file.";
    }
} else {
    // echo "File upload failed.";
}
// Close the database connection at the end
$mysqli->close();
// Close the database connection

?>
<?php
include "html/index.php";

?> 