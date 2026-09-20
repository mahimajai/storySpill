<?php
require "auth_check.php";

$userId = $_SESSION["user_id"];
$bookId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($bookId > 0) {
    $deleteStmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND book_id = ?");
    $deleteStmt->execute([$userId, $bookId]);
}

header("Location: wishlist.php");
exit;
?>
