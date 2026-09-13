<?php
session_start();
require "db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION["user_id"];
$errors = [];

$bookId = isset($_GET["id"]) ? (int) $_GET["id"] : (isset($_POST["book_id"]) ? (int) $_POST["book_id"] : 0);

if ($bookId <= 0) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"]);
    $author = trim($_POST["author"]);
    $genre = trim($_POST["genre"]);

    if ($title === "") {
        $errors[] = "Title is required.";
    }

    if ($author === "") {
        $errors[] = "Author is required.";
    }

    if ($genre === "") {
        $errors[] = "Please select a genre.";
    }

    if (empty($errors)) {
        $updateStmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, genre = ? WHERE id = ? AND user_id = ?");
        $updateStmt->execute([$title, $author, $genre, $bookId, $userId]);

        header("Location: dashboard.php?updated=1");
        exit;
    }

    $book = ["id" => $bookId, "title" => $title, "author" => $author, "genre" => $genre];
} else {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ? AND user_id = ?");
    $stmt->execute([$bookId, $userId]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StorySpill - Edit Book</title>
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

  <div class="form-container">
    <h2>Edit Book</h2>

    <?php if (!empty($errors)) { ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $error) { echo "<p>" . htmlspecialchars($error) . "</p>"; } ?>
      </div>
    <?php } ?>

    <form method="POST" action="edit_book.php">
      <input type="hidden" name="book_id" value="<?php echo $book["id"]; ?>">

      <div class="form-group">
        <label for="title">Book Title</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($book["title"]); ?>">
      </div>

      <div class="form-group">
        <label for="author">Author Name</label>
        <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($book["author"]); ?>">
      </div>

      <div class="form-group">
        <label for="genre">Genre</label>
        <select id="genre" name="genre">
          <option value="">Select a genre</option>
          <option value="Fiction" <?php echo ($book["genre"] === "Fiction") ? "selected" : ""; ?>>Fiction</option>
          <option value="Mystery" <?php echo ($book["genre"] === "Mystery") ? "selected" : ""; ?>>Mystery</option>
          <option value="Drama" <?php echo ($book["genre"] === "Drama") ? "selected" : ""; ?>>Drama</option>
          <option value="Romance" <?php echo ($book["genre"] === "Romance") ? "selected" : ""; ?>>Romance</option>
          <option value="Fantasy" <?php echo ($book["genre"] === "Fantasy") ? "selected" : ""; ?>>Fantasy</option>
        </select>
      </div>

      <button type="submit" class="submit-btn">Save Changes</button>
    </form>
  </div>

  <footer>
    <p>&copy; 2026 StorySpill. All rights reserved.</p>
  </footer>

  <script src="js/script.js"></script>
</body>
</html>
