<?php
session_start();
require "db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];

$stmt = $pdo->prepare("SELECT wishlist.id AS wishlist_id, books.id AS book_id, books.title, books.author, books.genre, books.pdf_path
                        FROM wishlist
                        JOIN books ON wishlist.book_id = books.id
                        WHERE wishlist.user_id = ?
                        ORDER BY wishlist.id DESC");
$stmt->execute([$userId]);
$wishlistBooks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StorySpill - Wishlist</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body data-protected="true">

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

  <div class="dashboard-section">
    <h3>My Wishlist</h3>

    <?php if (count($wishlistBooks) === 0) { ?>
      <p>Your wishlist is empty. <a href="index.php">Browse books</a> to save some.</p>
    <?php } else { ?>
      <table>
        <thead>
          <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Genre</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($wishlistBooks as $item) { ?>
            <tr>
              <td><?php echo htmlspecialchars($item["title"]); ?></td>
              <td><?php echo htmlspecialchars($item["author"]); ?></td>
              <td><?php echo htmlspecialchars($item["genre"]); ?></td>
              <td>
                <?php if (!empty($item["pdf_path"])) { ?>
                  <a href="<?php echo htmlspecialchars($item["pdf_path"]); ?>" target="_blank" class="read-btn">Read</a>
                <?php } ?>
                <a href="remove_wishlist.php?id=<?php echo $item["book_id"]; ?>" class="remove-btn">Remove</a>
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