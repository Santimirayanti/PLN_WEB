<?php
session_start();

if (!isset($_SESSION['id'])) {
  die("Anda harus login!");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - PLN Web App</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">
  <!-- <script>
      document.addEventListener("DOMContentLoaded", function () {
        // Cek apakah pengguna sudah login
        if (!localStorage.getItem("isLoggedIn")) {
          window.location.href = "../auth/login.html"; // Redirect ke login jika belum login
        }
      });

      // Fungsi Logout
      function logout() {
        console.log("Logout function triggered"); // Debugging
        localStorage.removeItem("isLoggedIn"); // Hapus status login
        window.location.href = "../auth/login.html"; // Redirect ke login
      }
    </script> -->

  <div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-[#ffffff] text-white p-5 flex flex-col items-center">
      <img src="C:\Users\Asus\Downloads\pln_app (4)\pln_app\assets\img\logo_pln.png"
        alt="PLN Logo"
        class="w-24 mb-4">
      <nav class="mt-5 w-full">
        <a href="home.php" class="block py-2 px-4 hover:bg-[#8ac8e0] hover:text-white text-[#009DDB]">Dashboard</a>
        <a href="new_project.php" class="block py-2 px-4 hover:bg-[#8ac8e0] hover:text-white text-[#009DDB]">New Project</a>
        <a href="tracker.php" class="block py-2 px-4 hover:bg-[#8ac8e0] hover:text-white text-[#009DDB]">Tracker</a>
        <button id="logoutButton" class="block py-2 px-4 w-full text-left hover:bg-[#FF6B6B] text-[#009DDB]">
          Logout
        </button>
      </nav>
    </aside>
    </button>
    </nav>
    </aside>
    </button>
    </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-6">
      <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

      <!-- Cards Section -->
      <!-- Cards Section -->
      <div class="grid grid-cols-4 gap-4">
        <div class="bg-[#009DDB] p-4 rounded-lg shadow">
          <h3 class="text-lg font-semibold">Total Projects</h3>
          <p id="total-projects" class="text-2xl font-bold">0</p>
        </div>
        <div class="bg-[#009DDB] p-4 rounded-lg shadow">
          <h3 class="text-lg font-semibold">Completed Projects</h3>
          <p id="completed-projects" class="text-2xl font-bold">0</p>
        </div>
        <div class="bg-[#009DDB] p-4 rounded-lg shadow">
          <h3 class="text-lg font-semibold">In Progress</h3>
          <p id="in-progress" class="text-2xl font-bold">0</p>
        </div>
        <div class="bg-[#009DDB] p-4 rounded-lg shadow">
          <h3 class="text-lg font-semibold">Pending</h3>
          <p id="pending-projects" class="text-2xl font-bold">0</p>
        </div>
      </div>

      <!-- Chart Section -->
      <div class="bg-[#009DDB] p-4 rounded-lg shadow mt-6">
        <h3 class="text-lg font-semibold mb-4 text-center text-white">
          Project Status Overview
        </h3>
        <div class="flex justify-center">
          <canvas id="projectChart" class="w-48 h-48"></canvas>
        </div>
      </div>

      <script>
        document.addEventListener("DOMContentLoaded", function() {
          // Ambil data projects dari localStorage
          let projects = JSON.parse(localStorage.getItem("projects")) || [];

          let total = projects.length;
          let completed = projects.filter((p) => p.status === "Selesai").length;
          let inProgress = projects.filter(
            (p) => p.status === "Dalam Progress"
          ).length;
          let pending = projects.filter(
            (p) => p.status === "Belum Dimulai"
          ).length;

          // Update elemen HTML dengan data
          document.getElementById("total-projects").textContent = total;
          document.getElementById("completed-projects").textContent = completed;
          document.getElementById("in-progress").textContent = inProgress;
          document.getElementById("pending-projects").textContent = pending;

          // Buat Chart
          const ctx = document.getElementById("projectChart").getContext("2d");
          new Chart(ctx, {
            type: "doughnut",
            data: {
              labels: ["Completed", "In Progress", "Pending"],
              datasets: [{
                data: [completed, inProgress, pending],
                backgroundColor: ["#4CAF50", "#FFC107", "#F44336"],
              }, ],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
            },
          });

          // Event Listener untuk tombol Logout
          document
            .getElementById("logoutButton")
            .addEventListener("click", logout);
        });
      </script>
</body>

</html>