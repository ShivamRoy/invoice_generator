
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice Analytics Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f9fa; color: #333; margin: 0; }
        h1 { text-align: center; color: #007bff; margin-top: 20px; }
        h2 { background: #007bff; color: white; padding: 10px; border-radius: 5px; }
        .container { width: 95%; margin: 20px auto; }
        .summary-cards { display: flex; gap: 20px; justify-content: center; margin-bottom: 30px; }
        .card {
            flex: 1; background: white; padding: 20px; border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1); text-align: center;
        }
        .card h3 { margin: 0; font-size: 1.2rem; color: #555; }
        .card p { font-size: 1.8rem; margin-top: 8px; color: #007bff; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
        .download-btn {
            display: block; width: fit-content; margin: 10px auto 30px;
            background: #28a745; color: white; padding: 10px 20px;
            text-decoration: none; border-radius: 5px; font-weight: bold;
        }
        .download-btn:hover { background: #218838; }
    </style>
</head>
<body>
<div class="container">
    <h1>Financial Dashboard (FY 2025–26)</h1>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="card">
            <h3>Total Invoiced Amount (₹)</h3>
            <p><?= number_format($totalInvoiced, 2) ?></p>
        </div>
        <div class="card">
            <h3>Total Payment Received (₹)</h3>
            <p><?= number_format($totalReceived, 2) ?></p>
        </div>
    </div>

    <!-- Download Button -->
    <a href="export_excel.php" class="download-btn">⬇️ Download Full Report (Excel)</a>

    <!-- SEDI-wise Summary -->
    <h2>SEDI-wise Financial Summary</h2>
    <table>
        <tr>
            <th>SEDI Name</th>
            <th>Total Invoices</th>
            <th>Invoice Numbers</th>
            <th>Total Invoiced (₹)</th>
            <th>Payment Received (₹)</th>
            <th>Pending (₹)</th>
        </tr>
        <?php while($row = $sediResult->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['sedi_name']) ?></td>
            <td><?= $row['total_invoices'] ?></td>
            <td><?= htmlspecialchars($row['invoice_numbers']) ?></td>
            <td><?= number_format($row['total_invoiced'], 2) ?></td>
            <td><?= number_format($row['total_received'], 2) ?></td>
            <td><?= number_format($row['total_pending'], 2) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Month-wise Summary -->
    <h2>Month-wise Financial Summary</h2>
    <table>
        <tr>
            <th>Month</th>
            <th>Total Invoices</th>
            <th>Total Invoiced (₹)</th>
            <th>Payment Received (₹)</th>
            <th>Pending (₹)</th>
        </tr>
        <?php while($row = $monthResult->fetch_assoc()): ?>
        <tr>
            <td><?= $row['month'] ?></td>
            <td><?= $row['total_invoices'] ?></td>
            <td><?= number_format($row['total_invoiced'], 2) ?></td>
            <td><?= number_format($row['total_received'], 2) ?></td>
            <td><?= number_format($row['total_pending'], 2) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>