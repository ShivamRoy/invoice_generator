<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SEDI-wise Month-wise Financial Report</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #0d6efd;
            margin: 20px 0;
        }

        .filters {
            text-align: center;
            margin-bottom: 20px;
        }

        select {
            padding: 6px 10px;
            font-size: 14px;
            margin: 0 5px;
        }

        table.dataTable th {
            background: #0d6efd;
            color: white;
        }

        table.dataTable tr:nth-child(even) {
            background: #f2f2f2;
        }

        table {
            width: 95%;
            margin: auto;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <h1>SEDI-wise Month-wise Financial Report (FY 2025–26)</h1>

    <div class="container col-md-10">
        <div class="filters">
            <label for="sediFilter">Filter by SEDI:</label>
            <select id="sediFilter">
                <option value="">All</option>
                <?php while ($sedi = $sediList->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($sedi['sedi_name']) ?>"><?= htmlspecialchars($sedi['sedi_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label for="monthFilter">Filter by Month:</label>
            <select id="monthFilter">
                <option value="">All</option>
                <?php while ($month = $monthList->fetch_assoc()): ?>
                    <option value="<?= $month['month'] ?>"><?= $month['month'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <table id="reportTable" class="display nowrap" style="width:95%">
            <thead>
                <tr>
                    <th>SEDI Name</th>
                    <th>Month</th>
                    <th>Total Invoiced (₹)</th>
                    <th>Total Received (₹)</th>
                    <th>Pending (₹)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()):
                    $pending = $row['total_invoiced'] - $row['total_received'];
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['sedi_name']) ?></td>
                        <td><?= $row['month'] ?></td>
                        <td><?= number_format($row['total_invoiced'], 2) ?></td>
                        <td><?= number_format($row['total_received'], 2) ?></td>
                        <td><?= number_format($pending, 2) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            const table = $('#reportTable').DataTable({
                dom: 'Bfrtip',
                buttons: ['excelHtml5', 'csvHtml5', 'print'],
                pageLength: 25,
                responsive: true
            });

            $('#sediFilter, #monthFilter').on('change', function() {
                const sediVal = $('#sediFilter').val();
                const monthVal = $('#monthFilter').val();

                table.column(0).search(sediVal);
                table.column(1).search(monthVal);
                table.draw();
            });
        });
    </script>
</body>

</html>