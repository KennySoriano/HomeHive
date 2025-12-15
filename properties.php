<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start(); 
require_once 'config.php';
include 'includes/session_checker.php';



// Pagination settings
$itemsPerPage = 6; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $itemsPerPage;

// Get search and filter inputs
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$typeSort = isset($_GET['type_sort']) ? $_GET['type_sort'] : '';
$priceRange = isset($_GET['price_range']) ? $_GET['price_range'] : '';
$locationSort = isset($_GET['location_sort']) ? $_GET['location_sort'] : '';
$parkingSort = isset($_GET['parking_sort']) ? $_GET['parking_sort'] : '';
$kitchenSort = isset($_GET['kitchen_sort']) ? $_GET['kitchen_sort'] : '';
$floorsSort = isset($_GET['floors_sort']) ? $_GET['floors_sort'] : '';

// Base filter conditions
$where = "p.status = 'Approved'";
if (!empty($search)) {
    $searchSafe = $conn->real_escape_string($search);
    $where .= " AND (p.name LIKE '%$searchSafe%' OR p.location LIKE '%$searchSafe%')";
}
if (!empty($typeSort)) $where .= " AND p.type = '".$conn->real_escape_string($typeSort)."'";
if (!empty($locationSort)) $where .= " AND p.location LIKE '%".$conn->real_escape_string($locationSort)."%'";
if (!empty($parkingSort)) $where .= " AND p.parking = '".$conn->real_escape_string($parkingSort)."'";
if (!empty($kitchenSort)) $where .= " AND p.kitchen = '".$conn->real_escape_string($kitchenSort)."'";
if (!empty($floorsSort)) {
    if ($floorsSort === '2+') $where .= " AND p.floors > 2";
    else $where .= " AND p.floors = '".$conn->real_escape_string($floorsSort)."'";
}

// Price sorting
$orderBy = " ORDER BY RAND()";
if (!empty($priceRange)) {
    $priceRangeClean = str_replace([',', ' ', '~'], '', trim($priceRange));

    if (preg_match('/^(\d+)-(\d+)$/', $priceRangeClean, $matches)) {
        $min = (int)$matches[1];
        $max = (int)$matches[2];
        if ($min > $max) [$min, $max] = [$max, $min];
        $orderBy = " ORDER BY (p.price BETWEEN $min AND $max) DESC, p.price ASC";
    } elseif (preg_match('/^(\d+)\+$/', $priceRangeClean, $matches)) {
        $min = (int)$matches[1];
        $orderBy = " ORDER BY (p.price >= $min) DESC, p.price ASC";
    } elseif (preg_match('/^\d+$/', $priceRangeClean)) {
        $exact = (int)$priceRangeClean;
        $orderBy = " ORDER BY (p.price = $exact) DESC, ABS(p.price - $exact) ASC, p.price ASC";
    }
}

// 1️⃣ Get paginated property IDs first
$idSql = "SELECT p.id 
          FROM properties p
          WHERE $where
          $orderBy
          LIMIT $offset, $itemsPerPage";

$idResult = $conn->query($idSql);
$propertyIds = [];
if ($idResult && $idResult->num_rows > 0) {
    while ($row = $idResult->fetch_assoc()) {
        $propertyIds[] = $row['id'];
    }
}

$properties = [];
if (!empty($propertyIds)) {
    $idsStr = implode(',', $propertyIds);

    // 2️⃣ Fetch full property info + images
    $sql = "SELECT p.*, a.image_path 
            FROM properties p
            LEFT JOIN apartmentimages a ON p.id = a.apartment_id
            WHERE p.id IN ($idsStr)
            ORDER BY FIELD(p.id, $idsStr)"; // preserve original order

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
           if (!isset($properties[$id])) {
    $properties[$id] = [
        'id' => $id,
        'property_id' => $row['property_id'],
        'name' => $row['name'],
        'location' => $row['location'],
        'price' => number_format($row['price'], 2),
        'type' => $row['type'],
        'description' => $row['description'],
        'bedrooms' => $row['bedrooms'],
        'bathrooms' => $row['bathrooms'],
        'kitchen' => $row['kitchen'],
        'parking' => $row['parking'],
        'owner_id' => $row['owner_id'],
        'floors' => $row['floors'],
        'images' => [],
        'latitude' => $row['latitude'],
        'longitude' => $row['longitude'],
    ];
}


            $image_path = !empty($row['image_path'])
                ? 'userdashboard/uploads/properties/' . basename($row['image_path'])
                : 'userdashboard/uploads/properties/default-image.jpg';

            $properties[$id]['images'][] = $image_path;
        }
    }
}

// 3️⃣ Count total items for pagination
$countSql = "SELECT COUNT(*) AS total FROM properties p WHERE $where";
$countResult = $conn->query($countSql);
$totalItems = ($countResult && $row = $countResult->fetch_assoc()) ? (int)$row['total'] : 0;
$totalPages = ceil($totalItems / $itemsPerPage);



$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <? include 'meta.php'; ?>
    <link href="other/tailwind/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/index.css">
        <link rel="stylesheet" href="assets/css/properties.css">
    <link rel="stylesheet" href="assets/css/dropdownmenu.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        
    <title>Properties List</title>
</head>
<body>
    <?php include 'includes/disclaimer-pictures.php' ?>
 <?php include 'includes/landingpage-navbar.php' ?>


  <button id="showMapBtn" style="
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 999;
    padding: 12px 24px;
    background-color:#FB8C00;
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    animation: bounce 1.5s infinite;
" onclick="window.location.href='properties-map.php'">
    <i class="fas fa-map" style="font-size: 20px;"></i> Show Map
</button>


<div class="page-heading header-text">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Properties</h3>
                </div>
            </div>
        </div>
    </div>

<div class="filters">
    
    <form method="GET" action="">
        <div class="search-box" style="margin-bottom: 10px;">
            <input type="text" autocomplete="off" name="search" placeholder="🔍  Search by Name" value="<?= htmlspecialchars($search) ?>" />
        </div>
        <div class="sorting-options">
       
           
    <!-- Sort by Type -->
    <input list="property_types" autocomplete="off"name="type_sort" id="type_sort" class="filter-input" placeholder="🏠 House Type" onchange="this.form.submit()" value="<?= $typeSort ?>">
    <datalist id="property_types">
        <option value="Studio" <?= $typeSort == 'Studio' ? 'selected' : '' ?>>Studio (Single Space)</option>
        <option value="1-Bedroom" <?= $typeSort == '1-Bedroom' ? 'selected' : '' ?>>1-Bedroom (Compact)</option>
        <option value="2-Bedroom" <?= $typeSort == '2-Bedroom' ? 'selected' : '' ?>>2-Bedroom (Cozy)</option>
        <option value="3-Bedroom" <?= $typeSort == '3-Bedroom' ? 'selected' : '' ?>>3-Bedroom (Spacious)</option>
        <option value="Condo" <?= $typeSort == 'Condo' ? 'selected' : '' ?>>Condo (Modern Living)</option>
    </datalist>

    <!-- Sort by Price Range -->
    <input list="price_ranges" autocomplete="off"name="price_range" id="price_range" class="filter-input" placeholder="💵 Price" onchange="this.form.submit()" value="<?= $priceRange ?>">
    <datalist id="price_ranges">
        <option value="1000-5000" <?= $priceRange == '1000-5000' ? 'selected' : '' ?>>₱5,000 and below (Budget-Friendly)</option>
        <option value="5001-10000" <?= $priceRange == '5001-10000' ? 'selected' : '' ?>>₱5,001 ~ ₱10,000 (Affordable)</option>
        <option value="10001-15000" <?= $priceRange == '10001-15000' ? 'selected' : '' ?>>₱10,001 ~ ₱15,000 (Mid-Range)</option>
        <option value="15001-30000" <?= $priceRange == '15001-30000' ? 'selected' : '' ?>>₱15,001 ~ ₱30,000 (Comfortable)</option>
        <option value="30000+" <?= $priceRange == '30000+' ? 'selected' : '' ?>>₱30,000 and above (Premium)</option>
    </datalist>

    <input list="locations" autocomplete="off" name="location_sort" id="location_sort" class="filter-input" placeholder="📍 Location" onchange="this.form.submit()" value="<?= $locationSort ?>">
    <datalist id="locations">
        <!-- Luzon -->
        <option value="Manila" <?= $locationSort == 'Manila' ? 'selected' : '' ?>>Luzon - Manila</option>
        <option value="Quezon City" <?= $locationSort == 'Quezon City' ? 'selected' : '' ?>>Luzon - Quezon City</option>
        <option value="Taguig" <?= $locationSort == 'Taguig' ? 'selected' : '' ?>>Luzon - Taguig</option>
        <option value="Caloocan" <?= $locationSort == 'Caloocan' ? 'selected' : '' ?>>Luzon - Caloocan</option>
        <option value="Pasig" <?= $locationSort == 'Pasig' ? 'selected' : '' ?>>Luzon - Pasig</option>
        <option value="Muntinlupa" <?= $locationSort == 'Muntinlupa' ? 'selected' : '' ?>>Luzon - Muntinlupa</option>
        <option value="Parañaque" <?= $locationSort == 'Parañaque' ? 'selected' : '' ?>>Luzon - Parañaque</option>
        <option value="Marikina" <?= $locationSort == 'Marikina' ? 'selected' : '' ?>>Luzon - Marikina</option>
        <option value="Las Piñas" <?= $locationSort == 'Las Piñas' ? 'selected' : '' ?>>Luzon - Las Piñas</option>
        <option value="Mandaluyong" <?= $locationSort == 'Mandaluyong' ? 'selected' : '' ?>>Luzon - Mandaluyong</option>
        <option value="Malabon" <?= $locationSort == 'Malabon' ? 'selected' : '' ?>>Luzon - Malabon</option>
        <option value="Navotas" <?= $locationSort == 'Navotas' ? 'selected' : '' ?>>Luzon - Navotas</option>
        <option value="San Juan" <?= $locationSort == 'San Juan' ? 'selected' : '' ?>>Luzon - San Juan</option>
        <option value="Valenzuela" <?= $locationSort == 'Valenzuela' ? 'selected' : '' ?>>Luzon - Valenzuela</option>
        <option value="Pateros" <?= $locationSort == 'Pateros' ? 'selected' : '' ?>>Luzon - Pateros</option>
        <option value="Makati" <?= $locationSort == 'Makati' ? 'selected' : '' ?>>Luzon - Makati</option>
        <option value="Pasay" <?= $locationSort == 'Pasay' ? 'selected' : '' ?>>Luzon - Pasay</option>
        <option value="Baguio" <?= $locationSort == 'Baguio' ? 'selected' : '' ?>>Luzon - Baguio</option>
        <option value="Laguna" <?= $locationSort == 'Laguna' ? 'selected' : '' ?>>Luzon - Laguna</option>
        <option value="Batangas" <?= $locationSort == 'Batangas' ? 'selected' : '' ?>>Luzon - Batangas</option>
        <option value="Cavite" <?= $locationSort == 'Cavite' ? 'selected' : '' ?>>Luzon - Cavite</option>
        <option value="Pampanga" <?= $locationSort == 'Pampanga' ? 'selected' : '' ?>>Luzon - Pampanga</option>
        <option value="Tarlac" <?= $locationSort == 'Tarlac' ? 'selected' : '' ?>>Luzon - Tarlac</option>
        <option value="Bulacan" <?= $locationSort == 'Bulacan' ? 'selected' : '' ?>>Luzon - Bulacan</option>
        <option value="Zambales" <?= $locationSort == 'Zambales' ? 'selected' : '' ?>>Luzon - Zambales</option>
        <option value="Nueva Ecija" <?= $locationSort == 'Nueva Ecija' ? 'selected' : '' ?>>Luzon - Nueva Ecija</option>
        <option value="Isabela" <?= $locationSort == 'Isabela' ? 'selected' : '' ?>>Luzon - Isabela</option>
        <option value="Quezon" <?= $locationSort == 'Quezon' ? 'selected' : '' ?>>Luzon - Quezon</option>
        <option value="Rizal" <?= $locationSort == 'Rizal' ? 'selected' : '' ?>>Luzon - Rizal</option>
        <option value="Aurora" <?= $locationSort == 'Aurora' ? 'selected' : '' ?>>Luzon - Aurora</option>
        <option value="Albay" <?= $locationSort == 'Albay' ? 'selected' : '' ?>>Luzon - Albay</option>
        <option value="Cagayan" <?= $locationSort == 'Cagayan' ? 'selected' : '' ?>>Luzon - Cagayan</option>
        <option value="La Union" <?= $locationSort == 'La Union' ? 'selected' : '' ?>>Luzon - La Union</option>
        <option value="Pangasinan" <?= $locationSort == 'Pangasinan' ? 'selected' : '' ?>>Luzon - Pangasinan</option>

        <!-- Visayas -->
        <option value="Cebu" <?= $locationSort == 'Cebu' ? 'selected' : '' ?>>Visayas - Cebu</option>
        <option value="Iloilo" <?= $locationSort == 'Iloilo' ? 'selected' : '' ?>>Visayas - Iloilo</option>
        <option value="Negros Occidental" <?= $locationSort == 'Negros Occidental' ? 'selected' : '' ?>>Visayas - Negros Occidental</option>
        <option value="Leyte" <?= $locationSort == 'Leyte' ? 'selected' : '' ?>>Visayas - Leyte</option>
        <option value="Bohol" <?= $locationSort == 'Bohol' ? 'selected' : '' ?>>Visayas - Bohol</option>
        <option value="Negros Oriental" <?= $locationSort == 'Negros Oriental' ? 'selected' : '' ?>>Visayas - Negros Oriental</option>
        <option value="Siquijor" <?= $locationSort == 'Siquijor' ? 'selected' : '' ?>>Visayas - Siquijor</option>
        <option value="Capiz" <?= $locationSort == 'Capiz' ? 'selected' : '' ?>>Visayas - Capiz</option>
        <option value="Samar" <?= $locationSort == 'Samar' ? 'selected' : '' ?>>Visayas - Samar</option>
        <option value="Southern Leyte" <?= $locationSort == 'Southern Leyte' ? 'selected' : '' ?>>Visayas - Southern Leyte</option>
        <option value="Bacolod" <?= $locationSort == 'Bacolod' ? 'selected' : '' ?>>Visayas - Bacolod</option>
        <option value="Ormoc" <?= $locationSort == 'Ormoc' ? 'selected' : '' ?>>Visayas - Ormoc</option>

        <!-- Mindanao -->
        <option value="Davao" <?= $locationSort == 'Davao' ? 'selected' : '' ?>>Mindanao - Davao</option>
        <option value="Cagayan de Oro" <?= $locationSort == 'Cagayan de Oro' ? 'selected' : '' ?>>Mindanao - Cagayan de Oro</option>
        <option value="Zamboanga" <?= $locationSort == 'Zamboanga' ? 'selected' : '' ?>>Mindanao - Zamboanga</option>
        <option value="Butuan" <?= $locationSort == 'Butuan' ? 'selected' : '' ?>>Mindanao - Butuan</option>
        <option value="General Santos" <?= $locationSort == 'General Santos' ? 'selected' : '' ?>>Mindanao - General Santos</option>
        <option value="Iligan" <?= $locationSort == 'Iligan' ? 'selected' : '' ?>>Mindanao - Iligan</option>
        <option value="Pagadian" <?= $locationSort == 'Pagadian' ? 'selected' : '' ?>>Mindanao - Pagadian</option>
        <option value="Davao del Norte" <?= $locationSort == 'Davao del Norte' ? 'selected' : '' ?>>Mindanao - Davao del Norte</option>
        <option value="Bukidnon" <?= $locationSort == 'Bukidnon' ? 'selected' : '' ?>>Mindanao - Bukidnon</option>
        <option value="Lanao del Sur" <?= $locationSort == 'Lanao del Sur' ? 'selected' : '' ?>>Mindanao - Lanao del Sur</option>
        <option value="Sultan Kudarat" <?= $locationSort == 'Sultan Kudarat' ? 'selected' : '' ?>>Mindanao - Sultan Kudarat</option>
        <option value="Agusan del Sur" <?= $locationSort == 'Agusan del Sur' ? 'selected' : '' ?>>Mindanao - Agusan del Sur</option>
    </datalist>


            <!-- Sort by Parking -->
            <input list="parking_options" autocomplete="off" name="parking_sort" id="parking_sort" class="filter-input" placeholder="🚗 Parking" onchange="this.form.submit()" value="<?= $parkingSort ?>">
            <datalist id="parking_options">
                <option value="Available" <?= $parkingSort == 'Available' ? 'selected' : '' ?>>Parking Available</option>
                <option value="None" <?= $parkingSort == 'None' ? 'selected' : '' ?>>No Parking</option>
            </datalist>

            <!-- Sort by Kitchen -->
            <input list="kitchen_options" autocomplete="off" name="kitchen_sort" id="kitchen_sort" class="filter-input" placeholder="🍽️ Kitchen" onchange="this.form.submit()" value="<?= $kitchenSort ?>">
            <datalist id="kitchen_options">
                <option value="Available" <?= $kitchenSort == 'Available' ? 'selected' : '' ?>>Kitchen Available</option>
                <option value="None" <?= $kitchenSort == 'None' ? 'selected' : '' ?>>No Kitchen</option>
            </datalist>

            <!-- Sort by Floors -->
            <input list="floors_options" autocomplete="off" name="floors_sort" id="floors_sort" class="filter-input" placeholder="🏢 Floors" onchange="this.form.submit()" value="<?= $floorsSort ?>">
            <datalist id="floors_options">
            <option value="1" <?= $floorsSort == '1' ? 'selected' : '' ?>>One Floor</option>
    <option value="2" <?= $floorsSort == '2' ? 'selected' : '' ?>>Two Floors</option>
    <option value="2+" <?= $floorsSort == '2+' ? 'selected' : '' ?>>Above Two Floors</option>
            </datalist>
            
        </div>
    </form>
</div>


    <div class="property-grid">
    <?php
if (empty($properties)) {
    echo "<div style='text-align: center; width: 100%; font-size: 18px; color: #666;'>
            <p>No properties found.</p>
          </div>";
} else {
   $conn = new mysqli("127.0.0.1", "u503094516_homehive", "HomeHive2025!", "u503094516_homehivedb");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

foreach ($properties as $property) {
    $propertyOwnerID = $property['owner_id'];

    // ✅ Owner details
    $stmt = $conn->prepare("SELECT Fname, Mname, Lname, phone, streetAddress, city, state, postal, email, UploadIDPhoto, ProfilePic, latitude, longitude FROM accountsdb WHERE userID = ?");
    $stmt->bind_param("i", $propertyOwnerID);
    $stmt->execute();
    $stmt->bind_result($fname, $mname, $lname, $phone, $street, $city, $state, $postal, $email, $uploadIDPhoto, $profilePic, $latitude, $longitude);
    $stmt->fetch();
    $stmt->close();

    $fullName = trim($fname . " " . (!empty($mname) ? $mname . " " : "") . $lname);

    $profilePic = !empty($profilePic)
        ? "/userdashboard/uploads/profile_pics/" . basename($profilePic)
        : "https://static.vecteezy.com/system/resources/thumbnails/057/350/992/small/flat-orange-male-avatar-icon-png.png";

    // ✅ Get average rating
    $avgRating = 0;
    $stmt = $conn->prepare("SELECT AVG(rating) AS avg_rating FROM reviews WHERE property_id = ?");
    $stmt->bind_param("i", $property['id']);
    $stmt->execute();
    $stmt->bind_result($avgRating);
    $stmt->fetch();
    $stmt->close();
    $avgRating = $avgRating ? round($avgRating, 1) : 0;

    // ✅ Get total views using existing property_id
    $viewsCount = 0;
    if (!empty($property['property_id'])) {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM property_view_logs WHERE property_id = ?");
        $stmt->bind_param("i", $property['property_id']);
        $stmt->execute();
        $stmt->bind_result($viewsCount);
        $stmt->fetch();
        $stmt->close();
    }

    // ✅ Display property card
    echo "<div style='cursor:pointer;' class='property-card' onclick=\"window.location.href='viewProperty.php?id={$property['id']}'\">
        <div class='owner-info'>
            <img class='profile-pic' src='$profilePic' alt='Profile Picture'>
            <div class='owner-details' style='display:flex; justify-content:space-between; align-items:center; width:100%;'>
                <p style='margin:0; font-weight:bold;'>$fullName</p>
                <div style='display:flex; align-items:center; gap:10px;'>
                    <p style='margin:0; font-size:14px; color:#555;' title='Total Views'>
                        <i class='fas fa-eye' style='color:#FB8C00; margin-right:3px;'></i>
                        $viewsCount
                    </p>
                    <p style='margin:0; font-size:14px; color:#555;' title='Average Rating'>
                        <i class='fas fa-star' style='color:#FFD700; margin-right:3px;'></i>
                        $avgRating
                    </p>
                </div>
            </div>
        </div>
        <div class='property-images'>";

    foreach ($property['images'] as $index => $image_path) {
        $active_class = ($index === 0) ? 'active' : '';
        echo "<img class='$active_class' src='userdashboard/uploads/properties/" . basename($image_path) . "'>";
    }

    echo "</div>
        <div class='property-details'>
            <h2 class='property-name'>{$property['name']}</h2>
            <p><strong>Property Type:</strong> " . htmlspecialchars($property['type']) . "</p>
            <hr>
            <h4 class='property-price'><i class='fas fa-money-bill-wave' style='color: #27ae60;'></i> ₱{$property['price']}/month</h4>
            <hr>
            <p class='property-location'><i class='fas fa-map-marker-alt' style='color:rgb(219, 52, 52);'></i> {$property['location']}</p>
            <p><i class='fas fa-bed' style='color: #8e44ad;'></i> Bedrooms: {$property['bedrooms']}</p>
            <p><i class='fas fa-bath' style='color: #3498db;'></i> Bathrooms: {$property['bathrooms']}</p>
            <p><i class='fas fa-utensils' style='color: #e67e22;'></i> Kitchen: {$property['kitchen']}</p>
            <p><i class='fas fa-car' style='color: #16a085;'></i> Parking: {$property['parking']}</p>
            <p><i class='fas fa-home' style='color: #e67e22;'></i> Floors: {$property['floors']}</p>
            <hr>
        </div>
    </div>";
}

$conn->close();

}
?>

</div>

<div class="pagination-container" style="display:flex; justify-content:center; align-items:center; gap:6px; margin:20px 0; flex-wrap:wrap;">
<?php
// Preserve all current GET parameters
$queryParams = $_GET;

// First page <<
if ($page > 1) {
    $queryParams['page'] = 1;
    echo '<a href="?' . http_build_query($queryParams) . '" class="pagination-btn">&laquo;</a>';
}

// Previous page <
if ($page > 1) {
    $queryParams['page'] = $page - 1;
    echo '<a href="?' . http_build_query($queryParams) . '" class="pagination-btn">&lt;</a>';
}

// Page numbers (show max 5 pages around current for cleaner UI)
$start = max(1, $page - 2);
$end = min($totalPages, $page + 2);
for ($i = $start; $i <= $end; $i++) {
    $queryParams['page'] = $i;
    $activeClass = $i === $page ? 'active' : '';
    echo '<a href="?' . http_build_query($queryParams) . '" class="pagination-btn ' . $activeClass . '">' . $i . '</a>';
}

// Next page >
if ($page < $totalPages) {
    $queryParams['page'] = $page + 1;
    echo '<a href="?' . http_build_query($queryParams) . '" class="pagination-btn">&gt;</a>';
}

// Last page >>
if ($page < $totalPages) {
    $queryParams['page'] = $totalPages;
    echo '<a href="?' . http_build_query($queryParams) . '" class="pagination-btn">&raquo;</a>';
}
?>
</div>

<style>
.pagination-container a.pagination-btn {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 5px;
    background-color: #FB8C00;
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s;
}
.pagination-container a.pagination-btn:hover {
    background-color: #F57C00;
}
.pagination-container a.pagination-btn.active {
    background-color: #5D4037;
}
  #property-tooltip {
    position: fixed;
    pointer-events: none;
    padding: 6px 10px;
    background: #333;
    color: #fff;
    border-radius: 4px;
    font-size: 14px;
    display: none;
    z-index: 1000;
    line-height: 1.2;
    transition: transform 0.05s ease;
  }
</style>

<?php include 'footer.php'?>
<!-- External Libraries First -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js/minified/introjs.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://cdn.jsdelivr.net/npm/intro.js/minified/intro.min.js"></script>

<!-- Your Project Scripts -->
<script src="assets/js/login.js"></script>
<script src="other/tailwind/js/tailwind.min.js"></script>
<script src="assets/js/isotope.min.js"></script>
<script src="assets/js/owl-carousel.js"></script>
<script src="assets/js/counter.js"></script>
<script src="assets/js/custom.js"></script>

<!-- Tooltip -->
<div id="property-tooltip"></div>
<script>
    
 document.addEventListener("DOMContentLoaded", () => {
  // Run the tour only if the user has not disabled it before
  if (!localStorage.getItem("properties_tour")) {
    const tour = introJs();

    const checkboxHTML = `
      <br><br>
      <label style="font-size:14px;">
        <input type="checkbox" id="dont-show-tour-again"> Don’t show this again
      </label>
    `;

    tour.setOptions({
      steps: [
        // 0. Intro greeting (with checkbox + reminder)
        {
          intro: `
            <div style="font-size:15px; line-height:1.6; text-align:center;">
              <strong>Welcome to the Properties Page!</strong><br><br>
              Take a short tour to learn how to search and filter listings.<br><br>
              Click <strong>Next</strong> to start, or <strong>Skip</strong> to explore on your own.
              ${checkboxHTML}
            </div>
          `,
          position: "center"
        },

        // 1. Map view
        {
          element: document.querySelector("#showMapBtn"),
          intro:
            "View all available properties on a map to easily compare locations and nearby amenities.",
          position: "left"
        },

        // 2. Filters overview
        {
          element: document.querySelector(".filters"),
          intro:
            "Use these filters to narrow your search results. Combine multiple filters to find your ideal property.",
          position: "right"
        },

        // 3. Search by name
        {
          element: document.querySelector(".search-box input"),
          intro:
            "Already know a property name? Search for it directly here.",
          position: "bottom"
        },

        // 4–9. Individual filters
        {
          element: document.querySelector("#type_sort"),
          intro: "Filter by property type — house, apartment, or condo.",
          position: "bottom"
        },
        {
          element: document.querySelector("#price_range"),
          intro: "Set your preferred price range here.",
          position: "bottom"
        },
        {
          element: document.querySelector("#location_sort"),
          intro: "Choose a specific city or neighborhood to refine your results.",
          position: "bottom"
        },
        {
          element: document.querySelector("#parking_sort"),
          intro: "Filter properties based on parking availability.",
          position: "bottom"
        },
        {
          element: document.querySelector("#kitchen_sort"),
          intro: "Show only listings that include a kitchen area.",
          position: "bottom"
        },
        {
          element: document.querySelector("#floors_sort"),
          intro: "Filter by the number of floors — single-story or multi-level homes.",
          position: "bottom"
        },

        // 10. Property listings
        {
          element: document.querySelector(".property-grid"),
          intro:
            "Here you'll see all properties that match your filters. Click a card to view details.",
          position: "top"
        },

        // 11. Pagination
        {
          element: document.querySelector(".pagination-container"),
          intro:
            "Use these controls to navigate between pages of property listings.",
          position: "top"
        },

        // 12. End (with checkbox)
        {
          intro: `
            <div style="font-size:15px; line-height:1.6; text-align:center;">
              <strong>You’re all set!</strong><br><br>
              You now know how to search, filter, and explore properties efficiently.<br><br>
              ${checkboxHTML}
            </div>
          `,
          position: "center"
        }
      ],
      nextLabel: "Next",
      prevLabel: "Back",
      doneLabel: "Finish",
      showProgress: true,
      showBullets: false,
      exitOnOverlayClick: false,
      disableInteraction: false
    });

    // Function to save preference
    const savePreference = () => {
      const checkbox = document.getElementById("dont-show-tour-again");
      if (checkbox && checkbox.checked) {
        localStorage.setItem("properties_tour", "true");
      }
    };

    // Check if "Don't show again" is ticked on the first step
    tour.onbeforechange(function () {
      const step = tour._currentStep;
      const checkbox = document.getElementById("dont-show-tour-again");

      if (step === 0 && checkbox && checkbox.checked) {
        localStorage.setItem("properties_tour", "true");
        tour.exit();
        return false;
      }
    });

    // Save preference on exit or completion
    tour.onexit(savePreference);
    tour.oncomplete(savePreference);

    tour.start();
  }
});



document.addEventListener('DOMContentLoaded', () => {
  // Tooltip & Property Card Messages
  const tooltip = document.getElementById('property-tooltip');
const messages = [
  "This could be your dream home 😍",
  "Check out this amazing property!",
  "Don't miss this listing!",
  "Your next home is here!",
  "Take a closer look at this property!",
  "Tara, check mo na itong bahay na ito! 🏡",
  "Ang ganda ng place na ito, perfect sa'yo!",
  "Don't wait, baka mauna ang iba!",
  "Ganda ng location nito, malapit sa lahat!",
  "Bahay na swak sa budget mo 💵",
  "Take a peek, baka ito na ang future home mo!",
  "Ang cozy ng bahay na ito 😍",
  "Perfect ito for family or starter home!",
  "Check mo yung amenities, super convenient!",
  "Baka ikaw na ang next lucky owner!"
];


  document.querySelectorAll('.property-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
      const randomMessage = messages[Math.floor(Math.random() * messages.length)];
      tooltip.innerHTML = `${randomMessage}<br><small style="color:#ccc;">Click to view this property</small>`;
      tooltip.style.display = 'block';
    });
    card.addEventListener('mouseleave', () => tooltip.style.display = 'none');
    card.addEventListener('mousemove', e => {
      const offsetX = 15, offsetY = 15;
      tooltip.style.left = e.clientX + offsetX + 'px';
      tooltip.style.top = e.clientY + offsetY + 'px';
    });
  });

  // Property Images Carousel
  document.querySelectorAll('.property-images').forEach(propertyImages => {
    const images = propertyImages.querySelectorAll('img');
    let currentIndex = 0;
    setInterval(() => {
      images[currentIndex].classList.remove('active');
      currentIndex = (currentIndex + 1) % images.length;
      images[currentIndex].classList.add('active');
    }, 3000);
  });

  // Filters & Search
  const form = document.querySelector("form");
  const searchInput = form.querySelector("input[name='search']");
  const typeSort = form.querySelector("select[name='type_sort']");
  const priceRange = form.querySelector("select[name='price_range']");
  const propertyContainer = document.querySelector(".property-grid");

  function fetchFilteredData() {
    const params = new URLSearchParams(new FormData(form)).toString();
    fetch("properties.php?" + params)
      .then(res => res.text())
      .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, "text/html");
        propertyContainer.innerHTML = doc.querySelector(".property-grid").innerHTML;
      });
  }

  searchInput.addEventListener("input", () => {
    clearTimeout(searchInput.dataset.timer);
    searchInput.dataset.timer = setTimeout(fetchFilteredData, 300);
  });
  typeSort.addEventListener("change", fetchFilteredData);
  priceRange.addEventListener("change", fetchFilteredData);

  // Placeholder Focus Effect
  const inputs = [
    {id: 'type_sort', text: "🏠 Filter by Type", icon: "🏠"},
    {id: 'price_range', text: "💵 Filter by Price Range", icon: "💵"},
    {id: 'location_sort', text: "📍 Filter by Location", icon: "📍"},
    {id: 'parking_sort', text: "🚗 Filter by Parking", icon: "🚗"},
    {id: 'kitchen_sort', text: "🍽 Filter by Kitchen", icon: "🍽"},
    {id: 'floors_sort', text: "🏢 Filter by Floors", icon: "🏢"}
  ];

  inputs.forEach(inp => {
    const el = document.getElementById(inp.id);
    el.addEventListener('focus', () => el.value || el.setAttribute('placeholder', inp.text));
    el.addEventListener('blur', () => el.value ? el.setAttribute('placeholder', inp.text) : el.setAttribute('placeholder', inp.icon));
  });



  // Logout Button
  window.openLogoutModal = function(event) {
    event.preventDefault();
    Swal.fire({
      title: 'Are you sure?',
      text: 'You will be logged out from your session.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, logout',
      cancelButtonText: 'Cancel',
      reverseButtons: true,
      confirmButtonColor: '#f39c12'
    }).then(result => { if(result.isConfirmed) window.location.href='logout.php'; });
  };

  // Logo Scroll Effect
  window.addEventListener('scroll', () => {
    const logo = document.getElementById('logo-img');
    if (window.scrollY > 20) {
      logo.src = 'https://i.imgur.com/Q5BsPbV.png';
      logo.style.width = '140px';
      logo.style.marginTop = '-30px';
    }
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



<?php include 'askbeeai.php'; ?>
<div id="property-tooltip" style="
    position: fixed;
    pointer-events: none;
    padding: 6px 10px;
    background: #333;
    color: #fff;
    border-radius: 4px;
    font-size: 14px;
    display: none;
    z-index: 1000;
    line-height: 1.2;
    transition: transform 0.05s ease;
">
</div>

</body>
</html>
