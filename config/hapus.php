<?php
require 'database.php';
require '../vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

session_start();

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID proyek tidak ditemukan!";
    exit;
}

$project_id = $_GET['id'];

function deleteFromGoogleDrive($fileId) {
    $client = new Client();
    $client->setAuthConfig('../cool-tooling-453512-a6-8f648e07039c.json');
    $client->addScope(Drive::DRIVE_FILE);

    $service = new Drive($client);

    try {
        $service->files->delete($fileId);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

$checkSql = "SELECT file_path FROM uploads WHERE project_id = ?";
$checkStmt = mysqli_prepare($conn, $checkSql);
mysqli_stmt_bind_param($checkStmt, "i", $project_id);
mysqli_stmt_execute($checkStmt);
$checkResult = mysqli_stmt_get_result($checkStmt);

while ($fileData = mysqli_fetch_assoc($checkResult)) {
    $filePath = $fileData['file_path'];

    if (preg_match('/id=([a-zA-Z0-9_-]+)/', $filePath, $matches)) {
        $google_drive_id = $matches[1];

        deleteFromGoogleDrive($google_drive_id);
    }
}
mysqli_stmt_close($checkStmt);

$deleteUploadsSql = "DELETE FROM uploads WHERE project_id = ?";
$deleteUploadsStmt = mysqli_prepare($conn, $deleteUploadsSql);
mysqli_stmt_bind_param($deleteUploadsStmt, "i", $project_id);
mysqli_stmt_execute($deleteUploadsStmt);
mysqli_stmt_close($deleteUploadsStmt);

$deleteProjectsSql = "DELETE FROM projects WHERE id = ?";
$deleteProjectsStmt = mysqli_prepare($conn, $deleteProjectsSql);
mysqli_stmt_bind_param($deleteProjectsStmt, "i", $project_id);
if (mysqli_stmt_execute($deleteProjectsStmt)) {
    header("Location: ../pages/user/tracker.php?success=Proyek berhasil dihapus!");
    exit;
} else {
    echo "Gagal menghapus data dari tabel projects!";
}
mysqli_stmt_close($deleteProjectsStmt);

mysqli_close($conn);
?>
