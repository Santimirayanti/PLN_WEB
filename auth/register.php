<?php
session_start();
require '../config/database.php';

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../index.php");
    exit;
}

if ($_SESSION['role'] !== 'Admin') {
  header("Location: ../pages/user/home.php");
  exit;
}

$messages = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $passwordRepeat = trim($_POST["repeatPassword"]);

    $errors = [];

    // Validasi input
    if (empty($username) || empty($email) || empty($password) || empty($passwordRepeat)) {
        $errors[] = "Semua kolom harus diisi!";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid!";
    }
    if (strlen($password) < 4) {
        $errors[] = "Password setidaknya harus 4 karakter!";
    }
    if ($password !== $passwordRepeat) {
        $errors[] = "Password tidak sama!";
    }

    // Cek apakah email sudah ada di database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $errors[] = "Email sudah digunakan!";
    }
    mysqli_stmt_close($stmt);

    // Jika ada error, tampilkan pesan
    if (!empty($errors)) {
        foreach ($errors as $error) {
            $messages .= "<div class='bg-red-500 text-white p-2 rounded mt-2'>$error</div>";
        }
    } else {
        // Hash password dan simpan ke database
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password_hash);
            if (mysqli_stmt_execute($stmt)) {
                $messages = "<div class='bg-green-500 text-white p-2 rounded mt-2'>Akun berhasil dibuat. Silakan <a href='../index.php' class='underline'>login</a>.</div>";
            } else {
                $messages = "<div class='bg-red-500 text-white p-2 rounded mt-2'>Terjadi kesalahan saat registrasi.</div>";
            }
            mysqli_stmt_close($stmt);
        } else {
            $messages = "<div class='bg-red-500 text-white p-2 rounded mt-2'>Kesalahan dalam SQL.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg text-center w-96">
        <h2 class="text-2xl font-bold text-blue-600">Register</h2>

        <?php if (!empty($messages)) : ?>
            <div class="mt-4"><?php echo $messages; ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="mt-6">
            <input type="text" name="username" class="border p-2 rounded w-full" placeholder="Buat Username" required />
            <input type="email" name="email" class="border p-2 rounded w-full mt-4" placeholder="Masukkan Email" required />
            <input type="password" name="password" class="border p-2 rounded w-full mt-4" placeholder="Buat Password" required />
            <input type="password" name="repeatPassword" class="border p-2 rounded w-full mt-4" placeholder="Ulangi Password" required />
            <button type="submit" class="mt-4 px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 w-full">Daftar</button>
            <button onclick="window.history.back()" class="mt-4 px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-gray-600 w-full">Kembali</button>
        </form>
    </div>
</body>

</html>
