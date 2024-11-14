<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Upload</title>
</head>
<body>
    <h2>Upload CSV File</h2>
    <form id="uploadForm" action="index.php" method="POST" enctype="multipart/form-data">
        <label for="csvFile">Select CSV File:</label>
        <input type="file" id="csvFile" name="csvFile" accept=".csv" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
