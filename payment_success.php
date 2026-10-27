<?php
require "includes/auth_check.php";

$type = $_GET["type"] ?? "buy";
$bookId = (int)($_GET["book_id"] ?? 0);
$days = (int)($_GET["days"] ?? 0);
$txn = htmlspecialchars($_GET["txn"] ?? "");

$title = "Book";
if ($bookId > 0) {
    try {
        $s = $pdo->prepare("SELECT title FROM books WHERE id = ?");
        $s->execute([$bookId]);
        $row = $s->fetch(PDO::FETCH_ASSOC);
        if ($row) $title = $row["title"];
    } catch (Throwable $e) {}
}
$isBuy = ($type === "buy");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment successful – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <style>
    .success-box {
      max-width: 480px; margin: 60px auto; text-align: center;
      background: #161616; border: 1px solid #333; border-radius: 16px; padding: 36px 28px;
    }
    .success-icon {
      width: 72px; height: 72px; border-radius: 50%; background: rgba(46,204,113,0.15);
      color: #2ecc71; display: flex; align-items: center; justify-content: center;
      font-size: 2rem; margin: 0 auto 18px;
    }
    .success-box h1 { font-size: 1.4rem; color: #fff; margin-bottom: 8px; }
    .success-box p { color: #aaa; font-size: 0.9rem; }
    .btn-lib {
      display: inline-block; margin-top: 20px; background: #c9a84c; color: #111;
      font-weight: 700; border: none; border-radius: 50px; padding: 12px 28px; text-decoration: none;
    }
    .btn-lib:hover { background: #e0c060; color: #111; }
    .btn-shelf {
      display: inline-block; margin-top: 12px; margin-left: 8px; color: #c9a84c;
      border: 1px solid #444; border-radius: 50px; padding: 10px 20px; text-decoration: none; font-size: 0.9rem;
    }
  </style>
</head>
<body>
<?php include "includes/navbar.php"; ?>
<div class="app-container">
  <div class="success-box">
    <div class="success-icon"><i class="fas fa-check"></i></div>
    <h1>Payment successful</h1>
    <p>
      <?php if ($isBuy): ?>
        You bought <strong style="color:#eee"><?php echo htmlspecialchars($title); ?></strong>.
        Lifetime access is in your library.
      <?php else: ?>
        You rented <strong style="color:#eee"><?php echo htmlspecialchars($title); ?></strong>
        for <?php echo max(1, $days); ?> day(s). Timer is running in My Library.
      <?php endif; ?>
    </p>
    <?php if ($txn): ?>
      <p style="color:#666; font-size:0.75rem">Txn: <?php echo $txn; ?></p>
    <?php endif; ?>
    <a class="btn-lib" href="my_library.php"><i class="fas fa-book"></i> Go to My Library</a>
    <div>
      <a class="btn-shelf" href="index.php">Back to shelf</a>
      <?php if ($bookId): ?>
        <a class="btn-shelf" href="read_book.php?id=<?php echo $bookId; ?>">Read now</a>
      <?php endif; ?>
    </div>
  </div>
</div>
</body>
</html>