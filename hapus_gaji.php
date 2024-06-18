<?php
// Fetch employee ID and salary ID from URL parameters
$employeeId = isset($_GET['id']) ? $_GET['id'] : null;
$salaryId = isset($_GET['salary_id']) ? $_GET['salary_id'] : null;

if (!$employeeId || !$salaryId) {
    die("Error: Employee ID and Salary ID are required.");
}

// Fetch existing salary details for confirmation
$salaryUrl = "http://localhost:8080/salaries/$salaryId";
$salaryResponse = @file_get_contents($salaryUrl);

if ($salaryResponse === FALSE) {
    die("Error: Failed to retrieve salary data.");
}

$salaryData = json_decode($salaryResponse, true);

if (!$salaryData['meta']['success']) {
    die("Error: " . $salaryData['meta']['message']);
}

$salary = $salaryData['results'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize cURL session for DELETE request
    $ch = curl_init("http://localhost:8080/salaries/$salaryId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

    // Execute the DELETE request
    $response = curl_exec($ch);
    $response_data = json_decode($response, true);
    curl_close($ch);

    // Check if the request was successful
    if ($response_data['meta']['success']) {
        echo '<script>alert("Data gaji berhasil dihapus!"); window.location.href="rincian.php?id=' . $employeeId . '";</script>';
    } else {
        echo '<script>alert("Gagal menghapus data: ' . htmlspecialchars($response_data['meta']['message']) . '");</script>';
    }
}
?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hapus Gaji</title>
        <!-- Bootstrap CSS -->
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
    <div class="container mt-5">
        <h1 class="mb-4">Hapus Gaji</h1>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Detail Gaji</h5>
                <p class="card-text">
                    <strong>Bulan:</strong> <?php echo htmlspecialchars(getMonthName($salary['month'])); ?><br>
                    <strong>Tahun:</strong> <?php echo $salary['year']; ?><br>
                    <strong>Gaji Pokok:</strong> <?php echo formatCurrency($salary['basic_salary']); ?><br>
                    <strong>Bonus:</strong> <?php echo formatCurrency($salary['bonus']); ?><br>
                    <strong>PPH 5%:</strong> <?php echo formatCurrency($salary['fee']); ?><br>
                    <strong>Total Gaji:</strong> <?php echo formatCurrency($salary['final_salary']); ?>
                </p>
                <form action="hapus_gaji.php?id=<?php echo $employeeId; ?>&salary_id=<?php echo $salaryId; ?>" method="post">
                    <p>Apakah Anda yakin ingin menghapus gaji ini?</p>
                    <div class="d-flex justify-content-between">
                        <a href="rincian.php?id=<?php echo $employeeId; ?>" class="btn btn-primary">Batal</a>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.amazonaws.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>

<?php
// Helper function to get month name from month number
function getMonthName($monthNumber) {
    $months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    return $months[$monthNumber - 1];
}

// Helper function to format currency
function formatCurrency($amount) {
    return "Rp. " . number_format($amount, 0, ',', '.');
}
?>