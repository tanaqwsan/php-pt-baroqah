<?php
$nodata = false;
function fetchHttp($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        $nodata = true;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode == 404) {
        $nodata = true;
    }

    curl_close($ch);
    return $response;
}
// Fetch employee data from the API
$apiUrl = "http://localhost:8080/employees";
$response = fetchHttp($apiUrl);

if ($response === FALSE) {
    $nodata = true;
} else{
    $data = json_decode($response, true);
    $employees = $data['results'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Karyawan</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center m-4 bg-light">
<div class="container bg-white p-4 rounded shadow-lg">
    <a href="tambah_karyawan.php" class="btn btn-success mb-4">Tambah Karyawan</a>
    <h1 class="mb-4">Daftar Karyawan</h1>
    <ul class="list-group">
        <?php if (!$nodata) { foreach ($employees as $employee) { ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong><?php echo $employee['name']; ?></strong><br>
                    <?php echo $employee['position'] == 1 ? 'Manajer' : ($employee['position'] == 2 ? 'Supervisor' : 'Staff'); ?><br>
                    <?php echo $employee['nip']; ?>
                </div>
                <div>
                    <a href="rincian.php?id=<?php echo $employee['id']; ?>" class="btn btn-primary btn-sm mr-2">Rincian</a>
                    <a href="ubah_data.php?id=<?php echo $employee['id']; ?>" class="btn btn-warning btn-sm mr-2">Ubah data</a>
                    <a href="hapus_data.php?id=<?php echo $employee['id']; ?>&nama=<?php echo $employee['name'];?>" class="btn btn-danger btn-sm">Hapus data</a>
                </div>
            </li>
        <?php }}else{
            echo "<p>Data tidak tersedia.</p>";
        }  ?>
    </ul>
    <a href="index.php" class="btn btn-primary mt-4">Kembali</a>
</div>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
