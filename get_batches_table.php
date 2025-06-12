<?php
include '../testmysql.php';

$query = "SELECT DISTINCT Batch, `User Attempts`, `Exam Date` FROM make_generator ORDER BY Batch";
$result = $mysqli->query($query);
?>

<form id="batchForm">
    <table>
        <thead>
            <tr>
                <th>Select</th>
                <th>Batch Number</th>
                <th>User Attempts</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td><input type="checkbox" class="batchCheckbox" name="batches[]" value="' . $row['Batch'] . '" data-batch="' . $row['Batch'] . '" data-user-attempts="' . $row['User Attempts'] . '" data-date="' . $row['Exam Date'] . '"></td>';
                echo '<td>' . $row['Batch'] . '</td>';
                echo '<td>' . $row['User Attempts'] . '</td>';
                echo '<td>' . $row['Exam Date'] . '</td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
    <button  id="generateInvoiceBtn" type="submit">Generate PDF</button>
</form>

<div id="errorMessage" style="color: red; display: none;">Please select at least one batch.</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $("#generateInvoiceBtn").click(function(e) {
            e.preventDefault(); // Prevent form submission

            var selectedBatches = [];
            $("input[name='batches[]']:checked").each(function() {
                var batchNumber = $(this).data('batch'); // Get the batch number
                var userAttempts = $(this).data('user-attempts'); // Get the user attempts
                var date = $(this).data('date'); // Get the date

                selectedBatches.push({
                    batchNumber: batchNumber,
                    userAttempts: userAttempts,
                    date: date
                });
            });

            // Check if any batch is selected
            if (selectedBatches.length > 0) {
                // Send the data to get_new_genpdf.php via AJAX
                $.ajax({
                    url: 'get_new_genpdf.php',
                    type: 'GET',
                    data: {
                        batches: JSON.stringify(selectedBatches) // Send as JSON
                    },
                    success: function(response) {
                        // Handle the PDF response (open in new window)
                        var pdfWindow = window.open();
                        pdfWindow.document.write(response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error: " + error);
                    }
                });
            } else {
                alert('Please select at least one batch.');
            }
        });
    });
</script>
