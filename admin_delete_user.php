<?php
require "includes/admin_auth_check.php";

$userId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($userId > 0) {
    $books = $pdo->prepare("SELECT * FROM books WHERE user_id = ?");
    $books->execute([$userId]);
    foreach ($books->fetchAll(PDO::FETCH_ASSOC) as $book) {
        foreach (["cover_image", "intro_video", "pdf_path"] as $field) {
            if (!empty($book[$field]) && strpos($book[$field], "uploads/") === 0 && file_exists($book[$field])) {
                @unlink($book[$field]);
            }
        }
    }
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$userId]);
}

header("Location: admin_dashboard.php?user_deleted=1");
exit;
?>