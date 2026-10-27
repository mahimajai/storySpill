<?php
require "includes/auth_check.php";
require_once "includes/library_access.php";

$bookId = (int)($_GET["id"] ?? 0);
$userId = (int)$_SESSION["user_id"];

$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
$stmt->execute([$bookId]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$book) {
    header("Location: index.php");
    exit;
}

$access = can_read_book($pdo, $userId, $bookId);
if (!$access["ok"]) {
    header("Location: checkout.php?book_id=" . $bookId . "&mode=rent");
    exit;
}

$pdf = $book["pdf_path"] ?? "";
if ($pdf !== "") {
    header("Location: " . $pdf);
    exit;
}
?>
<!DOCTYPE html>
<html><head><title>Read</title><link rel="stylesheet" href="css/style.css"></head>
<body><?php include "includes/navbar.php"; ?>
<div class="app-container"><p style="color:#ccc">No PDF available for this book.</p>
<a href="my_library.php" style="color:#c9a84c">My Library</a></div></body></html>