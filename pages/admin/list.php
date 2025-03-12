<?php
require '../../config/database.php';

session_start();
require '../../config/database.php';

if (!isset($_SESSION['id'])) {
  header("Location: ../../auth/login.php");
  exit;
}

if ($_SESSION['role'] !== 'Admin') {
  header("Location: ../user/home.php");
  exit;
}

$user_id = (int) $_SESSION['id'];

$query = "SELECT p.id, u.username AS user_name, p.name AS project_name, p.approval, 
                 p.status, GROUP_CONCAT(up.file_path) AS photo_paths
          FROM projects p
          JOIN users u ON p.user_id = u.id
          LEFT JOIN uploads up ON p.id = up.project_id
          GROUP BY p.id";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracker - PLN Web App</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">
    <button onclick="window.location.href='approver_home.html'"
        class="mb-4 px-4 py-2 bg-gray-500 text-white rounded">Kembali</button>

    <h1 class="text-2xl font-bold mb-4">Tracker Pekerjaan</h1>
    <table class="w-full bg-white shadow-md rounded-lg overflow-hidden">
        <thead class="bg-[#009DDB] text-white">
            <tr>
                <th class="p-4">User</th>
                <th class="p-4">Nama Pekerjaan</th>
                <th class="p-4">Status</th>
                <th class="p-4">Approved</th>
                <th class="p-4">Ubah Approval</th>
                <th class="p-4">Photo</th>
                <th class="p-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="border-b">
                    <td class="p-4 text-center align-middle"><?php echo htmlspecialchars($row['user_name']); ?></td>
                    <td class="p-4"><?php echo htmlspecialchars($row['project_name']); ?></td>
                    <td class="p-4 text-center font-bold
                        <?php echo ($row['status'] === 'Selesai') ? 'text-green-500' : 'text-gray-500'; ?>">
                        <?php echo htmlspecialchars($row['status']); ?>
                    </td>
                    <!-- Tampilkan Approval -->
                    <td class="p-4 text-center font-semibold 
    <?php
                echo ($row['approval'] === 'Butuh Peninjauan') ? 'text-yellow-500' : (($row['approval'] === 'Disetujui') ? 'text-green-500' : (($row['approval'] === 'Ditolak') ? 'text-red-500' : 'text-gray-500'));
    ?>">
                        <?php echo htmlspecialchars($row['approval']); ?>
                    </td>

                    <!-- Dropdown Ubah Approval -->
                    <td class="p-4">
                        <select class="approval-dropdown w-full p-2 border rounded"
                            data-project-id="<?php echo $row['id']; ?>">
                            <option value="Butuh Peninjauan" <?php echo ($row['approval'] === 'Butuh Peninjauan') ? 'selected' : ''; ?>>Butuh Peninjauan</option>
                            <option value="Ditolak" <?php echo ($row['approval'] === 'Ditolak') ? 'selected' : ''; ?>>Ditolak</option>
                            <option value="Disetujui" <?php echo ($row['approval'] === 'Disetujui') ? 'selected' : ''; ?>>Disetujui</option>
                        </select>
                    </td>
                    <td class="p-4">
                        <?php
                        $photos = explode(',', $row['photo_paths']);
                        ?>
                        <div class="flex gap-2 imageContainer">
                            <?php foreach ($photos as $photo): ?>
                                <?php if (!empty($photo)): ?>
                                    <img src="../../<?php echo htmlspecialchars($photo); ?>"
                                        class="w-20 h-20 object-cover cursor-pointer border rounded-md hover:scale-105 transition"
                                        onclick="openModal(this)">
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td class="p-4 text-center align-middle">
                        <button class="approval-submit px-4 py-2 bg-green-500 text-white rounded"
                            data-project-id="<?php echo $row['id']; ?>">Kirim</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- AJAX untuk Update Approval -->
    <script>
        document.querySelectorAll('.approval-submit').forEach(button => {
            button.addEventListener('click', async function() {
                let projectId = this.getAttribute('data-project-id');
                let selectElement = document.querySelector(`select[data-project-id='${projectId}']`);

                if (!selectElement) {
                    console.error(`Approval dropdown not found for project ID: ${projectId}`);
                    return;
                }

                let selectedApproval = selectElement.value;

                try {
                    let response = await fetch('../../config/approve.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `project_id=${encodeURIComponent(projectId)}&approval=${encodeURIComponent(selectedApproval)}`
                    });

                    let result = await response.text();

                    if (response.ok) {
                        alert(result);
                        location.reload();
                    } else {
                        console.error('Server error:', result);
                        alert('Terjadi kesalahan saat mengupdate status.');
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    alert('Gagal menghubungi server. Periksa koneksi Anda.');
                }
            });
        });
    </script>
</body>

</html>