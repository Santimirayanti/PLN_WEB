<?php
// session_start();
require '../config/database.php'; // Pastikan koneksi ke database benar

$messages = "";

// Jika sudah login, langsung arahkan ke dashboard
// if (isset($_SESSION['role'])) {
//     if ($_SESSION['role'] == 'admin') {
//         header("Location: admin/dashboard.php");
//         exit();
//     } else {
//         header("Location: user/dashboard.php");
//         exit();
//     }
// }

// Fungsi untuk autentikasi user
function authenticate_user($conn, $username, $password)
{
    $query = "SELECT id, username, password, role, email FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        return $user; // Return data user jika login sukses
    }
    return false; // Return false jika gagal
}

// Cek data login jika form dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi input kosong
    if (empty($username) || empty($password)) {
        $messages = "<div class='text-red-500'>Username dan Password wajib diisi!</div>";
    } else {
        $user = authenticate_user($conn, $username, $password);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];

            // Arahkan berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
                exit();
            } else {
                header("Location: user/dashboard.php");
                exit();
            }
        } else {
            $messages = "<div class='text-red-500'>Username atau Password salah!</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Halaman Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex justify-center items-center h-screen">
  <div class="bg-white p-8 rounded-lg shadow-lg text-center w-96">
    <img src="..\assets\img\logo_pln.png" alt="Logo PLN" class="mx-auto w-24 mb-4" />
    <h2 class="text-2xl font-bold text-blue-600">Login</h2>

    <!-- Menampilkan pesan error jika ada -->
    <?php if (!empty($messages)) echo "<p class='mt-4'>$messages</p>"; ?>

    <!-- Form Login -->
    <form method="POST" class="mt-6">
      <input type="text" name="username" class="border p-2 rounded w-full" placeholder="Username" required />
      <input type="password" name="password" class="border p-2 rounded w-full mt-4" placeholder="Password" required />
      <button type="submit" name="login" class="mt-4 px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 w-full">
        Login
      </button>
    </form>

    <p class="mt-4 text-gray-600">Belum punya akun?</p>
    <a href="register.php" class="text-blue-500 underline cursor-pointer mt-2">Buat Akun</a>
  </div>
</body>

</html>
