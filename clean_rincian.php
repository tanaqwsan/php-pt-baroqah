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

// Fetch position data to get basic salary
$positionId = $employee['position'];
$positionUrl = "http://localhost:8080/positions/$positionId";
$positionResponse = @file_get_contents($positionUrl);

if ($positionResponse === FALSE) {
    $positionData = ['meta' => ['success' => false, 'message' => 'Failed to retrieve position data.']];
} else {
    $positionData = json_decode($positionResponse, true);
}

$position = $positionData['meta']['success'] ? $positionData['results'] : ['basic_salary' => 0, 'name' => 'Unknown'];

// Fetch salary data from the API
$salaryUrl = "http://localhost:8080/salaries/$employeeId/employee";
$salaryResponse = @file_get_contents($salaryUrl);

if ($salaryResponse === FALSE) {
    $salaryData = ['meta' => ['success' => false, 'message' => 'Failed to retrieve salary data.']];
} else {
    $salaryData = json_decode($salaryResponse, true);
}

$salaries = $salaryData['meta']['success'] ? $salaryData['results'] : [];

// Helper function to get month name from month number
function getMonthName($monthNumber) {
    $months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    return $months[$monthNumber - 1];
}

// Helper function to format currency
function formatCurrency($amount) {
    return "Rp. " . number_format($amount, 0, ',', '.');
}

// Format dates from 'dmY' to 'd-m-Y'
function formatDate($date) {
    return DateTime::createFromFormat('dmY', $date)->format('d-m-Y');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji <?php echo $employee['name'] ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .container {
                border: none;
                box-shadow: none;
                padding: 0;
                margin: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body class="container p-4 mt-5">
<div class="container p-4">
    <h2 class="mb-4 text-center">Slip Gaji Karyawan<br>PT Baroqah</h2>
    <div class="mb-4">
        <h4>Rincian Karyawan</h4>
        <p><strong>Nama:</strong> <?php echo htmlspecialchars($employee['name']); ?></p>
        <p><strong>NIP:</strong> <?php echo $employee['nip']; ?></p>
        <p><strong>Golongan:</strong> <?php echo htmlspecialchars($position['name']); ?></p>
        <p><strong>Gaji Pokok:</strong> <?php echo formatCurrency($position['basic_salary']); ?></p>
        <p><strong>Tanggal Lahir:</strong> <?php echo formatDate($employee['birth_date']); ?></p>
        <p><strong>Tanggal Masuk Kerja:</strong> <?php echo formatDate($employee['first_work_date']); ?></p>
    </div>
    <div>
        <h4>Riwayat Gaji</h4>
        <?php if (!empty($salaries)) { ?>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Gaji Pokok</th>
                    <th>Bonus</th>
                    <th>PPH 5%</th>
                    <th>Total Gaji</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($salaries as $salary) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars(getMonthName($salary['month'])); ?></td>
                        <td><?php echo $salary['year']; ?></td>
                        <td><?php echo formatCurrency($salary['basic_salary']); ?></td>
                        <td><?php echo formatCurrency($salary['bonus']); ?></td>
                        <td><?php echo formatCurrency($salary['fee']); ?></td>
                        <td><?php echo formatCurrency($salary['final_salary']); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p>Belum ada gaji.</p>
        <?php } ?>
    </div>
</div>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Automatically trigger print dialog when page loads
    window.onload = function() {
        window.print();
    };
</script>
</body>
</html>
