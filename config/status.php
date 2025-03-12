<?php
require 'database.php';
session_start();

header('Content-Type: application/json');

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Metode tidak valid!"]);
    exit;
}

$project_id = $_POST['project_id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$project_id || !$status) {
    echo json_encode(["success" => false, "message" => "Data tidak lengkap!"]);
    exit;
}

$updateProjectSql = "UPDATE projects SET status = ? WHERE id = ?";
$stmtProject = mysqli_prepare($conn, $updateProjectSql);
mysqli_stmt_bind_param($stmtProject, "si", $status, $project_id);
$executeProject = mysqli_stmt_execute($stmtProject);
mysqli_stmt_close($stmtProject);

if ($executeProject) {
    echo json_encode(["success" => true, "message" => "Status berhasil diperbarui!"]);
} else {
    echo json_encode(["success" => false, "message" => "Gagal memperbarui status!"]);
}

mysqli_close($conn);
?>
