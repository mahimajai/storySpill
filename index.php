<?php
session_start();
require "db_connect.php";

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

if ($search !== "") {
    $likeSearch = "%" . $search . "%";
    $stmt = $pdo->prepare("SELECT books.*, users.name AS uploader_name FROM books
                            JOIN users ON books.user_id = users.id
                            WHERE books.title LIKE ? OR books.author LIKE ?
                            ORDER BY books.created_at DESC");
    $stmt->execute([$likeSearch, $likeSearch]);
} else {
    $stmt = $pdo->query("SELECT books.*, users.name AS uploader_name FROM books
                          JOIN users ON books.user_id = users.id
                          ORDER BY books.created_at DESC");
}

$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
$isLoggedIn = isset($_SESSION["user_id"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StorySpill - Home</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <nav class="navbar">
    <div class="logo">StorySpill</div>
    <button class="menu-btn" id="menuBtn">&#9776;</button>
    <ul class="nav-links" id="navLinks">
      <li><a href="index.php">Home</a></li>
      <li><a href="dashboard.php">Profile</a></li>
      <li><a href="upload.php">Upload Book</a></li>
      <li><a href="wishlist.php">Wishlist</a></li>
      <?php if ($isLoggedIn) { ?>
        <li><a href="logout.php">Logout</a></li>
      <?php } else { ?>
        <li><a href="signup.php">Sign Up</a></li>
      <?php } ?>
    </ul>
  </nav>

  <div class="search-bar">
    <form method="GET" action="index.php">
      <input type="text" name="search" placeholder="Search by book name or author..." value="<?php echo htmlspecialchars($search); ?>">
    </form>
  </div>

  <section class="book-grid-section">
    <h2>Browse Books</h2>

    <?php if (count($books) === 0) { ?>
      <p>No books found.</p>
    <?php } ?>

    <div class="book-grid">
      <?php foreach ($books as $book) { ?>
        <div class="book-card">
          <?php if (!empty($book["cover_image"])) { ?>
            <img src="<?php echo htmlspecialchars($book["cover_image"]); ?>" alt="<?php echo htmlspecialchars($book["title"]); ?>">
          <?php } else { ?>
            <img src="https://via.placeholder.com/200x220?text=Book+Cover" alt="<?php echo htmlspecialchars($book["title"]); ?>">
          <?php } ?>
          <h3><?php echo htmlspecialchars($book["title"]); ?></h3>
          <p>by <?php echo htmlspecialchars($book["author"]); ?></p>
          <p>Uploaded by <?php echo htmlspecialchars($book["uploader_name"]); ?></p>

          <div class="book-card-actions">
            <?php if (!empty($book["pdf_path"])) { ?>
              <a href="<?php echo htmlspecialchars($book["pdf_path"]); ?>" target="_blank" class="read-btn">Read</a>
            <?php } ?>

            <?php if ($isLoggedIn) { ?>
              <a href="save_wishlist.php?id=<?php echo $book["id"]; ?>" class="save-btn">Save</a>
            <?php } else { ?>
              <a href="login.php" class="save-btn">Save</a>
            <?php } ?>
          </div>
        </div>
      <?php } ?>
    </div>
  </section>

  <footer>
    <p>&copy; 2026 StorySpill. All rights reserved.</p>
  </footer>

  <script src="js/script.js"></script>
</body>
</html>