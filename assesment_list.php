<?php
include "../testmysql.php";

$all_list = "SELECT * FROM make_generator";
$result = $mysqli->query($all_list);

// Now include your table page
include "html/assesment_list.php";
?>
