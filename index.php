<?php
session_start();
require_once 'config.php';
include 'includes/session_checker.php';
$user_id = $_SESSION['user_idnumber'] ?? null;

// Handle AJAX submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'])) {
    header('Content-Type: application/json');
    if (!$user_id) {
        echo json_encode(['success'=>false,'login_required'=>true]);
        exit;
    }
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    if ($rating < 1 || $rating > 5) {
        echo json_encode(['success'=>false,'message'=>'Invalid rating value']);
        exit;
    }
    // Check if user already rated
    $stmt = $conn->prepare("SELECT id FROM homehive_ratings WHERE user_id=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $stmt->store_result();
    if($stmt->num_rows>0){
        $stmt->close();
        $stmt = $conn->prepare("UPDATE homehive_ratings SET rating=?, comment=?, created_at=NOW() WHERE user_id=?");
        $stmt->bind_param("isi",$rating,$comment,$user_id);
        $stmt->execute();
        echo json_encode(['success'=>true,'updated'=>true]);
        exit;
    }
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO homehive_ratings (user_id,rating,comment) VALUES (?,?,?)");
    $stmt->bind_param("iis",$user_id,$rating,$comment);
    if($stmt->execute()) echo json_encode(['success'=>true,'updated'=>false]);
    else echo json_encode(['success'=>false,'message'=>'Database error']);
    exit;
}

// Fetch average rating and total
$res = $conn->query("SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM homehive_ratings");
$row = $res->fetch_assoc();
$avgRating = round((float)$row['avg_rating'], 1); 

$totalRatings = $row['total'];

// Fetch user feedback if exists
$userRating = $userComment = null;
if($user_id){
    $stmt = $conn->prepare("SELECT rating, comment FROM homehive_ratings WHERE user_id=?");
    $stmt->bind_param("i",$user_id);
    $stmt->execute();
    $stmt->bind_result($userRating,$userComment);
    $stmt->fetch();
    $stmt->close();
}

$feedbacks = [];
$result = $conn->query("
    SELECT DISTINCT h.comment, h.rating, a.Fname 
    FROM homehive_ratings h
    JOIN accountsdb a ON h.user_id = a.userID
    WHERE h.rating IN (4,5)      -- Only 4 and 5 stars
    ORDER BY RAND()
");

if ($result && $result->num_rows > 0) {
    $lastComment = '';
    $lastUser = '';

    while ($r = $result->fetch_assoc()) {
        // Skip consecutive duplicates (same user + same comment)
        if ($r['comment'] === $lastComment && $r['Fname'] === $lastUser) {
            continue;
        }

        $feedbacks[] = [
            'comment' => $r['comment'],
            'rating'  => $r['rating'],
            'Fname'   => $r['Fname']
        ];

        // Track last row to avoid consecutive duplicates
        $lastComment = $r['comment'];
        $lastUser = $r['Fname'];
    }
}


// Get today's date
$today = date('Y-m-d');

// Check if the user has already been counted today via cookie
if (!isset($_COOKIE['homehive_viewed'])) {
    // Set a cookie to expire in 24 hours
    setcookie('homehive_viewed', '1', time() + 86400, "/"); 

    // Increment total views in database (just store date)
    $stmt = $conn->prepare("INSERT INTO site_views (view_date) VALUES (?)");
    $stmt->bind_param("s", $today);
    $stmt->execute();
}

// Total all-time views
$totalViewsResult = $conn->query("SELECT COUNT(*) as total_views FROM site_views");
$totalViews = $totalViewsResult->fetch_assoc()['total_views'];

// Optional: Today's views
$todayViewsResult = $conn->query("SELECT COUNT(*) as today_views FROM site_views WHERE view_date='$today'");
$todayViews = $todayViewsResult->fetch_assoc()['today_views'];

// Users & Properties as before
$userResult = $conn->query("SELECT COUNT(*) as user_count FROM accountsdb");
$userCount = $userResult->fetch_assoc()['user_count'];

$propertyResult = $conn->query("SELECT COUNT(*) as property_count FROM properties");
$propertyCount = $propertyResult->fetch_assoc()['property_count'];


?>


<!DOCTYPE html>
<html lang="en">
   <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
     <?php include 'meta.php'; ?>
    <title>HomeHive Official</title>


    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="other/tailwind/css/tailwind.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <link href="other/tailwind/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/index.css">
     <link rel="stylesheet" href="assets/css/homehivetutorial.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/dropdownmenu.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@7/swiper-bundle.min.css"/>
  </head>

<body>

<?php include 'includes/disclaimer-pictures.php' ?>

<?php include 'includes/landingpage-navbar.php' ?>




<div class="main-banner">
  <div class="owl-carousel owl-banner">
    <div class="item item-1">
      <div class="header-text">
        <span class="category"><em>BEE</em> SMART</span>
        <h2>Welcome to HomeHive</h2>
        <p style="color:#FAFAFA;">Make smart choices with homes that fit your lifestyle perfectly.</p>
      </div>
    </div>
    
    <div class="item item-2">
      <div class="header-text">
        <span class="category"><em>BEE</em> SECURE</span>
        <h2>Find Your New Home</h2>
        <p style="color:#FAFAFA;">Feel safe knowing every listing is verified and trustworthy for your peace of mind.</p>
      </div>
    </div>
    
    <div class="item item-3">
      <div class="header-text">
        <span class="category"><em>BEE</em> HOME</span>
        <h2>Save Money with HomeHive</h2>
        <p style="color:#FAFAFA;">Enjoy comfort and savings with exclusive deals and offers made just for you.</p>
      </div>
    </div>
  </div>
</div>



  <section>
  <div class="featured section" data-aos="fade-down" data-aos-easing="linear">
    <div class="container">
      <div class="row">
        <div class="col-lg-4">
          <div class="left-image">
            <img src="assets/images/featured.jpg" alt="">
            <a href="property-details.html"><img src="assets/images/featured-icon.png" alt="" style="max-width: 60px; padding: 0px;"></a>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="section-heading">
            <h6>| Featured</h6>
            <h2> Place to Live,<br>Not Just a Place to Stay</h2>
          </div>
          <div class="accordion" id="accordionExample">
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                  <i class="fa fa-arrow-right" aria-hidden="true"></i>  <h5 style="margin-left:10px";>  About HomeHive  </h5> 
                </button>
              </h2>
              <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                <div class="accordion-body">
            <p>
  HomeHive is an academic project that simulates a rental platform for educational purposes. 
  All listings and information are for demonstration only. 
  No actual rentals, payments, or personal data collection take place on this site.
</p>

             
             </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
               <i class="fa fa-arrow-right" aria-hidden="true"></i> <h5 style="margin-left:10px";>  How Does This Work?  </h5> 
                </button>
              </h2>
              <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                  <P>
                    <strong>Browse</strong>: Explore our verified apartment listings with detailed photos and information.<br>
                    <strong>Inquire</strong>: Reach out directly to property owners for viewings or questions.<br>
                    <strong>Move In</strong>: Once you find your ideal home, we guide you through the leasing process for a smooth transition.<br>
                  </p>
                  </div>
              </div>
            </div>
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <i class="fa fa-arrow-right" aria-hidden="true"></i> <h5 style="margin-left:10px";>  Why Choose HomeHive? </h5> 
                </button>
              </h2>
              <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                <div class="accordion-body">
                <p>HomeHive offers accurate, up-to-date listings with helpful filters, neighborhood insights, and reliable customer support. We make your apartment search quick, easy, and stress-free.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3" data-aos="zoom-out-right">
          <div class="info-table">
            <ul>
              <li>
                <img src="assets/images/info-icon-01.png" alt="" style="max-width: 52px;">
                <h4>Accuracy<br><span>Verified Listings</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-02.png" alt="" style="max-width: 52px;">
                <h4>Contract<br><span>Contract Ready</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-03.png" alt="" style="max-width: 52px;">
                <h4>Payment<br><span>Payment Process</span></h4>
              </li>
              <li>
                <img src="assets/images/info-icon-04.png" alt="" style="max-width: 52px;">
                <h4>Safety<br><span>24/7 Under Control</span></h4>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  </section>

  <div class="video section" data-aos="fade-right">
    <div class="container">
      <div class="row">
        <div class="col-lg-4 offset-lg-4">
          <div class="section-heading text-center">
            <h6>| Video View</h6>
            <h2>Create a Home that Lasts</h2>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="video-content">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 offset-lg-1">
        <div class="video-frame" style="position: relative; display: inline-block;">
<video id="clickableVideo" width="100%" autoplay muted playsinline loop preload="auto"
       poster="assets/images/video-frame.jpg" title="Click to Watch on YouTube"
       style="cursor: pointer;">
  <source src="assets/video/HomeHiveVideo.mp4" type="video/mp4">
  
  Your browser does not support the video tag.
</video>

</div>

        </div>
      </div>
    </div>
  </div>
  <div class="fun-facts">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="wrapper">
            <div class="row">

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<!-- Include Font Awesome once in <head> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Parallax Stats Section -->
<section class="stats-section" 
  style="
    position:relative;
    padding:60px 0; 
    background-image:url('https://images.pexels.com/photos/111962/pexels-photo-111962.jpeg?_gl=1*1kjtio6*_ga*MTM1MTU5MzI1NC4xNzU0NTMyMjU1*_ga_8JE65Q40S6*czE3NTg3MjM0NzgkbzYkZzEkdDE3NTg3MjM1NjQkajM1JGwwJGgw'); 
    background-size:cover; 
    background-position:center; 
    background-attachment:fixed; 
    background-repeat:no-repeat;
  ">
  
  <!-- Dark overlay for contrast -->
  <div style="
    position:absolute;
    top:0; left:0; width:100%; height:100%;
    background:rgba(0,0,0,0.4);
    z-index:1;">
  </div>

  <!-- Stats Content -->
  <div class="container" style="position:relative; z-index:2;">
    <div class="row">
<h1 style="
    text-align: center;
    color: #FAFAFA; 
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    padding: 15px 20px;
    border-radius: 15px;
    font-family: Arial, sans-serif;
    font-size: 24px;
    font-weight: bold;
    margin: 20px auto;
    display: inline-block;
    border: 1px solid rgba(255, 255, 255, 0.2);
">
    Statistics as of <?php echo date('F j, Y'); ?>
</h1>



      <!-- Total Properties -->
      <div class="col-lg-3 col-md-6">
        <div class="counter glass-card" data-aos="fade-up" style="text-align:center;"data-aos-delay="0">
          <i class="fas fa-house" 
             style="font-size:40px; color:#FF7F00; display:block; margin-top:8px; margin-bottom:5px;"></i>
          <h2 style="color:orange;" class="timer count-title count-number" 
              data-to="<?php echo $propertyCount; ?>" 
              data-speed="1000">
              <?php echo $totalApartmentCount; ?>
          </h2>
          <p class="count-text"style="color:orange;">Total Properties</p>
        </div>
      </div>

      <!-- Total Users -->
      <div class="col-lg-3 col-md-6">
        <div class="counter glass-card" data-aos="fade-up" style="text-align:center;"data-aos-delay="500">
          <i class="fas fa-users" 
             style="font-size:40px; color:#FF7F00; display:block; margin-top:8px; margin-bottom:5px;"></i>
          <h2 style="color:orange;" class="timer count-title count-number" 
              data-to="<?php echo $userCount; ?>" 
              data-speed="1000">
              <?php echo $totalTenantCount; ?>
          </h2>
          <p class="count-text"style="color:orange;">Total Users</p>
        </div>
      </div>

      <!-- Total Ratings -->
      <div class="col-lg-3 col-md-6">
        <div class="counter glass-card" data-aos="fade-up" style="text-align:center;"data-aos-delay="650">
          <i class="fas fa-star" 
             style="font-size:40px; color:#FF7F00; display:block; margin-top:8px; margin-bottom:5px;"></i>
       <h2 style="color:orange;">
<?php echo $avgRating;?>    
</h2>

          <p class="count-text"style="color:orange;">Total Ratings</p>
        </div>
      </div>

      <!-- Total Website Views -->
      <div class="col-lg-3 col-md-6">
        <div class="counter glass-card" data-aos="fade-up" style="text-align:center;"data-aos-delay="750">
          <i class="fas fa-eye" 
             style="font-size:40px; color:#FF7F00; display:block; margin-top:8px; margin-bottom:5px;"></i>
          <h2 style="color:orange;" class="timer count-title count-number" 
              data-to="<?php echo $totalViews; ?>" 
              data-speed="1000">
              <?php echo $totalViews; ?>
          </h2>
          <p class="count-text" style="color:orange;">Total Website Views</p>
        </div>
      </div>

    </div>

    <!-- Thank You Message -->
    <div class="row">
      <div class="col-12 text-center" style="margin-top:25px;">
        <p style="font-size:18px; color:#fff;">
Thank you for being part of our journey!
With your support, HomeHive keeps growing stronger and better each day.<br>
Always remember: Bee Smart. Bee Secure. Bee Home.
        </p>
      </div>
    </div>

  </div>
</section>

<!-- Glass Card CSS -->
<style>
.glass-card {
  background: rgba(255, 255, 255, 0.15); /* semi-transparent */
  border-radius: 12px;
  padding: 20px;
  margin-bottom: 20px;
  color: #fff; /* text color */
  backdrop-filter: blur(10px); /* glass effect */
  -webkit-backdrop-filter: blur(10px); /* for Safari */
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}
</style>


  <div class="section best-deal"  data-aos="fade-up">
    <div class="container">
      <div class="row">
        <div class="col-lg-4">
          <div class="section-heading">
          <h6>| Explore Your Options!</h6>
          <h2>Find Your Perfect Apartment Today!</h2>
          </div>
        </div>
        <div class="col-lg-12">
          <div class="tabs-content">
            <div class="row">
              <div class="nav-wrapper ">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="appartment-tab" data-bs-toggle="tab" data-bs-target="#appartment" type="button" role="tab" aria-controls="appartment" aria-selected="true">Studio Type</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="villa-tab" data-bs-toggle="tab" data-bs-target="#villa" type="button" role="tab" aria-controls="villa" aria-selected="false">Duplex Apartment</button>
                  </li>
                  <li class="nav-item" role="presentation">
                    <button class="nav-link" id="studiotype-tab" data-bs-toggle="tab" data-bs-target="#studiotype" type="button" role="tab" aria-controls="studiotype" aria-selected="false">Townhouse</button>
                  </li>
                </ul>
              </div>              
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="appartment" role="tabpanel" aria-labelledby="appartment-tab">
                  <div class="row">
                    <div class="col-lg-3">
                      <div class="info-table">
                        <ul>
                          <li>Total Flat Space <span>20–40 m² </span></li>
                          <li>Floors<span>1</span></li>
                        </ul>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <img src="assets/images/studiohouse.jpg" alt="">
                    </div>
                    <div class="col-lg-3">
                    <h4>What is a Studio Type Apartment?</h4>
                  <p>A studio apartment is a single room that serves as the living room, bedroom, and kitchen. The bathroom is usually a separate room, and sometimes there's a closet. Some studios have a small extra area called an alcove or L-shaped section that can be used for dining or sleeping.</p>
                  <br><br>
                </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="villa" role="tabpanel" aria-labelledby="villa-tab">
                  <div class="row">
                    <div class="col-lg-3">
                      <div class="info-table">
                        <ul>
                        <li>Total Flat Space <span>70–150 m²</span></li>
                        <li>Floors <span>2 </span></li>
                        </ul>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <img src="assets/images/duplexhouse.jpg" alt="">
                    </div>
                    <div class="col-lg-3">
                      <h4>What is a Duplex Apartment?</h4>
                      <p>A duplex is a two-story apartment or house where the living space is split across two levels. Each unit typically mirrors the other in layout, offering a separate living and sleeping area, providing more privacy and space compared to a single-level home.</p>
                    </div>
                  </div>
                </div>
                <div class="tab-pane fade" id="studiotype" role="tabpanel" aria-labelledby="studiotype-tab">
                  <div class="row">
                    <div class="col-lg-3">
                      <div class="info-table">
                        <ul>
                        <li>Total Flat Space <span>90–180 m²</span></li>
                        <li>Floors <span>2 to 3</span></li>
                        </ul>
                      </div>
                    </div>
                    <div class="col-lg-6">
                      <img src="assets/images/townhouse.jpg" alt="">
                    </div>
                    <div class="col-lg-3">
                      <h4>What is a Townhouse</h4>
                      <p>Townhouses are multi-story homes that share walls with neighboring units in a compound. While they have similar designs, the key difference is that when you buy a townhouse, you own both the land and the house, offering more space and privacy compared to apartment living.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
        </div>
  </div>
<style>

:root {
  --hh-sunset-amber: #FB8C00;
  --hh-rich-gold: #F57C00;
  --hh-bee-brown: #5D4037;
  --hh-cream-white: #FFF8E1;
  --hh-golden-shadow: #FFCC80;
  --hh-soft-honey: #FFE0B2;
}
/* HomeHive Rating Section Styles with Bee Brown Background */
.homehive-rating-section {
  background: var(--hh-bee-brown);
  padding: 4rem 0;
  font-family: 'Segoe UI', system-ui, sans-serif;
}

.homehive-rating-section .container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: start;
}

/* Left Section Styles */
.rating-left h3 {
  color: var(--hh-cream-white);
  font-size: 1.8rem;
  margin-bottom: 1rem;
  font-weight: 600;
}
/* Average Stars - container */
.average-stars {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
  color: #ccc; /* empty star color */
}

/* Filled portion of stars */
.average-stars i.filled {
  color: var(--hh-rich-gold); /* solid star color */
}

/* Optional: half stars for decimal */
.average-stars i.half::before {
  content: '\f005'; /* FontAwesome star */
  color: var(--hh-rich-gold);
  position: absolute;
  width: 50%; /* fill half */
  overflow: hidden;
}

.average-stars i {
  font-size: 1.5rem;
  color: var(--hh-rich-gold);
  text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.average-score {
  font-size: 2rem;
  font-weight: 700;
  color: var(--hh-cream-white);
  margin-bottom: 0.5rem;
}

.total-reviews {
  color: var(--hh-golden-shadow);
  font-size: 0.9rem;
  margin-bottom: 2rem;
}

.rating-left h4 {
  color: var(--hh-cream-white);
  font-size: 1.3rem;
  margin-bottom: 1rem;
  font-weight: 600;
}

.feedback-slider {
  position: relative;
  min-height: 200px;
}

.feedback-slide {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.5s ease-in-out;
  background: var(--hh-cream-white);
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  border-left: 4px solid var(--hh-sunset-amber);
}

.feedback-slide.active {
  opacity: 1;
  transform: translateY(0);
  z-index: 1;
}

.feedback-slide .stars {
  display: flex;
  gap: 0.3rem;
  margin-bottom: 1rem;
}

.feedback-slide .stars i {
  color: var(--hh-rich-gold);
  font-size: 0.9rem;
}

.feedback-slide p {
  color: var(--hh-bee-brown);
  line-height: 1.6;
  margin-bottom: 1rem;
  font-style: italic;
}

.feedback-slide .anonymous {
  color: var(--hh-bee-brown);
  opacity: 0.7;
  font-size: 0.9rem;
  font-weight: 500;
}

/* Right Section Styles */
.rating-right {
  background: var(--hh-cream-white);
  padding: 2rem;
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.3);
  border: 2px solid var(--hh-golden-shadow);
}

.rating-right h3 {
  color: var(--hh-bee-brown);
  font-size: 1.5rem;
  margin-bottom: 1.5rem;
  font-weight: 600;
  text-align: center;
}

#rating-stars {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  margin-bottom: 1.5rem;
}

#rating-stars .star {
  font-size: 2rem;
  color: var(--hh-golden-shadow);
  cursor: pointer;
  transition: all 0.2s ease;
}

#rating-stars .star:hover,
#rating-stars .star.active {
  color: var(--hh-sunset-amber);
  transform: scale(1.1);
}

#rating-stars .star.fa-solid {
  color: var(--hh-rich-gold);
}

#rating-comment {
  width: 100%;
  min-height: 120px;
  padding: 1rem;
  border: 2px solid var(--hh-golden-shadow);
  border-radius: 8px;
  resize: vertical;
  font-family: inherit;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  margin-bottom: 1.5rem;
  background: white;
  color: var(--hh-bee-brown);
}

#rating-comment:focus {
  outline: none;
  border-color: var(--hh-sunset-amber);
  box-shadow: 0 0 0 3px rgba(251, 140, 0, 0.2);
}

#rating-comment::placeholder {
  color: var(--hh-bee-brown);
}

#submitRating {
  width: 100%;
  background: linear-gradient(135deg, var(--hh-sunset-amber) 0%, var(--hh-rich-gold) 100%);
  color: var(--hh-cream-white);
  border: none;
  padding: 1rem 2rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

#submitRating:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(245, 124, 0, 0.4);
}

#submitRating:active {
  transform: translateY(0);
}

/* Responsive Design */
@media (max-width: 768px) {
  .homehive-rating-section .container {
    grid-template-columns: 1fr;
    gap: 2rem;
    padding: 0 1rem;
  }
  
  .homehive-rating-section {
    padding: 2rem 0;
  }
  
  .rating-right {
    padding: 1.5rem;
  }
  
  #rating-stars .star {
    font-size: 1.7rem;
  }
  
  .average-stars i {
    font-size: 1.3rem;
  }
  
  .average-score {
    font-size: 1.7rem;
  }
}

/* Animation for star rating */
@keyframes starPop {
  0% { transform: scale(1); }
  50% { transform: scale(1.3); }
  100% { transform: scale(1.1); }
}

#rating-stars .star:hover {
  animation: starPop 0.3s ease;
}

/* Feedback slider animation */
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.feedback-slide.active {
  animation: slideIn 0.5s ease forwards;
}

/* Bee-themed decorative elements */
.rating-left::before {
  font-size: 2rem;
  position: absolute;
  left: -1rem;
  top: -1rem;
  opacity: 0.3;
  color: var(--hh-golden-shadow);
}

.rating-right::after {
  font-size: 2rem;
  position: absolute;
  right: -1rem;
  bottom: -1rem;
  opacity: 0.3;
  color: var(--hh-rich-gold);
}

.rating-left, .rating-right {
  position: relative;
}

/* Additional contrast improvements for dark background */
.rating-left .average-stars i,
.rating-left .feedback-slide .stars i {
  filter: brightness(1.2);
}
#submitRating {
    background-color: #FF6600; /* bright HomeHive orange */
    color: #fff;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

#submitRating:hover {
    background-color: #FF7F2A; /* slightly lighter orange on hover */
    transform: translateY(-2px);
}
</style>

</head>
<body>
<section class="homehive-rating-section">
  <div class="container">

    <!-- Left: Average Rating & Feedback Slider -->
    <div class="rating-left">
      <h3>Average Rating</h3>
    <div class="average-stars">
<?php
for($i=1; $i<=5; $i++){
    if($i <= floor($avgRating)){
        echo '<i class="fa-solid fa-star filled"></i>'; // full star
    } elseif($i == ceil($avgRating) && ($avgRating - floor($avgRating)) >= 0.5){
        echo '<i class="fa-solid fa-star-half-stroke filled"></i>'; // half star
    } else {
        echo '<i class="fa-regular fa-star"></i>'; // empty star
    }
}
?>

</div>

      <div class="average-score"><?php echo $avgRating;?> / 5</div>
      <div class="total-reviews"><?php echo $totalRatings;?> review<?php echo $totalRatings!=1?'s':''; ?></div>

      <h4>User Feedbacks</h4>
      <div class="feedback-slider">
        <?php foreach($feedbacks as $i=>$f): ?>
            <div class="feedback-slide <?php echo $i==0?'active':'';?>">
                <div class="stars">
                    <?php for($s=1;$s<=5;$s++){
                        echo $s <= $f['rating'] ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                    } ?>
                </div>
              <p><?php echo htmlspecialchars($f['comment'] ?? ''); ?></p>
<div class="anonymous">— <?= htmlspecialchars($f['Fname'] ?? 'Anonymous') ?></div>

            </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Right: User Feedback Submission -->
    <div class="rating-right">
      <h3><?php echo $userRating ? 'Your Feedback' : 'Submit Your Feedback'; ?></h3>
      <div id="rating-stars">
        <?php
        for($i=1;$i<=5;$i++){
            $class = ($userRating && $i <= $userRating)?'fa-solid':'fa-regular';
            echo "<i class='$class fa-star star' data-value='$i'></i>";
        }
        ?>
      </div>
     <textarea id="rating-comment" placeholder="Write your comment..."><?php echo htmlspecialchars($userComment ?? ''); ?></textarea>

      <button id="submitRating">
        <?php echo $userRating ? 'Update Feedback' : 'Submit Feedback'; ?>
      </button>
    </div>

  </div>
</section>


<?php include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


  <script src="assets/js/isotope.min.js"></script>
  <script src="assets/js/owl-carousel.js"></script>
  <script src="assets/js/counter.js"></script>
  <script src="assets/js/custom.js"></script>
  <script src="other/jquery/jquery.min.js"></script>
  <script src="other/tailwind/js/tailwind.min.js"></script>
  <script src="assets/js/index.js"></script>

  <script>
    AOS.init({
    duration: 1000,
     delay: 300,   
    easing: 'ease-in-out',
  });

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

let selectedRating = <?php echo $userRating ?? 0;?>;

function updateStars(rating){
    $('#rating-stars .star').each(function(){
        $(this).toggleClass('fa-solid', $(this).data('value')<=rating);
        $(this).toggleClass('fa-regular', $(this).data('value')>rating);
    });
}
$('#rating-stars .star').hover(function(){
    let val=$(this).data('value');
    $('#rating-stars .star').each(function(){
        $(this).toggleClass('fa-solid', $(this).data('value')<=val);
        $(this).toggleClass('fa-regular', $(this).data('value')>val);
    });
},function(){updateStars(selectedRating);}).click(function(){selectedRating=$(this).data('value'); updateStars(selectedRating);});

$('#submitRating').click(function(){
    let comment = $('#rating-comment').val().trim();

    // Check login first
    if(!<?php echo isset($_SESSION['user_idnumber']) ? 'true' : 'false'; ?>){
        Swal.fire({
            title: 'Login Required',
            text: 'You need to log in to submit a rating.',
            icon: 'info',
            confirmButtonColor: '#FF6600'
        });
        return;
    }

    // Check if a rating is selected
    if(selectedRating === 0){
        Swal.fire({
            title: 'Oops!',
            text: 'Please select a rating.',
            icon: 'warning',
            confirmButtonColor: '#FF6600'
        });
        return;
    }

    // Check if comment is empty
    if(comment === ''){
        Swal.fire({
            title: 'Oops!',
            text: 'Please enter a comment.',
            icon: 'warning',
            confirmButtonColor: '#FF6600'
        });
        return;
    }

    // Confirmation first with reversed buttons
    Swal.fire({
        title: 'Submit Feedback?',
        text: `You are about to submit a ${selectedRating}-star rating.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, submit',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#FF6600',
        cancelButtonColor: '#999999',
        reverseButtons: true
    }).then((result) => {
        if(result.isConfirmed){
            $.post('', {rating: selectedRating, comment: comment}, function(res){
                if(res.success){
                    Swal.fire({
                        title: 'Thank you!',
                        text: 'Your feedback has been submitted.',
                        icon: 'success',
                        confirmButtonColor: '#FF6600'
                    }).then(() => location.reload());
                }
                else{
                    Swal.fire({
                        title: 'Error',
                        text: res.message || 'Something went wrong.',
                        icon: 'error',
                        confirmButtonColor: '#FF6600'
                    });
                }
            }, 'json');
        }
    });
});


// Simple slider for feedbacks
let currentSlide=0;
let totalSlides=$('.feedback-slide').length;
setInterval(()=>{
    $('.feedback-slide').removeClass('active');
    currentSlide=(currentSlide+1)%totalSlides;
    $('.feedback-slide').eq(currentSlide).addClass('active');
},4000);

</script>

  </body>
</html>