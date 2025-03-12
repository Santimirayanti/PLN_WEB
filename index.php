<?php
require 'config/database.php';
session_start();

$messages = "";

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Admin') {
        header("Location: pages/admin/home.php");
        exit();
    } else {
        header("Location: pages/user/home.php");
        exit();
    }
}

// Fungsi untuk autentikasi user
function authenticate_user($conn, $username, $password)
{
    $sql = "SELECT id, username, password, role, email FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
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
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];

            // Arahkan berdasarkan role
            if ($user['role'] == 'Admin') {
                header("Location: pages/admin/home.php");
                exit();
            } else if ($user['role'] == 'User') {
                header("Location: pages/user/home.php");
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
  <style>
      body {
        background: linear-gradient(to right, #1e3a8a, #3b82f6);
        overflow: hidden;
      }
      .particles {
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        pointer-events: none;
        z-index: -1;
      }
    </style>
</head>

<body class="bg-gray-100 flex justify-center items-center h-screen">
<canvas class="particles"></canvas>
  <div class="bg-white p-8 rounded-lg shadow-lg text-center w-96">
    <img src="assets\img\logo_pln.png" alt="Logo PLN" class="mx-auto w-24 mb-4" />
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
  </div>

  <script>
      const canvas = document.querySelector(".particles");
      const ctx = canvas.getContext("2d");
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
      let particlesArray = [];

      class Particle {
        constructor() {
          this.x = Math.random() * canvas.width;
          this.y = Math.random() * canvas.height;
          this.size = Math.random() * 3 + 1;
          this.speedX = Math.random() * 1 - 0.5;
          this.speedY = Math.random() * 1 - 0.5;
        }
        update() {
          this.x += this.speedX;
          this.y += this.speedY;
          if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
          if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
        }
        draw() {
          ctx.fillStyle = "rgba(255, 255, 255, 0.7)";
          ctx.beginPath();
          ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
          ctx.fill();
        }
      }

      function init() {
        particlesArray = [];
        for (let i = 0; i < 100; i++) {
          particlesArray.push(new Particle());
        }
      }

      function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        particlesArray.forEach((particle) => {
          particle.update();
          particle.draw();
        });
        requestAnimationFrame(animate);
      }

      init();
      animate();
    </script>
</body>

</html>
