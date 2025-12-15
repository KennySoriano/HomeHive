<?php
session_start();

// ✅ Always set timezone before any date() calls
date_default_timezone_set('Asia/Manila');

require_once 'config.php';
include 'debug/debug.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';

if (!isset($_SESSION['otp'], $_SESSION['otp_time'])) {
    header("Location: loginuser.php");
    exit();
}

// OTP expiration: 5 minutes
if (time() - $_SESSION['otp_time'] > 300) {
    session_unset();
    session_destroy();
    header("Location: loginuser.php?expired=1");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputOtp = trim($_POST['otp'] ?? '');

    if ($inputOtp == $_SESSION['otp']) {
        // Gather login info
        $userEmail = $_SESSION['email'];
        $userName = $_SESSION['first_name'];
        $loginTime = date("Y-m-d H:i:s"); // ✅ Philippines time now
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $role = $_SESSION['sRole'];
        $userIdNumber = $_SESSION['user_idnumber']; // ✅ receiver_id for inbox

        // --- Update last_login in database ---
        $stmt = $conn->prepare("UPDATE accountsdb SET last_login = ? WHERE email = ?");
        $stmt->bind_param("ss", $loginTime, $userEmail);
        $stmt->execute();
        $stmt->close();
        // --------------------------------------

        // --- Generate new session token ---
        $newSessionToken = bin2hex(random_bytes(32)); // Generate a new session token
        $_SESSION['session_token'] = $newSessionToken; // Store the session token in the session

        // Update session token in the database
        $stmt = $conn->prepare("UPDATE accountsdb SET session_token = ? WHERE email = ?");
        $stmt->bind_param("ss", $newSessionToken, $userEmail);
        $stmt->execute();
        $stmt->close();
        // --------------------------------------

        // ✅ Insert a new inbox notification
        $inbox_id = mt_rand(5000000000, 5999999999); // generate unique inbox_id
        $title = "Login Successful";
        $displayTime = (new DateTime($loginTime))->format('F j Y g:i a');

        $notifMessage = "Hello $userName,<br><br>"
                      . "We noticed a successful login to your account on <strong>$loginTime</strong>.<br>"
                      . "If this was you, no further action is needed.<br><br>"
                      . "If you did not initiate this login, we recommend you review your account activity and change your password immediately.<br><br>"
                      . "For more details about this login, please check your registered email address.<br><br>"
                      . "Keeping your account secure is our top priority.<br><br>"
                      . "Thank you,<br>"
                      . "HomeHive Support Team";

        $stmt = $conn->prepare("INSERT INTO inbox 
            (inbox_id, `from`, receiver_id, title, message, created_at, status, type)
            VALUES (?, 'system', ?, ?, ?, ?, 'unread', 'primary')");

        $stmt->bind_param("iisss", $inbox_id, $userIdNumber, $title, $notifMessage, $loginTime);
        $stmt->execute();
        $stmt->close();
        // --------------------------------------

        // Clear OTP session
        unset($_SESSION['otp'], $_SESSION['otp_time']);

        // Send login notification email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'homehiveofficial2025@gmail.com';
            $mail->Password   = 'gbzj cxiq fmvu nksd';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('homehiveofficial2025@gmail.com', 'HomeHive');
            $mail->addAddress($userEmail, $userName);
            $mail->isHTML(true);

            $mail->Subject = 'HomeHive Login Successful';
            $mail->Body = "
Hello " . htmlspecialchars($userName) . ",<br><br>

We are writing to inform you that your HomeHive account has been successfully accessed. For your security, please review the login details below to ensure that it was you who performed this action.<br><br>

<b>Login Information:</b><br>
&bull; <strong>Date & Time:</strong> $loginTime<br>
&bull; <strong>IP Address:</strong> $ipAddress<br>
&bull; <strong>Browser / Device:</strong> $userAgent<br>

If this login was not initiated by you, we strongly recommend that you:<br>
- Change your HomeHive account password immediately.<br>
- Contact HomeHive support at <a href='mailto:email@homehive.com'>email@homehive.com</a> for further assistance.<br><br>

HomeHive takes your account security very seriously. Monitoring unusual activity helps us keep your data safe and secure.<br><br>

Thank you for trusting HomeHive for your property management needs.<br><br>

Best regards,<br>
<strong>The HomeHive Team</strong>
";

            $mail->send();
        } catch (Exception $e) {
            // Optional: log $mail->ErrorInfo but don't block login
        }

        // Redirect based on role
        if ($role === 'Admin') {
            header("Location:/userdashboard/admin/admindashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    } else {
        $message = "Invalid OTP. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>OTP Verification - HomeHive</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --hh-sunset-amber: #FB8C00;
    --hh-rich-gold: #F57C00;
    --hh-bee-brown: #5D4037;
    --hh-cream-white: #FFF8E1;
    --hh-golden-shadow: #FFCC80;
    --hh-soft-honey: #FFE0B2;
        --s: 44px; /* control the size*/
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
    font-family: 'Poppins', sans-serif;
}

body {
  background:
    var(--g1),var(--g1) var(--s) calc(1.73*var(--s)),
    var(--g2),var(--g2) var(--s) calc(1.73*var(--s)),
    var(--g3) var(--s) 0,var(--g3) 0 calc(1.73*var(--s)) 
    var(--c2);
  background-size: calc(2*var(--s)) calc(3.46*var(--s));
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 20px;
     caret-color: transparent
}

.otp-card {
    background: white;
    padding: 40px 30px;
    border-radius: 20px;
    box-shadow: 0 15px 30px rgba(93, 64, 55, 0.2);
    width: 100%;
    max-width: 420px;
    text-align: center;
    transition: transform 0.3s ease;
}

.otp-card:hover {
    transform: translateY(-5px);
}

.logo {
    margin-bottom: 20px;
}

.logo i {
    font-size: 42px;
    color: var(--hh-sunset-amber);
    background: rgba(251, 140, 0, 0.1);
    width: 80px;
    height: 80px;
    line-height: 80px;
    border-radius: 50%;
    text-align: center;
}

h2 {
    color: var(--hh-bee-brown);
    font-size: 28px;
    margin-bottom: 10px;
    font-weight: 700;
}

.subtitle {
    color: var(--hh-bee-brown);
    margin-bottom: 25px;
    font-size: 16px;
    line-height: 1.5;
}

.alert {
    background: var(--hh-soft-honey);
    color: var(--hh-bee-brown);
    padding: 12px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 14px;
    border-left: 4px solid var(--hh-sunset-amber);
}

.otp-input-container {
    margin-bottom: 25px;
}

.otp-input {
    width: 100%;
    padding: 16px;
    border: 2px solid var(--hh-golden-shadow);
    border-radius: 12px;
    font-size: 20px;
    text-align: center;
    letter-spacing: 8px;
    font-weight: 600;
    transition: all 0.3s;
    outline: none;
}

.otp-input:focus {
    border-color: var(--hh-sunset-amber);
    box-shadow: 0 0 0 3px rgba(251, 140, 0, 0.2);
}

.otp-input::placeholder {
    letter-spacing: normal;
    color: #ccc;
}

.btn-verify {
    width: 100%;
    background: var(--hh-sunset-amber);
    color: white;
    border: none;
    padding: 16px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 10px rgba(251, 140, 0, 0.3);
}

.btn-verify:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(251, 140, 0, 0.4);
}

.btn-verify:active {
    transform: translateY(0);
}

.footer-text {
    margin-top: 25px;
    color: var(--hh-bee-brown);
    font-size: 14px;
}

.footer-text a {
    color: var(--hh-sunset-amber);
    text-decoration: none;
    font-weight: 600;
}

.footer-text a:hover {
    text-decoration: underline;
}

.timer {
    margin-top: 15px;
    color: var(--hh-sunset-amber);
    font-weight: 600;
}

@media (max-width: 480px) {
    .otp-card {
        padding: 30px 20px;
    }
    
    h2 {
        font-size: 24px;
    }
    
    .otp-input {
        padding: 14px;
        font-size: 18px;
    }
}
</style>
</head>
<body>

<div class="otp-card">
   <img src="https://i.imgur.com/Q5BsPbV.png" alt="HomeHive Logo" style="width:120px; height:120px;">

    <h2>OTP Verification</h2>
    <p class="subtitle">Enter the 6-digit code sent to your email address. The code will expire in 5 minutes.</p>
    
    <?php if($message): ?>
    <div class="alert">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>
    
    <form method="POST" autocomplete="off">
        <div class="otp-input-container">
            <input type="text" name="otp" required maxlength="6" pattern="\d{6}" 
                class="otp-input" placeholder="000000">
        </div>
        
        <button type="submit" class="btn-verify">
            Verify OTP
        </button>
    </form>
    
    <div class="timer" id="timer">
        <i class="far fa-clock"></i> 05:00
    </div>
    
    <p class="footer-text">
        Didn't receive the code?<a href="loginuser.php">Try logging in again</a>
    </p>
</div>

<script>
// Get remaining time from PHP
let timeLeft = <?php 
    $remaining = 300 - (time() - $_SESSION['otp_time']); 
    echo ($remaining > 0 ? $remaining : 0); 
?>;

const timerEl = document.getElementById('timer');

function updateTimer() {
    let minutes = Math.floor(timeLeft / 60);
    let seconds = timeLeft % 60;
    timerEl.innerHTML = `<i class="far fa-clock"></i> ${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
    if(timeLeft <= 0){
        clearInterval(timerInterval);
        // Optionally redirect on expiration
        window.location.href = "loginuser.php?expired=1";
    }
    timeLeft--;
}

updateTimer(); // initial display
const timerInterval = setInterval(updateTimer, 1000);
</script>


</body>
</html>
