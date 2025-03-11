<?php
require 'database.php';
session_start();

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = $_POST['project_id'] ?? null;
    $category = $_POST['category'] ?? null;
    
    if (!$project_id || !$category || empty($_FILES['image']['name'])) {
        echo "Harap isi semua bidang!";
        exit;
    }

    // Simpan file yang diunggah
    $uploadDir = '../uploads/';
    $fileName = time() . '-' . basename($_FILES["image"]["name"]);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $filePath)) {
        // Simpan informasi ke database
        $sql = "INSERT INTO uploads (project_id, file_path, category) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iss", $project_id, '../' . $filePath, $category);
        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../pages/user/tracker.php");
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
