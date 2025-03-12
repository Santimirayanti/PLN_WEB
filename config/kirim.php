<?php
require 'database.php';
session_start();

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../../auth/login.php");
    exit;
}

echo "Status: " . ($_POST['status'] ?? 'NULL') . "<br>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST['project_id'] ?? null;
    $category = $_POST['category'] ?? null;
    $approval = 'Butuh Peninjauan';
    $status = $_POST['status'] ?? null;

    if (!$project_id || !$category || empty($_FILES['image']['name'])) {
        echo "Harap isi semua bidang!";
        exit;
    }

    // Buat direktori jika belum ada
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Cek apakah ada gambar lama di database
    $checkSql = "SELECT file_path FROM uploads WHERE project_id = ?";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($checkStmt, "i", $project_id);
    mysqli_stmt_execute($checkStmt);
    $checkResult = mysqli_stmt_get_result($checkStmt);
    $existingImage = mysqli_fetch_assoc($checkResult)['file_path'] ?? null;
    mysqli_stmt_close($checkStmt);

    // Jika ada gambar lama, hapus dari server dan database
    if ($existingImage) {
        $oldFilePath = '../' . $existingImage;
        if (file_exists($oldFilePath)) {
            unlink($oldFilePath); // Hapus file lama dari server
        }

        // Hapus entry lama dari database
        $deleteSql = "DELETE FROM uploads WHERE project_id = ?";
        $deleteStmt = mysqli_prepare($conn, $deleteSql);
        mysqli_stmt_bind_param($deleteStmt, "i", $project_id);
        mysqli_stmt_execute($deleteStmt);
        mysqli_stmt_close($deleteStmt);
    }

    // Simpan file baru dengan nama unik
    $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $fileName = uniqid() . "." . $fileExtension;
    $filePath = $uploadDir . $fileName;
    $dbFilePath = "uploads/" . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $filePath)) {
        // Simpan informasi file baru ke database
        $sql = "INSERT INTO uploads (project_id, file_path, category) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $project_id, $dbFilePath, $category);

        if (mysqli_stmt_execute($stmt)) {
            // Update kolom approval di tabel projects
            $updateSql = "UPDATE projects SET approval = ?, status = ? WHERE id = ?";
            $updateStmt = mysqli_prepare($conn, $updateSql);
            mysqli_stmt_bind_param($updateStmt, "ssi", $approval, $status, $project_id);
            mysqli_stmt_execute($updateStmt);
            mysqli_stmt_close($updateStmt);

            // Redirect ke halaman tracker
            header("Location: ../pages/user/tracker.php?success=Berhasil Diajukan");
            exit;
        } else {
            echo "Gagal menyimpan data!";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Gagal mengunggah file!";
    }
}

mysqli_close($conn);
?>
