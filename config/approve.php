<?php
require 'database.php';

session_start();

$user_id = $_SESSION['id'] ?? null;
if (!$user_id) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $project_id = $_POST['project_id'];
    $approval = $_POST['approval'];

    $stmt = $conn->prepare("UPDATE projects SET approval = ? WHERE id = ?");
    $stmt->bind_param("si", $approval, $project_id);

    if ($stmt->execute()) {
        echo "Approval berhasil diperbarui!";
    } else {
        echo "Gagal memperbarui approval.";
    }

    $stmt->close();
    $conn->close();
}
?>
