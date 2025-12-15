<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeHive Thesis Presentation</title>
    <style>
        :root {
            --hh-sunset-amber: #FB8C00;
            --hh-rich-gold: #F57C00;
            --hh-bee-brown: #5D4037;
            --hh-cream-white: #FFF8E1;
            --hh-golden-shadow: #FFCC80;
            --hh-soft-honey: #FFE0B2;
            --hh-dark-brown: #3E2723;
            --hh-light-cream: #FFFDF5;
            --hh-border-light: rgba(93, 64, 55, 0.1);
            --hh-shadow: rgba(93, 64, 55, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            flex-direction: column;
            height: 100vh;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--hh-light-cream) 0%, var(--hh-cream-white) 100%);
            margin: 0;
            padding: 20px;
            color: var(--hh-dark-brown);
            overflow: hidden;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 800px;
            width: 100%;
            padding: 40px;
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px var(--hh-shadow);
            position: relative;
            z-index: 2;
            border: 1px solid var(--hh-border-light);
        }

        .logo-container {
            margin-bottom: 30px;
            text-align: center;
        }

        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }
     .logo img {
    height: 100px;
    max-width: 100%;
}


        .logo-text {
            font-size: 32px;
            font-weight: 700;
            color: var(--hh-dark-brown);
            letter-spacing: 1px;
        }

        .logo-subtitle {
            font-size: 16px;
            color: var(--hh-bee-brown);
            margin-top: 5px;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .content {
            text-align: center;
            margin-bottom: 40px;
            max-width: 600px;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 15px;
            color: var(--hh-dark-brown);
        }

        p {
            font-size: 18px;
            line-height: 1.6;
            color: var(--hh-bee-brown);
            margin-bottom: 20px;
        }

        .button {
            padding: 18px 40px;
            font-size: 20px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            background: linear-gradient(135deg, var(--hh-rich-gold) 0%, var(--hh-sunset-amber) 100%);
            color: white;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 15px var(--hh-shadow);
            position: relative;
            overflow: hidden;
            letter-spacing: 1px;
        }

        .button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(93, 64, 55, 0.15);
        }

        .button:active {
            transform: translateY(1px);
        }

        .button::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--hh-sunset-amber) 0%, var(--hh-rich-gold) 100%);
            opacity: 0;
            transition: opacity 0.3s;
            z-index: -1;
        }

        .button:hover::after {
            opacity: 1;
        }

        .HomeHive-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
            opacity: 0.1;
        }

        .HomeHive {
            position: absolute;
            width: 100px;
            height: 57.74px;
            background-color: var(--hh-golden-shadow);
            margin: 28.87px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.4;
        }

        .HomeHive:before,
        .HomeHive:after {
            content: "";
            position: absolute;
            width: 0;
            border-left: 50px solid transparent;
            border-right: 50px solid transparent;
        }

        .HomeHive:before {
            bottom: 100%;
            border-bottom: 28.87px solid var(--hh-golden-shadow);
        }

        .HomeHive:after {
            top: 100%;
            width: 0;
            border-top: 28.87px solid var(--hh-golden-shadow);
        }

        .HomeHive:nth-child(1) {
            top: 10%;
            left: 5%;
        }

        .HomeHive:nth-child(2) {
            top: 20%;
            right: 10%;
        }

        .HomeHive:nth-child(3) {
            bottom: 15%;
            left: 15%;
        }

        .HomeHive:nth-child(4) {
            bottom: 25%;
            right: 5%;
        }

        .HomeHive:nth-child(5) {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .bee {
            position: absolute;
            width: 40px;
            height: 40px;
            background: radial-gradient(circle at 30% 30%, var(--hh-rich-gold) 0%, var(--hh-bee-brown) 70%);
            border-radius: 50%;
            z-index: 1;
            animation: fly 15s infinite linear;
        }

        .bee::before {
            content: '';
            position: absolute;
            top: 10px;
            left: -5px;
            width: 15px;
            height: 10px;
            background: var(--hh-dark-brown);
            border-radius: 50%;
            transform: rotate(-30deg);
        }

        .bee::after {
            content: '';
            position: absolute;
            top: 5px;
            right: -5px;
            width: 20px;
            height: 15px;
            background: transparent;
            border: 2px solid var(--hh-dark-brown);
            border-radius: 50%;
        }

        .bee .wing {
            position: absolute;
            width: 20px;
            height: 15px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            top: -5px;
            left: 5px;
            animation: flap 0.2s infinite alternate;
        }

        .bee .wing:nth-child(2) {
            left: 15px;
            animation-delay: 0.1s;
        }

        @keyframes fly {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }
            25% {
                transform: translate(200px, 150px) rotate(90deg);
            }
            50% {
                transform: translate(400px, 0) rotate(180deg);
            }
            75% {
                transform: translate(200px, -150px) rotate(270deg);
            }
            100% {
                transform: translate(0, 0) rotate(360deg);
            }
        }

        @keyframes flap {
            0% {
                transform: scaleY(1);
            }
            100% {
                transform: scaleY(0.7);
            }
        }

        .footer {
            margin-top: 30px;
            font-size: 14px;
            color: var(--hh-bee-brown);
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="HomeHive-bg">
        <div class="HomeHive"></div>
        <div class="HomeHive"></div>
        <div class="HomeHive"></div>
        <div class="HomeHive"></div>
        <div class="HomeHive"></div>
        
        <div class="bee">
            <div class="wing"></div>
            <div class="wing"></div>
        </div>
    </div>
    
    <div class="container">
        <div class="logo-container">
            <div class="logo">
<img src="https://i.imgur.com/Q5BsPbV.png" alt="Logo">

              
            </div>
            <div class="logo-subtitle">Bee Smart. Bee Secure. Bee Home</div>
        </div>
        
  <div class="content">
    <h1>Thesis Presentation</h1>
    <p>Welcome! This is our research presentation where we share our findings and ideas.</p>
    <p>This presentation is for <strong>thesis team members only</strong>. Please do not share the link with others.</p>
</div>

        
        <button class="button" onclick="redirectToPresentation()">Open</button>
        
        <div class="footer">
            &copy; 2025 HomeHive Academic Research | All Rights Reserved
        </div>
    </div>

    <script>
        function redirectToPresentation() {
            window.location.href = "https://novalichessti-my.sharepoint.com/:p:/g/personal/flores_228238_novaliches_sti_edu_ph/ETJ_awPiOz1IofIpTExVh2IBtM1I4pHYgGYyZRniJcAnWg?e=xwL0ui";
        }
    </script>
</body>
</html>