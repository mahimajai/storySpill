<?php
require "includes/admin_auth_check.php";

$bookId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$fromUser = isset($_GET["from_user"]) ? (int)$_GET["from_user"] : 0;

if ($bookId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        foreach (["cover_image", "intro_video", "pdf_path"] as $field) {
            if (!empty($book[$field]) && strpos($book[$field], "uploads/") === 0 && file_exists($book[$field])) {
                @unlink($book[$field]);
            }
        }
        $pdo->prepare("DELETE FROM books WHERE id = ?")->execute([$bookId]);
    }
}

if ($fromUser > 0) {
    header("Location: admin_user.php?id=" . $fromUser . "&book_deleted=1");
} else {
    header("Location: admin_dashboard.php?book_deleted=1");
}
exit;
?>