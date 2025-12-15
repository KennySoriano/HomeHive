<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mobile Site Coming Soon | HomeHive</title>
  <style>
    :root {
      --hh-sunset-amber: #FB8C00;
      --hh-rich-gold: #F57C00;
      --hh-bee-brown: #5D4037;
      --hh-cream-white: #FFF8E1;
      --hh-golden-shadow: #FFCC80;
      --hh-soft-honey: #FFE0B2;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: linear-gradient(135deg, var(--hh-soft-honey), #ffffff);
      color: var(--hh-bee-brown);
      text-align: center;
      overflow: hidden;
    }

    .container {
      width: 80%;
      max-width: 450px;
      padding: 2rem;
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      position: relative;
      z-index: 1;
      backdrop-filter: blur(5px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      top: -30px;
    }

    h1 {
      color: var(--hh-rich-gold);
      margin-bottom: 1rem;
      font-size: 2rem;
      font-weight: 700;
    }

    p {
      font-size: 1.1rem;
      line-height: 1.6;
    }

    .logo {
      margin-bottom: 1.5rem;
      display: flex;
      justify-content: center;
    }

    .logo img {
      width: 100px;
      height: auto; /* Maintain aspect ratio */
      object-fit: contain;
    }

    .bg-shapes {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: 0;
    }

    .bg-shapes div {
      position: absolute;
      border-radius: 50%;
      background: linear-gradient(45deg, var(--hh-rich-gold), var(--hh-sunset-amber));
      opacity: 0.08;
    }

    .bg-shapes div:nth-child(1) {
      width: 300px;
      height: 300px;
      top: -100px;
      right: -100px;
    }

    .bg-shapes div:nth-child(2) {
      width: 200px;
      height: 200px;
      bottom: -50px;
      left: -50px;
    }

    @media (max-width: 480px) {
      .container {
        padding: 1.5rem;
      }

      h1 {
        font-size: 1.5rem;
      }

      p {
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <div class="bg-shapes">
    <div></div>
    <div></div>
  </div>

  <div class="container" role="main">
    <div class="logo" aria-label="HomeHive Logo">
      <img src="assets/images/HomeHiveLogo-White-removebg.png" alt="HomeHive Logo" />
    </div>

 <h1 style="font-size: 2rem; color: #FB8C00;">Mobile Version Coming Soon</h1>
  <p style="font-size: 1rem; max-width: 500px; margin: 1rem auto;">
    We're working hard to bring you the best experience on mobile devices.  
    <br><br>
     In the meantime, please visit us on a desktop browser.
  </p>

  <hr style="margin: 2rem auto; width: 50%; border: 1px solid #FFCC80;">

  <p style="font-size: 1rem;">
     Questions or need help? Contact us at <strong>homehiveofficial2025@gmail.com</strong>
  </p>
  </div>
</body>
<script>
  (function redirectOnMobile() {
    const isMobile = () => window.innerWidth <= 768;

    // Prevent redirect loop
    const isComingSoon = window.location.pathname.includes("comingsoon.php");

    if (isMobile() && !isComingSoon) {
        window.location.href = "comingsoon.php";
    }

    // Optional: prevent desktop users from staying on comingsoon.php
    if (!isMobile() && isComingSoon) {
        window.location.href = "index.php"; // or your main page
    }

    // Watch for window resizing
    window.addEventListener("resize", () => {
        const nowMobile = isMobile();
        const nowComingSoon = window.location.pathname.includes("comingsoon.php");

        if (nowMobile && !nowComingSoon) {
            window.location.href = "comingsoon.php";
        } else if (!nowMobile && nowComingSoon) {
            window.location.href = "index.php";
        }
    });
})();
</script>
</html>
