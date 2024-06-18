<?php
// Fetch position level from URL parameters
$positionLevel = isset($_GET['level']) ? $_GET['level'] : null;

if (!$positionLevel) {
    die("Error: No position level provided.");
}

// Fetch existing position details from the API for display confirmation
$positionUrl = "http://localhost:8080/positions/$positionLevel";
$positionResponse = @file_get_contents($positionUrl);

if ($positionResponse === FALSE) {
    die("Error: Failed to retrieve position data.");
}

$positionData = json_decode($positionResponse, true);

if (!$positionData['meta']['success']) {
    die("Error: " . $positionData['meta']['message']);
}

$position = $positionData['results'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize cURL session for DELETE request
    $ch = curl_init("http://localhost:8080/positions/$positionLevel");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

    // Execute the DELETE request
    $response = curl_exec($ch);
    $response_data = json_decode($response, true);
    curl_close($ch);

    // Check if the request was successful
    if ($response_data['meta']['success']) {
        echo '<script>alert("Data berhasil dihapus!"); window.location.href="jabatan.php";</script>';
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
    <title>Hapus Jabatan</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Hapus Jabatan</h1>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">Level: <?php echo htmlspecialchars($position['level']); ?></h5>
            <h6 class="card-subtitle mb-2 text-muted">Nama Jabatan: <?php echo htmlspecialchars($position['name']); ?></h6>
            <p class="card-text">
                Gaji Pokok: <?php echo "Rp. " . number_format($position['basic_salary'], 0, ',', '.'); ?><br>
                Bonus: <?php echo htmlspecialchars($position['bonus']); ?>%
            </p>
            <form action="hapus_jabatan.php?level=<?php echo $positionLevel; ?>" method="post">
                <p>Apakah Anda yakin ingin menghapus jabatan ini?</p>
                <div class="d-flex justify-content-between">
                    <a href="jabatan.php" class="btn btn-primary">Batal</a>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
