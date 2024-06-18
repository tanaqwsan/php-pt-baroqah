<?php
// Fetch position level from URL parameters
$positionLevel = isset($_GET['level']) ? $_GET['level'] : null;

if (!$positionLevel) {
    die("Error: No position level provided.");
}

// Fetch existing position details from the API
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

    // Initialize cURL session for PUT request
    $ch = curl_init("http://localhost:8080/positions/$positionLevel");
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
        echo '<script>alert("Data berhasil diperbarui!"); window.location.href="jabatan.php";</script>';
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
    <title>Ubah Jabatan</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center p-5 bg-light">
<div class="container bg-white p-4 rounded shadow-lg">
    <h1 class="mb-4">Ubah Jabatan</h1>
    <form action="ubah_jabatan.php?level=<?php echo $positionLevel; ?>" method="post">
        <div class="form-group">
            <label for="level">Level</label>
            <input type="number" id="level" name="level" class="form-control" value="<?php echo htmlspecialchars($position['level']); ?>" required readonly>
        </div>
        <div class="form-group">
            <label for="name">Nama Jabatan</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($position['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="basic_salary">Gaji Pokok</label>
            <input type="number" id="basic_salary" name="basic_salary" class="form-control" value="<?php echo htmlspecialchars($position['basic_salary']); ?>" required>
        </div>
        <div class="form-group">
            <label for="bonus">Bonus (%)</label>
            <input type="number" id="bonus" name="bonus" class="form-control" value="<?php echo htmlspecialchars($position['bonus']); ?>" required>
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
