<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();

require_once 'config.php'; // make sure $conn is available
date_default_timezone_set('Asia/Manila'); // ✅ Correct PH timezone
$property_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_idnumber'] ?? 0; // adjust to your session variable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    header('Content-Type: application/json');

    $user_id = $_SESSION['user_idnumber'] ?? 0; // tenant logged in
    $property_id_post = (int)($_POST['property_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    $rating = (int)($_POST['rating'] ?? 0);

    $response = ['success' => false, 'message' => ''];

    // Not logged in
    if ($user_id <= 0) {
        $response['message'] = 'You must be logged in to write a review.';
        echo json_encode($response);
        exit;
    }

    // Eligibility check: completed rental application
    $stmt = $conn->prepare("SELECT id FROM rentalapplications 
                            WHERE property_id = ? AND receiver_id = ? AND status = 'completed' LIMIT 1");
    $stmt->bind_param("ii", $property_id_post, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $response['message'] = 'You are not eligible to write a review for this property.';
        echo json_encode($response);
        exit;
    }
    $stmt->close();

    // Validate message & rating
    if (empty($message)) {
        $response['message'] = 'Review cannot be empty.';
        echo json_encode($response);
        exit;
    }
    if ($rating < 1 || $rating > 5) {
        $response['message'] = 'Please select a valid rating.';
        echo json_encode($response);
        exit;
    }

    // Generate review_id: 6000 + 6 random digits
    $review_id = 6000000000 + random_int(100000, 999999);

    // Insert into reviews
    $stmt = $conn->prepare("INSERT INTO reviews (review_id, property_id, user_id, message, rating, posted_at)
                            VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiisi", $review_id, $property_id_post, $user_id, $message, $rating);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Review posted successfully!';
    } else {
        $response['message'] = 'Error saving review: ' . $stmt->error;
    }
    $stmt->close();

    echo json_encode($response);
    exit;
}
$canWriteReview = false;

if ($property_id > 0 && $user_id > 0) {
    $stmt = $conn->prepare("SELECT id FROM rentalapplications 
                            WHERE property_id = ? AND receiver_id = ? AND status = 'completed' 
                            LIMIT 1");
    $stmt->bind_param("ii", $property_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $canWriteReview = true;
    }
    $stmt->close();
}
date_default_timezone_set('Asia/Manila'); // set PH timezone

$internalId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($internalId > 0) {

    // 1. First fetch property_id using primary id
    $stmt = $conn->prepare("SELECT property_id FROM properties WHERE id = ?");
    $stmt->bind_param("i", $internalId);
    $stmt->execute();
    $stmt->bind_result($property_id);
    $stmt->fetch();
    $stmt->close();

    if (!$property_id) {
        return;
    }
    $today = date('Y-m-d');
    $viewKey = "viewed_property_{$property_id}_{$today}";

    if (!isset($_SESSION[$viewKey])) {

        // 4. Log the view using user_idnumber session
        $viewerIdNumber = isset($_SESSION['user_idnumber']) ? $_SESSION['user_idnumber'] : NULL;

        $logStmt = $conn->prepare("
            INSERT INTO property_view_logs (property_id, viewer_id, viewed_at)
            VALUES (?, ?, NOW())
        ");
        $logStmt->bind_param("ii", $property_id, $viewerIdNumber);
        $logStmt->execute();
        $logStmt->close();

        // 5. Mark as viewed for the day
        $_SESSION[$viewKey] = true;
    }
}

$reviews = [];
$avg_rating = 0;
$total_reviews = 0;

if ($property_id > 0) {
    $stmtReviews = $conn->prepare("
        SELECT r.*, a.Fname, a.Mname, a.Lname, a.ProfilePic 
        FROM reviews r
        JOIN accountsdb a ON r.user_id = a.userID
        WHERE r.property_id = ?
        ORDER BY r.posted_at DESC
    ");
    $stmtReviews->bind_param("i", $property_id);
    $stmtReviews->execute();
    $resReviews = $stmtReviews->get_result();

    while ($row = $resReviews->fetch_assoc()) {
        $reviews[] = $row;
        $avg_rating += $row['rating'];
    }
    $stmtReviews->close();

    $total_reviews = count($reviews);
    if ($total_reviews > 0) {
        $avg_rating = round($avg_rating / $total_reviews, 1);
    }
}
// Now your rentalmessages check
$check = $conn->prepare("SELECT id FROM rentalmessages WHERE sender_id = ? AND receiver_id = ? AND property_id = ?");
$check->bind_param("iii", $sender_id, $receiver_id, $property_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Message already sent - reject new message to avoid spam
    $response['success'] = false;
    $response['message'] = "You have already sent a message for this property to this owner.";
    echo json_encode($response);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    header('Content-Type: application/json');

    $sender_id = $_SESSION['user_idnumber'] ?? null;
    $message = trim($_POST['message'] ?? '');
    $property_id_post = (int)($_POST['property_id'] ?? 0);
    $receiver_id = (int)($_POST['receiver_id'] ?? 0);

    $response = ['success' => false, 'message' => '', 'code' => '']; // added 'code' for JS handling

    // NOT LOGGED IN CHECK
    if (!$sender_id) {
        $response['message'] = 'User not logged in.';
        $response['code'] = 'not_logged_in';
        echo json_encode($response);
        exit;
    }

    // CHECK EMAIL VERIFIED STATUS
    $stmtVerify = $conn->prepare("SELECT verify_email FROM accountsdb WHERE userID = ?");
    $stmtVerify->bind_param("i", $sender_id);
    $stmtVerify->execute();
    $stmtVerify->bind_result($verify_email);
    if (!$stmtVerify->fetch()) {
        $response['message'] = 'User not found.';
        $stmtVerify->close();
        echo json_encode($response);
        exit;
    }
    $stmtVerify->close();
    
        if ((int)$receiver_id === (int)$sender_id) {
            $response['message'] = 'You cannot rent your own property.';
            echo json_encode($response);
            exit;
        }

    if ($verify_email != 1) {
        $response['message'] = 'You must verify your email before applying.';
        $response['code'] = 'not_verified';
        echo json_encode($response);
        exit;
    }

    if (empty($message)) {
        $response['message'] = 'Message cannot be empty.';
        echo json_encode($response);
        exit;
    }
    if (!$property_id_post || !$receiver_id) {
        $response['message'] = 'Invalid property or receiver ID.';
        echo json_encode($response);
        exit;
    }

   

    // Check receiver exists
    $stmtCheck = $conn->prepare("SELECT userID FROM accountsdb WHERE userID = ?");
    $stmtCheck->bind_param("i", $receiver_id);
    $stmtCheck->execute();
    $stmtCheck->store_result();
    if ($stmtCheck->num_rows === 0) {
        $response['message'] = "Receiver ID {$receiver_id} does not exist.";
        $stmtCheck->close();
        echo json_encode($response);
        exit;
    }
    $stmtCheck->close();

    // Check duplicate message
    $checkDup = $conn->prepare("SELECT id FROM rentalmessages WHERE sender_id = ? AND receiver_id = ? AND property_id = ?");
    $checkDup->bind_param("iii", $sender_id, $receiver_id, $property_id_post);
    $checkDup->execute();
    $checkDup->store_result();
    if ($checkDup->num_rows > 0) {
        $response['message'] = "You have already sent a message for this property to this owner.";
        echo json_encode($response);
        $checkDup->close();
        exit;
    }
    $checkDup->close();

// ✅ Insert into rentalmessages
$stmtInsert = $conn->prepare("INSERT INTO rentalmessages (sender_id, receiver_id, property_id, message, sent_at) VALUES (?, ?, ?, ?, NOW())");
$stmtInsert->bind_param("iiis", $sender_id, $receiver_id, $property_id_post, $message);



if ($stmtInsert->execute()) {

    // ✅ Fetch sender full name from accountsdb
    $stmtName = $conn->prepare("SELECT Fname, Mname, Lname FROM accountsdb WHERE userID = ?");
    $stmtName->bind_param("i", $sender_id);
    $stmtName->execute();
    $stmtName->bind_result($Fname, $Mname, $Lname);
    $stmtName->fetch();
    $stmtName->close();

    // Build full name (skip middle if empty)
    $from_name = trim($Fname . ' ' . (!empty($Mname) ? $Mname . ' ' : '') . $Lname);

    // ✅ Generate inbox_id pattern 5000 + 6 random digits
    $inbox_id = '5000' . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // Title stays New Inquiry
    $title = 'New Inquiry';

    // Message content
    $inbox_message = "Someone wants to rent your property. Please check your messages.\n\nMessage:\n" . $message;

    // Current date/time PH timezone
    $created_at = date('Y-m-d H:i:s');

    // Insert into inbox
    $stmtInbox = $conn->prepare("INSERT INTO inbox (inbox_id, `from`, receiver_id, title, message, created_at, status, type)
                                VALUES (?, ?, ?, ?, ?, ?, 'unread', 'primary')");
    $stmtInbox->bind_param("isisss", $inbox_id, $from_name, $receiver_id, $title, $inbox_message, $created_at);
    $stmtInbox->execute();
    $stmtInbox->close();

    $response['success'] = true;
    $response['message'] = "Message sent successfully!";
} else {
    $response['message'] = "Error saving message: " . $stmtInsert->error;
}
$stmtInsert->close();

echo json_encode($response);
exit;
}

// === END APPLY BUTTON SECTION ===


// Get property ID from URL
$property_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch main property and owner info
$query = "
SELECT 
  a.userID, a.Fname, a.Lname, a.email, a.phone, a.Birthdate, a.city, a.state, a.streetAddress, a.postal, 
  a.UploadedIDType, a.UploadIDPhoto, a.ProfilePic,
  p.property_id AS actual_property_id, a.pRole, a.sRole, a.created_at AS account_created_at,
  p.id AS property_id, p.owner_id, p.name AS property_name, p.location, p.price, p.type, p.description, 
  p.floors, p.parking, p.bedrooms, p.bathrooms, p.kitchen, p.latitude, p.longitude, 
  p.created_at AS property_created_at, p.status,
  v.document_type, v.document_path, v.certificate_path, v.created_at AS document_created_at
FROM accountsdb a
JOIN properties p ON a.userID = p.owner_id
LEFT JOIN verificationdocuments v ON a.userID = v.owner_id
WHERE p.id = ?";


$stmt = $conn->prepare($query);
$stmt->bind_param("i", $property_id);
$stmt->execute();
$result = $stmt->get_result();
$property = $result->fetch_assoc();

if (!$property) {
    echo "Property not found.";
    exit;
}

// Fetch property images
$imageQuery = "SELECT image_path FROM apartmentimages WHERE apartment_id = ?";
$stmt = $conn->prepare($imageQuery);
$stmt->bind_param("i", $property_id);
$stmt->execute();
$imageResult = $stmt->get_result();
$images = [];
while ($image = $imageResult->fetch_assoc()) {
    $images[] = 'userdashboard/uploads/properties/' . basename($image['image_path']);
}

// Default profile picture fallback
$profilePicPath = !empty($property['ProfilePic'])
    ? "/userdashboard/uploads/profile_pics/" . basename($property['ProfilePic'])
    : "https://static.vecteezy.com/system/resources/thumbnails/057/350/992/small/flat-orange-male-avatar-icon-png.png";

// Fetch up to 4 random recommended properties (excluding current one)
$recommended = [];
$recQuery = "SELECT id, name, location, price, bedrooms, bathrooms FROM properties WHERE id != ? AND status = 'approved' ORDER BY RAND() LIMIT 4";

$stmt = $conn->prepare($recQuery);
$stmt->bind_param("i", $property_id);
$stmt->execute();
$recResult = $stmt->get_result();

while ($row = $recResult->fetch_assoc()) {
    // Get one image per recommended property
    $imgStmt = $conn->prepare("SELECT image_path FROM apartmentimages WHERE apartment_id = ? LIMIT 1");
    $imgStmt->bind_param("i", $row['id']);
    $imgStmt->execute();
    $imgRes = $imgStmt->get_result();
    
    if ($img = $imgRes->fetch_assoc()) {
        $row['image'] = 'userdashboard/uploads/properties/' . basename($img['image_path']);
    } else {
        $row['image'] = 'https://via.placeholder.com/400x300?text=No+Image';
    }

    $recommended[] = $row;
}

// Fetch total views for this property
$totalViews = 0;
if (!empty($property['actual_property_id'])) {
    $stmtViews = $conn->prepare("SELECT COUNT(*) FROM property_view_logs WHERE property_id = ?");
    $stmtViews->bind_param("i", $property['actual_property_id']);
    $stmtViews->execute();
    $stmtViews->bind_result($totalViews);
    $stmtViews->fetch();
    $stmtViews->close();
}


// User full name for message sender display
$user_first_name = $_SESSION['first_name'] ?? 'Guest';
$user_last_name = $_SESSION['last_name'] ?? '';
$user_fullname = trim("$user_first_name $user_last_name");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Property Details - <?php echo htmlspecialchars($property['property_name']); ?></title>

    <!-- Your existing CSS and JS links -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/fontawesome.css" />
    <link rel="stylesheet" href="assets/css/viewProperty.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <style>
      /* === Floating Apply Button Styles === */
      #applyBtn {
        position: fixed;
        bottom: 25px;
        right: 25px;
        background: #fb8c00;
        color: white;
        padding: 14px 28px;
        border-radius: 50px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(251, 140, 0, 0.6);
        border: none;
        font-size: 18px;
        z-index: 9999;
        transition: background-color 0.3s ease;
      }
      #applyBtn:hover {
        background: #f57c00;
        box-shadow: 0 6px 14px rgba(245, 124, 0, 0.8);
      }
    </style>
</head>
<body>
<?php include 'includes/disclaimer-pictures.php' ?>
 <div class="container">
    <div class='button-container'>
    <a href="javascript:window.history.back();" class="back-btn">
    <i class="fas fa-arrow-left"></i> Back
</a>
</div>
<h1><?php echo htmlspecialchars($property['property_name']); ?></h1>

<!-- Property Images Section -->
<h3>Property Images</h3>
<div class="image-gallery-container">
    <button class="nav-btn left" onclick="showPrevImage()">
        <i class="fas fa-chevron-left"></i>
    </button>

    <img id="mainDisplay" src="<?php echo $images[0]; ?>" alt="Main Property Image" class="main-image" />

    <button class="nav-btn right" onclick="showNextImage()">
        <i class="fas fa-chevron-right"></i>
    </button>

    <div class="thumbnail-row">
        <?php foreach ($images as $index => $image_url): ?>
            <img src="<?php echo $image_url; ?>" alt="Thumbnail" class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" onclick="selectMainImage(this, '<?php echo $image_url; ?>', <?php echo $index; ?>)" />
        <?php endforeach; ?>
    </div>
</div>

<!-- Property Info Section -->
<div class="property-info">
    <div class="property-details">
        <h4>Property Details</h4>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
            <p class="highlighted-owner" style="margin: 0; text-align: center;">
                <img src="<?php echo htmlspecialchars($profilePicPath); ?>" alt="Profile Picture" width="50" height="50" style="border-radius: 50%; vertical-align: middle; margin-right: 8px;" />
                <strong><?php echo htmlspecialchars($property['Fname'] . ' ' . $property['Lname']); ?></strong>
            </p>
            <p class="highlighted-price" style="margin: 0;">
                <i class="fas fa-money-bill-wave"></i> Price Rate: <strong>₱<?php echo number_format(htmlspecialchars($property['price']), 2); ?></strong>
            </p>
        </div>
    </div>
    <p class="hh-address"><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> <?php echo htmlspecialchars($property['location']); ?></p>
    <p class="hh-description"><strong>Description:</strong> <?php echo htmlspecialchars($property['description']); ?></p>
<div class="features-grid">
    <div class="feature-cube">
        <i class="fas fa-bed" style="color: #8e44ad;"></i>
        <strong>Bedrooms:</strong> <?php echo htmlspecialchars($property['bedrooms']); ?>
    </div>

    <div class="feature-cube">
        <i class="fas fa-home" style="color: #e67e22;"></i>
        <strong>Floors:</strong> <?php echo htmlspecialchars($property['floors']); ?>
    </div>

    <div class="feature-cube">
        <i class="fas fa-bath" style="color: #3498db;"></i>
        <strong>Bathrooms:</strong> <?php echo htmlspecialchars($property['bathrooms']); ?>
    </div>

    <div class="feature-cube">
        <i class="fas fa-car" style="color: #16a085;"></i>
        <strong>Parking:</strong> <?php echo htmlspecialchars($property['parking']); ?>
    </div>

    <div class="feature-cube">
        <i class="fas fa-utensils" style="color: #e67e22;"></i>
        <strong>Kitchen:</strong> <?php echo htmlspecialchars($property['kitchen']); ?>
    </div>

    <div class="feature-cube">
        <i class="fas fa-house" style="color: #e67e22;"></i>
        <strong>Type:</strong> <?php echo htmlspecialchars($property['type']); ?>
    </div>

    <!-- 🆕 Views feature cube -->
   <!-- 🆕 Views feature cube -->
<div class="feature-cube">
    <i class="fas fa-eye" style="color: #5D4037;"></i>
    <strong>Views:</strong> <?= $totalViews ?>
</div>

</div>

</div>

<!-- Map and Nearby Places -->
<div style="display: flex; gap: 20px; align-items: flex-start; margin-top: 20px;">
  <div id="map" style="width: 60%; height: 500px; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);"></div>

  <div id="place-list" style="padding: 10px; background: #fff; flex: 1; border-radius: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
    <h3 style="display: flex; align-items: center; gap: 8px; color: #5D4037;">
      <span id="info-btn" style="cursor: pointer; background: #FB8C00; border-radius: 50%; width: 25px; height: 25px; display: inline-flex; justify-content: center; align-items: center; font-weight: bold; color: white;" title="Click for help">?</span>
      Nearby Places Around This Property:
    </h3>
    <ul id="places" style="list-style: none; padding: 0; margin: 0;"></ul>
  </div>
</div>
<!-- Reviews Section -->
<section class="hh-reviews">
  <div class="hh-reviews-header">
    <div class="hh-reviews-summary">
      <h2><i class="fas fa-star text-amber"></i> <?= $avg_rating ?: '0.0' ?></h2>
      <div class="stars">
        <?php 
        $fullStars = floor($avg_rating);
        $halfStar = ($avg_rating - $fullStars) >= 0.5;
        for ($i = 0; $i < 5; $i++) {
          if ($i < $fullStars) echo '<i class="fas fa-star text-amber"></i>';
          elseif ($halfStar && $i == $fullStars) echo '<i class="fas fa-star-half-alt text-amber"></i>';
          else echo '<i class="far fa-star"></i>';
        }
        ?>
      </div>
      <p class="hh-reviews-sub"><?= $total_reviews ?> review<?= $total_reviews != 1 ? 's' : '' ?></p>
    </div>
<button id="submitReviewBtn" 
        class="btn btn-primary"
        data-allowed="<?= $canWriteReview ? '1' : '0' ?>"
        style="background-color:#ff9800; color:#fff; border:none; padding:8px 16px; border-radius:6px; font-weight:600;">
  Write a Review
</button>

  </div>

  <?php if ($total_reviews > 0): ?>
    <?php foreach ($reviews as $review): ?>
      <div class="hh-review">
        <div class="hh-review-user">
          <img src="<?= !empty($review['ProfilePic']) 
                       ? '/userdashboard/uploads/profile_pics/'.basename($review['ProfilePic']) 
                       : 'https://static.vecteezy.com/system/resources/thumbnails/057/350/992/small/flat-orange-male-avatar-icon-png.png' ?>" 
               alt="User" />
          <div>
            <strong><?= htmlspecialchars($review['Fname'].' '.(!empty($review['Mname']) ? $review['Mname'].' ' : '').$review['Lname']) ?></strong>
            <div class="stars small">
              <?php 
              $fullStars = floor($review['rating']);
              $halfStar = ($review['rating'] - $fullStars) >= 0.5;
              for ($i = 0; $i < 5; $i++) {
                if ($i < $fullStars) echo '<i class="fas fa-star text-amber"></i>';
                elseif ($halfStar && $i == $fullStars) echo '<i class="fas fa-star-half-alt text-amber"></i>';
                else echo '<i class="far fa-star"></i>';
              }
              ?>
            </div>
            <small><?= date('F d, Y', strtotime($review['posted_at'])) ?></small>
          </div>
        </div>
        <p class="hh-review-text"><?= nl2br(htmlspecialchars($review['message'])) ?></p>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No reviews yet. Be the first to write one!</p>
  <?php endif; ?>
</section>


<!-- Recommended Properties -->
<?php if (!empty($recommended)): ?>
<section class="hh-recommend-section">
  <h2 class="hh-recommend-heading">Properties You May Also Like</h2>
  <div class="hh-recommend-grid">
    <?php foreach ($recommended as $rec): ?>
      <div class="hh-property-card" onclick="window.location.href='viewProperty.php?id=<?= $rec['id'] ?>'" title="Click to view this property">
        <img src="<?= htmlspecialchars($rec['image']) ?>" alt="<?= htmlspecialchars($rec['name']) ?>" class="hh-card-image" />
        <div class="hh-card-info">
          <h3><?= htmlspecialchars($rec['name']) ?></h3>
          <p><i class="fas fa-money-bill-wave" style="color: #27ae60;"></i> ₱<?= number_format($rec['price']) ?> / month</p>
          <p><i class="fas fa-map-marker-alt" style="color: #e74c3c;"></i> <?= htmlspecialchars($rec['location']) ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- === APPLY BUTTON SECTION: Floating Apply to Rent Button === -->
<button id="applyBtn" aria-label="Apply to Rent">Apply to Rent</button>

</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>


window.onload = function () {
    var latitude = <?php echo $property['latitude']; ?>;
    var longitude = <?php echo $property['longitude']; ?>;
    var propertyname = "<?php echo htmlspecialchars($property['property_name']); ?>";

    var map = L.map('map').setView([latitude, longitude], 15);
    const redIcon = new L.Icon({
          iconUrl: 'assets/images/house-pin.png', // Updated icon path
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        iconSize: [50, 50],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    L.marker([latitude, longitude], { icon: redIcon }).addTo(map)
        .bindPopup('<b>' + propertyname + '</b>')
        .openPopup();

    let currentPlaceMarker = null;

    const amenityIcons = {
        mall: "fa-store",
        cafe: "fa-mug-hot",
        restaurant: "fa-utensils",
        fast_food: "fa-burger",
        convenience: "fa-shop",
        supermarket: "fa-cart-shopping",
        bank: "fa-university",
        pharmacy: "fa-prescription-bottle",
        park: "fa-tree"
    };

    const overpassUrl = 'https://overpass-api.de/api/interpreter';
    const query = `
        [out:json];
        (
            node["amenity"~"mall|cafe|restaurant|park|supermarket|bank|pharmacy|fast_food|convenience"](around:100,${latitude},${longitude});
        );
        out body;
    `;

    fetch(overpassUrl, {
        method: 'POST',
        body: query,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    })
    .then(res => res.json())
    .then(data => {
        const list = document.getElementById("places");
        list.style.maxHeight = "400px";
        list.style.overflowY = "auto";
        list.style.paddingRight = "8px";

        list.innerHTML = '';

        if (!data.elements || data.elements.length === 0) {
            list.innerHTML = `<li style="color: #FB8C00; font-style: italic; padding: 10px;">
               No nearby places found within a 100-meter radius of this property.
            </li>`;
            return;
        }

        data.elements.forEach(el => {
            if (el.lat && el.lon) {
                const name = el.tags.name || "Unnamed";
                const type = el.tags.amenity || "POI";
                const iconClass = amenityIcons[type] || "fa-map-marker-alt";

                const li = document.createElement("li");
                li.innerHTML = `
<button style="
    width: 100%;
    text-align: left;
    margin: 8px 0;
    padding: 12px 16px;
    border: 1px solid #ccc;
    background: #FFCC80;
    cursor: pointer;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    font-family: Arial, sans-serif;
    display: flex;
    align-items: center;
    gap: 12px;
">
    <div style="
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #FFF3E0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: #FB8C00;
        flex-shrink: 0;
    ">
        <i class="fas ${iconClass}"></i>
    </div>
    <div>
        <div style="font-weight: 600; font-size: 1rem; color: #5D4037;">${name}</div>
        <small style="color: #5D4037; font-style: italic;">(${type})</small>
    </div>
</button>
`;

                li.addEventListener("click", () => {
                    if (currentPlaceMarker) {
                        map.removeLayer(currentPlaceMarker);
                    }
                    currentPlaceMarker = L.marker([el.lat, el.lon]).addTo(map);
                    map.setView([el.lat, el.lon], 20);
                    currentPlaceMarker.bindPopup(`<b>${name}</b><br>${type}`).openPopup();
                });

                list.appendChild(li);
            }
        });
    })
    .catch(err => {
        console.error("Overpass error:", err);
        const list = document.getElementById("places");
        list.innerHTML = `<li style="color: red; font-style: italic; padding: 10px;">
            Error loading nearby places. Please try again later.
        </li>`;
    });

    document.getElementById('info-btn').addEventListener('click', () => {
      Swal.fire({
        title: 'Nearby Places Info',
        text: `This section shows nearby amenities within a 100-meter radius of the property—such as cafes, restaurants, parks, banks, and more. If nothing appears, it could be due to internet issues or temporary data unavailability.`,
        icon: 'info',
        confirmButtonText: 'Got it!',
        confirmButtonColor: '#FB8C00',
        customClass: {
          confirmButton: 'no-border-button'
        }
      });
    });

    const images = <?php echo json_encode($images); ?>;
    let currentIndex = 0;
    const mainImage = document.getElementById('mainDisplay');

    window.selectMainImage = function(el, url, index) {
        currentIndex = index;
        fadeToImage(url);
        updateThumbnails(index);
    }

    window.showPrevImage = function() {
        currentIndex = (currentIndex - 1 + images.length) % images.length;
        fadeToImage(images[currentIndex]);
        updateThumbnails(currentIndex);
    }

    window.showNextImage = function() {
        currentIndex = (currentIndex + 1) % images.length;
        fadeToImage(images[currentIndex]);
        updateThumbnails(currentIndex);
    }

    function fadeToImage(url) {
        mainImage.classList.add('fade-out');

        setTimeout(() => {
            mainImage.src = url;
            mainImage.classList.remove('fade-out');
            mainImage.classList.add('fade-in');

            setTimeout(() => {
                mainImage.classList.remove('fade-in');
            }, 500);
        }, 300);
    }

    function updateThumbnails(index) {
        const thumbnails = document.querySelectorAll('.thumbnail');
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        thumbnails[index].classList.add('active');
    }

document.getElementById('submitReviewBtn').addEventListener('click', () => {
  const allowed = document.getElementById('submitReviewBtn').dataset.allowed;
  const propertyId = <?= (int)$property_id ?>;

  if (allowed === '1') {
    Swal.fire({
      title: 'Write your review',
      html: `
        <div style="margin-bottom:8px;">
          <div id="starRating">
            ${[1,2,3,4,5].map(i => `<i class="fa fa-star" data-star="${i}" style="font-size:24px;color:#ccc;cursor:pointer;"></i>`).join('')}
          </div>
        </div>
        <textarea id="reviewText" class="swal2-textarea" placeholder="Write your review..."></textarea>
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: 'Submit',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#ff9800', // orange
      cancelButtonColor: '#ff9800',  // orange
      didOpen: () => {
        let selectedRating = 0;
        const stars = document.querySelectorAll('#starRating .fa-star');
        stars.forEach(star => {
          star.addEventListener('click', () => {
            selectedRating = star.dataset.star;
            stars.forEach(s => s.style.color = '#ccc');
            for (let i=0;i<selectedRating;i++) stars[i].style.color = '#ffcc00';
            document.getElementById('starRating').dataset.rating = selectedRating;
          });
        });
      },
      preConfirm: () => {
        const message = document.getElementById('reviewText').value.trim();
        const rating = document.getElementById('starRating').dataset.rating || 0;
        if (!message) {
          Swal.showValidationMessage('Please enter your review');
          return false;
        }
        if (rating == 0) {
          Swal.showValidationMessage('Please select a rating');
          return false;
        }
        return {message, rating};
      }
    }).then(result => {
      if (result.isConfirmed) {
        fetch(window.location.href, {
          method: 'POST',
          headers: {'Content-Type': 'application/x-www-form-urlencoded'},
          body: `action=submit_review&property_id=${propertyId}&message=${encodeURIComponent(result.value.message)}&rating=${result.value.rating}`
        })
        .then(res => res.json())
        .then(data => {
          // ✅ normal toast
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: data.success ? 'success' : 'error',
            title: data.success ? 'Review Posted!' : 'Error',
            text: data.message,
            showConfirmButton: false,
            timer: 3000
          });
        });
      }
    });
  } else {
    Swal.fire({
      icon: 'warning',
      title: 'Oops!',
     text: 'Review submission is only available to tenants with a completed rental history for this property. No eligible record was found for your account.',

      confirmButtonColor: '#ff9800'
    });
  }
});

const applyBtn = document.createElement('button');
applyBtn.id = 'applyBtn';
applyBtn.textContent = <?php echo ($_SESSION['user_idnumber'] ?? 0) == $property['owner_id'] ? "'Manage Property'" : "'Apply to Rent'"; ?>;
applyBtn.style.position = 'fixed';
applyBtn.style.bottom = '25px';
applyBtn.style.right = '25px';
applyBtn.style.background = '#fb8c00';
applyBtn.style.color = 'white';
applyBtn.style.padding = '14px 28px';
applyBtn.style.borderRadius = '50px';
applyBtn.style.fontWeight = '700';
applyBtn.style.cursor = 'pointer';
applyBtn.style.boxShadow = '0 4px 12px rgba(251, 140, 0, 0.6)';
applyBtn.style.border = 'none';
applyBtn.style.fontSize = '18px';
applyBtn.style.zIndex = '9999';
document.body.appendChild(applyBtn);

// Redirect if Manage Property
if (applyBtn.textContent === "Manage Property") {
    applyBtn.addEventListener("click", () => {
        window.location.href = "userdashboard/myproperties.php";
    });
}

applyBtn.addEventListener('click', () => {
  if (applyBtn.textContent === "Manage Property") {
    // Redirect only
    window.location.href = "userdashboard/myproperties.php";
  } else {
    // Show SweetAlert2 only when Apply to Rent
    Swal.fire({
      title: 'Send Rental Application',
      html: `
        <p><strong>Property:</strong> <?php echo addslashes(htmlspecialchars($property['property_name'])); ?></p>
        <p><strong>Sender:</strong> <?php echo addslashes(htmlspecialchars($user_fullname)); ?></p>
        <select id="messageSelect" class="swal2-select" style="width: 80%; max-width: 800%; box-sizing: border-box; padding: 10px; font-size: 16px; margin-top: 12px;">
          <option value="" disabled selected>-- Select a message --</option>
          <option value="I’m interested in this property.">I’m interested in this property.</option>
          <option value="I want to rent this property.">I want to rent this property.</option>
          <option value="This property is good for me.">This property is good for me.</option>
          <option value="I’m ready to apply.">I’m ready to apply.</option>
          <option value="I want to move in soon.">I want to move in soon.</option>
          <option value="I want to rent this place.">I want to rent this place.</option>
          <option value="This property is perfect for me.">This property is perfect for me.</option>
        </select>
      `,
      showCancelButton: true,
      confirmButtonText: 'Send',
      confirmButtonColor: '#fb8c00',
      preConfirm: () => {
        const message = document.getElementById('messageSelect').value;
        if (!message) {
          Swal.showValidationMessage('Please select a message');
          return false;
        }
        return message;
      }
    }).then((result) => {
      if (result.isConfirmed) {
        // Show loading while sending
        Swal.fire({
          title: 'Sending...',
          text: 'Please wait while your message is being sent.',
          allowOutsideClick: false,
          allowEscapeKey: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        fetch(window.location.href, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: new URLSearchParams({
            action: 'send_message',
            message: result.value,
            property_id: '<?php echo (int)$property['property_id']; ?>',
            receiver_id: '<?php echo (int)$property['owner_id']; ?>'
          })
        })
        .then(res => res.json())
        .then(data => {
          Swal.close(); // close loading state
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: data.message,
              showCancelButton: true,
              confirmButtonText: 'Go to Chat',
              cancelButtonText: 'Cancel',
              confirmButtonColor: '#fb8c00',
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = `https://homehiveph.site/userdashboard/chat?receiver_id=<?php echo (int)$property['owner_id']; ?>`;
              }
            });
          } else {
            if (data.code === 'not_logged_in') {
              Swal.fire({
                icon: 'warning',
                title: 'Sign In Required',
                text: 'You must sign in to apply for this property.',
                footer: `Don't have an account yet? <a href="https://homehiveph.site/createaccount" target="_blank" style="color:#FB8C00;">Sign up</a>`,
                showCancelButton: true,
                confirmButtonText: 'Sign In',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#fb8c00',
              }).then(result => {
                if (result.isConfirmed) {
                  window.location.href = 'https://homehiveph.site/loginuser.php';
                }
              });
            } 
            else if (data.code === 'not_verified') {
              Swal.fire({
                icon: 'info',
                title: 'Email Verification Required',
                text: 'Please verify your email to apply for this property.',
                showCancelButton: true,
                confirmButtonText: 'Verify Now',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#fb8c00',
              }).then(result => {
                if (result.isConfirmed) {
                  window.location.href = 'https://homehiveph.site/userdashboard/dashboard-myprofile.php';
                }
              });
            }
            else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                footer: `<a href="https://homehiveph.site/about.php?article=article1" target="_blank" rel="noopener" style="color: #fb8c00; text-decoration: underline;">Need help? Click here</a>`,
                confirmButtonText: 'I understand',
                confirmButtonColor: '#fb8c00',
                showCancelButton: false
              });
            }
          }
        })
        .catch(() => {
          Swal.close();
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An unexpected error occurred.',
            confirmButtonColor: '#fb8c00'
          });
        });
      }
    });
  }
});


};
</script>

</body>
</html>
