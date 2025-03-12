<?php
require 'database.php';
session_start();

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../index.php?error=User tidak terautentikasi!");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST['project_id'] ?? null;
    $category = $_POST['category'] ?? null;
    $approval = 'Butuh Peninjauan';

    if (!$project_id || !$category) {
        header("Location: ../pages/user/tracker.php?error=Data tidak lengkap!");
        exit;
    }

    $updateProjectSql = "UPDATE projects SET approval = ? WHERE id = ?";
    $stmtProject = mysqli_prepare($conn, $updateProjectSql);
    mysqli_stmt_bind_param($stmtProject, "si", $approval, $project_id);
    $executeProject = mysqli_stmt_execute($stmtProject);
    mysqli_stmt_close($stmtProject);

    $updateUploadSql = "UPDATE uploads SET category = ? WHERE project_id = ?";
    $stmtUpload = mysqli_prepare($conn, $updateUploadSql);
    mysqli_stmt_bind_param($stmtUpload, "si", $category, $project_id);
    $executeUpload = mysqli_stmt_execute($stmtUpload);
    mysqli_stmt_close($stmtUpload);

    mysqli_close($conn);

    if ($executeProject && $executeUpload) {
        header("Location: ../pages/user/tracker.php?success=Kategori berhasil diperbarui!");
        exit;
    } else {
        header("Location: ../pages/user/tracker.php?error=Gagal memperbarui data!");
        exit;
    }
} else {
    header("Location: ../pages/user/tracker.php?error=Metode tidak valid!");
    exit;
}

mysqli_close($conn);
?>
