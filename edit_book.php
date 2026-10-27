<?php
require "includes/auth_check.php";

$bookId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$stmt = $pdo->prepare("SELECT * FROM books WHERE id = ? AND user_id = ?");
$stmt->execute([$bookId, $_SESSION["user_id"]]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    header("Location: dashboard.php");
    exit;
}

$errors = [];
$title  = $book["title"];
$author = $book["author"];
$genre  = $book["genre"];

$genreList = [
    "Fiction", "Non-Fiction", "Mystery", "Thriller", "Drama",
    "Romance", "Fantasy", "Science Fiction", "Horror", "Adventure",
    "Historical Fiction", "Biography", "Autobiography", "Self-Help",
    "Poetry", "Comedy / Humor", "Crime", "Young Adult", "Children's",
    "Fairy Tale", "Classic Literature", "Graphic Novel", "Spiritual / Religious",
    "Philosophy", "Business", "Health & Wellness", "Travel", "Cooking",
    "Art & Photography", "True Crime", "Short Stories",
    "Learning & Growth", "Life & Philosophy", "Mind & Psychology",
    "AI & Tech", "Culture & Society", "History & Science",
    "Business & Startups", "Money & Economics", "World & Politics", "India"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title  = trim($_POST["title"] ?? "");
    $author = trim($_POST["author"] ?? "");
    $genre  = trim($_POST["genre"] ?? "");

    if ($title === "")  $errors[] = "Title is required.";
    if ($author === "") $errors[] = "Author is required.";
    if ($genre === "")  $errors[] = "Genre is required.";

    if (empty($errors)) {
        $pdo->prepare("UPDATE books SET title=?, author=?, genre=? WHERE id=? AND user_id=?")
            ->execute([$title, $author, $genre, $bookId, $_SESSION["user_id"]]);
        header("Location: dashboard.php?updated=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Book – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="form-page">
  <div class="form-card">
    <h2>Edit Book</h2>
    <p class="form-sub"><?php echo htmlspecialchars($book["title"]); ?></p>

    <?php if (!empty($errors)): ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
      </div>
      <div class="form-group">
        <label>Author</label>
        <input type="text" name="author" value="<?php echo htmlspecialchars($author); ?>" required>
      </div>
      <div class="form-group">
        <label>Genre</label>
        <select name="genre" required>
          <?php foreach ($genreList as $g): ?>
            <option value="<?php echo htmlspecialchars($g); ?>" <?php echo $genre===$g?'selected':''; ?>>
              <?php echo htmlspecialchars($g); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="submit-btn">Save Changes</button>
    </form>
  </div>
</div>

<footer class="site-footer">
  <p>© 2026 StorySpill · A personal digital bookshelf</p>
</footer>

</body>
</html>
