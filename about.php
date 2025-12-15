<?php
session_start(); 
require_once 'config.php';
require 'vendor/autoload.php';
include 'includes/session_checker.php';
?>


<!DOCTYPE HTML>
<html lang="en">

<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'meta.php';?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
    <link rel="stylesheet" href="assets/css/index.css">
     <link rel="stylesheet" href="assets/css/dropdownmenu.css">
    <link href="other/tailwind/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    

</head>
<body class="hh-body">
<?php include 'includes/disclaimer-pictures.php' ?>

    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

   <?php include 'includes/landingpage-navbar.php' ?>


</head>
<body>
    <!-- 🐝 Floating Button -->
<a href="https://homehiveph.site/behind-homehive.php" 
   style="
     position: fixed;
     bottom: 25px;
     right: 25px;
     background: linear-gradient(135deg, #FB8C00, #F57C00);
     color: white;
     text-decoration: none;
     padding: 14px 20px;
     border-radius: 50px;
     box-shadow: 0 4px 10px rgba(0,0,0,0.2);
     font-family: 'Poppins', sans-serif;
     transition: all 0.3s ease;
     display: flex;
     align-items: center;
     gap: 12px;
     z-index: 9999;
   "
   onmouseover="this.style.transform='scale(1.08)'; this.style.boxShadow='0 6px 15px rgba(0,0,0,0.25)';"
   onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.2)';">

   <!-- Icon (Font Awesome Users) -->
   <i class="fas fa-users" style="font-size:20px;"></i>

   <!-- Texts -->
   <div style="display:flex; flex-direction:column; line-height:1.2;">
     <span style="font-size:15px; font-weight:600;">The HomeHive Team</span>
<span style="font-size:12px; opacity:0.9;">Meet the people behind it</span>

   </div>
</a>


  <div class="container">
    <section class="about-section">
      <div class="content-area">
      
        <div class="content-display" id="contentDisplay">
        <div class="loading">🐝 Pick an article and discover what’s buzzing in HomeHive</div>

        </div>
      </div>



 <div class="articles-area">
  <!-- Articles group -->
  <div class="article-group">
    <h3>Articles</h3>
    <div class="article-list">
      <div class="article-item" data-file="about-us/about-homehive.txt">
        <h4>About HomeHive</h4>
        <p>Learn more about our platform and services.</p>
      </div>
      <div class="article-item" data-file="about-us/community-guidelines.txt">
        <h4>Community Guidelines</h4>
        <p>Understand the do’s and don’ts of using HomeHive.</p>
      </div>
      <div class="article-item" data-file="article3.txt">
        <h4>Contact Information</h4>
        <p>Get in touch with the HomeHive team.</p>
      </div>
    </div>
  </div>

  <!-- FAQ group -->
  <div class="article-group">
    <h3>FAQ</h3>
    <div class="article-list">
      <div class="article-item" data-file="about-us/forgot-password.txt">
        <h4>Forgot Password</h4>
        <p>Steps to recover your account credentials.</p>
        <!-- Hidden button by default -->
        <a href="https://homehiveph.site/resetpassword/forgot_password" 
           class="get-started-btn"
           style="display: none; margin-top: 10px; padding: 8px 8px; background-color: #FB8C00; color: #fff; border-radius: 5px; text-decoration: none;">
           Get Started
        </a>
      </div>

      <div class="article-item" data-file="faq2.txt">
        <h4>Property Rejected</h4>
        <p>Why your property listing may be declined.</p>
      </div>
    </div>
  </div>

  <!-- Tutorials group (NEW) -->
<div class="article-group">
  <h3>Tutorials</h3>
  <div class="article-list">
    <!-- Tutorial 1 -->
    <div class="article-item" data-file="tutorials/getting-started.txt">
      <h4>Getting Started</h4>
      <p>Learn how to begin using HomeHive step by step.</p>
    </div>

    <!-- Tutorial 2 -->
    <div class="article-item" data-file="tutorials/creating-account.txt">
      <h4>Creating an Account</h4>
      <p>Understand how to sign up and set up your HomeHive account.</p>
    </div>

    <!-- Tutorial 3 -->
    <div class="article-item" data-file="tutorials/apply-lessor.txt">
      <h4>Apply as Lessor / Property Owner</h4>
      <p>Guide on how to list your properties and manage your account.</p>
    </div>

    <!-- Tutorial 4 -->
    <div class="article-item" data-file="tutorials/explore-map.txt">
      <h4>Explore via Map</h4>
      <p>Discover properties easily using our interactive map feature.</p>
    </div>

    
    
  </div>
</div>


    </section>
  </div>
</body>
<?php include 'footer.php';?>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
<script src="assets/js/login.js"></script>
<script src="other/tailwind/js/tailwind.min.js"></script>
<script src="assets/js/isotope.min.js"></script>
<script src="assets/js/owl-carousel.js"></script>
<script src="assets/js/counter.js"></script>
<script src="assets/js/custom.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/intro.js/minified/intro.min.js"></script>


<script>
    
document.addEventListener("DOMContentLoaded", function () {
  if (!localStorage.getItem("articles_tour_done")) {
    const tour = introJs();

    const checkboxHTML = `
      <br><br>
      <label style="font-size:14px;">
        <input type="checkbox" id="dont-show-articles-tour"> Don’t show this tour again
      </label>
    `;

    tour.setOptions({
      steps: [
        {
          intro: `
            <div style="font-size:15px; line-height:1.6; text-align:center; max-width:380px; margin:0 auto;">
              <strong>Welcome to the HomeHive Help Center</strong><br><br>
              Need some guidance? You’re in the right place.<br>
              Let’s take a quick tour so you can easily find the help you need.
              ${checkboxHTML}
            </div>
          `,
          position: "center"
        },
        {
          element: document.querySelector('.article-group:nth-of-type(1) h3'),
          intro: `
            <div style="font-size:15px; line-height:1.6;">
              Select <strong>Articles</strong> to read helpful guides and updates about how HomeHive works.
            </div>
          `,
          position: "right"
        },
        {
          element: document.querySelector('.article-group:nth-of-type(2) h3'),
          intro: `
            <div style="font-size:15px; line-height:1.6;">
              Visit the <strong>FAQ</strong> section to find answers to common questions from our users.
            </div>
          `,
          position: "right"
        },
        {
          element: document.querySelector('.article-group:nth-of-type(3) h3'),
          intro: `
            <div style="font-size:15px; line-height:1.6;">
              Open <strong>Tutorials</strong> to learn step-by-step how to use HomeHive more effectively.
            </div>
          `,
          position: "right"
        },
        {
          intro: `
            <div style="font-size:15px; line-height:1.6; text-align:center; max-width:380px; margin:0 auto;">
              That’s the overview!<br>
              You can now click any section title to start exploring.<br><br>
              <label style="display:flex; justify-content:center; align-items:center; gap:6px;">
                <input type="checkbox" id="dont-show-articles-tour">
                Don’t show this tour again
              </label>
            </div>
          `,
          position: "center"
        }
      ],
      showProgress: true,
      showBullets: false,
      nextLabel: "Next",
      prevLabel: "Back",
      doneLabel: "Finish",
      exitOnOverlayClick: false
    });

    function handleEnd() {
      const checkbox = document.getElementById("dont-show-articles-tour");
      if (checkbox && checkbox.checked) {
        localStorage.setItem("articles_tour_done", "true");
      }
    }

    // ✅ Early exit if checked on the first step
    tour.onbeforechange(function () {
      const step = tour._currentStep;
      const checkbox = document.getElementById("dont-show-articles-tour");

      if (step === 0 && checkbox && checkbox.checked) {
        localStorage.setItem("articles_tour_done", "true");
        tour.exit();
        return false;
      }
    });

    tour.oncomplete(handleEnd);
    tour.onexit(handleEnd);

    tour.start();
  }
});

document.addEventListener('DOMContentLoaded', function () {
  // --- COLLAPSE / EXPAND (Accordion + Toggle) ---
  // Start with all lists hidden
  document.querySelectorAll('.article-group .article-list').forEach(list => {
    list.classList.add('hidden');
  });

  document.querySelectorAll('.article-group h3').forEach(h3 => {
    h3.style.cursor = 'pointer';
    if (!h3.querySelector('.arrow')) {
      h3.insertAdjacentHTML('beforeend', ' <span class="arrow">▼</span>');
    }

    h3.addEventListener('click', function () {
      const list = this.nextElementSibling;
      const isOpen = !list.classList.contains('hidden'); // check if already open

      // close all groups first
      document.querySelectorAll('.article-group .article-list').forEach(l => l.classList.add('hidden'));
      document.querySelectorAll('.article-group h3').forEach(head => head.classList.remove('open'));

      if (!isOpen) {
        // open the clicked one only if it was closed
        list.classList.remove('hidden');
        this.classList.add('open');
      }
      // if it was already open, leave everything closed
    });
  });

  // --- SHOW "GET STARTED" BUTTON ON CLICK ---
  const items = document.querySelectorAll('.article-item');
  items.forEach(item => {
    item.addEventListener('click', () => {
      document.querySelectorAll('.get-started-btn').forEach(btn => {
        btn.style.display = 'none';
      });
      const btn = item.querySelector('.get-started-btn');
      if (btn) btn.style.display = 'inline-block';
    });
  });

  // --- LOAD ARTICLE CONTENT ---
  const contentDisplay = document.getElementById('contentDisplay');
  const articleItems = document.querySelectorAll('.article-item');

  function highlightHomeHive(text) {
    return text.replace(/HomeHive/gi, '<span class="highlight-homehive">HomeHive</span>');
  }

  function displayContent(fileName, title) {
    contentDisplay.innerHTML = `<div class="loading">Loading...</div>`;
    fetch(fileName)
      .then(res => {
        if (!res.ok) throw new Error("File not found");
        return res.text();
      })
.then(data => {
  const highlighted = highlightHomeHive(data);
  contentDisplay.innerHTML = `
    <h2>${title}</h2>
    ${highlighted}
  `;
})

      .catch(() => {
        contentDisplay.innerHTML = `<div class="loading">Content not available</div>`;
      });
  }

  function setActiveAndDisplay(fileName) {
    articleItems.forEach(i => {
      if (i.getAttribute('data-file') === fileName) i.classList.add('active');
      else i.classList.remove('active');
    });
    const activeItem = document.querySelector(`.article-item[data-file="${fileName}"]`);
    const title = activeItem ? activeItem.querySelector('h4').textContent : 'Article';
    displayContent(fileName, title);
  }

  articleItems.forEach(item => {
    item.addEventListener('click', function () {
      const fileName = this.getAttribute('data-file');
      setActiveAndDisplay(fileName);
      history.replaceState(null, '', '?article=' + fileName.replace('.txt', ''));
    });
  });
});

function openLogoutModal(event) {
  event.preventDefault(); // Prevent default link/button behavior

  Swal.fire({
    title: 'Are you sure?',
    text: 'You will be logged out from your session.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, logout',
    cancelButtonText: 'Cancel',
    reverseButtons: true,
    confirmButtonColor: '#f39c12' // Orange color
  }).then((result) => {
    if (result.isConfirmed) {
      // Redirect to logout handler
      window.location.href = '../logout.php';
    }
  });
}
</script>

<style>
/* 🌟 Theme Colors */
:root {
  --hh-sunset-amber: #FB8C00;
  --hh-rich-gold: #F57C00;
  --hh-bee-brown: #5D4037;
  --hh-cream-white: #FFF8E1;
  --hh-golden-shadow: #FFCC80;
  --hh-soft-honey: #FFE0B2;

  --s: 44px; 
  --c1: #e6973d;
  --c2: #272c20;
  --c:#0000,var(--c1) .5deg 119.5deg,#0000 120deg;
  --g1:conic-gradient(from  60deg at 56.25% calc(425%/6),var(--c));
  --g2:conic-gradient(from 180deg at 43.75% calc(425%/6),var(--c));
  --g3:conic-gradient(from -60deg at 50%   calc(175%/12),var(--c));
}

/* 🐝 Background Pattern */
body {
  background:
    var(--g1),var(--g1) var(--s) calc(1.73*var(--s)),
    var(--g2),var(--g2) var(--s) calc(1.73*var(--s)),
    var(--g3) var(--s) 0,var(--g3) 0 calc(1.73*var(--s)) 
    var(--c2);
  background-size: calc(2*var(--s)) calc(3.46*var(--s));
}

h1 {
  text-align: center;
  margin-bottom: 10px;
  color: #2c3e50;
}

.subtitle {
  text-align: center;
  margin-bottom: 40px;
  color: #7f8c8d;
  font-size: 1.1rem;
}

/* 🐝 About Section Layout */
.about-section {
  display: flex;
  flex-wrap: wrap;
  gap: 30px;
  background: var(--hh-cream-white);
  border-radius: 10px;
  overflow: hidden;
  margin: 50px auto;
  max-width: 1000px;
  width: 100%;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.content-area {
  flex: 2;
  min-width: 400px;
  padding: 20px;
  border-right: 1px solid #eee;
}
.articles-area {
  flex: 1;
  min-width: 350px;
  padding: 20px;
  background-color: var(--hh-cream-white);
}

/* Responsive */
@media (max-width: 768px) {
  .about-section {
    flex-direction: column;
    margin: 20px auto;
    padding: 10px;
  }
  .content-area {
    border-right: none;
    border-bottom: 1px solid #eee;
  }
}

/* 🐝 Scrollbar styling */
.content-display::-webkit-scrollbar {
  width: 10px;
}
.content-display::-webkit-scrollbar-track {
  background: var(--hh-cream-white);
  border-radius: 8px;
}
.content-display::-webkit-scrollbar-thumb {
  background-color: var(--hh-sunset-amber);
  border-radius: 8px;
  border: 2px solid var(--hh-cream-white);
}
.content-display::-webkit-scrollbar-thumb:hover {
  background-color: var(--hh-rich-gold);
}

/* 🐝 Content Display Box */
.content-display {
  min-height: 800px;
  padding: 30px;
  background-color: var(--hh-cream-white);
  border-radius: 8px;
  margin-bottom: 20px;
  overflow-y: auto;
  max-height: 500px;
}
.content-display h2 {
  color: #2c3e50;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 2px solid var(--hh-sunset-amber);
}
.content-display p {
  margin-bottom: 15px;
}

/* 🐝 Hide lists by default */
.article-list.hidden {
  display: none;
}
.article-list {
  display: flex;
  flex-direction: column;
  gap: 15px;

  /* ✅ Add these for scroll */
  max-height: 300px; /* adjust height to what you like */
  overflow-y: auto;
  padding-right: 5px; /* small gap for scrollbar */
}

/* ✅ Optional: custom scrollbar style for the list */
.article-list::-webkit-scrollbar {
  width: 8px;
}
.article-list::-webkit-scrollbar-track {
  background: var(--hh-cream-white);
  border-radius: 4px;
}
.article-list::-webkit-scrollbar-thumb {
  background-color: var(--hh-sunset-amber);
  border-radius: 4px;
}
.article-list::-webkit-scrollbar-thumb:hover {
  background-color: var(--hh-rich-gold);
}

/* 🐝 Ribbon Header */
.article-group {
  margin-bottom: 30px;
}
.article-group h3 {
  position: relative;
  display: block;
  background: linear-gradient(135deg, var(--hh-sunset-amber) 0%, var(--hh-rich-gold) 100%);
  color: #fff;
  padding: 14px 20px;
  margin: 20px 0 0;
  font-size: 17px;
  font-weight: 600;
  border-radius: 6px 6px 0 0;
  cursor: pointer;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
  transition: background 0.3s ease, transform 0.2s ease;
}
.article-group h3:hover {
  background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
  transform: translateY(-1px);
}
/* Ribbon triangles */
.article-group h3::before,
.article-group h3::after {
  content: "";
  position: absolute;
  top: 100%;
  border-style: solid;
  border-color: transparent;
}
.article-group h3::before {
  left: 0;
  border-width: 8px 8px 0 0;
  border-right-color: #E65100;
}
.article-group h3::after {
  right: 0;
  border-width: 8px 0 0 8px;
  border-left-color: #E65100;
}
/* Arrow style */
.article-group h3 .arrow {
  float: right;
  transition: transform 0.3s ease;
  font-size: 14px;
}
.article-group h3.open .arrow {
  transform: rotate(-180deg);
}

/* 🐝 Article Items */
.article-list {
  display: flex;
  flex-direction: column;
  gap: 15px;
}
.article-item {
  padding: 12px 15px;
  background: #fafafa;
  border-bottom: 1px solid #eee;
  cursor: pointer;
  transition: all 0.25s ease;
  border-left: 4px solid transparent;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}
.article-item:hover {
  transform: translateX(5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  border-left: 4px solid var(--hh-sunset-amber);
}
.article-item.active {
  background-color: var(--hh-soft-honey);
  border-left: 4px solid var(--hh-sunset-amber);
}
.article-item h4 {
  color: #2c3e50;
  margin-bottom: 5px;
  font-size: 1rem;
}
.article-item p {
  color: #7f8c8d;
  font-size: 0.85rem;
}

.loading {
  text-align: center;
  padding: 20px;
  color: #7f8c8d;
}

/* 🐝 Highlighted text */
.highlight-homehive {
  background-color: #FFF3E0;
  color: #E65100;
  padding: 0 3px;
  border-radius: 3px;
  font-weight: bold;
}
</style>

</html>
