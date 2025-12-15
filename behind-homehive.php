<?php 

include 'includes/session_checker.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php include 'meta.php';?>
    <title>Behind HomeHive</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
             cursor: default;
        }
        /* WebKit browsers (Chrome, Edge, Safari) */
::-webkit-scrollbar {
    width: 12px; /* scrollbar width */
}

::-webkit-scrollbar-track {
    background: #FFF8E1; /* track background */
    border-radius: 8px;
}

::-webkit-scrollbar-thumb {
    background-color: #FB8C00; /* orange thumb */
    border-radius: 8px;
    border: 3px solid #FFF8E1; /* optional: space around thumb */
}

::-webkit-scrollbar-thumb:hover {
    background-color: #F57C00; /* darker orange on hover */
}

/* Firefox */
* {
    scrollbar-width: thin;
    scrollbar-color: #FB8C00 #FFF8E1; /* thumb track */
}

        
        body {
            background:
                var(--g1),var(--g1) var(--s) calc(1.73*var(--s)),
                var(--g2),var(--g2) var(--s) calc(1.73*var(--s)),
                var(--g3) var(--s) 0,var(--g3) 0 calc(1.73*var(--s)) 
                var(--c2);
            background-size: calc(2*var(--s)) calc(3.46*var(--s));
            color: #fff;
            min-height: 100vh;
            padding: 40px 20px;
        }
        /* Default cursor globally */
body, div, section, article, span, p {
    cursor: default;
}

/* Keep pointer for links and buttons */
a, button {
    cursor: pointer;
}

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Top Bar */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 15px 40px;
            background-color: #6b4f3b;
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 10;
            border-radius: 10px;
            margin-bottom: 40px;
        }

        /* Back Button */
        .back-btn {
            background: #FB8C00;
            border: none;
            color: #fff;
            font-size: 1rem;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .back-btn:hover {
            background: #a56f47;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Logo */
        .nav-logo {
            width: 120px;
            height: auto;
            transition: transform 0.3s ease;
        }

        .nav-logo:hover {
            transform: scale(1.05);
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 60px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #FB8C00, #F57C00, #5D4037);
        }
        
        .page-header h1 {
            font-size: 3rem;
            margin-bottom: 15px;
            text-shadow: #FAFAFA;
            background: white;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .page-header p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
       .team-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px; /* adjust gap as needed */
    margin-bottom: 80px;
}

/* Make 5 cards fit in one row */
.profile-card {
    flex: 1 1 calc((100% - 4 * 20px) / 5); /* 5 cards, subtract total gaps */
    max-width: 300px; /* optional: prevent it from being too wide on large screens */
    height: 400px;
    perspective: 1000px;
    cursor: pointer;
}

/* Optional: make responsive on smaller screens */
@media (max-width: 1200px) {
    .profile-card {
        flex: 1 1 calc((100% - 3 * 20px) / 4); /* 4 cards per row */
    }
}

@media (max-width: 992px) {
    .profile-card {
        flex: 1 1 calc((100% - 2 * 20px) / 3); /* 3 cards per row */
    }
}

@media (max-width: 768px) {
    .profile-card {
        flex: 1 1 calc((100% - 20px) / 2); /* 2 cards per row */
    }
}

@media (max-width: 480px) {
    .profile-card {
        flex: 1 1 100%; /* 1 card per row */
    }
}

        
        .card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            transition: transform 0.8s;
            transform-style: preserve-3d;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border-radius: 15px;
        }
        
        .profile-card:hover .card-inner {
            transform: rotateY(180deg);
        }
        
        .card-front, .card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            border-radius: 15px;
            overflow: hidden;
        }
        
        .card-front {
            background: linear-gradient(45deg, #FB8C00, #5D4037);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .card-back {
            background: #5D4037;
            transform: rotateY(180deg);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }
        
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .profile-card:hover .profile-img {
            border-color: rgba(255, 255, 255, 0.6);
            transform: scale(1.05);
        }
        
        .name {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .role {
            font-size: 1.2rem;
            color: #f8f9fa;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }

        .role::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 2px;
            background: #FFCC80;
        }
        
        .details {
            margin-bottom: 25px;
            line-height: 1.6;
            font-size: 1rem;
        }
        
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .social-icons a:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-5px);
        }
        
        .special-thanks {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            max-width: 900px;
            margin: 0 auto;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .special-thanks::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #FB8C00, #F57C00, #5D4037);
        }
        
        .special-thanks h2 {
            font-size: 2.2rem;
            margin-bottom: 30px;
            color: white;
        }
        
        .advisors-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
        }
        
        .advisor-card {
            background: linear-gradient(45deg, #FB8C00, #5D4037);
            border-radius: 10px;
            padding: 25px;
            width: 250px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .advisor-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: #FFCC80;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .advisor-card:hover {
            transform: translateY(-10px);
            background: #FB8C00;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .advisor-card:hover::before {
            transform: scaleX(1);
        }
        
        .advisor-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 15px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .advisor-card:hover .advisor-img {
            border-color: rgba(255, 255, 255, 0.6);
            transform: scale(1.05);
        }
        
        .advisor-name {
            font-size: 1.4rem;
            margin-bottom: 5px;
        }
        
        .advisor-role {
            font-size: 1rem;
            color: #fdbb2d;
            margin-bottom: 15px;
        }
        
        .advisor-details {
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        
        .quote {
            font-style: italic;
            margin-top: 20px;
            padding: 15px;
            border-left: 3px solid #fdbb2d;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 0 10px 10px 0;
            position: relative;
        }

        .quote::before {
            content: '"';
            font-size: 3rem;
            position: absolute;
            top: -10px;
            left: 10px;
            color: rgba(255, 255, 255, 0.3);
        }
        
        @media (max-width: 768px) {
            .profile-card, .advisor-card {
                width: 280px;
            }
            
            .page-header h1 {
                font-size: 2.5rem;
            }
            
            .special-thanks {
                padding: 25px;
            }

            .top-bar {
                padding: 15px 20px;
            }
        }

        /* Animation for page load */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-header, .profile-card, .special-thanks {
            animation: fadeInUp 0.6s ease forwards;
        }

        .profile-card:nth-child(2) {
            animation-delay: 0.1s;
        }

        .profile-card:nth-child(3) {
            animation-delay: 0.2s;
        }

        .profile-card:nth-child(4) {
            animation-delay: 0.3s;
        }

        .profile-card:nth-child(5) {
            animation-delay: 0.4s;
        }

        .special-thanks {
            animation-delay: 0.5s;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Top bar with back button and logo -->
        <div class="top-bar">
         <button class="back-btn" onclick="window.location.href='https://homehiveph.site/about?article=about-us/about-homehive'">
    Back
</button>

            <img src="https://i.imgur.com/Q5BsPbV.png" alt="HomeHive Logo" class="nav-logo">
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <h1>Bee Smart. Bee Secure. Bee Home</h1>
            <p>Meet the talented individuals who brought this project to life. Each team member contributed their unique skills and expertise to create something truly special.</p>
        </div>
        
        <div class="team-container">
        <!-- Team Member 1 -->
<div class="profile-card">
    <div class="card-inner">
        <div class="card-front">
            <img src="assets/dev-pics/homehive-daguro.jpg" class="profile-img">
            <h2 class="name">Daguro, Elijah</h2>
            <p class="role">Quality Assurance</p>
        </div>
        <div class="card-back">
            <p class="details">
                Elijah helped identify system requirements and analyzed possible user scenarios. 
                He prepared and reviewed test cases to ensure that each feature worked as intended 
                and assisted in improving the system’s overall reliability and performance.
            </p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Team Member 2 -->
<div class="profile-card">
    <div class="card-inner">
        <div class="card-front">
            <img src="assets/dev-pics/homehive-flores.jpg" class="profile-img">
            <h2 class="name">Flores, Angelo</h2>
            <p class="role">Project Manager</p>
        </div>
        <div class="card-back">
            <p class="details">
                Angelo managed the team’s activities and handled project paperwork. 
                He organized schedules, assigned tasks, and regularly updated members on progress. 
                His leadership helped the group stay focused and meet deadlines efficiently.
            </p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Team Member 3 -->
<div class="profile-card">
    <div class="card-inner">
        <div class="card-front">
            <img src="assets/dev-pics/homehive-estacio.jpg" class="profile-img">
            <h2 class="name">Estacio, Zachary</h2>
            <p class="role">System Analyst</p>
        </div>
        <div class="card-back">
            <p class="details">
                Zachary assisted in identifying and resolving system issues, ensuring the platform ran efficiently. 
                He helped analyze possible bugs, suggested improvements, and supported the team in maintaining system stability and reliability.
            </p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Team Member 4 -->
<div class="profile-card">
    <div class="card-inner">
        <div class="card-front">
            <img src="assets/dev-pics/homehive-puyo.jpg" alt="Analyn Puyo" class="profile-img">
            <h2 class="name">Puyo,<br>Analyn</h2>
            <p class="role">Quality Assurance</p>
        </div>
        <div class="card-back">
            <p class="details">
                Analyn supported the team in reviewing project outputs and ensuring that system functions met the required standards. 
                She contributed to maintaining overall project consistency and quality.
            </p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Team Member 5 -->
<div class="profile-card">
    <div class="card-inner">
        <div class="card-front">
            <img src="assets/dev-pics/homehive-soriano.jpg" class="profile-img">
            <h2 class="name">Soriano, Kenny</h2>
            <p class="role">Lead Programmer</p>
        </div>
        <div class="card-back">
            <p class="details">
                Kenny led the development of the system’s main features and ensured smooth integration across all modules. 
                He focused on improving performance, solving technical challenges, and maintaining the overall quality of the platform.
            </p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook"></i></a>
            </div>
        </div>
    </div>
</div>

        
        <div class="special-thanks">
            <h2>Special Thanks to Our Advisors</h2>
            <p>We extend our deepest gratitude to the advisors who provided invaluable guidance, mentorship, and expertise throughout this project.</p>
            
            <div class="quote">
                "The guidance and wisdom of our advisors were instrumental in shaping this project and helping us overcome challenges along the way."
            </div>
            
          <div class="advisors-container">
    <div class="advisor-card">
        <img src="assets/dev-pics/credits-juville.jpg" alt="Juville Agpaoa" class="advisor-img">
        <h3 class="advisor-name">Agpaoa, Juville</h3>
        <p class="advisor-role">Thesis Adviser</p>
        <p class="advisor-details">
            Offered valuable guidance and practical insights throughout the development process,
            ensuring that the project aligned with both academic and industry standards.
        </p>
    </div>
    
    <div class="advisor-card">
        <img src="assets/dev-pics/credits-karen.jpg" alt="Cristy Karen" class="advisor-img">
        <h3 class="advisor-name">Karen, Cristy</h3>
        <p class="advisor-role">Thesis Coordinator</p>
        <p class="advisor-details">
            Provided overall supervision and expert evaluation of the study,
            offering critical feedback that strengthened the system’s technical and research foundation.
        </p>
    </div>
</div>

                
                
                
            </div>
        </div>'
    </div>

    <script>
        // Add a simple animation for page load
        document.addEventListener('DOMContentLoaded', function() {
            // Add hover effect to cards for touch devices
            const cards = document.querySelectorAll('.profile-card');
            
            cards.forEach(card => {
                card.addEventListener('touchstart', function() {
                    this.classList.add('hover');
                });
                
                card.addEventListener('touchend', function() {
                    this.classList.remove('hover');
                });
            });
        });
    </script>
</body>
</html>