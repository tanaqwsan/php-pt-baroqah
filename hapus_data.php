<?php
// Fetch employee ID from URL parameters
$employeeId = isset($_GET['id']) ? $_GET['id'] : null;
$employeeName = isset($_GET['nama']) ? $_GET['nama'] : null;

if (!$employeeId) {
    die("Error: No employee ID provided.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize cURL session for DELETE request
    $ch = curl_init("http://localhost:8080/employees/$employeeId");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

    // Execute the DELETE request
    $response = curl_exec($ch);
    $response_data = json_decode($response, true);
    curl_close($ch);

    // Check if the request was successful
    if ($response_data['meta']['success']) {
        echo '<script>alert("Data berhasil dihapus!"); window.location.href="karyawan.php";</script>';
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
    <title>Hapus Data Karyawan</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center p-5 bg-light">
<div class="container bg-white p-4 rounded shadow-lg">
    <h1 class="mb-4">Hapus Data Karyawan</h1>
    <form action="hapus_data.php?id=<?php echo $employeeId; ?>" method="post">
        <p>Apakah Anda yakin ingin menghapus data karyawan dengan nama <?php echo htmlspecialchars($employeeName); ?>?</p>
        <div class="d-flex justify-content-between">
            <a href="karyawan.php" class="btn btn-primary">Batal</a>
            <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
    </form>
</div>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
