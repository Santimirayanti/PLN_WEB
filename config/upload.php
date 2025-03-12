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

function uploadToGoogleDrive($filePath, $fileName) {
    $client = new Client();
    $client->setAuthConfig('../cool-tooling-453512-a6-8f648e07039c.json');
    $client->addScope(Drive::DRIVE_FILE);

    $service = new Drive($client);

    $fileMetadata = new Drive\DriveFile([
        'name' => $fileName,
        'parents' => ['1KWIc79jxvXcphJOoJQ7rUgctgcyAFHHM']
    ]);

    $content = file_get_contents($filePath);

    $file = $service->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => mime_content_type($filePath),
        'uploadType' => 'multipart',
        'fields' => 'id'
    ]);

    $fileId = $file->id ?? false;
    
    if ($fileId) {
        $permission = new Drive\Permission();
        $permission->setType('anyone');
        $permission->setRole('reader');

        $service->permissions->create($fileId, $permission);

        return $fileId;
    }
    
    return false;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['image'])) {
    $project_id = $_POST['project_id'] ?? null;
    if (!$project_id) {
        die("ID proyek tidak ditemukan!");
    }

    $fileName = uniqid() . "_" . basename($_FILES["image"]["name"]);
    $filePath = $_FILES["image"]["tmp_name"];
    $fileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (!in_array($fileType, $allowedTypes)) {
        die("Format file tidak diizinkan!");
    }

    $fileId = uploadToGoogleDrive($filePath, $fileName);
    if ($fileId) {
        $googleDrivePath = "https://drive.google.com/uc?id=" . $fileId;

        $insertSql = "INSERT INTO uploads (project_id, file_path, uploaded_at) VALUES (?, ?, NOW())";
        $insertStmt = mysqli_prepare($conn, $insertSql);
        mysqli_stmt_bind_param($insertStmt, "is", $project_id, $googleDrivePath);

        if (mysqli_stmt_execute($insertStmt)) {
            header("Location: ../pages/user/tracker.php?success=Gambar berhasil diupload!");
            exit;
        } else {
            echo "Gagal menyimpan data ke database!";
        }
        mysqli_stmt_close($insertStmt);
    } else {
        echo "Gagal mengunggah ke Google Drive!";
    }
} else {
    echo "Permintaan tidak valid!";
}

mysqli_close($conn);
?>
