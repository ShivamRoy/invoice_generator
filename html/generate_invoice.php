<?php
// Fetch SEDI values from the database

$query = "SELECT DISTINCT `SEDI` FROM make_generator";
$result = $mysqli->query($query);

// Start the dropdown form ?>

<form method="POST" action="">
<label for="sedi">Select SEDI:</label>
<select name="sedi" id="sedi">

<?php
echo '<option value="" disabled selected>Select a SEDI</option>';
// Loop through the results and create the dropdown options
while ($row = $result->fetch_assoc()) {
    $sedi = $row['SEDI'];
    echo '<option value="' . $sedi . '">' . $sedi . '</option>';
}
?>

<!-- // Close the dropdown and form -->
</select>
<input type="submit" value="Submit">
</form>
<div id="batchResult"></div>
<iframe id="pdfFrame" style="width: 100%; height: 600px; border: none;"></iframe>
<!-- <iframe src="invoice1.pdf" style="width: 100%; height: 600px; border: none;"></iframe> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>


<script>
document.getElementById('sediForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const sedi = document.getElementById('sedi').value;
    if (!sedi) return;

    // Make an AJAX request to fetch data
    const xhr = new XMLHttpRequest();
    xhr.open('GET', `get_batch.php?sedi=${sedi}`, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            const batchData = response.batchData; // Assume the result from PHP is in batchData
            
            // Display batch data in the div
            let batchResultHtml = '<h3>Batch Details for SEDI: ' + sedi + '</h3>';
            if (batchData.length > 0) {
                batchResultHtml += '<ul>';
                batchData.forEach(batch => {
                    batchResultHtml += `<li>${batch}</li>`;
                });
                batchResultHtml += '</ul>';
            } else {
                batchResultHtml += '<p>No batch data found for this SEDI.</p>';
            }
            document.getElementById('batchResult').innerHTML = batchResultHtml;

            // Now generate the PDF using jsPDF
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF();

            pdf.setFontSize(16);
            pdf.text('Batch Details for SEDI: ' + sedi, 10, 10);

            let y = 20;
            if (batchData.length > 0) {
                batchData.forEach(batch => {
                    pdf.text('Batch: ' + batch, 10, y);
                    y += 10;
                });
            } else {
                pdf.text('No batch data found for this SEDI.', 10, y);
            }

            // Display PDF in the div
            const pdfOutput = pdf.output('datauristring');
            document.getElementById('batchResult').innerHTML += `<iframe src="${pdfOutput}" width="100%" height="500px"></iframe>`;
        }
    };
    xhr.send();
});
</script>


<?php 
// Close the database connection
$mysqli->close();
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
<script>
        // When the SEDI dropdown value changes
        $(document).ready(function() {
            $("#sedi").change(function() {
                var selectedSEDI = $(this).val(); // Get the selected value

                // Check if a valid value is selected (not the default 'Select a SEDI' option)
                if (selectedSEDI) {
                    // Send the selected SEDI to the get_batch.php page via AJAX
                    $.ajax({
                        url: 'get_batch.php', // Page to send data to
                        type: 'GET', // HTTP method
                        data: { sedi: selectedSEDI }, // Data to send (SEDI value)
                        success: function(response) {
                            // Process the response from get_batch.php (e.g., show it in a div)
                            $("#batchResult").html(response);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error: " + error);
                        }
                    });
                }
            });
        });
    </script>


