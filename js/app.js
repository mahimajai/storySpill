document.addEventListener('DOMContentLoaded', () => {
  const navToggle = document.getElementById('navToggle');
  const navLinks = document.getElementById('navLinks');
  if (navToggle) {
    navToggle.addEventListener('click', () => navLinks.classList.toggle('open'));
  }

  // Admin dropdown
  const adminBtn = document.getElementById('adminDropBtn');
  const adminMenu = document.getElementById('adminDropMenu');
  if (adminBtn && adminMenu) {
    adminBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      adminMenu.classList.toggle('show');
    });
    document.addEventListener('click', () => adminMenu.classList.remove('show'));
  }

  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('searchForm').submit();
      }
    });
  }

  const allBooks = Array.from(document.querySelectorAll('.book'));
  const emptyState = document.getElementById('emptyState');
  const totalReadsEl = document.getElementById('total-reads');

  document.querySelectorAll('.cat-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      filterBooks(btn.dataset.genre);
    });
  });

  function filterBooks(genre) {
    let visibleCount = 0;
    allBooks.forEach((book, i) => {
      const matches = (genre === 'ALL' || book.dataset.genre === genre);
      book.classList.remove('falling', 'hidden-book');
      book.style.display = '';
      book.style.animationDelay = '';
      book.style.opacity = '';
      book.style.transform = '';
      book.style.transition = '';

      if (matches) {
        visibleCount++;
        book.style.opacity = '0';
        book.style.transform = 'translateY(20px)';
        setTimeout(() => {
          book.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
          book.style.opacity = '1';
          book.style.transform = '';
        }, 30 + (visibleCount * 25));
      } else {
        const delay = (i % 12) * 40;
        book.style.animationDelay = delay + 'ms';
        book.classList.add('falling');
        setTimeout(() => {
          book.classList.add('hidden-book');
          book.style.display = 'none';
        }, 600 + delay);
      }
    });
    if (totalReadsEl) totalReadsEl.textContent = visibleCount;
    setTimeout(() => {
      if (emptyState) {
        if (visibleCount === 0) emptyState.classList.remove('d-none');
        else emptyState.classList.add('d-none');
      }
    }, 700);
  }

  allBooks.forEach(book => {
    book.addEventListener('click', () => openBook(book));
  });

  function openBook(bookEl) {
    const id = bookEl.dataset.id;
    const title = bookEl.dataset.title;
    const author = bookEl.dataset.author;
    const genre = bookEl.dataset.genre;
    const uploader = bookEl.dataset.uploader;
    const cover = bookEl.dataset.cover;
    const pdf = bookEl.dataset.pdf;
    const video = bookEl.dataset.video;
    const color = bookEl.dataset.color;

    document.getElementById('modalNumber').textContent = '#' + String(id).padStart(2, '0');
    document.getElementById('modalEssayNum').textContent = '#' + String(id).padStart(2, '0');
    document.getElementById('modalCoverTitle').textContent = title;
    document.getElementById('modalCoverAuthor').textContent = author;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalSubtitle').textContent = 'by ' + author;
    document.getElementById('modalAuthor').textContent = author;
    document.getElementById('modalGenre').textContent = genre;
    document.getElementById('modalUploader').textContent = uploader;

    const coverImg = document.getElementById('modalCoverImg');
    const coverVideo = document.getElementById('modalCoverVideo');
    const coverFallback = document.getElementById('modalCoverFallback');
    const modalCover = document.getElementById('modalCover');
    const hoverHint = document.getElementById('hoverHint');

    coverVideo.pause();
    coverVideo.removeAttribute('src');
    coverVideo.load();
    coverVideo.style.opacity = '0';
    coverVideo.muted = false; // SOUND ON

    if (cover && cover !== '') {
      coverImg.src = cover;
      coverImg.style.display = 'block';
      coverImg.style.opacity = '1';
      coverFallback.style.display = 'none';
      modalCover.style.background = '#111';
    } else {
      coverImg.style.display = 'none';
      coverFallback.style.display = 'flex';
      modalCover.style.background = 'linear-gradient(145deg, ' + color + 'cc, ' + color + ', ' + color + '88)';
    }

    if (video && video !== '') {
      coverVideo.src = video;
      coverVideo.muted = false;
      coverVideo.load();
      hoverHint.style.display = 'flex';
      hoverHint.style.opacity = '1';
      hoverHint.innerHTML = '<i class="fas fa-play"></i> Hover to play';

      modalCover.onmouseenter = () => {
        coverVideo.style.opacity = '1';
        if (coverImg) coverImg.style.opacity = '0';
        coverVideo.muted = false;
        const playPromise = coverVideo.play();
        if (playPromise !== undefined) {
          playPromise.catch(() => {
            // Autoplay with sound may be blocked — try muted then unmute on interaction
            coverVideo.muted = true;
            coverVideo.play().then(() => {
              coverVideo.muted = false;
            }).catch(() => {});
          });
        }
        hoverHint.style.opacity = '0';
      };
      modalCover.onmouseleave = () => {
        coverVideo.pause();
        coverVideo.currentTime = 0;
        coverVideo.style.opacity = '0';
        if (coverImg && coverImg.style.display !== 'none') coverImg.style.opacity = '1';
        hoverHint.style.opacity = '1';
      };
    } else {
      hoverHint.style.display = 'none';
      modalCover.onmouseenter = null;
      modalCover.onmouseleave = null;
    }

    document.getElementById('modalTags').innerHTML =
      '<span class="tag">' + genre + '</span><span class="tag">BOOK</span>';

    const readBtn = document.getElementById('readBtn');
    if (pdf && pdf !== '') {
      readBtn.href = pdf;
      readBtn.onclick = null;
    } else {
      readBtn.href = '#';
      readBtn.onclick = (e) => {
        e.preventDefault();
        showToast('No PDF available for this book.');
      };
    }

    const shelveBtn = document.getElementById('shelveBtn');
    if (shelveBtn) {
      const already = (window.WISHLIST_IDS || []).includes(parseInt(id));
      if (already) {
        shelveBtn.innerHTML = '<i class="fas fa-check"></i> SHELVED';
        shelveBtn.disabled = true;
      } else {
        shelveBtn.innerHTML = '<i class="fas fa-bookmark"></i> SHELVE IT';
        shelveBtn.disabled = false;
        shelveBtn.onclick = () => shelveBook(id, shelveBtn);
      }
    }

    const modalEl = document.getElementById('bookModal');
    modalEl.addEventListener('hidden.bs.modal', () => {
      coverVideo.pause();
      coverVideo.currentTime = 0;
      coverVideo.muted = false;
    }, { once: true });

    new bootstrap.Modal(modalEl).show();
  }

  function shelveBook(id, btn) {
    fetch('save_wishlist.php?id=' + id)
      .then(r => r.text())
      .then(() => {
        btn.innerHTML = '<i class="fas fa-check"></i> SHELVED';
        btn.disabled = true;
        if (!window.WISHLIST_IDS.includes(parseInt(id))) {
          window.WISHLIST_IDS.push(parseInt(id));
        }
        const rc = document.getElementById('read-count');
        if (rc) rc.textContent = window.WISHLIST_IDS.length;
        showToast('Added to your wishlist!');
      })
      .catch(() => showToast('Could not save. Try again.'));
  }

  const surpriseBtn = document.getElementById('surpriseBtn');
  if (surpriseBtn) {
    surpriseBtn.addEventListener('click', () => {
      const visible = allBooks.filter(b => !b.classList.contains('hidden-book') && b.style.display !== 'none');
      if (visible.length === 0) return;
      let spins = 0;
      const interval = setInterval(() => {
        visible.forEach(b => b.style.transform = '');
        const r = visible[Math.floor(Math.random() * visible.length)];
        if (r) r.style.transform = 'translateY(-20px) scale(1.08)';
        spins++;
        if (spins >= 8) {
          clearInterval(interval);
          visible.forEach(b => b.style.transform = '');
          openBook(visible[Math.floor(Math.random() * visible.length)]);
        }
      }, 110);
    });
  }

  function showToast(msg) {
    const el = document.getElementById('toast');
    document.getElementById('toastMsg').textContent = msg;
    new bootstrap.Toast(el, { delay: 2500 }).show();
  }
});
