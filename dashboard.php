<?php
require "includes/auth_check.php";

$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$_SESSION["user_id"]]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

$booksStmt = $pdo->prepare("SELECT * FROM books WHERE user_id = ? ORDER BY created_at DESC");
$booksStmt->execute([$_SESSION["user_id"]]);
$books = $booksStmt->fetchAll(PDO::FETCH_ASSOC);
$totalBooks = count($books);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="app-container">
  <?php if (isset($_GET["uploaded"])): ?>
    <div class="form-message success-message" style="max-width:600px;margin:0 auto 20px">
      <p>Book uploaded successfully!</p>
    </div>
  <?php endif; ?>
  <?php if (isset($_GET["updated"])): ?>
    <div class="form-message success-message" style="max-width:600px;margin:0 auto 20px">
      <p>Book updated successfully!</p>
    </div>
  <?php endif; ?>

  <div class="dash-header" style="justify-content:space-between; align-items:center;">
    <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
      <div class="dash-avatar"><i class="fas fa-user"></i></div>
      <div class="dash-info">
        <h2><?php echo htmlspecialchars($user["name"]); ?></h2>
        <p><?php echo htmlspecialchars($user["email"]); ?></p>
        <p><strong><?php echo $totalBooks; ?></strong> books on the shelf</p>
      </div>
    </div>
    <div class="profile-actions" style="display:flex; gap:10px; flex-wrap:wrap;">
      <a href="upload.php" class="btn-profile-action primary">
        <i class="fas fa-upload"></i> Upload Book
      </a>
      <a href="change_password.php" class="btn-profile-action">
        <i class="fas fa-key"></i> Change Password
      </a>
    </div>
  </div>

  <h3 class="section-title">My Uploaded Books</h3>

  <?php if ($totalBooks === 0): ?>
    <p style="color:#888">You haven't uploaded any books yet. <a href="upload.php">Upload your first one</a>.</p>
  <?php else: ?>
    <div style="overflow-x:auto">
      <table class="table-dark">
        <thead>
          <tr>
            <th>Title</th>
            <th>Genre</th>
            <th>Author</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($books as $book): ?>
            <tr>
              <td><?php echo htmlspecialchars($book["title"]); ?></td>
              <td><?php echo htmlspecialchars($book["genre"]); ?></td>
              <td><?php echo htmlspecialchars($book["author"]); ?></td>
              <td>
                <a href="edit_book.php?id=<?php echo $book["id"]; ?>" class="btn-sm-action btn-edit">Edit</a>
                <a href="delete_book.php?id=<?php echo $book["id"]; ?>" class="btn-sm-action btn-delete"
                   onclick="return confirm('Delete this book?');">Delete</a>
              </td>
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
