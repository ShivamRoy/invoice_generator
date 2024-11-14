<?php
$query = "SELECT DISTINCT `SEDI` FROM make_generator";
$result = $mysqli->query($query); ?>

<form method="POST" action="">
    <label for="sedi">Select SEDI:</label>
    <select name="sedi" id="sedi">

        <?php
        echo '<option value="" disabled selected>Select a SEDI</option>';
        // Loop through the results and create the dropdown options
        while ($row = $result->fetch_assoc()) {
            $sedi = $row['SEDI'];
            echo '<option value="' . $sedi . '">' . $sedi . '</option>';
        } ?>
    </select>
    
    <input type="submit" value="Submit">
</form>
<div id="batchResult"></div>
<iframe id="pdfFrame" style="width: 100%; height: 600px; border: none;"></iframe>
<?php
// Close the database connection
$mysqli->close(); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    data: {
                        sedi: selectedSEDI
                    }, // Data to send (SEDI value)
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