<?php
require "includes/admin_auth_check.php";

$bookId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$fromUser = isset($_GET["from_user"]) ? (int)$_GET["from_user"] : 0;

$stmt = $pdo->prepare("SELECT books.*, users.name AS uploader_name FROM books JOIN users ON books.user_id = users.id WHERE books.id = ?");
$stmt->execute([$bookId]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$book) {
    header("Location: admin_dashboard.php");
    exit;
}

$errors = [];
$genres = [
    "Fiction","Non-Fiction","Mystery","Thriller","Romance","Fantasy",
    "Science Fiction","Philosophy","Business","Self-Help","History & Science",
    "AI & Tech","Life & Philosophy","Mind & Psychology","Money & Economics",
    "India","Culture & Society","World & Politics","Learning & Growth","Business & Startups"
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title  = trim($_POST["title"] ?? "");
    $author = trim($_POST["author"] ?? "");
    $genre  = trim($_POST["genre"] ?? "");

    if ($title === "") $errors[] = "Title is required.";
    if ($author === "") $errors[] = "Author is required.";
    if ($genre === "") $errors[] = "Genre is required.";

    if (empty($errors)) {
        $pdo->prepare("UPDATE books SET title = ?, author = ?, genre = ? WHERE id = ?")
            ->execute([$title, $author, $genre, $bookId]);

        if ($fromUser > 0) {
            header("Location: admin_user.php?id=" . $fromUser . "&book_updated=1");
        } else {
            header("Location: admin_dashboard.php?book_updated=1");
        }
        exit;
    }
    $book["title"] = $title;
    $book["author"] = $author;
    $book["genre"] = $genre;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Book – Admin · StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="form-page">
  <div class="form-card">
    <h2>Edit Book (Admin)</h2>
    <p class="form-sub">Uploaded by <?php echo htmlspecialchars($book["uploader_name"]); ?></p>

    <?php if (!empty($errors)): ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($book["title"]); ?>" required>
      </div>
      <div class="form-group">
        <label>Author</label>
        <input type="text" name="author" value="<?php echo htmlspecialchars($book["author"]); ?>" required>
      </div>
      <div class="form-group">
        <label>Genre</label>
        <select name="genre" required>
          <?php foreach ($genres as $g): ?>
            <option value="<?php echo htmlspecialchars($g); ?>"
              <?php echo ($book["genre"] === $g) ? "selected" : ""; ?>>
              <?php echo htmlspecialchars($g); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <button type="submit" class="submit-btn">Save Changes</button>
    </form>
    <p style="text-align:center; margin-top:14px;">
      <a href="<?php echo $fromUser ? 'admin_user.php?id='.$fromUser : 'admin_dashboard.php'; ?>" style="color:#888;">Cancel</a>
    </p>
  </div>
</div>
</body>
</html>