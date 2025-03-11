<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $project_id = (int) $_GET['id'];

    // Ambil file path dari database
    $file_sql = "SELECT file_path FROM uploads WHERE project_id = ?";
    $file_stmt = mysqli_prepare($conn, $file_sql);
    mysqli_stmt_bind_param($file_stmt, "i", $project_id);
    mysqli_stmt_execute($file_stmt);
    $file_result = mysqli_stmt_get_result($file_stmt);

    while ($file_data = mysqli_fetch_assoc($file_result)) {
        $file_path = $file_data['file_path'];

        // Hapus file dari server
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    mysqli_stmt_close($file_stmt);

    // Hapus data dari tabel uploads
    $delete_sql = "DELETE FROM uploads WHERE project_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "i", $project_id);
    mysqli_stmt_execute($delete_stmt);
    mysqli_stmt_close($delete_stmt);

    // Reset approval jika tidak ada gambar tersisa
    $reset_sql = "UPDATE projects SET approval = NULL WHERE id = ?";
    $reset_stmt = mysqli_prepare($conn, $reset_sql);
    mysqli_stmt_bind_param($reset_stmt, "i", $project_id);
    mysqli_stmt_execute($reset_stmt);
    mysqli_stmt_close($reset_stmt);

    $_SESSION['message'] = "Gambar berhasil dihapus!";
}

header("Location: tracker.php");
exit();
?>
