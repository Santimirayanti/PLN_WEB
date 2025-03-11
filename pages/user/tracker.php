<?php
require '../../config/database.php';
session_start();

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../../auth/login.php");
    exit;
}

$sql = "SELECT id, name, status, approval FROM projects WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracker Pekerjaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">

    <h1 class="text-2xl font-bold mb-4">Tracker Pekerjaan</h1>

    <div class="overflow-x-auto">
        <table class="w-full min-w-max bg-white shadow-md rounded-lg overflow-hidden text-sm md:text-base">
            <thead class="bg-[#009DDB] text-white">
                <tr>
                    <th class="p-4">Nama Pekerjaan</th>
                    <th class="p-4">Status</th>
                    <th class="p-4">Approval</th>
                    <th class="p-4">Gambar</th>
                    <th class="p-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = mysqli_fetch_assoc($result)) :
                    // Ambil gambar berdasarkan project_id
                    $upload_sql = "SELECT file_path FROM uploads WHERE project_id = ? LIMIT 1";
                    $upload_stmt = mysqli_prepare($conn, $upload_sql);
                    mysqli_stmt_bind_param($upload_stmt, "i", $row['id']);
                    mysqli_stmt_execute($upload_stmt);
                    $upload_result = mysqli_stmt_get_result($upload_stmt);
                    $upload_data = mysqli_fetch_assoc($upload_result);
                    $image_path = $upload_data['file_path'] ?? null;
                    mysqli_stmt_close($upload_stmt);
                ?>
                    <tr class="border-b">
                        <!-- Nama Pekerjaan -->
                        <td class="p-4"><?= htmlspecialchars($row['name']) ?></td>

                        <!-- Status Proyek -->
                        <td class="p-4 text-center">
                            <?php
                            $statusClass = match ($row['status']) {
                                'Belum Dimulai' => 'bg-gray-500',
                                'Dalam Progress' => 'bg-yellow-500',
                                'Selesai' => 'bg-green-500',
                                default => 'bg-gray-500'
                            };
                            ?>
                            <span class="px-4 py-2 <?= $statusClass ?> text-white rounded"><?= htmlspecialchars($row['status']) ?></span>
                        </td>

                        <!-- Status Approval -->
                        <td class="p-4 text-center">
                            <?php
                            $approvalClass = match ($row['approval'] ?? 'Belum Diajukan') {
                                'Disetujui' => 'bg-green-500',
                                'Butuh Peninjauan' => 'bg-yellow-500',
                                'Ditolak' => 'bg-red-300',
                                default => 'bg-gray-500'
                            };
                            $approvalText = $row['approval'] ?? 'Belum Diajukan';
                            ?>
                            <span class="px-4 py-2 <?= $approvalClass ?> text-white rounded"><?= $approvalText ?></span>
                        </td>

                        <!-- Gambar -->
                        <td class="p-4 text-center">
                            <?php if ($image_path) : ?>
                                <a href="<?= htmlspecialchars($image_path) ?>" target="_blank">
                                    <img src="<?= htmlspecialchars($image_path) ?>" class="w-16 h-16 rounded border" alt="Gambar">
                                </a>
                            <?php else : ?>
                                <span class="text-gray-500">Belum Upload</span>
                            <?php endif; ?>
                        </td>

                        <!-- Aksi -->
                        <td class="p-4 text-center">
                            <?php if ($row['approval'] == 'Belum Diajukan') : ?>
                                <form method="POST" action="../../config/kirim.php" enctype="multipart/form-data">
                                    <input type="hidden" name="project_id" value="<?= $row['id'] ?>">

                                    <select name="category" required class="w-full p-2 border rounded">
                                        <option value="Upload foto pelaksanaan">Upload foto pelaksanaan</option>
                                        <optgroup label="Foto Kegiatan">
                                            <option value="Pengecekan Dokumen">Pengecekan Dokumen DP3, JSA, WP</option>
                                            <option value="Pantauan hotspot">Pantauan hotspot sebelum manuver</option>
                                            <option value="Manuver SOP">Manuver menggunakan SOP Berbasis foto</option>
                                            <option value="Pengecekan Arus">Pengecekan parameter arus-tegangan setelah pembebasan</option>
                                            <option value="Pemasangan Grounding">Pemasangan grounding</option>
                                            <option value="Pemasangan LOTO">Pemasangan LOTO</option>
                                            <option value="Safety briefing">Safety briefing sebelum pemeliharaan</option>
                                        </optgroup>
                                    </select>

                                    <input type="file" name="image" accept=".jpg,.jpeg,.png" required class="mt-2 w-full">

                                    <button type="submit" class="mt-2 px-4 py-1 bg-blue-500 text-white rounded">Kirim</button>
                                </form>
                            <?php endif; ?>

                            <a href="hapus.php?id=<?= $row['id'] ?>" class="px-4 py-1 bg-red-500 text-white rounded" onclick="return confirm('Apakah Anda yakin ingin menghapus?');">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

</html>
<?php mysqli_close($conn); ?>