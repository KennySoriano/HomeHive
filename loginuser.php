<?php
session_start();
require_once 'config.php';
include 'debug/debug.php';
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo json_encode(['status' => 'error', 'message' => 'Connection Failed: ' . $conn->connect_error]);
        exit();
    }

    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['pass']) ? trim($_POST['pass']) : '';
    $captchaResponse = $_POST['g-recaptcha-response'] ?? '';

    $recaptchaSecret = '6LetV5srAAAAAGtjKYWxEsxxsLoiLeGbaGwmPnOg';

    if (empty($captchaResponse)) {
        echo json_encode(['status' => 'error', 'message' => 'Please complete the captcha.']);
        exit();
    }

    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$captchaResponse}");
    $captchaResult = json_decode($verify);

    if (!$captchaResult->success) {
        echo json_encode(['status' => 'error', 'message' => 'Captcha verification failed.']);
        exit();
    }

    if (empty($email) || empty($password)) {
        $response = ['status' => 'warning', 'message' => 'Email and password are required.'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = ['status' => 'error', 'message' => 'Invalid Email or Password.'];
    } else {
        $sql = "SELECT id, userID, email, pRole, sRole, Fname, Lname, password FROM accountsdb WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $response = ['status' => 'error', 'message' => 'User not found.'];
        } else {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                session_regenerate_id(true);

                // Keep user session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_idnumber'] = $user['userID'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['pRole'] = $user['pRole'];
                $_SESSION['sRole'] = $user['sRole'];
                $_SESSION['first_name'] = $user['Fname'];
                $_SESSION['last_name'] = $user['Lname'];

                // Generate OTP
                $otp = random_int(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['otp_time'] = time(); // store timestamp

                // Send OTP via PHPMailer
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'homehiveofficial2025@gmail.com';
                    $mail->Password = 'gbzj cxiq fmvu nksd';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('homehiveofficial2025@gmail.com', 'HomeHive');
                    $mail->addAddress($user['email'], $user['Fname'].' '.$user['Lname']);
                    $mail->isHTML(true);
                  $mail->Subject = 'HomeHive Login Verification';
$mail->Body = "
Hello " . htmlspecialchars($user['Fname']) . ",<br><br>

We received a request to access your HomeHive account. To ensure the security of your account, please use the following One-Time Password (OTP) to complete your login process:<br><br>

<b>$otp</b><br><br>

This OTP is valid for 5 minutes from the time it was issued. Please do not share this code with anyone. HomeHive will never ask you for your OTP via email, phone, or messages outside this system.<br><br>

If you did not request this login, please ignore this message. No changes will be made to your account, but we recommend reviewing your recent account activity and updating your password for added security.<br><br>

Additional security tips:<br>
- Always keep your account credentials private.<br>
- Avoid using public or shared computers to log in.<br>
- Contact HomeHive support immediately if you notice any suspicious activity.<br><br>

Thank you for prioritizing security and choosing HomeHive.<br><br>

Best regards,<br>
The HomeHive Team
";


                    $mail->send();

                    $response = [
                        'status' => 'success',
                        'message' => 'OTP sent to your email',
                        'redirect' => 'otpverify.php'
                    ];

                } catch (Exception $e) {
                    $response = [
                        'status' => 'error',
                        'message' => 'Failed to send OTP: ' . $mail->ErrorInfo
                    ];
                }
            } else {
                $response = ['status' => 'error', 'message' => 'Invalid Email or Password.'];
            }
        }
        $stmt->close();
    }
    $conn->close();
    echo json_encode($response);
    exit();
}
?>




<!DOCTYPE HTML>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=0.90" />
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
  
  <title>HomeHive - Sign In</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

  <!-- Reset & Core Styles -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css" />
  <link rel="stylesheet" href="assets/css/index.css" />
  <link rel="stylesheet" href="assets/css/dropdownmenu.css" />
  <link rel="stylesheet" href="assets/css/userlogin.css" />

  <!-- Tailwind CSS -->
  <link href="other/tailwind/css/tailwind.css" rel="stylesheet" />

  <!-- Plugins & Animations -->
  <link rel="stylesheet" href="assets/css/animate.css" />
  <link rel="stylesheet" href="assets/css/owl.css" />
  <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />

  <!-- Icons -->
  <link rel="stylesheet" href="assets/css/fontawesome.css" />
  <link rel="apple-touch-icon" sizes="180x180" href="https://i.imgur.com/dGRwk3F.png">
<link rel="icon" type="image/png" sizes="32x32" href="https://i.imgur.com/dGRwk3F.png">
<link rel="icon" type="image/png" sizes="16x16" href="https://i.imgur.com/dGRwk3F.png">
</head>

<style>
* {
  user-select: none;
  caret-color: transparent;
}

input,
textarea,
select {
  user-select: text !important;
  caret-color: auto !important;
}
</style>


<body <?php echo !empty($pending_modal) ? 'data-pending-modal="true"' : ''; ?>>

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



    <div class="login-box" id="login-box">
        <form id="login-form" method="POST" autocomplete="off">
        <h2 class="fs-title" id="fs-title">Welcome</h2>
        <p id="signup-link">Don't have an account? <a href="createaccount.php">Sign up</a></p>
        
        <input type="email" name="email" id="email" placeholder="Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />

        <div class="password-wrapper">
            <input type="password" name="pass" id="pass" placeholder="Password" required value="<?php echo isset($_POST['pass']) ? htmlspecialchars($_POST['pass']) : ''; ?>" />
            <button type="button" id="toggle-password" class="eye-icon">
                <i class="fa fa-eye"></i> 
            </button>
        </div>

        <p id="forgot-password-link">Forgot password? <a href="https://homehiveph.site/resetpassword/forgot_password" class="help-link">Click here</a></p>
<div class="g-recaptcha" data-sitekey="6LetV5srAAAAAC2iQJq_7y4IlhpR8YEAehXJGqsA" style ="margin-bottom:16px;"></div>



        <input type="submit" class="action-button" id="submit-btn" value="Login" />

         <div class="or-divider"><span>OR</span></div>
        <div class="social-login">
    <button type="button" class="social-btn fb-btn" onclick="showComingSoon('Facebook')">
        <i class="fab fa-facebook-f"></i> Login via Facebook
    </button>
</div>


    </form>
    </div>
       </div>
<?php include 'footer.php'; ?>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
<script src="other/tailwind/js/tailwind.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="assets/js/login.js"></script>
<script src="assets/js/logoscrolled.js"></script>
<script src="assets/js/isotope.min.js"></script>
<script src="assets/js/owl-carousel.js"></script>
<script src="assets/js/counter.js"></script>
<script src="assets/js/custom.js"></script>
 <script>

$(document).ready(function() {

    $('#login-form').submit(function(event) {
        event.preventDefault();

        if (grecaptcha.getResponse() === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Captcha Required',
                text: 'Please verify that you are not a robot.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#FB8C00'
            });
            return;
        }

        Swal.fire({
            title: 'Logging in...',
            html: 'Please wait.',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        setTimeout(() => {
            $.ajax({
                type: 'POST',
                url: '', // current page handles the POST
                data: $(this).serialize(),
                success: function(response) {
                    const result = JSON.parse(response);

                    if (result.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'OTP Sent!',
                            html: 'Redirecting to verification page...',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            willClose: () => {
                                window.location.href = result.redirect;
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed',
                            text: result.message || 'Invalid Email or Password',
                            confirmButtonColor: '#FB8C00'
                        });
                        grecaptcha.reset();
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Error',
                        text: 'An unexpected error occurred.',
                        confirmButtonColor: '#FB8C00'
                    });
                    grecaptcha.reset();
                }
            });
        }, 500); // short delay to show loading
    });

});


function showComingSoon(platform) {
    Swal.fire({
      title: `Login with ${platform} is coming soon!`,
      text: `At HomeHive, we’re making it easier for you to connect and manage your home. While ${platform} login is being prepared, why not create a HomeHive account now? It’s quick, easy, and gives you full access to all features!`,
      icon: 'info',
      confirmButtonText: 'Create Account',
      confirmButtonColor: '#FB8C00',  // Orange button
      background: '#FFF8E1',
      color: '#5D4037',
      width: 450,
      showCancelButton: true,
      cancelButtonText: 'Maybe Later',
      cancelButtonColor: '#A1887F',
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = 'createaccount.php';
      }
    });
  }
    </script>

</html>
