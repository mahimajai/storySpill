<?php
require "includes/admin_auth_check.php";

$userId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($userId <= 0) {
    header("Location: admin_dashboard.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: admin_dashboard.php");
    exit;
}

$booksStmt = $pdo->prepare("SELECT * FROM books WHERE user_id = ? ORDER BY created_at DESC");
$booksStmt->execute([$userId]);
$books = $booksStmt->fetchAll(PDO::FETCH_ASSOC);

$wishStmt = $pdo->prepare(
    "SELECT books.id, books.title, books.genre, books.author
     FROM wishlist JOIN books ON wishlist.book_id = books.id
     WHERE wishlist.user_id = ?"
);
$wishStmt->execute([$userId]);
$wishlist = $wishStmt->fetchAll(PDO::FETCH_ASSOC);

$isOnline = $user["last_activity"] && (strtotime($user["last_activity"]) >= time() - 300);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($user["name"]); ?> – Admin · StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="app-container">
  <p style="margin-bottom:16px">
    <a href="admin_dashboard.php" style="color:#c9a84c; text-decoration:none;">
      <i class="fas fa-arrow-left"></i> Back to Dashboard
    </a>
  </p>

  <div class="dash-header" style="justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
    <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
      <div class="dash-avatar"><i class="fas fa-user"></i></div>
      <div class="dash-info">
        <h2><?php echo htmlspecialchars($user["name"]); ?></h2>
        <p><?php echo htmlspecialchars($user["email"]); ?></p>
        <p>
          <span style="color:<?php echo $isOnline ? '#2ecc71' : '#888'; ?>">
            <?php echo $isOnline ? '● Online' : '○ Offline'; ?>
          </span>
          · Joined <?php echo date("M j, Y", strtotime($user["created_at"])); ?>
          <?php if ($user["last_activity"]): ?>
            · Last active <?php echo date("M j, g:i A", strtotime($user["last_activity"])); ?>
          <?php endif; ?>
        </p>
        <p><strong><?php echo count($books); ?></strong> books uploaded · <strong><?php echo count($wishlist); ?></strong> in wishlist</p>
      </div>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
      <a href="admin_delete_user.php?id=<?php echo $user['id']; ?>" class="btn-profile-action"
         style="border-color:#e74c3c; color:#e74c3c;"
         onclick="return confirm('Delete this user and ALL their books permanently?');">
        <i class="fas fa-trash"></i> Delete User
      </a>
    </div>
  </div>

  <h3 class="section-title" style="font-size:1.15rem">Uploaded Books</h3>
  <?php if (empty($books)): ?>
    <p style="color:#888">This user has not uploaded any books.</p>
  <?php else: ?>
    <div style="overflow-x:auto; margin-bottom:36px">
      <table class="table-dark">
        <thead>
          <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Genre</th>
            <th>Uploaded</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($books as $book): ?>
            <tr>
              <td><?php echo htmlspecialchars($book["title"]); ?></td>
              <td><?php echo htmlspecialchars($book["author"]); ?></td>
              <td><?php echo htmlspecialchars($book["genre"]); ?></td>
              <td><?php echo date("M j, Y", strtotime($book["created_at"])); ?></td>
              <td>
                <a href="admin_edit_book.php?id=<?php echo $book["id"]; ?>&from_user=<?php echo $userId; ?>" class="btn-sm-action btn-edit">Edit</a>
                <a href="admin_delete_book.php?id=<?php echo $book["id"]; ?>&from_user=<?php echo $userId; ?>" class="btn-sm-action btn-delete"
                   onclick="return confirm('Delete this book permanently?');">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  <h3 class="section-title" style="font-size:1.15rem">Wishlist</h3>
  <?php if (empty($wishlist)): ?>
    <p style="color:#888">No books in wishlist.</p>
  <?php else: ?>
    <div style="overflow-x:auto">
      <table class="table-dark">
        <thead>
          <tr><th>Title</th><th>Author</th><th>Genre</th></tr>
        </thead>
        <tbody>
          <?php foreach ($wishlist as $w): ?>
            <tr>
              <td><?php echo htmlspecialchars($w["title"]); ?></td>
              <td><?php echo htmlspecialchars($w["author"]); ?></td>
              <td><?php echo htmlspecialchars($w["genre"]); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<footer class="site-footer">
  <p>© 2026 StorySpill · A personal digital bookshelf</p>
</footer>

</body>
</html>