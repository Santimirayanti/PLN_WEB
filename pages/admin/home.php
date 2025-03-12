<?php
session_start();
require '../../config/database.php';

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../../index.php");
    exit;
}

if ($_SESSION['role'] !== 'Admin') {
  header("Location: ../user/home.php");
  exit;
}

$user_id = (int) $_SESSION['id'];


$query = "SELECT projects.id AS project_id, projects.name, projects.date, 
       projects.status, projects.approval, users.username, 
       uploads.category, GROUP_CONCAT(uploads.file_path) AS photo_paths
FROM projects
JOIN users ON projects.user_id = users.id
LEFT JOIN uploads ON projects.id = uploads.project_id
GROUP BY projects.id, uploads.category";
          
$result = mysqli_query($conn, $query);


$projects = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Approver PLN Web App</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100">
  <header class="bg-white shadow-md py-4 px-6 flex justify-between items-center">
    <button id="menu-toggle" class="text-gray-700 text-2xl">☰</button>
    <img src="../../assets/img/logo_pln.png" alt="PLN Logo" class="w-20" />
  </header>

  <aside id="sidebar" class="fixed top-0 left-0 w-64 h-full bg-white shadow-lg p-6 transform -translate-x-full transition-transform duration-300">
    <button id="close-menu" class="text-gray-700 text-xl absolute top-4 right-4">✖</button>
    <nav class="mt-10">
      <a href="home.php" class="block py-3 px-4 text-lg text-[#009DDB] hover:bg-[#8ac8e0] hover:text-white">Dashboard</a>
      <a href="list.php" class="block py-3 px-4 text-lg text-[#009DDB] hover:bg-[#8ac8e0] hover:text-white">Approval List</a>
      <a href="../../auth/register.php" class="block py-3 px-4 text-lg text-[#009DDB] hover:bg-[#8ac8e0] hover:text-white">Create Account User</a>
      <button onclick="window.location.href='../../auth/logout.php'" class="block py-3 px-4 w-full text-left text-lg text-red-600 hover:bg-red-400 hover:text-white">Logout</button>
    </nav>
  </aside>

  <main id="main-content" class="p-8 transition-all duration-300">
    <h1 class="text-3xl font-bold text-gray-700 mb-6">Dashboard Approver</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div class="bg-[#8ac8e0] p-6 rounded-xl shadow-lg">
        <h3 class="text-lg font-semibold">Total Projects Pending Approval</h3>
        <p class="text-3xl font-bold"> <?php echo count(array_filter($projects, fn($p) => $p['approval'] === 'Butuh Peninjauan')); ?> </p>
      </div>
      <div class="bg-[#8ac8e0] p-6 rounded-xl shadow-lg">
        <h3 class="text-lg font-semibold">Approved Projects</h3>
        <p class="text-3xl font-bold"> <?php echo count(array_filter($projects, fn($p) => $p['approval'] === 'Disetujui')); ?> </p>
      </div>
      <div class="bg-[#8ac8e0] p-6 rounded-xl shadow-lg">
        <h3 class="text-lg font-semibold">Rejected Projects</h3>
        <p class="text-3xl font-bold"> <?php echo count(array_filter($projects, fn($p) => $p['approval'] === 'Ditolak')); ?> </p>
      </div>
    </div>
    <div class="bg-[#8ac8e0] p-6 rounded-xl shadow-lg mt-8">
      <h3 class="text-lg font-semibold text-center">Projects Awaiting Approval</h3>
      <label for="status-filter" class="block text-gray-700 mt-4">Filter by Status:</label>
      <select id="status-filter" class="w-full p-2 border border-gray-300 rounded mt-2">
        <option value="all">All</option>
        <option value="Butuh Peninjauan">Butuh Peninjauan</option>
        <option value="Disetujui">Disetujui</option>
        <option value="Ditolak">Ditolak</option>
      </select>
      <div class="flex justify-end mt-4">
        <button onclick="exportToCSV()" 
            class="px-5 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 shadow-md transition">
            📥 Download CSV
        </button>
    </div>
      <div class="overflow-y-auto max-h-64">
        <table class="w-full mt-4 border-collapse border border-gray-300">
          <thead>
            <tr class="bg-gray-300">
              <th class="border p-3">Project Name</th>
              <th class="border p-3">Submitted By</th>
              <th class="border p-3">Status</th>
              <th class="border p-3">Approval</th>
            </tr>
          </thead>
          <tbody id="approval-table">
            <?php foreach ($projects as $project): ?>
              <tr data-status="<?php echo htmlspecialchars($project['approval']); ?>">
                <td class="bg-white border p-3"><?php echo htmlspecialchars($project['name']); ?></td>
                <td class="bg-white border p-3"><?php echo htmlspecialchars($project['username']); ?></td>

                <td class="bg-white border p-3 text-center 
      <?php
              echo ($project['status'] === 'Dalam Progress') ? 'text-yellow-500' : (($project['status'] === 'Selesai') ? 'text-green-500' : 'text-gray-500'); ?> font-bold">
                  <?php echo htmlspecialchars($project['status']); ?>
                </td>

                <td class="bg-white border p-3 text-center 
      <?php
              echo ($project['approval'] === 'Butuh Peninjauan') ? 'text-yellow-500' : (($project['approval'] === 'Disetujui') ? 'text-green-500' : (($project['approval'] === 'Ditolak') ? 'text-red-500' : 'text-gray-500')); ?> font-bold">
                  <?php echo htmlspecialchars($project['approval']); ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>

        </table>
      </div>
    </div>
  </main>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      let menuToggle = document.getElementById("menu-toggle");
      let closeMenu = document.getElementById("close-menu");
      let sidebar = document.getElementById("sidebar");
      let mainContent = document.getElementById("main-content");

      menuToggle.addEventListener("click", function() {
        sidebar.classList.toggle("-translate-x-full");
        mainContent.classList.toggle("ml-64");
      });

      closeMenu.addEventListener("click", function() {
        sidebar.classList.add("-translate-x-full");
        mainContent.classList.remove("ml-64");
      });

      document.getElementById("status-filter").addEventListener("change", function() {
        let selectedStatus = this.value;
        let rows = document.querySelectorAll("#approval-table tr");

        rows.forEach(row => {
          let status = row.getAttribute("data-status")?.trim();
          row.style.display = (selectedStatus === "all" || status === selectedStatus) ? "table-row" : "none";
        });
      });
    });
  </script>
  <script>
    function exportToCSV() {
        let table = document.getElementById("approval-table"); 
        let rows = table.getElementsByTagName("tr");
        let csvContent = "data:text/csv;charset=utf-8,";
        let headers = ["Project Name", "Submitted By", "Status", "Approval"];

        // Tambahkan header ke CSV
        csvContent += headers.join(",") + "\n";

        // Loop setiap baris dan ambil data
        for (let i = 0; i < rows.length; i++) {
            let cols = rows[i].getElementsByTagName("td");
            let rowData = [];

            for (let j = 0; j < cols.length; j++) {
                rowData.push(cols[j].innerText.trim());
            }

            csvContent += rowData.join(",") + "\n";
        }

        // Membuat file CSV dan unduh
        let encodedUri = encodeURI(csvContent);
        let link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "Projects_Awaiting_Approval.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
</body>

</html>