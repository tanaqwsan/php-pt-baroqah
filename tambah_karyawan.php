<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect data from form
    $name = $_POST['name'];
    $address = $_POST['address'];
    $position = $_POST['position'];
    $birth_date = $_POST['birth_date'];
    $first_work_date = $_POST['first_work_date'];

    // Convert dates from 'dd-mm-yyyy' to 'dmY'
    $birth_date = DateTime::createFromFormat('d-m-Y', $birth_date)->format('dmY');
    $first_work_date = DateTime::createFromFormat('d-m-Y', $first_work_date)->format('dmY');

    // Initialize cURL session
    $ch = curl_init('http://localhost:8080/employees');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);

    // Set the form-data fields
    $formData = [
        'name' => $name,
        'address' => $address,
        'position' => $position,
        'birth_date' => $birth_date,
        'first_work_date' => $first_work_date,
    ];
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formData));

    // Execute the POST request
    $response = curl_exec($ch);
    $response_data = json_decode($response, true);
    curl_close($ch);

    // Check if the request was successful
    if ($response_data['meta']['success']) {
        echo '<script>window.location.href="karyawan.php";</script>';
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
    <title>Tambah Karyawan</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center m-4 bg-light">
<div class="container bg-white p-4 rounded shadow-lg">
    <h1 class="mb-4">Tambah Karyawan</h1>
    <form action="tambah_karyawan.php" method="post">
        <div class="form-group">
            <label for="name">Nama</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="address">Alamat</label>
            <input type="text" class="form-control" id="address" name="address" required>
        </div>
        <div class="form-group">
            <label for="position">Golongan</label>
            <select id="position" name="position" class="form-control" required>
                <option value="1">Manajer</option>
                <option value="2">Supervisor</option>
                <option value="3">Staff</option>
            </select>
        </div>
        <div class="form-group">
            <label for="birth_date">Tanggal Lahir</label>
            <input type="text" class="form-control" id="birth_date" name="birth_date" placeholder="dd-mm-yyyy" required>
        </div>
        <div class="form-group">
            <label for="first_work_date">Tanggal Masuk Kerja</label>
            <input type="text" class="form-control" id="first_work_date" name="first_work_date" placeholder="dd-mm-yyyy" required>
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
