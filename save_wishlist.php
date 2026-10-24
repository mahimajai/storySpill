<?php
require "includes/auth_check.php";

$bookId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($bookId > 0) {
    try {
        $pdo->prepare("INSERT IGNORE INTO wishlist (user_id, book_id) VALUES (?, ?)")
            ->execute([$_SESSION["user_id"], $bookId]);
    } catch (Exception $e) {
        // ignore duplicate
    }
}

// If AJAX, just return ok
if (!empty($_SERVER["HTTP_X_REQUESTED_WITH"]) || 
    (isset($_SERVER["HTTP_ACCEPT"]) && strpos($_SERVER["HTTP_ACCEPT"], "text/html") === false)) {
    echo "ok";
    exit;
}

header("Location: " . ($_SERVER["HTTP_REFERER"] ?? "index.php"));
exit;
?>
