<?php
require "includes/auth_check.php";

$bookId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($bookId > 0) {
    // Only allow owner to delete
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ? AND user_id = ?");
    $stmt->execute([$bookId, $_SESSION["user_id"]]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($book) {
        // Delete files if local
        foreach (["cover_image", "intro_video", "pdf_path"] as $field) {
            if (!empty($book[$field]) && strpos($book[$field], "uploads/") === 0 && file_exists($book[$field])) {
                @unlink($book[$field]);
            }
        }
        $pdo->prepare("DELETE FROM books WHERE id = ?")->execute([$bookId]);
    }
}

header("Location: dashboard.php");
exit;
?>
