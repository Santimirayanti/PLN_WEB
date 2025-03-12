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
      <a href="approver_home.html" class="block py-3 px-4 text-lg text-[#009DDB] hover:bg-[#8ac8e0] hover:text-white">Dashboard</a>
      <a href="approver_list.html" class="block py-3 px-4 text-lg text-[#009DDB] hover:bg-[#8ac8e0] hover:text-white">Approval List</a>
      <button onclick="window.location.href='../../auth/login.html'" class="block py-3 px-4 w-full text-left text-lg text-red-600 hover:bg-red-400 hover:text-white">Logout</button>
    </nav>
  </aside>
  
  <main id="main-content" class="p-8 transition-all duration-300">
    <h1 class="text-3xl font-bold text-gray-700 mb-6">Dashboard Approver</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div class="bg-[#8ac8e0] p-6 rounded-xl shadow-lg">
        <h3 class="text-lg font-semibold text-black-500">Total Projects Pending Approval</h3>
        <p id="pending-approval" class="text-3xl font-bold text-black-500">0</p>
      </div>
      <div class="bg-[#8ac8e0] p-6 rounded-xl shadow-lg">
        <h3 class="text-lg font-semibold text-black-500">Approved Projects</h3>
        <p id="approved-projects" class="text-3xl font-bold text-black-500">0</p>
      </div>
      <div class="bg-[#8ac8e0] p-6 rounded-xl shadow-lg">
        <h3 class="text-lg font-semibold text-black-500">Rejected Projects</h3>
        <p id="rejected-projects" class="text-3xl font-bold text-black-500">0</p>
      </div>
    </div>
    <div class="bg-[#8ac8e0] p-6 rounded-xl shadow-lg mt-8">
      <h3 class="text-lg font-semibold text-black-500 text-center">Projects Awaiting Approval</h3>
      
      <label for="status-filter" class="block text-gray-700 mt-4">Filter by Status:</label>
      <select id="status-filter" class="w-full p-2 border border-gray-300 rounded mt-2 bold">
        <option value="all">All</option>
        <option value="Pending">Need Review</option>
        <option value="Approved">Approved</option>
        <option value="Rejected">Rejected</option>
      </select>
      
      <div class="overflow-y-auto max-h-64">
        <table class="w-full mt-4 border-collapse border border-gray-300">
          <thead>
            <tr class="bg-gray-300">
              <th class="border p-3">Project Name</th>
              <th class="border p-3">Submitted By</th>
              <th class="border p-3">Status</th>
            </tr>
          </thead>
          <tbody id="approval-table">
            <tr data-status="Pending">
              <td class="bg-white border p-3">Pembangunan Gardu Induk</td>
              <td class="bg-white border p-3">Ahmad Yani</td>
              <td class="bg-white border p-3 text-center text-yellow-500 font-bold">Need Review</td>
            </tr>
            <tr data-status="Approved">
              <td class="bg-white border p-3">Modernisasi Jaringan Listrik</td>
              <td class="bg-white border p-3">Siti Rahma</td>
              <td class="bg-white border p-3 text-center text-green-500 font-bold">Approved</td>
            </tr>
            <tr data-status="Rejected">
              <td class="bg-white border p-3">Proyek Pembangkit Tenaga Surya</td>
              <td class="bg-white border p-3">Budi Santoso</td>
              <td class="bg-white border p-3 text-center text-red-500 font-bold">Reject</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </main>
  
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      let menuToggle = document.getElementById("menu-toggle");
      let closeMenu = document.getElementById("close-menu");
      let sidebar = document.getElementById("sidebar");
      let mainContent = document.getElementById("main-content");
      
      menuToggle.addEventListener("click", function () {
        sidebar.classList.toggle("-translate-x-full");
        mainContent.classList.toggle("ml-64");
      });
      closeMenu.addEventListener("click", function () {
        sidebar.classList.add("-translate-x-full");
        mainContent.classList.remove("ml-64");
      });

      // Filtering functionality
      document.getElementById("status-filter").addEventListener("change", function () {
        let selectedStatus = this.value;
        let rows = document.querySelectorAll("#approval-table tr");
        
        rows.forEach(row => {
          let status = row.getAttribute("data-status");
          if (selectedStatus === "all" || status === selectedStatus) {
            row.style.display = "table-row";
          } else {
            row.style.display = "none";
          }
        });
      });
    });
  </script>
</body>
</html>