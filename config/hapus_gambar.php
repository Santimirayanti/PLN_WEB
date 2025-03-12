<?php
require 'database.php';
require '../vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

session_start();

header('Content-Type: application/json');

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../index.php");
    exit;
}

function deleteFromGoogleDrive($fileId) {
    $client = new Client();
    $client->setAuthConfig('../cool-tooling-453512-a6-8f648e07039c.json'); // Ubah dengan lokasi file JSON kredensial Anda
    $client->addScope(Drive::DRIVE_FILE);

    $service = new Drive($client);

    try {
        $service->files->delete($fileId);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_id']) && isset($_POST['file_path'])) {
    $upload_id = $_POST['upload_id'];
    $filePath = $_POST['file_path']; 

    if (preg_match('/id=([a-zA-Z0-9_-]+)/', $filePath, $matches)) {
        $google_drive_id = $matches[1];

        if (deleteFromGoogleDrive($google_drive_id)) {
            $deleteSql = "DELETE FROM uploads WHERE id = ?";
            $deleteStmt = mysqli_prepare($conn, $deleteSql);
            mysqli_stmt_bind_param($deleteStmt, "i", $upload_id);

            if (mysqli_stmt_execute($deleteStmt)) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "error" => "Gagal menghapus dari database"]);
            }

            mysqli_stmt_close($deleteStmt);
        } else {
            echo json_encode(["success" => false, "error" => "Gagal menghapus dari Google Drive"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "File ID tidak valid"]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
}

mysqli_close($conn);
?>
