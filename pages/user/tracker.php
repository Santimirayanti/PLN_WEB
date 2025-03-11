<?php
session_start();
require '../../config/database.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Ambil proyek milik user yang login
$sql = "SELECT id, name, status, approval FROM projects WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Proses upload dan update approval
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['approve_id'])) {
    $approve_id = (int) $_POST['approve_id'];
    $category = mysqli_real_escape_string($conn, $_POST['category'] ?? '');

    // Validasi kategori
    $valid_categories = [
        "Upload foto pelaksanaan",
        "Pengecekan Dokumen",
        "Pantauan hotspot",
        "Manuver SOP",
        "Pengecekan Arus",
        "Pemasangan Grounding",
        "Pemasangan LOTO",
        "Safety briefing"
    ];

    if (!in_array($category, $valid_categories)) {
        $_SESSION['message'] = "Kategori tidak valid!";
        header("Location: tracker.php");
        exit();
    }

    // Cek apakah proyek ini milik user dan sudah selesai
    $check_sql = "SELECT id FROM projects WHERE id = ? AND user_id = ? AND status = 'Selesai' AND approval IS NULL";
    $check_stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($check_stmt, "ii", $approve_id, $user_id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);

    if (mysqli_stmt_num_rows($check_stmt) > 0 && isset($_FILES["image"])) {
        $upload_dir = "uploads/";
        $file_name = basename($_FILES["image"]["name"]);
        $file_path = $upload_dir . $file_name;
        $image_type = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        // Validasi file
        $allowed_types = ['jpg', 'jpeg', 'png'];
        if (in_array($image_type, $allowed_types) && $_FILES["image"]["size"] < 5000000) {
            if (is_uploaded_file($_FILES["image"]["tmp_name"]) && move_uploaded_file($_FILES["image"]["tmp_name"], $file_path)) {
                // Simpan ke tabel uploads
                $insert_sql = "INSERT INTO uploads (project_id, category, file_path) VALUES (?, ?, ?)";
                $insert_stmt = mysqli_prepare($conn, $insert_sql);
                mysqli_stmt_bind_param($insert_stmt, "iss", $approve_id, $category, $file_path);
                mysqli_stmt_execute($insert_stmt);
                mysqli_stmt_close($insert_stmt);

                // Update approval
                $update_sql = "UPDATE projects SET approval = 'Butuh Peninjauan' WHERE id = ?";
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "i", $approve_id);
                mysqli_stmt_execute($update_stmt);
                mysqli_stmt_close($update_stmt);

                $_SESSION['message'] = "Gambar berhasil diunggah!";
            } else {
                $_SESSION['message'] = "Gagal mengunggah gambar!";
            }
        } else {
            $_SESSION['message'] = "Format file tidak didukung atau ukuran terlalu besar!";
        }
    }
    mysqli_stmt_close($check_stmt);
    header("Location: tracker.php");
    exit();
}

// Proses hapus gambar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = (int) $_POST['delete_id'];

    // Ambil file path dari database
    $file_sql = "SELECT file_path FROM uploads WHERE project_id = ?";
    $file_stmt = mysqli_prepare($conn, $file_sql);
    mysqli_stmt_bind_param($file_stmt, "i", $delete_id);
    mysqli_stmt_execute($file_stmt);
    $file_result = mysqli_stmt_get_result($file_stmt);
    $file_data = mysqli_fetch_assoc($file_result);
    mysqli_stmt_close($file_stmt);

    if ($file_data) {
        $file_path = $file_data['file_path'];

        // Hapus file dari server
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Hapus data dari tabel uploads
        $delete_sql = "DELETE FROM uploads WHERE project_id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_sql);
        mysqli_stmt_bind_param($delete_stmt, "i", $delete_id);
        mysqli_stmt_execute($delete_stmt);
        mysqli_stmt_close($delete_stmt);

        // Reset approval jika tidak ada gambar tersisa
        $reset_sql = "UPDATE projects SET approval = NULL WHERE id = ?";
        $reset_stmt = mysqli_prepare($conn, $reset_sql);
        mysqli_stmt_bind_param($reset_stmt, "i", $delete_id);
        mysqli_stmt_execute($reset_stmt);
        mysqli_stmt_close($reset_stmt);

        $_SESSION['message'] = "Gambar berhasil dihapus!";
    } else {
        $_SESSION['message'] = "Gambar tidak ditemukan!";
    }

    header("Location: tracker.php");
    exit();
}
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
    <button onclick="window.location.href='home.php'" class="mb-4 px-4 py-2 bg-gray-500 text-white rounded">Kembali</button>

    <h1 class="text-2xl font-bold mb-4">Tracker Pekerjaan Anda</h1>

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
                <?php while ($row = mysqli_fetch_assoc($result)) :
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
                        <td class="p-4"><?= htmlspecialchars($row['name']) ?></td>
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
                        <td class="p-4 text-center">
                            <?php
                            $approvalClass = match ($row['approval']) {
                                'Disetujui' => 'bg-green-500',
                                'Butuh Peninjauan' => 'bg-yellow-500',
                                default => 'bg-gray-300'
                            };
                            $approvalText = $row['approval'] ?? '-';
                            ?>
                            <span class="px-4 py-2 <?= $approvalClass ?> text-white rounded"><?= $approvalText ?></span>
                        </td>
                        <td class="p-4 text-center">
                            <?php if ($image_path) : ?>
                                <a href="<?= htmlspecialchars($image_path) ?>" target="_blank">
                                    <img src="<?= htmlspecialchars($image_path) ?>" class="w-16 h-16 rounded border" alt="Gambar">
                                </a>
                            <?php else : ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-center">
                            <?php if ($row['status'] === 'Selesai' && empty($row['approval'])) : ?>
                                <form method="POST" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="approve_id" value="<?= $row['id'] ?>">
                                    <select name="category" class="w-full p-2 border rounded">
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
                                    <input type="file" name="image" required class="mt-2 w-full">
                                    <button type="submit" class="mt-2 px-4 py-1 bg-blue-500 text-white rounded">Kirim</button>
                                </form>
                                <form method="POST" action="hapus.php">
                                    <input type="hidden" name="project_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="delete" class="mt-2 px-4 py-1 bg-red-500 text-white rounded">Hapus</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php mysqli_close($conn); ?>