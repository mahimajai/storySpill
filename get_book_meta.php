<?php
session_start();
require "db_connect.php";
header("Content-Type: application/json");

$bookId = (int)($_GET["book_id"] ?? 0);
if ($bookId <= 0) {
    echo json_encode(["reviews" => [], "avg" => null]);
    exit;
}

$stmt = $pdo->prepare(
    "SELECT r.rating, r.review_text, r.created_at, u.name
     FROM reviews r JOIN users u ON r.user_id = u.id
     WHERE r.book_id = ? ORDER BY r.created_at DESC LIMIT 20"
);
$stmt->execute([$bookId]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

$avg = $pdo->prepare("SELECT ROUND(AVG(rating),1) AS avg_rating, COUNT(*) AS cnt FROM reviews WHERE book_id = ?");
$avg->execute([$bookId]);
$stats = $avg->fetch(PDO::FETCH_ASSOC);

echo json_encode(["reviews" => $reviews, "avg" => $stats["avg_rating"], "count" => (int)$stats["cnt"]]);
