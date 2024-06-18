<?php
// Fetch employee ID and month from URL parameters
$employeeId = isset($_GET['id']) ? $_GET['id'] : null;
$month = isset($_GET['month']) ? $_GET['month'] : null;
$postMonth = isset($_POST['month']) ? $_POST['month'] : null;
$postYear = isset($_POST['year']) ? $_POST['year'] : null;
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


// Helper function to format currency
function formatCurrency($amount) {
    return "Rp. " . number_format($amount, 0, ',', '.');
}

if (!$employeeId || !$month) {
    die("Error: Employee ID and month are required.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Initialize cURL session for POST request
    $ch = curl_init("http://localhost:8080/salaries/$employeeId/employee/$postMonth/$postYear");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
    ]);

    // Execute the POST request
    $response = curl_exec($ch);
    $response_data = json_decode($response, true);
    curl_close($ch);

    // Check if the request was successful
    if ($response_data['meta']['success']) {
        echo '<script>alert("Data gaji berhasil ditambahkan!"); window.location.href="rincian.php?id=' . $employeeId . '";</script>';
    } else {
        echo '<script>alert("Gagal menambahkan data: ' . htmlspecialchars($response_data['meta']['message']) . '");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Gaji</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center p-5 bg-light">
<div class="container bg-white p-4 rounded shadow-lg">
    <h1 class="mb-4">Buat Gaji</h1>
    <form action="buat_gaji.php?id=<?php echo $employeeId; ?>&month=<?php echo $month; ?>" method="post">
        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo $employee['name']; ?>" required readonly>
        </div>
        <div class="form-group">
            <label for="basic_salary">Gaji Pokok</label>
            <input type="text" id="basic_salary" name="basic_salary" value="<?php $theBasicSalary = $position['basic_salary']; echo htmlspecialchars(formatCurrency($position['basic_salary'])) ?>" class="form-control" required readonly>
        </div>
        <div class="form-group">
            <label for="bonus">Bonus</label>
            <input type="text" id="bonus" name="bonus" value="<?php $theBonus = $position['basic_salary']*$position['bonus']/100; echo formatCurrency($theBonus) ?>" class="form-control" required readonly>
        </div>
        <div class="form-group">
            <label for="fee">PPH (5%)</label>
            <input type="text" id="fee" name="fee" value="<?php $theFee = 5 * ($theBonus + $theBasicSalary) / 100; echo formatCurrency($theFee) ?>" class="form-control" required readonly>
        </div>
        <div class="form-group">
            <label for="final_salary">Total Gaji</label>
            <input type="text" id="final_salary" name="final_salary" value="<?php $theFinalSalary = $theBasicSalary + $theBonus - $theFee; echo formatCurrency($theFinalSalary) ?>" class="form-control" readonly>
        </div>
        <div class="form-group">
            <label for="month">Bulan</label>
            <select id="month" name="month" class="form-control" required>
                <option value="1" <?php if($month==1){echo "selected='true'";} ?>>Januari</option>
                <option value="2" <?php if($month==2){echo "selected='true'";} ?>>Februari</option>
                <option value="3" <?php if($month==3){echo "selected='true'";} ?>>Maret</option>
                <option value="4" <?php if($month==4){echo "selected='true'";} ?>>April</option>
                <option value="5" <?php if($month==5){echo "selected='true'";} ?>>Mei</option>
                <option value="6" <?php if($month==6){echo "selected='true'";} ?>>Juni</option>
                <option value="7" <?php if($month==7){echo "selected='true'";} ?>>Juli</option>
                <option value="8" <?php if($month==8){echo "selected='true'";} ?>>Agustus</option>
                <option value="9" <?php if($month==9){echo "selected='true'";} ?>>September</option>
                <option value="10" <?php if($month==10){echo "selected='true'";} ?>>Oktober</option>
                <option value="11" <?php if($month==11){echo "selected='true'";} ?>>November</option>
                <option value="12" <?php if($month==12){echo "selected='true'";} ?>>Desember</option>
            </select>
        </div>
        <div class="form-group">
            <label for="year">Tahun</label>
            <input type="number" id="year" name="year" value="2024" class="form-control">
        </div>
        <div class="d-flex justify-content-between">
            <a href="rincian.php?id=<?php echo $employeeId; ?>" class="btn btn-primary">Kembali</a>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </div>
    </form>
</div>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
