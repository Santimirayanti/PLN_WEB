<?php 
session_start();
require '../config/database.php';

if (!isset($_SESSION['id'])) {
    die("Anda harus login!");
}

$messages = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $user_id = (int) $_SESSION['id'];
    $date = trim($_POST['date']);
    $status = trim($_POST['status']);

    if (empty($name) || empty($date) || empty($status)) {
        $messages = "<div class='text-red-500'>Semua kolom harus diisi!</div>";
    } else {
        $sql = "INSERT INTO projects (user_id, name, date, status) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "isss", $user_id, $name, $date, $status);
            if (mysqli_stmt_execute($stmt)) {
                $messages = "<div class='text-green-500'>Proyek berhasil ditambahkan!</div>";
            } else {
                $messages = "<div class='text-red-500'>Terjadi kesalahan saat menambahkan proyek: " . mysqli_error($conn) . "</div>";
            }
            mysqli_stmt_close($stmt);
        } else {
            $messages = "<div class='text-red-500'>Kesalahan pada query SQL: " . mysqli_error($conn) . "</div>";
        }
    }

    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Project - PLN Web App</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white p-6 rounded-lg shadow-md w-96">
      <h2 class="text-2xl font-bold mb-4 text-center">New Project</h2>

      <!-- Tampilkan pesan error atau sukses -->
      <div class="mb-4 text-center"><?= $messages; ?></div>

      <form action="" method="POST"> <!-- Perbaikan: Tambahkan method POST -->
        <div class="mb-4">
          <label class="block text-gray-700">Nama Pekerjaan</label>
          <input type="text" name="name" class="w-full p-2 border rounded" required />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Tanggal</label>
          <input type="date" name="date" class="w-full p-2 border rounded" required />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Status</label>
          <select name="status" class="w-full p-2 border rounded">
            <option value="Belum Dimulai">Belum Dimulai</option>
            <option value="Dalam Progress">Dalam Progress</option>
            <option value="Selesai">Selesai</option>
          </select>
        </div>
        <button type="submit" class="w-full bg-[#009DDB] text-white py-2 rounded mb-2">Tambah Pekerjaan</button>
        <a href="home.php" class="block w-full bg-gray-500 text-white py-2 rounded text-center">Kembali</a>
      </form>
    </div>
  </body>
</html>
