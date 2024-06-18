<?php
// Fetch employee ID from URL parameters
$employeeId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$employeeId) {
    die("Error: No employee ID provided.");
}

// Fetch existing employee details from the API for pre-filling the form
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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from form
    $name = $_POST['name'];
    $address = $_POST['address'];
    $position = $_POST['position'];
    $birth_date = DateTime::createFromFormat('d-m-Y', $_POST['birth_date'])->format('dmY');
    $first_work_date = DateTime::createFromFormat('d-m-Y', $_POST['first_work_date'])->format('dmY');

    // Create data array to send
    $data = [
        'name' => $name,
        'address' => $address,
        'position' => $position,
        'birth_date' => $birth_date,
        'first_work_date' => $first_work_date
    ];

    // Initialize cURL session for PUT request
    $ch = curl_init("http://localhost:8080/employees/$employeeId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    // Execute the PUT request
    $response = curl_exec($ch);
    $response_data = json_decode($response, true);
    curl_close($ch);

    // Check if the request was successful
    if ($response_data['meta']['success']) {
        echo '<script>window.location.href="karyawan.php";</script>';
    } else {
        echo '<script>alert("Gagal memperbarui data: ' . htmlspecialchars($response_data['meta']['message']) . '");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data Karyawan</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center p-5 bg-light">
<div class="container bg-white p-4 rounded shadow-lg">
    <h1 class="mb-4">Ubah Data Karyawan</h1>
    <form action="ubah_data.php?id=<?php echo $employeeId; ?>" method="post">
        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($employee['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="address">Alamat</label>
            <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($employee['address']); ?>" required>
        </div>
        <div class="form-group">
            <label for="position">Golongan</label>
            <select id="position" name="position" class="form-control" required>
                <option value="1" <?php echo $employee['position'] == 1 ? 'selected' : ''; ?>>Manajer</option>
                <option value="2" <?php echo $employee['position'] == 2 ? 'selected' : ''; ?>>Supervisor</option>
                <option value="3" <?php echo $employee['position'] == 3 ? 'selected' : ''; ?>>Karyawan</option>
            </select>
        </div>
        <div class="form-group">
            <label for="birth_date">Tanggal Lahir</label>
            <input type="text" id="birth_date" name="birth_date" class="form-control" placeholder="dd-mm-yyyy" value="<?php echo DateTime::createFromFormat('dmY', $employee['birth_date'])->format('d-m-Y'); ?>" required>
        </div>
        <div class="form-group">
            <label for="first_work_date">Tanggal Masuk Kerja</label>
            <input type="text" id="first_work_date" name="first_work_date" class="form-control" placeholder="dd-mm-yyyy" value="<?php echo DateTime::createFromFormat('dmY', $employee['first_work_date'])->format('d-m-Y'); ?>" required>
        </div>
        <div class="d-flex justify-content-between">
            <a href="karyawan.php" class="btn btn-primary">Kembali</a>
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
