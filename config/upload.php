<?php
require 'database.php';
require '../vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

session_start();

// Pastikan user sudah login
$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../index.php");
    exit;
}

// Fungsi untuk mengunggah gambar ke Google Drive dan mengatur izin publik
function uploadToGoogleDrive($filePath, $fileName) {
    $client = new Client();
    $client->setAuthConfig('../cool-tooling-453512-a6-8f648e07039c.json'); // Ganti dengan lokasi file kredensial JSON Anda
    $client->addScope(Drive::DRIVE_FILE);

    $service = new Drive($client);

    // Metadata file
    $fileMetadata = new Drive\DriveFile([
        'name' => $fileName,
        'parents' => ['1KWIc79jxvXcphJOoJQ7rUgctgcyAFHHM'] // Ganti dengan ID folder Google Drive Anda
    ]);

    // Baca konten file
    $content = file_get_contents($filePath);

    // Upload file ke Google Drive
    $file = $service->files->create($fileMetadata, [
        'data' => $content,
        'mimeType' => mime_content_type($filePath),
        'uploadType' => 'multipart',
        'fields' => 'id'
    ]);

    // Ambil ID file
    $fileId = $file->id ?? false;
    
    if ($fileId) {
        // Set permission agar file bisa diakses secara publik
        $permission = new Drive\Permission();
        $permission->setType('anyone');
        $permission->setRole('reader');

        $service->permissions->create($fileId, $permission);

        return $fileId;
    }
    
    return false;
}

// Pastikan request adalah POST dan ada file yang diunggah
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['image'])) {
    $project_id = $_POST['project_id'] ?? null;
    if (!$project_id) {
        die("ID proyek tidak ditemukan!");
    }

    $fileName = uniqid() . "_" . basename($_FILES["image"]["name"]); // Buat nama unik
    $filePath = $_FILES["image"]["tmp_name"];
    $fileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

    // Validasi jenis file
    $allowedTypes = ['jpg', 'jpeg', 'png'];
    if (!in_array($fileType, $allowedTypes)) {
        die("Format file tidak diizinkan!");
    }

    // Upload ke Google Drive
    $fileId = uploadToGoogleDrive($filePath, $fileName);
    if ($fileId) {
        $googleDrivePath = "https://drive.google.com/uc?id=" . $fileId; // URL yang dapat diakses langsung

        // Simpan path ke database
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
