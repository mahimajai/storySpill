<?php
require "includes/auth_check.php";

$bookId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($bookId > 0) {
    $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND book_id = ?")
        ->execute([$_SESSION["user_id"], $bookId]);
}

header("Location: wishlist.php");
exit;
?>
