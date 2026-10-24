<?php
session_start();
require "db_connect.php";

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

// Always load ALL books — filtering happens client-side with fall animation
$sql = "SELECT books.*, users.name AS uploader_name 
        FROM books 
        JOIN users ON books.user_id = users.id 
        WHERE 1=1";
$params = [];

if ($search !== "") {
    $sql .= " AND (books.title LIKE ? OR books.author LIKE ? OR books.genre LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql .= " ORDER BY books.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

$genreStmt = $pdo->query("SELECT DISTINCT genre FROM books ORDER BY genre");
$allGenres = $genreStmt->fetchAll(PDO::FETCH_COLUMN);

$totalBooks = count($books);
$isLoggedIn = isset($_SESSION["user_id"]);

if ($isLoggedIn) {
    $pdo->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?")
        ->execute([$_SESSION["user_id"]]);
}

$wishlistIds = [];
if ($isLoggedIn) {
    $w = $pdo->prepare("SELECT book_id FROM wishlist WHERE user_id = ?");
    $w->execute([$_SESSION["user_id"]]);
    $wishlistIds = $w->fetchAll(PDO::FETCH_COLUMN);
}

$spineColors = [
    "#FF2D55","#FF6B00","#FFCC00","#00E676","#00E5FF","#2979FF",
    "#D500F9","#FF1744","#76FF03","#FFEA00","#00B0FF","#F50057",
    "#651FFF","#1DE9B6","#FF9100","#C6FF00","#FF4081","#00BFA5",
    "#FF3D00","#AEEA00","#E040FB","#40C4FF","#FF6E40","#69F0AE",
    "#FF5252","#7C4DFF","#FFD740","#18FFFF","#FF80AB","#B2FF59"
];

// Abstract pattern overlays (CSS gradients mixed in)
$patterns = [
    "radial-gradient(circle at 30% 20%, rgba(255,255,255,0.25) 0%, transparent 50%)",
    "linear-gradient(135deg, rgba(255,255,255,0.2) 0%, transparent 40%, rgba(0,0,0,0.15) 100%)",
    "repeating-linear-gradient(45deg, transparent, transparent 6px, rgba(255,255,255,0.08) 6px, rgba(255,255,255,0.08) 12px)",
    "radial-gradient(ellipse at bottom, rgba(255,255,255,0.2) 0%, transparent 60%)",
    "linear-gradient(180deg, rgba(255,255,255,0.15) 0%, transparent 30%, rgba(0,0,0,0.2) 100%)",
    "repeating-linear-gradient(-30deg, transparent, transparent 8px, rgba(255,255,255,0.06) 8px, rgba(255,255,255,0.06) 16px)",
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StorySpill</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/navbar.php"; ?>

<div class="app-container">
  <header class="main-header">
    <h1 class="site-title">StorySpill</h1>
    <div class="reads-count">
      <span id="total-reads"><?php echo $totalBooks; ?></span> READS
      <?php if ($isLoggedIn): ?>
        &nbsp;·&nbsp; <span id="read-count"><?php echo count($wishlistIds); ?></span> SAVED
      <?php endif; ?>
    </div>
  </header>

  <?php if ($isLoggedIn): ?>
    <p class="welcome-bar">Welcome back, <strong><?php echo htmlspecialchars($_SESSION["user_name"]); ?></strong></p>
  <?php endif; ?>

  <div class="controls-section">
    <div class="search-wrapper">
      <i class="fas fa-search search-icon"></i>
      <form method="GET" action="index.php" id="searchForm" style="margin:0">
        <input type="text" name="search" id="searchInput" class="form-control search-input"
               placeholder="WHAT ARE YOU LOOKING FOR?"
               value="<?php echo htmlspecialchars($search); ?>">
      </form>
    </div>
    <button type="button" id="surpriseBtn" class="surprise-btn">SURPRISE ME</button>
  </div>

  <!-- Genre Categories (client-side filter with fall animation) -->
  <div class="categories-wrapper">
    <div class="categories-scroll" id="categories">
      <button type="button" class="cat-btn active" data-genre="ALL">ALL</button>
      <?php foreach ($allGenres as $g): ?>
        <button type="button" class="cat-btn" data-genre="<?php echo htmlspecialchars($g); ?>">
          <?php echo htmlspecialchars(strtoupper($g)); ?>
        </button>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="bookshelf-container">
    <div class="shelf-instruction">
      HOVER A BOOK TO SEE ITS NAME · CLICK TO OPEN · FILTER TO DROP UNMATCHED BOOKS
    </div>

    <div class="shelf" id="bookshelf">
      <?php if (empty($books)): ?>
        <div class="empty-state w-100" id="emptyState">
          <i class="fas fa-book-open fa-3x mb-3" style="opacity:0.3"></i>
          <p>No books on the shelf yet.<?php if ($isLoggedIn): ?> <a href="upload.php">Upload the first one</a><?php endif; ?></p>
        </div>
      <?php else: ?>
        <div class="empty-state w-100 d-none" id="emptyState">
          <i class="fas fa-book-open fa-3x mb-3" style="opacity:0.3"></i>
          <p>No books match this genre.</p>
        </div>
        <?php foreach ($books as $i => $book):
          $color = $spineColors[$book["id"] % count($spineColors)];
          $spineTitle = mb_strlen($book["title"]) > 36 ? mb_substr($book["title"], 0, 34).'…' : $book["title"];
        ?>
          <div class="book"
               style="background: <?php echo $patterns[$book['id'] % count($patterns)]; ?>, linear-gradient(90deg, <?php echo $color; ?> 0%, <?php echo $color; ?>ee 15%, <?php echo $color; ?> 50%, <?php echo $color; ?>cc 100%);"
               data-id="<?php echo $book["id"]; ?>"
               data-title="<?php echo htmlspecialchars($book["title"]); ?>"
               data-author="<?php echo htmlspecialchars($book["author"]); ?>"
               data-genre="<?php echo htmlspecialchars($book["genre"]); ?>"
               data-uploader="<?php echo htmlspecialchars($book["uploader_name"]); ?>"
               data-cover="<?php echo htmlspecialchars($book["cover_image"] ?? ''); ?>"
               data-pdf="<?php echo htmlspecialchars($book["pdf_path"] ?? ''); ?>"
               data-video="<?php echo htmlspecialchars($book["intro_video"] ?? ''); ?>"
               data-color="<?php echo $color; ?>"
               data-index="<?php echo $i; ?>">
            <div class="book-spine-text"><?php echo htmlspecialchars($spineTitle); ?></div>
            <div class="book-number">#<?php echo str_pad($book["id"], 2, "0", STR_PAD_LEFT); ?></div>
            <div class="book-tooltip"><?php echo htmlspecialchars($book["title"]); ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <div class="shelf-wood"></div>
  </div>
</div>

<!-- Book Detail Modal -->
<div class="modal fade" id="bookModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content book-modal-content">
      <button type="button" class="btn-close btn-close-white modal-close" data-bs-dismiss="modal"></button>
      <div class="row g-0">
        <div class="col-md-5 book-cover-col">
          <div class="book-cover-large" id="modalCover">
            <!-- Image -->
            <img id="modalCoverImg" src="" alt="" class="cover-media">
            <!-- Video (plays on hover) -->
            <video id="modalCoverVideo" class="cover-media cover-video" loop playsinline preload="metadata"></video>
            <!-- Fallback text cover -->
            <div class="cover-inner" id="modalCoverFallback">
              <div class="cover-number" id="modalNumber">#01</div>
              <h3 class="cover-title" id="modalCoverTitle">Title</h3>
              <p class="cover-author" id="modalCoverAuthor">Author</p>
            </div>
            <div class="hover-hint" id="hoverHint"><i class="fas fa-play"></i> Hover to play</div>
          </div>
        </div>
        <div class="col-md-7 book-info-col">
          <div class="book-info">
            <div class="book-label">STORYSPILL <span id="modalEssayNum">#01</span></div>
            <h2 class="book-title" id="modalTitle">Title</h2>
            <p class="book-subtitle" id="modalSubtitle">by Author</p>
            <div class="book-meta">
              <span class="meta-item"><i class="fas fa-user"></i> <span id="modalAuthor">Author</span></span>
              <span class="meta-item"><i class="fas fa-tag"></i> <span id="modalGenre">Genre</span></span>
              <span class="meta-item"><i class="fas fa-upload"></i> <span id="modalUploader">Uploader</span></span>
            </div>
            <div class="tags-container" id="modalTags"></div>
            <div class="action-buttons">
              <a href="#" id="readBtn" class="btn-read" target="_blank">
                READ <i class="fas fa-arrow-right"></i>
              </a>
              <?php if ($isLoggedIn): ?>
                <button class="btn btn-outline-light btn-sm" id="shelveBtn">
                  <i class="fas fa-bookmark"></i> SHELVE IT
                </button>
              <?php else: ?>
                <a href="login.php" class="btn btn-outline-light btn-sm">
                  <i class="fas fa-bookmark"></i> LOGIN TO SAVE
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1100">
  <div id="toast" class="toast align-items-center text-bg-dark border-0" role="alert">
    <div class="d-flex">
      <div class="toast-body" id="toastMsg"></div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  window.IS_LOGGED_IN = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
  window.WISHLIST_IDS = <?php echo json_encode(array_map('intval', $wishlistIds)); ?>;
</script>
<script src="js/app.js"></script>
</body>
</html>
