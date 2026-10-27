<?php
error_reporting(E_ALL);
ini_set("display_errors", "1");

require "includes/auth_check.php";

if (!file_exists(__DIR__ . "/includes/library_access.php")) {
    die("Missing file: includes/library_access.php — copy library_access.php into includes folder.");
}
require_once "includes/library_access.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$userId = (int)$_SESSION["user_id"];
$bookId = (int)($_POST["book_id"] ?? 0);
$mode = $_POST["mode"] ?? "";
$method = trim($_POST["payment_method"] ?? "Card");

if ($bookId <= 0 || !in_array($mode, ["buy", "rent"], true)) {
    header("Location: index.php");
    exit;
}

$ok = false;
if ($method === "Card") {
    $card = preg_replace("/\s+/", "", $_POST["card_number"] ?? "");
    $ok = strlen($card) >= 12;
} elseif ($method === "UPI") {
    $ok = strpos(trim($_POST["upi_id"] ?? ""), "@") !== false;
} elseif ($method === "NetBanking") {
    $ok = trim($_POST["bank_name"] ?? "") !== "" && trim($_POST["nb_user"] ?? "") !== "";
}

if (!$ok) {
    header("Location: checkout.php?book_id=" . $bookId . "&mode=" . urlencode($mode) . "&err=1");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$book) {
        header("Location: index.php");
        exit;
    }

    $price = (float)($book["price"] ?? 299);
    $txn = strtoupper(bin2hex(random_bytes(8)));
    $title = $book["title"];

    if ($mode === "buy") {
        if (function_exists("user_owns_book") && user_owns_book($pdo, $userId, $bookId)) {
            header("Location: payment_success.php?type=buy&book_id=" . $bookId);
            exit;
        }
        $pdo->prepare(
            "INSERT INTO purchases (user_id, book_id, amount, payment_method, transaction_id, status)
             VALUES (?, ?, ?, ?, ?, 'paid')"
        )->execute([$userId, $bookId, $price, $method, $txn]);

        header("Location: payment_success.php?type=buy&book_id=" . $bookId . "&txn=" . urlencode($txn));
        exit;
    }

    $days = (int)($_POST["days"] ?? 1);
    if ($days < 1) $days = 1;
    if ($days > 7) $days = 7;

    $rentAmount = round($price / 2, 2);
    $start = date("Y-m-d");
    $end = date("Y-m-d", strtotime("+{$days} days"));

    $pdo->prepare(
        "INSERT INTO rentals (user_id, book_id, days, rent_amount, start_date, end_date, payment_method, transaction_id, status)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')"
    )->execute([$userId, $bookId, $days, $rentAmount, $start, $end, $method, $txn]);

    header("Location: payment_success.php?type=rent&book_id=" . $bookId . "&days=" . $days . "&txn=" . urlencode($txn));
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo "<!DOCTYPE html><html><body style='background:#111;color:#eee;font-family:sans-serif;padding:40px'>";
    echo "<h2 style='color:#e74c3c'>Payment error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p style='color:#888'>Usually means <code>purchases</code> / <code>rentals</code> tables missing, or <code>price</code> column missing. Run payment SQL in phpMyAdmin.</p>";
    echo "<p><a href='index.php' style='color:#c9a84c'>Back to shelf</a></p>";
    echo "</body></html>";
    exit;
}