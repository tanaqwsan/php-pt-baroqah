<?php
// Fetch employee ID from URL parameters
$employeeId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$employeeId) {
    die("Error: No employee ID provided.");
}

// Fetch employee details from the API
$employeeUrl = "http://localhost:8080/employees/$employeeId";
$employeeResponse = @file_get_contents($employeeUrl);

if ($employeeResponse === FALSE) {
    die("Error: Failed to retrieve employee data.");
}

$employeeData = json_decode($employeeResponse, true);

if (!$employeeData['meta']['success']) {
    die("Error: " . $employeeData['meta']['message']);
}

$employee = $employeeData['results'];

// Fetch position details based on the employee's position
$positionId = $employee['position'];
$positionUrl = "http://localhost:8080/positions/$positionId";
$positionResponse = @file_get_contents($positionUrl);

if ($positionResponse === FALSE) {
    die("Error: Failed to retrieve position data.");
}

$positionData = json_decode($positionResponse, true);

if (!$positionData['meta']['success']) {
    die("Error: " . $positionData['meta']['message']);
}

$position = $positionData['results'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Gaji Per Bulan</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center bg-light">
<div class="container bg-white p-4 rounded shadow-lg" style="max-width: 400px;">
    <h2 class="mb-4 text-center">Cetak Gaji Per Bulan</h2>
    <div>
        <p><strong>Nama:</strong> <?php echo htmlspecialchars($employee['name']); ?></p>
        <p><strong>NIP:</strong> <?php echo $employee['nip']; ?></p>
        <p><strong>Golongan:</strong> <?php echo htmlspecialchars($position['name']); ?></p>
    </div>
    <form action="rincian_gaji_kustom_per_bulan.php" method="get">
        <input type="hidden" name="id" value="<?php echo $employeeId; ?>">
        <div class="form-group">
            <label for="month">Bulan</label>
            <input type="number" id="month" name="month" class="form-control" required>
        </div>
        <div class="d-flex justify-content-between">
            <a href="rincian.php?id=<?php echo $employeeId ?>" class="btn btn-primary">Kembali</a>
            <button type="submit" formtarget="_blank" class="btn btn-primary">Proses</button>
        </div>
    </form>
</div>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>