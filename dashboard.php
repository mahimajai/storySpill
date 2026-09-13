<?php
session_start();
require "db_connect.php";
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$userId = $_SESSION["user_id"];
$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);
$booksStmt = $pdo->prepare("SELECT * FROM books WHERE user_id = ? ORDER BY created_at DESC");
$booksStmt->execute([$userId]);
$books = $booksStmt->fetchAll(PDO::FETCH_ASSOC);
$totalBooks = count($books);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StorySpill - Profile</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body data-protected="true">

  <!-- Navbar -->
  <nav class="navbar">
    <div class="logo">StorySpill</div>
    <button class="menu-btn" id="menuBtn">&#9776;</button>
    <ul class="nav-links" id="navLinks">
      <li><a href="index.php">Home</a></li>
      <li><a href="dashboard.php">Profile</a></li>
      <li><a href="upload.php">Upload Book</a></li>
      <li><a href="wishlist.php">Wishlist</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <?php if (isset($_GET["uploaded"])) { ?>
    <div class="form-message success-message" style="margin:20px;">
      <p>Book uploaded successfully!</p>
    </div>
  <?php } ?>

  <?php if (isset($_GET["updated"])) { ?>
    <div class="form-message success-message" style="margin:20px;">
      <p>Book updated successfully!</p>
    </div>
  <?php } ?>

  <!-- Profile Header -->
  <div class="profile-header">
    <img src="https://via.placeholder.com/120x120?text=Photo" alt="Profile Photo">
    <div class="profile-info">
      <h2><?php echo htmlspecialchars($user["name"]); ?></h2>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($user["email"]); ?></p>
      <p><strong>Total Books Uploaded:</strong> <?php echo $totalBooks; ?></p>
    </div>
  </div>

  <!-- Uploaded Books Table -->
  <div class="dashboard-section">
    <h3>My Uploaded Books</h3>

    <?php if ($totalBooks === 0) { ?>
      <p>You haven't uploaded any books yet. <a href="upload.php">Upload your first book</a>.</p>
    <?php } else { ?>
      <table>
        <thead>
          <tr>
            <th>Title</th>
            <th>Genre</th>
            <th>Author</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($books as $book) { ?>
            <tr>
              <td><?php echo htmlspecialchars($book["title"]); ?></td>
              <td><?php echo htmlspecialchars($book["genre"]); ?></td>
              <td><?php echo htmlspecialchars($book["author"]); ?></td>
              <td>
                <a href="edit_book.php?id=<?php echo $book["id"]; ?>" class="edit-btn">Edit</a>
                <a href="delete_book.php?id=<?php echo $book["id"]; ?>" class="delete-btn" onclick="return confirm('Delete this book?');">Delete</a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    <?php } ?>
  </div>

  <footer>
    <p>&copy; 2026 StorySpill. All rights reserved.</p>
  </footer>

  <script src="js/script.js"></script>
</body>
</html>
