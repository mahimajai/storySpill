<?php
date_default_timezone_set("Asia/Kolkata");

function user_owns_book(PDO $pdo, int $userId, int $bookId): bool {
    $s = $pdo->prepare("SELECT id FROM purchases WHERE user_id = ? AND book_id = ? AND status = 'paid'");
    $s->execute([$userId, $bookId]);
    return (bool)$s->fetch();
}

/** Active if payment time + days*86400 seconds still in future */
function user_active_rental(PDO $pdo, int $userId, int $bookId): ?array {
    $s = $pdo->prepare(
        "SELECT * FROM rentals
         WHERE user_id = ? AND book_id = ? AND returned_at IS NULL
           AND TIMESTAMPADD(SECOND, days * 86400, created_at) >= NOW()
         ORDER BY id DESC LIMIT 1"
    );
    $s->execute([$userId, $bookId]);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    if (!$r) {
        $pdo->prepare(
            "UPDATE rentals SET status = 'expired'
             WHERE user_id = ? AND book_id = ? AND returned_at IS NULL
               AND TIMESTAMPADD(SECOND, days * 86400, created_at) < NOW()"
        )->execute([$userId, $bookId]);
        return null;
    }
    if (($r["status"] ?? "") !== "active") {
        $pdo->prepare("UPDATE rentals SET status = 'active' WHERE id = ?")->execute([$r["id"]]);
        $r["status"] = "active";
    }
    return $r;
}

function can_read_book(PDO $pdo, int $userId, int $bookId): array {
    if (user_owns_book($pdo, $userId, $bookId)) {
        return ["ok" => true, "type" => "purchase"];
    }
    $rent = user_active_rental($pdo, $userId, $bookId);
    if ($rent) {
        return ["ok" => true, "type" => "rental", "rental" => $rent];
    }
    return ["ok" => false, "type" => "none"];
}