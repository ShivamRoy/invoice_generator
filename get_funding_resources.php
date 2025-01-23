<?php
// Include database connection and necessary functions
include "../testmysql.php";
include "dbfunctions.php";

if (isset($_GET['sedi']) && !empty($_GET['sedi'])) {
    $selectedSEDI = htmlspecialchars($_GET['sedi']); // Sanitize the input

    // Debug: Echo the selected SEDI value for troubleshooting (optional)
    // echo $selectedSEDI;

    // Query to fetch funding resources based on the selected SEDI
    $query = "SELECT DISTINCT `Funding Resource` FROM `make_generator` WHERE `SEDI` = '$selectedSEDI'";

    // Fetch the data using your `getData()` function
    $query_res = getData($mysqli, $query, "array");

    // Check if any funding resources are returned and that they are not empty
    $validResources = array_filter($query_res, function ($row) {
        return !empty($row['Funding Resource']);
    });

    if (!empty($validResources)) {
        echo '<label for="funding_resource">Funding Resource:</label>';
        echo '<select name="fund" id="fund">';
        echo '<option value="" disabled selected>Select a Funding Resource</option>';

        // Loop through the valid result array to create the dropdown options
        foreach ($validResources as $row) {
            $fundResource = htmlspecialchars($row['Funding Resource']); // Sanitize output
            echo '<option value="' . $fundResource . '">' . $fundResource . '</option>';
        }

        echo '</select>';
    } else {
        // Don't display the dropdown if no valid funding resources are found
        echo '<p>No Funding Resources available for the selected SEDI.</p>';
    }
} else {
    // Invalid or missing SEDI parameter
    echo '<p>Invalid SEDI selected.</p>';
}
?>
