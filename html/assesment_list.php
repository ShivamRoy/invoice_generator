<!DOCTYPE html>
<html>
<head>
    <title>Assessment List</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px 10px;
            text-align: center;
        }
        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>

<h2>Make Generator Data</h2>

<!-- Dropdown filter -->
<label for="sediFilter">Filter by SEDI: </label>
<select id="sediFilter">
    <option value="">All</option>
    <?php
    // collect unique SEDIs from the result for dropdown
    $result->data_seek(0); // reset pointer to start
    $sedis = [];
    while ($row = $result->fetch_assoc()) {
        if (!in_array($row['SEDI'], $sedis)) {
            $sedis[] = $row['SEDI'];
            echo "<option value='" . htmlspecialchars($row['SEDI']) . "'>" . htmlspecialchars($row['SEDI']) . "</option>";
        }
    }
    $result->data_seek(0); // reset again for table rows
    ?>
</select>

<!-- Data table -->
<table id="dataTable">
    <thead>
        <tr>
            <th>Select</th>
            <th>S.No</th>
            <th>SEDI</th>
            <th>Batch</th>
            <th>User Attempts</th>
            <th>Exam Date</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sno = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr data-sedi='" . htmlspecialchars($row['SEDI']) . "'>";
            echo "<td><input type='checkbox' class='row-check' value='{$row['id']}'></td>";
            echo "<td>{$sno}</td>";
            echo "<td>{$row['SEDI']}</td>";
            echo "<td>{$row['Batch']}</td>";
            echo "<td>{$row['User Attempts']}</td>";
            echo "<td>{$row['Exam Date']}</td>";
            echo "</tr>";
            $sno++;
        }
        ?>
    </tbody>
</table>
<button id="generatePdfBtn">Generate PDF</button>

<script>
// Filter rows by SEDI
document.getElementById('sediFilter').addEventListener('change', function () {
    let selected = this.value;
    let rows = document.querySelectorAll('#dataTable tbody tr');

    rows.forEach(row => {
        if (selected === "" || row.getAttribute('data-sedi') === selected) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});
</script>

</body>
</html>

<script>
document.getElementById('generatePdfBtn').addEventListener('click', function() {
    let selected = [];
    document.querySelectorAll('.row-check:checked').forEach(cb => {
        selected.push(cb.value); // get "id" value
    });

    if (selected.length === 0) {
        alert("Please select at least one row!");
        return;
    }

    // Send IDs to get_batch.php via POST
    fetch('get_batch.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ids: selected })
    })
    .then(response => response.blob()) // expecting PDF
    .then(blob => {
        let url = window.URL.createObjectURL(blob);
        let a = document.createElement('a');
        a.href = url;
        a.download = "batch_report.pdf"; // filename
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
    })
    .catch(err => console.error("Error:", err));
});
</script>
