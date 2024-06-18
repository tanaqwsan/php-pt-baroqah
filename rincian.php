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
    <title>Rincian Karyawan</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center p-5 bg-light">
<div class="container bg-white p-4 rounded shadow-lg">
    <h1 class="mb-4">Rincian Karyawan</h1>
    <div class="employee-details mb-4">
        <p><strong>Nama:</strong> <?php echo htmlspecialchars($employee['name']); ?></p>
        <p><strong>NIP:</strong> <?php echo $employee['nip']; ?></p>
        <p><strong>Golongan:</strong> <?php echo htmlspecialchars($position['name']); ?></p>
        <p><strong>Gaji Pokok:</strong> <?php echo formatCurrency($position['basic_salary']); ?></p>
        <p><strong>Tanggal Lahir:</strong> <?php echo formatDate($employee['birth_date']); ?></p>
        <p><strong>Tanggal Masuk Kerja:</strong> <?php echo formatDate($employee['first_work_date']); ?></p>
    </div>
    <h2 class="mb-4">Riwayat Gaji</h2>
    <div class="salary-history mb-4">
        <?php if (!empty($salaries)) { ?>
            <table class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Gaji Pokok</th>
                    <th>Bonus</th>
                    <th>PPH 5%</th>
                    <th>Total Gaji</th>
                    <th>Action</th>
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
                        <td>
                            <a href="hapus_gaji.php?id=<?php echo $employeeId; ?>&salary_id=<?php echo $salary['id']; ?>" class="btn btn-danger btn-sm">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            <p>Belum ada gaji.</p>
        <?php } ?>
    </div>

    <div class="d-flex flex-column flex-md-row gap-2">
        <a href="karyawan.php" class="btn btn-primary ml-0 mb-4 mt-4 mr-4">Kembali</a>
        <a href="buat_gaji.php?id=<?php echo $employeeId; ?>&month=<?php echo htmlspecialchars(date('n')); ?>" class="btn btn-success m-4">Buat Gaji</a>
        <div class="ml-md-auto d-flex flex-column flex-md-row gap-2">
            <a href="cetak_gaji_kustom_bulan.php?id=<?php echo $employeeId ?>" class="btn btn-primary m-4">Cetak Gaji Per Bulan</a>
            <a href="cetak_gaji_custom.php?id=<?php echo $employeeId ?>" class="btn btn-primary m-4">Cetak Gaji Kustom</a>
            <a href="clean_rincian.php?id=<?php echo $employeeId ?>" target="_blank" class="btn btn-primary ml-4 mb-4 mt-4 mr-0">Cetak Gaji</a>
        </div>
    </div>

</div>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
