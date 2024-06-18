<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from form
    $level = $_POST['level'];
    $name = $_POST['name'];
    $basic_salary = $_POST['basic_salary'];
    $bonus = $_POST['bonus'];

    // Create data array to send
    $data = [
        'level' => $level,
        'name' => $name,
        'basic_salary' => $basic_salary,
        'bonus' => $bonus
    ];

    // Initialize cURL session for POST request
    $ch = curl_init("http://localhost:8080/positions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

    // Execute the POST request
    $response = curl_exec($ch);
    $response_data = json_decode($response, true);
    curl_close($ch);

    // Check if the request was successful
    if ($response_data['meta']['success']) {
        echo '<script>alert("Data berhasil ditambahkan!"); window.location.href="jabatan.php";</script>';
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
    <title>Tambah Jabatan</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Tambah Jabatan</h1>
    <form action="tambah_jabatan.php" method="post">
        <div class="form-group">
            <label for="level">Level</label>
            <input type="number" id="level" name="level" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="name">Nama Jabatan</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="basic_salary">Gaji Pokok</label>
            <input type="number" id="basic_salary" name="basic_salary" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="bonus">Bonus (%)</label>
            <input type="number" id="bonus" name="bonus" class="form-control" required>
        </div>
        <div class="d-flex justify-content-between">
            <a href="jabatan.php" class="btn btn-primary">Kembali</a>
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
