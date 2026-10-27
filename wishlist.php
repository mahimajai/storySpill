<?php
require "includes/auth_check.php";
require_once "includes/library_access.php";

$userId = (int)$_SESSION["user_id"];

$stmt = $pdo->prepare(
    "SELECT books.*, users.name AS uploader_name
     FROM wishlist
     JOIN books ON wishlist.book_id = books.id
     JOIN users ON books.user_id = users.id
     WHERE wishlist.user_id = ?
     ORDER BY wishlist.id DESC"
);
$stmt->execute([$userId]);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wishlist – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="app-container">
  <h2 class="section-title" style="text-align:center; margin-bottom:8px">My Wishlist</h2>
  <p style="text-align:center; color:#888; margin-bottom:30px; font-size:0.9rem">
    Saved books — Read only if already in My Library (bought or active rent).
  </p>

  <?php if (empty($books)): ?>
    <div class="empty-state">
      <i class="fas fa-bookmark fa-3x mb-3" style="opacity:0.3"></i>
      <p>Your wishlist is empty. <a href="index.php" style="color:#c9a84c">Browse the shelf</a></p>
    </div>
  <?php else: ?>
    <div style="overflow-x:auto">
      <table class="table-dark">
        <thead>
          <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Genre</th>
            <th>Price</th>
            <th>Uploaded by</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($books as $book):
            $bid = (int)$book["id"];
            $price = (float)($book["price"] ?? 299);
            $rent = round($price / 2, 2);
            $access = can_read_book($pdo, $userId, $bid);
            $canRead = !empty($access["ok"]);
          ?>
            <tr>
              <td><?php echo htmlspecialchars($book["title"]); ?></td>
              <td><?php echo htmlspecialchars($book["author"]); ?></td>
              <td><?php echo htmlspecialchars($book["genre"]); ?></td>
              <td>
                <span style="color:#c9a84c">₹<?php echo number_format($price, 2); ?></span>
                <span style="color:#666; font-size:0.8rem"> · rent ₹<?php echo number_format($rent, 2); ?></span>
              </td>
              <td><?php echo htmlspecialchars($book["uploader_name"]); ?></td>
              <td>
                <?php if ($canRead): ?>
                  <a href="read_book.php?id=<?php echo $bid; ?>" class="btn-sm-action btn-edit">Read</a>
                <?php else: ?>
                  <a href="checkout.php?book_id=<?php echo $bid; ?>&mode=buy" class="btn-sm-action btn-edit">Buy</a>
                  <a href="checkout.php?book_id=<?php echo $bid; ?>&mode=rent" class="btn-sm-action btn-edit">Rent</a>
                <?php endif; ?>
                <a href="remove_wishlist.php?id=<?php echo $bid; ?>" class="btn-sm-action btn-delete">Remove</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<script>
  document.getElementById('navToggle')?.addEventListener('click', () =>
    document.getElementById('navLinks').classList.toggle('open'));
</script>
</body>
</html>