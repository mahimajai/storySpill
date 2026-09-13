<?php
session_start();
require "db_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$title = "";
$author = "";
$genre = "";

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

    $coverPath = "";
    $videoPath = "";
    $pdfPath = "";
    $pdfOption = $_POST["pdf_option"] ?? "file"; // "file" or "link"

    if (!empty($_FILES["cover_image"]["name"])) {
        $coverName = time() . "_" . basename($_FILES["cover_image"]["name"]);
        $coverPath = "uploads/covers/" . $coverName;
       
        if (!move_uploaded_file($_FILES["cover_image"]["tmp_name"], $coverPath)) {
            $errors[] = "Could not save cover image. Check that uploads/covers/ exists.";
            $coverPath = "";
        }
    } else {
        $errors[] = "Cover image is required.";
    }

    if (!empty($_FILES["intro_video"]["name"])) {
        $videoName = time() . "_" . basename($_FILES["intro_video"]["name"]);
        $videoPath = "uploads/videos/" . $videoName;
        if (!move_uploaded_file($_FILES["intro_video"]["tmp_name"], $videoPath)) {
            $errors[] = "Could not save intro video. Check that uploads/videos/ exists.";
            $videoPath = "";
        }
    } else {
        $errors[] = "Intro video is required.";
    }

    if ($pdfOption === "link") {
        $pdfLink = trim($_POST["pdf_link"] ?? "");
        if ($pdfLink === "") {
            $errors[] = "PDF link is required.";
        } elseif (!filter_var($pdfLink, FILTER_VALIDATE_URL)) {
            $errors[] = "Enter a valid PDF link (must start with http:// or https://).";
        } else {
            $pdfPath = $pdfLink;
        }
    } else {
        if (!empty($_FILES["pdf_file"]["name"])) {
            $pdfExt = strtolower(pathinfo($_FILES["pdf_file"]["name"], PATHINFO_EXTENSION));
            if ($pdfExt !== "pdf") {
            
                $errors[] = "PDF file must have a .pdf extension.";
            } else {
                $pdfName = time() . "_" . basename($_FILES["pdf_file"]["name"]);
                $pdfPath = "uploads/pdfs/" . $pdfName;
                if (!move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $pdfPath)) {
                    $errors[] = "Could not save PDF file. Check that uploads/pdfs/ exists.";
                    $pdfPath = "";
                }
            }
        } else {
            $errors[] = "Please choose a PDF file or provide a link.";
        }
    }

    if (empty($errors)) {
        $insertStmt = $pdo->prepare("INSERT INTO books (user_id, title, author, genre, cover_image, intro_video, pdf_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->execute([$_SESSION["user_id"], $title, $author, $genre, $coverPath, $videoPath, $pdfPath]);

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
  <title>StorySpill - Upload Book</title>
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
    <h2>Upload a Book</h2>

    <?php if (!empty($errors)) { ?>
      <div class="form-message error-message">
        <?php foreach ($errors as $error) { echo "<p>" . htmlspecialchars($error) . "</p>"; } ?>
      </div>
    <?php } ?>

    <form id="uploadForm" method="POST" action="upload.php" enctype="multipart/form-data">
      <div class="form-group">
        <label for="bookTitle">Book Title</label>
        <input type="text" id="bookTitle" name="title" placeholder="Enter book title" value="<?php echo htmlspecialchars($title); ?>">
        <span class="error-text" id="titleError">Title is required</span>
      </div>

      <div class="form-group">
        <label for="bookAuthor">Author Name</label>
        <input type="text" id="bookAuthor" name="author" placeholder="Enter author name" value="<?php echo htmlspecialchars($author); ?>">
        <span class="error-text" id="authorError">Author is required</span>
      </div>

      <div class="form-group">
        <label for="bookGenre">Genre</label>
        <select id="bookGenre" name="genre">
          <option value="">Select a genre</option>
          <option value="Fiction" <?php echo ($genre === "Fiction") ? "selected" : ""; ?>>Fiction</option>
          <option value="Mystery" <?php echo ($genre === "Mystery") ? "selected" : ""; ?>>Mystery</option>
          <option value="Drama" <?php echo ($genre === "Drama") ? "selected" : ""; ?>>Drama</option>
          <option value="Romance" <?php echo ($genre === "Romance") ? "selected" : ""; ?>>Romance</option>
          <option value="Fantasy" <?php echo ($genre === "Fantasy") ? "selected" : ""; ?>>Fantasy</option>
            <option value="Spiritual" <?php echo ($genre === "Spiritual") ? "selected" : ""; ?>>Fantasy</option>
        </select>
        <span class="error-text" id="genreError">Please select a genre</span>
      </div>

      <div class="form-group">
        <label for="bookCover">Cover Image</label>
        <input type="file" id="bookCover" name="cover_image" accept="image/*">
        <span class="error-text" id="coverError">Cover image is required</span>
      </div>

      <div class="form-group">
        <label for="bookVideo">Introductory Video</label>
        <input type="file" id="bookVideo" name="intro_video" accept="video/*">
        <span class="error-text" id="videoError">Intro video is required</span>
      </div>

      <div class="form-group">
        <label>Book PDF</label>

        <div class="pdf-option-toggle">
          <label>
            <input type="radio" name="pdf_option" value="file" id="pdfOptionFile" checked>
            Upload from PC
          </label>
          <label>
            <input type="radio" name="pdf_option" value="link" id="pdfOptionLink">
            Provide a link
          </label>
        </div>

        <div id="pdfFileGroup">
          <input type="file" id="bookpdf" name="pdf_file" accept="application/pdf">
        </div>

        <div id="pdfLinkGroup" style="display: none;">
          <input type="url" id="bookpdfLink" name="pdf_link" placeholder="https://example.com/mybook.pdf">
        </div>

        <span class="error-text" id="pdfError">PDF file or link is required</span>
      </div>
      <button type="submit" class="submit-btn">Upload Book</button>
    </form>
  </div>

  <footer>
    <p>&copy; 2026 StorySpill. All rights reserved.</p>
  </footer>

  <script src="js/script.js"></script>
  <script>
   
    const pdfOptionFile = document.getElementById("pdfOptionFile");
    const pdfOptionLink = document.getElementById("pdfOptionLink");
    const pdfFileGroup = document.getElementById("pdfFileGroup");
    const pdfLinkGroup = document.getElementById("pdfLinkGroup");
    const pdfFileInput = document.getElementById("bookpdf");
    const pdfLinkInput = document.getElementById("bookpdfLink");

    function togglePdfInput() {
      if (pdfOptionLink.checked) {
        pdfFileGroup.style.display = "none";
        pdfLinkGroup.style.display = "block";
        pdfFileInput.value = "";
      } else {
        pdfFileGroup.style.display = "block";
        pdfLinkGroup.style.display = "none";
        pdfLinkInput.value = "";
      }
    }

    pdfOptionFile.addEventListener("change", togglePdfInput);
    pdfOptionLink.addEventListener("change", togglePdfInput);
  </script>
</body>
</html>