<?php
require '../../config/database.php';
session_start();

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SESSION['role'] !== 'User') {
    header("Location: ../admin/home.php");
    exit;
}


$successMessage = $_GET['success'] ?? null;

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
    <button onclick="window.location.href='home.php'"
        class="mb-4 px-4 py-2 bg-gray-500 text-white rounded">Kembali</button>

    <h1 class="text-2xl font-bold mb-4">Tracker Pekerjaan</h1>
    <?php if ($successMessage) : ?>
        <div class="mb-4 p-3 bg-green-500 text-white rounded">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

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

                        <td class="p-4 text-center">
                            <?php
                            $statusClass = match ($row['status']) {
                                'Belum Dimulai' => 'bg-gray-500',
                                'Dalam Progress' => 'bg-yellow-500',
                                'Selesai' => 'bg-green-500',
                                default => 'bg-gray-500'
                            };

                            $isRejected = ($row['approval'] == 'Ditolak');
                            ?>

                            <!-- Status sebagai teks -->
                            <div class="flex flex-col items-center">
                                <span class="px-4 py-2 <?= $statusClass ?> text-white rounded-full text-sm font-semibold">
                                    <?= htmlspecialchars($row['status']) ?>
                                </span>

                                <!-- Dropdown Status -->
                                <select class="status-dropdown mt-2 w-full max-w-[200px] p-2 border rounded-lg text-sm 
                        <?= $isRejected ? 'bg-gray-300 cursor-not-allowed opacity-60' : 'bg-white' ?>"
                                    data-project-id="<?= $row['id'] ?>"
                                    <?= $isRejected ? 'disabled' : 'onchange="updateStatus(this)"' ?>>
                                    <option value="Belum Dimulai"
                                        <?= ($row['status'] == 'Belum Dimulai') ? 'selected' : ''; ?>>Belum Dimulai</option>
                                    <option value="Dalam Progress"
                                        <?= ($row['status'] == 'Dalam Progress') ? 'selected' : ''; ?>>Dalam Progress
                                    </option>
                                    <option value="Selesai" <?= ($row['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai
                                    </option>
                                </select>

                                <?php if ($isRejected): ?>
                                    <p class="text-red-500 text-xs mt-1 italic">Status tidak bisa diubah karena ditolak.</p>
                                <?php endif; ?>
                            </div>
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
                        <td class="p-4 text-center imageContainer">
                            <?php
                            // Query untuk mengambil semua gambar berdasarkan project_id
                            $upload_sql = "SELECT file_path FROM uploads WHERE project_id = ?";
                            $upload_stmt = mysqli_prepare($conn, $upload_sql);
                            mysqli_stmt_bind_param($upload_stmt, "i", $row['id']);
                            mysqli_stmt_execute($upload_stmt);
                            $upload_result = mysqli_stmt_get_result($upload_stmt);
                            $images = mysqli_fetch_all($upload_result, MYSQLI_ASSOC);
                            mysqli_stmt_close($upload_stmt);

                            // Cek apakah ada gambar yang ditemukan
                            if (!empty($images)): ?>
                                <div class="flex flex-wrap justify-center gap-2">
                                    <?php foreach ($images as $image):
                                        $image_path = "../../" . $image['file_path']; ?>
                                        <?php if (file_exists($image_path)): ?>
                                            <img src="<?= htmlspecialchars($image_path); ?>"
                                                class="w-20 h-20 object-cover cursor-pointer border rounded-md hover:scale-105 transition"
                                                onclick="openModal(this)">
                                        <?php else: ?>
                                            <span class="text-gray-500">Gambar tidak ditemukan</span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span class="text-gray-500">Belum Upload</span>
                            <?php endif; ?>
                        </td>
    // Cek apakah ada gambar yang ditemukan
    if (!empty($images)): ?>
        <div class="flex flex-wrap justify-center gap-2">
            <?php foreach ($images as $image): 
                $image_path = "../../" . $image['file_path']; ?>
                <?php if (file_exists($image_path)): ?>
                    <img src="<?= htmlspecialchars($image_path); ?>"
                        class="w-20 h-20 object-cover cursor-pointer border rounded-md hover:scale-105 transition"
                        onclick="openModal(this)">
                <?php else: ?>
                    <span class="text-gray-500">Gambar tidak ditemukan</span>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <input type="file" name="image" accept=".jpg,.jpeg,.png" required
                                    class="p-2 mt-4 border rounded">
        <button type="upload" class="px-4 py-2 bg-blue-500 text-white rounded">Upload</button>
    <?php else: ?>
        <span class="text-gray-500">Belum Upload</span>
    <?php endif; ?>
</td>


                        <!-- Aksi -->
                        <td class="p-4 text-center">
                            <div class="flex items-center gap-2 justify-center">
                                <?php if ($row['approval'] == 'Belum Diajukan' || $row['approval'] == 'Butuh Peninjauan') : ?>
                                    <form method="POST" action="../../config/kirim.php" enctype="multipart/form-data">
                                        <input type="hidden" name="project_id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="status" value="<?= htmlspecialchars($row['status']) ?>">
                                        <!-- Input Hidden -->

                                        <select name="category" required class="p-2 border rounded">
                                            <option value="Upload foto pelaksanaan">Upload foto pelaksanaan</option>
                                            <optgroup label="Foto Kegiatan">
                                                <option value="Pengecekan Dokumen">Pengecekan Dokumen DP3, JSA, WP</option>
                                                <option value="Pantauan hotspot">Pantauan hotspot sebelum manuver</option>
                                                <option value="Manuver SOP">Manuver menggunakan SOP Berbasis foto</option>
                                                <option value="Pengecekan Arus">Pengecekan parameter arus-tegangan setelah
                                                    pembebasan</option>
                                                <option value="Pemasangan Grounding">Pemasangan grounding</option>
                                                <option value="Pemasangan LOTO">Pemasangan LOTO</option>
                                                <option value="Safety briefing">Safety briefing sebelum pemeliharaan</option>
                                            </optgroup>
                                        </select>

                                        <input type="file" name="image" accept=".jpg,.jpeg,.png" required
                                            class="p-2 border rounded">

                                        <button type="submit" class="px-4 py-1 bg-blue-500 text-white rounded">Kirim</button>
                                    </form>

                                <?php endif; ?>

                                <a href="../../config/hapus.php?id=<?= $row['id'] ?>"
                                    class="px-4 py-1 bg-red-500 text-white rounded"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus?');">Hapus</a>
                            </div>
                        </td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>

<!-- Modal -->
<div id="modal" class="fixed top-0 left-0 w-full h-full bg-black bg-opacity-75 flex items-center justify-center hidden">
    <button onclick="prevImage()" class="absolute left-4 text-white text-4xl font-bold">&larr;</button>
    <img id="modalImg" class="max-w-full max-h-full transition-transform duration-300">
    <button onclick="nextImage()" class="absolute right-4 text-white text-4xl font-bold">&rarr;</button>
    <button onclick="closeModal()" class="absolute top-4 right-4 text-white text-3xl font-bold">&times;</button>
</div>

<script>
    let images = [];
    let currentIndex = 0;

    function openModal(imgElement) {
        let row = imgElement.closest("tr"); // Ambil baris tempat gambar berada
        images = Array.from(row.querySelectorAll(".imageContainer img")).map(img => img.src);
        currentIndex = images.indexOf(imgElement.src);
        document.getElementById("modalImg").src = images[currentIndex];
        document.getElementById("modal").classList.remove("hidden");
    }

    function closeModal() {
        document.getElementById("modal").classList.add("hidden");
    }

    function prevImage() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        document.getElementById("modalImg").src = images[currentIndex];
    }

    function nextImage() {
        currentIndex = (currentIndex + 1) % images.length;
        document.getElementById("modalImg").src = images[currentIndex];
    }

    document.addEventListener("keydown", function(event) {
        if (event.key === "ArrowLeft") prevImage();
        if (event.key === "ArrowRight") nextImage();
        if (event.key === "Escape") closeModal();
    });

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".status-dropdown").forEach(select => {
            select.addEventListener("change", function() {
                let projectId = this.getAttribute("data-project-id");

                // Cari form yang sesuai dengan project_id
                let form = document.querySelector(
                    `form input[name="project_id"][value="${projectId}"]`)?.closest("form");

                if (form) {
                    let hiddenInput = form.querySelector("input[name='status']");
                    if (hiddenInput) {
                        hiddenInput.value = this.value; // Set nilai baru
                    }
                }
            });
        });
    });
</script>


</html>
<?php mysqli_close($conn); ?>