<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#FB8C00">
<meta name="msapplication-navbutton-color" content="#FB8C00">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"> 
<meta name="apple-mobile-web-app-capable" content="yes">
<link rel="apple-touch-icon" sizes="180x180" href="https://i.imgur.com/dGRwk3F.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://i.imgur.com/dGRwk3F.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://i.imgur.com/dGRwk3F.png">

  <title>HomeHive | Survey Form</title>
  <meta name="description" content="HomeHive Survey Form" />

  <!-- SweetAlert2 CSS + JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root {
      --hh-sunset-amber: #FB8C00;
      --hh-rich-gold: #F57C00;
      --hh-bee-brown: #5D4037;
      --hh-cream-white: #FFF8E1;
      --hh-golden-shadow: #FFCC80;
      --hh-soft-honey: #FFE0B2;

      --s: 44px; /* hexagon size */
      --c: #0000,var(--hh-sunset-amber) .5deg 119.5deg,#0000 120deg;
      --g1: conic-gradient(from  60deg at 56.25% calc(425%/6),var(--c));
      --g2: conic-gradient(from 180deg at 43.75% calc(425%/6),var(--c));
      --g3: conic-gradient(from -60deg at 50%   calc(175%/12),var(--c));
    }

    * {
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
      margin: 0;
      padding: 0;
    }

    body {
      background:
        var(--g1),var(--g1) var(--s) calc(1.73*var(--s)),
        var(--g2),var(--g2) var(--s) calc(1.73*var(--s)),
        var(--g3) var(--s) 0,var(--g3) 0 calc(1.73*var(--s)) var(--hh-cream-white);
      background-size: calc(2*var(--s)) calc(3.46*var(--s));
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 20px;
      text-align: center;
      line-height: 1.6;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
    }

    /* Scrollbar Styling */
    ::-webkit-scrollbar {
      width: 10px;
      height: 10px;
    }
    ::-webkit-scrollbar-track {
      background: var(--hh-cream-white);
    }
    ::-webkit-scrollbar-thumb {
      background: var(--hh-sunset-amber);
      border-radius: 5px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: var(--hh-rich-gold);
    }

    /* Container */
    .container {
      max-width: 800px;
      width: 100%;
      min-height: 100vh; /* full screen but flexible */
      margin: 0 auto;
      background: var(--hh-cream-white);
      border: 3px solid var(--hh-rich-gold);
      border-radius: 8px;
      overflow: hidden;
      position: relative;
      display: flex;
      flex-direction: column;
    }

    .container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 5px;
      background: linear-gradient(90deg, var(--hh-rich-gold), var(--hh-sunset-amber), var(--hh-rich-gold));
      z-index: 2;
    }

    .content {
      padding: 30px 20px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .form-container {
      position: relative;
      overflow: hidden;
      margin-top: 10px;
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    iframe {
      width: 100%;
      height: 100%;
      border: none;
      display: block;
      flex: 1;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      body {
        padding: 0;
        background: var(--hh-cream-white); /* plain background on mobile */
      }
      
      .container {
        width: 100%;
        max-width: 100%;
        height: 100vh;
        margin: 0;
        border-width: 2px;
        border-radius: 0;
        box-shadow: none;
      }
      
      .content {
        padding: 15px 10px;
      }
    }

    @media (max-width: 576px) {
      .content {
        padding: 10px 5px;
      } 
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Survey Content -->
    <div class="content">
      <div class="form-container">
        <iframe src="https://docs.google.com/forms/d/e/1FAIpQLSdwKS5IeZsehSi-exl3VDP90ZRxWh6L0K8gA_s9r1JEkvL0DA/viewform?embedded=true" 
                title="Google Form"></iframe>
      </div>
    </div>
  </div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
      title: 'HomeHive Survey',
      html: `
        <div style="text-align:left; font-size:15px; line-height:1.6;">
          <p><strong>Welcome!</strong></p>
          <p>
            We are inviting you to take part in the HomeHive Survey.
            This short form will help us understand how people use HomeHive 
            and what we can improve.
          </p>
          <p>
            <strong>For academic purposes only:</strong><br>
            This survey is part of an academic project. It is not a real rental
            service and no transactions are taking place.
          </p>
   <p>
  <strong>Your Privacy:</strong><br>
  We collect your email address but we do not ask for sensitive details like bank accounts.  
  Your answers and email are only used for research and to improve our services.
</p>

        </div>
      `,
      footer: '<small style="color:#666;">This survey uses Google Forms and is <strong>not endorsed by Google</strong>.</small>',
      confirmButtonText: 'Start Survey',
      confirmButtonColor: 'var(--hh-sunset-amber)'
    });
  });
</script>


</body>
</html>
