<?php
session_start();

if (!isset($_SESSION['registration_success']) || $_SESSION['registration_success'] !== true) {
    header('Location: loginuser.php'); // or login page, wherever you want
    exit();
}

// Optionally clear the flag so refreshing welcome.php won't let users revisit it directly
unset($_SESSION['registration_success']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Welcome to HomeHive</title>
  <style>
    :root {
      --hh-sunset-amber: #FB8C00;
      --hh-rich-gold: #F57C00;
      --hh-bee-brown: #5D4037;
      --hh-cream-white: #FFF8E1;
      --hh-golden-shadow: #FFCC80;
      --hh-soft-honey: #FFE0B2;

      --primary-color: var(--hh-sunset-amber);
      --secondary-color: var(--hh-rich-gold);
      --accent-color: var(--hh-golden-shadow);
      --light-color: var(--hh-cream-white);
      --dark-color: var(--hh-bee-brown);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: var(--light-color);
      color: var(--dark-color);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 2rem;
      background: linear-gradient(135deg, #fff7db 0%, #ffefb8 100%);
      animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .welcome-container {
      background-color: white;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(93, 64, 55, 0.15);
      padding: 3rem;
      max-width: 600px;
      width: 100%;
      text-align: center;
      position: relative;
      overflow: hidden;
      transform: translateY(0);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .welcome-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(93, 64, 55, 0.2);
    }

    .welcome-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
      background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
      animation: borderGrow 0.5s ease-out;
    }

    @keyframes borderGrow {
      from { width: 0; }
      to { width: 100%; }
    }

    h1 {
      color: var(--primary-color);
      margin-bottom: 1.5rem;
      font-weight: 700;
      font-size: 2.2rem;
      animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
      from { transform: translateY(20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    p {
      margin-bottom: 2rem;
      line-height: 1.6;
      color: var(--hh-bee-brown);
      animation: fadeIn 0.8s ease-out;
    }

    .icon-container {
      margin: 0 auto 1.5rem;
      width: 100px;
      height: 100px;
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: var(--hh-soft-honey);
      border-radius: 50%;
      padding: 1rem;
      box-shadow: 0 4px 12px rgba(251, 140, 0, 0.2);
      animation: bounceIn 0.8s ease-out;
    }

    @keyframes bounceIn {
      0% { transform: scale(0.8); opacity: 0; }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); opacity: 1; }
    }

    .icon {
      width: 64px;
      height: 64px;
      object-fit: contain;
      filter: drop-shadow(0 2px 4px rgba(93, 64, 55, 0.2));
    }

    .features-container {
      margin: 2rem 0;
      text-align: left;
      padding: 0 1.5rem;
    }

    .feature-item {
      display: flex;
      align-items: center;
      margin-bottom: 1rem;
      opacity: 0;
      transform: translateX(-20px);
      animation: slideIn 0.5s ease-out forwards;
    }

    @keyframes slideIn {
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    .feature-icon {
      width: 24px;
      height: 24px;
      margin-right: 12px;
      color: var(--primary-color);
    }

    .feature-text {
      font-size: 1rem;
      color: var(--hh-bee-brown);
    }

    .redirect-message {
      margin-top: 2rem;
      font-size: 0.9rem;
      color: #7a5a3c;
      animation: fadeIn 1s ease-out;
    }

    .countdown {
      font-weight: 700;
      color: var(--primary-color);
      font-size: 1.1rem;
    }

    .progress-container {
      margin-top: 1.5rem;
      animation: fadeIn 1.2s ease-out;
    }

    .progress-bar {
      width: 100%;
      height: 6px;
      background-color: var(--hh-soft-honey);
      border-radius: 3px;
      overflow: hidden;
      position: relative;
    }

    .progress {
      height: 100%;
      width: 0;
      background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
      border-radius: 3px;
      animation: progress 5s linear forwards;
      position: relative;
    }

    .progress::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(
        to right,
        rgba(255,255,255,0.8) 0%,
        rgba(255,255,255,0) 100%
      );
      animation: shine 2s infinite;
    }

    @keyframes progress {
      from { width: 0; }
      to { width: 100%; }
    }

    @keyframes shine {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }

    .btn {
      display: inline-block;
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 0.8rem 2rem;
      border-radius: 50px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
      cursor: pointer;
      margin-top: 1.5rem;
      box-shadow: 0 4px 12px rgba(251, 140, 0, 0.4);
      position: relative;
      overflow: hidden;
      animation: fadeIn 1s ease-out;
    }

    .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(245, 124, 0, 0.5);
    }

    .btn:active {
      transform: translateY(0);
    }

    .btn::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(
        to right,
        rgba(255,255,255,0) 0%,
        rgba(255,255,255,0.3) 50%,
        rgba(255,255,255,0) 100%
      );
      transform: translateX(-100%);
    }

    .btn:hover::after {
      animation: shine 1.5s infinite;
    }

    @media (max-width: 768px) {
      .welcome-container {
        padding: 2.5rem 1.5rem;
      }
      
      h1 {
        font-size: 1.8rem;
      }
      
      .icon-container {
        width: 80px;
        height: 80px;
      }
      
      .icon {
        width: 50px;
        height: 50px;
      }
      
      .features-container {
        padding: 0;
      }
    }
  </style>
</head>
<body>
  <div class="welcome-container">
    <div class="icon-container">
      <img src="https://i.imgur.com/Q5BsPbV.png" alt="HomeHive Icon" class="icon" />
    </div>
    
    <h1>Welcome to HomeHive!</h1>
    
    <p>
      Your account has been successfully created. Get ready to explore these features:
    </p>
    
    <div class="features-container" id="featuresList">
    
    </div>
    
    <div class="redirect-message">
      <p>
        Redirecting in <span class="countdown" id="countdown">5</span> seconds…
      </p>
    </div>
    
    <div class="progress-container">
      <div class="progress-bar">
        <div class="progress"></div>
      </div>
    </div>
    
    <a href="loginuser.php" class="btn">Continue to Login</a>
  </div>

  <script>

const features = [
  { text: "Browse Available Properties", delay: 0.8 },
  { text: "Submit Rental Applications", delay: 1.0 },
  { text: "View Application Status", delay: 1.2 },
  { text: "Explore More Features!", delay: 1.4 }
];



    function loadFeatures() {
      const container = document.getElementById('featuresList');
      
      features.forEach((feature, index) => {
        setTimeout(() => {
          const featureItem = document.createElement('div');
          featureItem.className = 'feature-item';
          featureItem.style.animationDelay = `${feature.delay}s`;
          
          featureItem.innerHTML = `
            <svg class="feature-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <span class="feature-text">${feature.text}</span>
          `;
          
          container.appendChild(featureItem);
        }, index * 300); 
      });
    }

   
    let seconds = 5;
    const countdownElement = document.getElementById('countdown');
    

    window.addEventListener('DOMContentLoaded', () => {
      loadFeatures();
      
      const countdown = setInterval(() => {
        seconds--;
        countdownElement.textContent = seconds;
        
     
        countdownElement.style.transform = 'scale(1.2)';
        setTimeout(() => {
          countdownElement.style.transform = 'scale(1)';
        }, 200);
        
        if (seconds <= 0) {
          clearInterval(countdown);
          // Add fade out animation before redirect
          document.body.style.animation = 'fadeIn 0.5s ease-out reverse';
          setTimeout(() => {
            window.location.href = 'loginuser.php';
          }, 500);
        }
      }, 1000);
      
      // Backup redirect after 5.5 seconds (accounts for animation)
      setTimeout(() => {
        document.body.style.animation = 'fadeIn 0.5s ease-out reverse';
        setTimeout(() => {
          window.location.href = 'loginuser.php';
        }, 500);
      }, 5500);
    });
  </script>
</body>
</html>