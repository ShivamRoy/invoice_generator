<?php 
function getData($mysqli, $query) {
    // Execute the query and check for errors
    $result = $mysqli->query($query);

    // If the query was successful and returned results
    if ($result) {
        // Fetch all results as an associative array
        $data = $result->fetch_all(MYSQLI_ASSOC);
        // Free the result set
        $result->free();
        // Return the data
        return $data;
    } else {
        // If the query fails, return an error message
        return 'Error: ' . $mysqli->error;
    }
}
?>