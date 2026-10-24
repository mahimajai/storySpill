<?php
session_start();
require "db_connect.php";

$bookId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

$stmt = $pdo->prepare("SELECT books.*, users.name AS uploader_name FROM books
                        JOIN users ON books.user_id = users.id
                        WHERE books.id = ?");
$stmt->execute([$bookId]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    header("Location: index.php");
    exit;
}

$isLoggedIn = isset($_SESSION["user_id"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StorySpill - <?php echo htmlspecialchars($book["title"]); ?></title>
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
        <li><a href="change_password.php">Change Password</a></li>
        <li><a href="logout.php">Logout</a></li>
      <?php } else { ?>
        <li><a href="signup.php">Sign Up</a></li>
      <?php } ?>
    </ul>
  </nav>

  <div class="insta-post">

    <div class="post-header">
      <div class="avatar-circle"><?php echo strtoupper(substr($book["uploader_name"], 0, 1)); ?></div>
      <div class="post-header-text">
        <strong><?php echo htmlspecialchars($book["uploader_name"]); ?></strong>
        <span>Genre: <?php echo htmlspecialchars($book["genre"]); ?></span>
      </div>
    </div>

    <div class="post-media">
      <?php if (!empty($book["intro_video"])) { ?>
        <video class="post-video" autoplay loop muted controls playsinline>
          <source src="<?php echo htmlspecialchars($book["intro_video"]); ?>">
        </video>
      <?php } elseif (!empty($book["cover_image"])) { ?>
        <img class="post-media-img" src="<?php echo htmlspecialchars($book["cover_image"]); ?>" alt="<?php echo htmlspecialchars($book["title"]); ?>">
      <?php } else { ?>
        <img class="post-media-img" src="https://via.placeholder.com/500x500?text=Book+Cover" alt="<?php echo htmlspecialchars($book["title"]); ?>">
      <?php } ?>
    </div>

    <div class="post-caption">
      <h2><?php echo htmlspecialchars($book["title"]); ?></h2>
      <p>by <?php echo htmlspecialchars($book["author"]); ?></p>

      <div class="book-card-actions">
        <?php if (!empty($book["pdf_path"])) { ?>
          <a href="<?php echo htmlspecialchars($book["pdf_path"]); ?>" target="_blank" class="read-btn">Read</a>
        <?php } ?>

        <?php if ($isLoggedIn) { ?>
          <a href="save_wishlist.php?id=<?php echo $book["id"]; ?>" class="save-btn">Save</a>
        <?php } else { ?>
          <a href="login.php" class="save-btn">Save</a>
        <?php } ?>

        <a href="index.php" class="back-btn">&larr; Back</a>
      </div>
    </div>

  </div>

  <footer>
    <p>&copy; 2026 StorySpill. All rights reserved.</p>
  </footer>

  <script src="js/script.js"></script>
</body>
</html>