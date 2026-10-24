
var menuBtn = document.getElementById("menuBtn");
var navLinks = document.getElementById("navLinks");

if (menuBtn) {
  menuBtn.addEventListener("click", function () {
    navLinks.classList.toggle("active");
  });
}

var bookCards = document.getElementsByClassName("book-card");

for (var i = 0; i < bookCards.length; i++) {
  (function (card) {
    var video = card.querySelector(".hover-video");

    if (!video) {
      return; 
    }

   
    video.muted = true;

    card.addEventListener("mouseenter", function () {
      video.currentTime = 0;
      var playPromise = video.play();

      
      if (playPromise !== undefined) {
        playPromise.catch(function () {});
      }
    });

    card.addEventListener("mouseleave", function () {
      video.pause();

      if (video.readyState > 0) {
        video.currentTime = 0;
      }
    });
  })(bookCards[i]);
}


function isValidEmail(email) {
  var pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return pattern.test(email);
}

var registerForm = document.getElementById("registerForm");

if (registerForm) {
  registerForm.addEventListener("submit", function (e) {
    var valid = true;

    var name = document.getElementById("regName");
    var email = document.getElementById("regEmail");
    var password = document.getElementById("regPassword");
    var confirmPassword = document.getElementById("regConfirmPassword");

    if (name.value.trim() === "") {
      document.getElementById("regNameError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("regNameError").style.display = "none";
    }

    if (!isValidEmail(email.value.trim())) {
      document.getElementById("regEmailError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("regEmailError").style.display = "none";
    }

    if (password.value.trim().length < 6) {
      document.getElementById("regPasswordError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("regPasswordError").style.display = "none";
    }

    if (confirmPassword.value !== password.value) {
      document.getElementById("regConfirmError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("regConfirmError").style.display = "none";
    }

    if (!valid) {
      e.preventDefault();
    }
  });
}

var loginForm = document.getElementById("loginForm");

if (loginForm) {
  loginForm.addEventListener("submit", function (e) {
    var valid = true;

    var email = document.getElementById("loginEmail");
    var password = document.getElementById("loginPassword");

    if (!isValidEmail(email.value.trim())) {
      document.getElementById("loginEmailError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("loginEmailError").style.display = "none";
    }

    if (password.value.trim() === "") {
      document.getElementById("loginPasswordError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("loginPasswordError").style.display = "none";
    }

    if (!valid) {
      e.preventDefault();
    }
  });
}

var uploadForm = document.getElementById("uploadForm");

if (uploadForm) {
  uploadForm.addEventListener("submit", function (e) {
    var valid = true;

    var title = document.getElementById("bookTitle");
    var author = document.getElementById("bookAuthor");
    var genre = document.getElementById("bookGenre");
    var cover = document.getElementById("bookCover");
    var video = document.getElementById("bookVideo");

    if (title.value.trim() === "") {
      document.getElementById("titleError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("titleError").style.display = "none";
    }

    if (author.value.trim() === "") {
      document.getElementById("authorError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("authorError").style.display = "none";
    }

    if (genre.value === "") {
      document.getElementById("genreError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("genreError").style.display = "none";
    }

    if (cover.value === "") {
      document.getElementById("coverError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("coverError").style.display = "none";
    }

    if (video.value === "") {
      document.getElementById("videoError").style.display = "block";
      valid = false;
    } else {
      document.getElementById("videoError").style.display = "none";
    }

    if (!valid) {
      e.preventDefault();
    }
  });
}