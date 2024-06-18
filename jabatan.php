


<?php
// Fetch position data from the API
$positionUrl = "http://localhost:8080/positions";
$positionResponse = @file_get_contents($positionUrl);
$nodata = false;
if ($positionResponse === FALSE) {
    $nodata = true;
} else{
    $positionData = json_decode($positionResponse, true);
    $positions = $positionData['results'];
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Jabatan</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center p-5 bg-light">
<div class="container bg-white p-4 rounded shadow-lg">
    <h1 class="mb-4">Daftar Jabatan</h1>
    <div class="mb-3">
        <a href="tambah_jabatan.php" class="btn btn-success">Tambah Jabatan</a>
    </div>
    <?php if (!$nodata) { foreach ($positions as $position) { ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($position['level']); ?></h5>
                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($position['name']); ?></h6>
                <p class="card-text">
                    Gaji Pokok: <?php echo "Rp. " . number_format($position['basic_salary'], 0, ',', '.'); ?><br>
                    Bonus: <?php echo htmlspecialchars($position['bonus']); ?>%
                </p>
                <a href="ubah_jabatan.php?level=<?php echo $position['level']; ?>" class="btn btn-warning">Ubah data</a>
                <a href="hapus_jabatan.php?level=<?php echo $position['level']; ?>" class="btn btn-danger">Hapus data</a>
            </div>
        </div>
    <?php }}else{
        echo "<p>Data tidak tersedia.</p>";
    } ?>
    <div class="d-flex justify-content-start">
        <a href="index.php" class="btn btn-primary">Kembali</a>
    </div>
</div>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
