<?php
require "includes/auth_check.php";

$genreRows = $pdo->query("SELECT name FROM genres ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
$genres = $genreRows ?: [
    "Fiction","Non-Fiction","Mystery","Thriller","Romance","Fantasy",
    "Science Fiction","Philosophy","Business","Self-Help","History & Science",
    "AI & Tech","Life & Philosophy","Mind & Psychology","Money & Economics",
    "India","Culture & Society","World & Politics","Learning & Growth","Business & Startups"
];

$errors = [];
$title = $author = $genre = "";

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
    if ($genre === "")  $errors[] = "Please select a genre.";

    $coverPath = $videoPath = $pdfPath = "";
    $pdfOption   = $_POST["pdf_option"] ?? "file";
    $videoOption = $_POST["video_option"] ?? "file";
    $allowedVideo = ["mp4", "webm", "ogg", "ogv"];

    if (!empty($_FILES["cover_image"]["name"])) {
        $coverName = time() . "_" . basename($_FILES["cover_image"]["name"]);
        $coverPath = "uploads/covers/" . $coverName;
        if (!move_uploaded_file($_FILES["cover_image"]["tmp_name"], $coverPath)) {
            $errors[] = "Could not save cover image.";
            $coverPath = "";
        }
    } else {
        $errors[] = "Cover image is required.";
    }

    if ($videoOption === "link") {
        $videoLink = trim($_POST["video_link"] ?? "");
        if ($videoLink === "") {
            $errors[] = "Video link is required.";
        } elseif (!filter_var($videoLink, FILTER_VALIDATE_URL)) {
            $errors[] = "Enter a valid video URL.";
        } else {
            $videoPath = $videoLink;
        }
    } else {
        if (!empty($_FILES["intro_video"]["name"])) {
            $videoName = time() . "_" . basename($_FILES["intro_video"]["name"]);
            $videoPath = "uploads/videos/" . $videoName;
            if (!move_uploaded_file($_FILES["intro_video"]["tmp_name"], $videoPath)) {
                $errors[] = "Could not save intro video.";
                $videoPath = "";
            }
        }
    }

    if ($pdfOption === "link") {
        $pdfLink = trim($_POST["pdf_link"] ?? "");
        if ($pdfLink === "") {
            $errors[] = "PDF link is required.";
        } elseif (!filter_var($pdfLink, FILTER_VALIDATE_URL)) {
            $errors[] = "Enter a valid PDF URL.";
        } else {
            $pdfPath = $pdfLink;
        }
    } else {
        if (!empty($_FILES["pdf_file"]["name"])) {
            $pdfExt = strtolower(pathinfo($_FILES["pdf_file"]["name"], PATHINFO_EXTENSION));
            if ($pdfExt !== "pdf") {
                $errors[] = "Only PDF files are allowed.";
            } else {
                $pdfName = time() . "_" . basename($_FILES["pdf_file"]["name"]);
                $pdfPath = "uploads/pdfs/" . $pdfName;
                if (!move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $pdfPath)) {
                    $errors[] = "Could not save PDF.";
                    $pdfPath = "";
                }
            }
        } else {
            $errors[] = "Please upload a PDF or provide a link.";
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO books (user_id, title, author, genre, price, cover_image, intro_video, pdf_path)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $_SESSION["user_id"], $title, $author, $genre,
            $coverPath ?: null, $videoPath ?: null, $pdfPath ?: null
        ]);
        header("Location: dashboard.php?uploaded=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload Book – StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include "includes/navbar.php"; ?>

<div class="form-page" style="max-width:560px">
  <div class="form-card">
    <h2>Upload Book</h2>
    <p class="form-sub">Add a new book to the library</p>

    <?php if (!empty($errors)): ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $e) echo "<p>".htmlspecialchars($e)."</p>"; ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="upload.php" enctype="multipart/form-data">
      <div class="form-group">
        <label>Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
      </div>
      <div class="form-group">
        <label>Author</label>
        <input type="text" name="author" value="<?php echo htmlspecialchars($author); ?>" required>
      </div>
      <div class="form-group">
        <label>Genre / Category</label>
        <select name="genre" required>
          <option value="">Select a genre</option>
          <?php foreach ($genreList as $g): ?>
            <option value="<?php echo htmlspecialchars($g); ?>" <?php echo $genre===$g?'selected':''; ?>>
              <?php echo htmlspecialchars($g); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Cover Image *</label>
        <input type="file" name="cover_image" accept="image/*" required>
      </div>

      <div class="form-group">
        <label>Introductory Video (optional)</label>
        <div class="video-option-toggle">
          <label><input type="radio" name="video_option" value="file" checked> Upload file</label>
          <label><input type="radio" name="video_option" value="link"> Provide link</label>
        </div>
        <input type="file" name="intro_video" accept="video/*" id="videoFile">
        <input type="url" name="video_link" placeholder="https://...mp4" id="videoLink" style="display:none; margin-top:8px">
      </div>

      <div class="form-group">
        <label>PDF / Book File *</label>
        <div class="pdf-option-toggle">
          <label><input type="radio" name="pdf_option" value="file" checked> Upload PDF</label>
          <label><input type="radio" name="pdf_option" value="link"> Provide link</label>
        </div>
        <input type="file" name="pdf_file" accept=".pdf" id="pdfFile">
        <input type="url" name="pdf_link" placeholder="https://...pdf" id="pdfLink" style="display:none; margin-top:8px">
      </div>

      <button type="submit" class="submit-btn">Upload Book</button>
    </form>
  </div>
</div>

<script>
  document.getElementById('navToggle')?.addEventListener('click', () =>
    document.getElementById('navLinks').classList.toggle('open'));

  document.querySelectorAll('input[name="video_option"]').forEach(r => {
    r.addEventListener('change', () => {
      document.getElementById('videoFile').style.display = r.value === 'file' ? 'block' : 'none';
      document.getElementById('videoLink').style.display = r.value === 'link' ? 'block' : 'none';
    });
  });
  document.querySelectorAll('input[name="pdf_option"]').forEach(r => {
    r.addEventListener('change', () => {
      document.getElementById('pdfFile').style.display = r.value === 'file' ? 'block' : 'none';
      document.getElementById('pdfLink').style.display = r.value === 'link' ? 'block' : 'none';
    });
  });
</script>
</body>
</html>
