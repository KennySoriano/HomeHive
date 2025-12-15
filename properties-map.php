<?php
session_start(); 
require_once 'config.php';

// Get search and filter inputs
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$typeSort = isset($_GET['type_sort']) ? $_GET['type_sort'] : '';
$priceRange = isset($_GET['price_range']) ? $_GET['price_range'] : '';
$locationSort = isset($_GET['location_sort']) ? $_GET['location_sort'] : '';
$parkingSort = isset($_GET['parking_sort']) ? $_GET['parking_sort'] : '';
$kitchenSort = isset($_GET['kitchen_sort']) ? $_GET['kitchen_sort'] : '';
$floorsSort = isset($_GET['floors_sort']) ? $_GET['floors_sort'] : '';

// Build SQL query
$sql = "SELECT p.id, p.property_id, p.name, p.location, p.price, p.type, p.description, 
                p.bedrooms, p.bathrooms, p.kitchen, p.parking, p.floors, 
                p.owner_id, p.latitude, p.longitude, a.image_path 
        FROM properties p
        LEFT JOIN apartmentimages a ON p.id = a.apartment_id
        WHERE p.status = 'Approved'";


// Default ordering
$sql .= " ORDER BY p.id DESC";

// Execute final query
$result = $conn->query($sql);

// Organize results
$properties = [];

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

$filePath = "locations.txt"; // Replace with the path to your .txt file

// Read the locations from the .txt file
$locationOptions = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="other/tailwind/css/tailwind.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/properties.css">
    <link rel="stylesheet" href="assets/css/dropdownmenu.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />

    <title>HomeHive - Map</title>
</head>
<body>

 
  <div class="sub-header">
    <div class="container">
      <div class="row">
        <div class="col-lg-8 col-md-8">
          <ul class="info">
            <li><i class="fa fa-envelope"></i> homehive@gmail.com</li>
            <li><i class="fa fa-map"></i> Philippines</li>
          </ul>
        </div>
        <div class="col-lg-4 col-md-4">
          <ul class="social-links">
            <li><a href="#"><i class="fab fa-facebook"></i></a></li>
            <li><a href="#" ><i class="fab fa-twitter"></i></a></li>
            <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
            <li><a href="#"><i class="fab fa-instagram"></i></a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                   <a href="index.php" class="logo">
    <img id="logo-img" src="https://i.imgur.com/Q5BsPbV.png" alt="HomeHive Logo"
         style="width: 150px; height: auto; margin-top: -30px;" />
</a>
                    
<ul class="nav">
    <li><a href="index.php" >Home</a></li>
    <li><a href="properties.php"class="active">Properties</a></li>
    <li><a href="about.php?article=article1">About Us</a></li>

    
    <!-- User Dropdown -->
    <li class="user-menu">
    <a href="#" class="dropbtn">
        <i class="fa fa-user"></i>
        <?php echo isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'Guest'; ?>
    </a>
    <div class="user-dropdown">
        <?php if (isset($_SESSION['user_id'])): ?>
            <button onclick="window.location.href='userdashboard/dashboard.php'" class="dropdown-btn dashboard-btn"><script src="https://animatedicons.co/scripts/embed-animated-icons.js"></script>
<animated-icons
  src="https://animatedicons.co/get-icon?name=dashboard&style=minimalistic&token=fa13e0db-49ee-4fc6-88d0-609496daffac"
  trigger="loop"
  attributes='{"variationThumbColour":"#536DFE","variationName":"Two Tone","variationNumber":2,"numberOfGroups":2,"backgroundIsGroup":false,"strokeWidth":1,"defaultColours":{"group-1":"#000000","group-2":"#FFDD3BFF","background":"#FFFFFF"}}'
  height="40"
  width="40"
></animated-icons>Dashboard</button>
            <button  onclick="openLogoutModal(event)"  class="dropdown-btn logout-btn"><animated-icons
  src="https://animatedicons.co/get-icon?name=exit&style=minimalistic&token=6e09845f-509a-4b0a-a8b0-c47e168ad977"
  trigger="loop"
  attributes='{"variationThumbColour":"#FFFFFF","variationName":"Normal","variationNumber":1,"numberOfGroups":1,"backgroundIsGroup":false,"strokeWidth":1,"defaultColours":{"group-1":"#000000","background":"#FFFFFF"}}'
  height="40"
  width="40"
></animated-icons>Log Out</button>
        <?php else: ?>
            <button onclick="window.location.href='loginuser.php'" class="dropdown-btn signin-btn"><script src="https://animatedicons.co/scripts/embed-animated-icons.js"></script>
<animated-icons
  src="https://animatedicons.co/get-icon?name=login&style=minimalistic&token=e611dfb8-fad8-4fd7-80da-af68bebdafb8"
  trigger="loop"
  attributes='{"variationThumbColour":"#FFFFFF","variationName":"Normal","variationNumber":1,"numberOfGroups":1,"backgroundIsGroup":false,"strokeWidth":1,"defaultColours":{"group-1":"#000000","background":"#FFFFFF"}}'
  height="40"
  width="40"
></animated-icons>Sign In</button>
            <button onclick="window.location.href='createaccount.php'" class="dropdown-btn signup-btn"><animated-icons
  src="https://animatedicons.co/get-icon?name=Register&style=minimalistic&token=be93a354-eb41-497f-bb52-cdf419e7d920"
  trigger="loop"
  attributes='{"variationThumbColour":"#FFFFFF","variationName":"Normal","variationNumber":1,"numberOfGroups":1,"backgroundIsGroup":false,"strokeWidth":1.5,"defaultColours":{"group-1":"#000000","background":"#FFFFFF"}}'
  height="40"
  width="40"
></animated-icons>Sign Up</button>
        <?php endif; ?>
    </div>
</li>
 </nav>
            </div>
        </div>
    </div>
  </header>

</div>
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
" onclick="window.location.href='properties.php'">
    <i class="fas fa-list" style="font-size: 20px;"></i> Show List
</button>




  <div style="position: relative;">
  <div id="propertyMap"
      style="height: 1200px; width: 100%; border-radius: 5px; margin-top: 30px; border: 2px solid #ccc; z-index: 1;">
      <!-- Map loads here -->
      
      <button id="locateBtn" title="Toggle My Location" class="locate-btn">
    <i class="fas fa-street-view"></i>
    <span class="locate-text"> My Location</span>
    <span id="locateIndicator" class="indicator off"></span>
</button>


  </div>
</div>

<div id="propertySidebar">
    <!-- Header -->
    <div class="sidebar-header">
        <h2>Property Details</h2>
        <button onclick="closeSidebar()" class="sidebar-close">&times;</button>
    </div>

    <!-- Content -->
    <div id="sidebarContent" class="sidebar-content">
        <!-- Dynamically filled via JS -->
    </div>
</div>


<div id="nearbyPopup" style="
    display: none;
    position: fixed;
    top: 120px;
    left: 20px; /* Now aligned to the left side */
    width: 380px;
    max-height: 450px;
    overflow-y: auto;
    background: linear-gradient(135deg, #FFF8E1 0%, #FFE0B2 100%);
    border: 2px solid #FB8C00;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    z-index: 999999;
    padding: 0;
    cursor: move;
    pointer-events: auto;
    transition: all 0.3s ease;
">
  <!-- Sticky header with close button -->
  <div style="
      position: sticky;
      top: 0;
      background: #FFF8E1;
      border-bottom: 2px solid #FB8C00;
      padding: 12px 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      z-index: 1000;
  ">
    <h3 style="
        margin: 0;
        font-size: 18px;
        font-weight: bold;
        color: #5D4037;
    ">
      Nearby Places (500m)
    </h3>
    <button onclick="document.getElementById('nearbyPopup').style.display='none';" style="
        background: linear-gradient(90deg, #FB8C00, #F57C00);
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 13px;
        font-weight: bold;
        transition: background 0.3s ease;
    " onmouseover="this.style.background='#F57C00'" onmouseout="this.style.background='linear-gradient(90deg, #FB8C00, #F57C00)'">
      Close
    </button>
  </div>

  <!-- Scrollable content -->
  <div style="padding: 15px;">
    <ul id="nearbyList" style="
        list-style: none;
        padding: 0;
        margin: 0;
        font-size: 15px;
        color: #333;
        line-height: 1.6;
    "></ul>
  </div>
</div>




<style>
/* Sidebar Container */
#propertySidebar {
    position: fixed;
    top: 0;
    right: -400px; /* Hidden initially */
    width: 380px;
    height: 100%;
    background-color: #ffffff;
    z-index: 999; /* Always on top */
    box-shadow: -4px 0 25px rgba(0,0,0,0.3);
    overflow-y: auto;
    border-radius: 0 10px 10px 0;
    transition: right 0.3s ease-in-out;
    font-family: 'Poppins', sans-serif;
}

/* Header */
.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0; /* light gray */
    background-color: #fff8f0; /* subtle orange tint */
}

.sidebar-header h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #ff7a00;
}

/* Close Button */
.sidebar-close {
    font-size: 1.5rem;
    font-weight: bold;
    color: #ff7a00;
    background: none;
    border: none;
    cursor: pointer;
    transition: transform 0.2s, color 0.2s;
}

.sidebar-close:hover {
    transform: rotate(90deg);
    color: #e55d00;
}

/* Content */
.sidebar-content {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.sidebar-content img {
    width: 100%;
    height: 48 0px;
    object-fit: cover;
    border-radius: 0.5rem;
}

/* Amenities Badges */
.sidebar-content .amenities {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.sidebar-content .amenities span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    background-color: #f3f4f6; /* light gray */
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    color: #374151; /* dark gray */
}

/* View More Details Button */
.sidebar-content .view-details-btn {
    display: block;
    text-align: center;
    background-color: #fb8c00;
    color: #ffffff;
    font-weight: 600;
    padding: 0.5rem 0;
    border-radius: 0.5rem;
    text-decoration: none;
    transition: background 0.3s;
}

.sidebar-content .view-details-btn:hover {
    background-color: #f57c00;
}

   .locate-btn {
    position: fixed;
    top: 500px;
    right: 20px;
    z-index: 1000;
    padding: 12px 16px;
    border-radius: 30px;
    background-color: #FB8C00;
    color: white;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    transition: width 0.3s ease, background-color 0.3s ease;
    white-space: nowrap;
    overflow: hidden;
    width: 50px;
}

.locate-btn i {
    font-size: 20px;
    transition: margin-right 0.3s ease;
}

.locate-text {
    opacity: 0;
    margin-left: 0;
    font-size: 14px;
    transition: opacity 0.3s ease, margin-left 0.3s ease;
}

.locate-btn:hover {
    width: 160px;
    border-radius: 30px;
}

.locate-btn:hover .locate-text {
    opacity: 1;
    margin-left: 10px;
}

.indicator {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-left: auto;
    margin-right: 4px;
    background-color: gray;
    transition: background-color 0.3s ease;
}

.indicator.on {
    background-color: #27ae60; /* Green for ON */
}

.indicator.off {
    background-color: #e74c3c; /* Red for OFF */
}

/* Cluster Bubble Style */
.marker-cluster {
    background-color: #FB8C00;  /* HomeHive orange */
    border-radius: 50%;
    color: white;
    text-align: center;
    line-height: 40px;           /* Vertically center number */
    font-weight: bold;
    border: 2px solid #fff;      /* White border for contrast */
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}

.marker-cluster-small {
    background-color: #FFB22C;   /* Slightly lighter orange for smaller clusters */
}

.marker-cluster-medium {
    background-color: #FB8C00;   /* Default orange */
}

.marker-cluster-large {
    background-color: #E65100;   /* Darker orange for large clusters */
}

/* Number inside cluster */
.marker-cluster div {
    font-size: 14px;
    line-height: 40px;
}
/* Modern tooltip style */
.property-tooltip {
    background: rgba(255, 248, 224, 0.95); /* soft orange background */
    border: 1px solid #FB8C00;
    border-radius: 8px;
    padding: 8px 12px;
    color: #333;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    pointer-events: none; /* Prevent tooltip from blocking mouse */
}
/* Sidebar image container for fixed 16:9 ratio */
.sidebar-image-container {
    width: 100%;
    aspect-ratio: 16 / 9; /* Modern way to enforce 16:9 */
    overflow: hidden;
    border-radius: 0.5rem; /* Rounded corners */
}

.sidebar-image {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Crop & fill without distortion */
    display: block;
}
#nearbyList table tbody tr {
    transition: background-color 0.25s ease, color 0.25s ease;
}

#nearbyList table tbody tr:hover {
    background-color: #FB8C00; /* HomeHive orange */
    color: white; /* Text color on hover */
    cursor: pointer;
}

</style>    




<footer class="hh-footer">
    <div class="container">
        <p>Copyright &copy; 2024 HomeHive Official. All rights reserved.</p>
    </div>
</footer>

<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>

<script>
let locationCircle; // to store the circle so we can remove/update it

window.onload = function () {
    // Create a marker cluster group
    var markers = L.markerClusterGroup({
        showCoverageOnHover: true,
        maxClusterRadius: 50,
        spiderfyOnMaxZoom: true,
        iconCreateFunction: function(cluster) {
            return L.divIcon({
                html: '<b>' + cluster.getChildCount() + '</b>',
                className: 'marker-cluster',
                iconSize: L.point(40, 40)
            });
        }
    });

    // Center page scroll
    const scrollY = (document.body.scrollHeight - window.innerHeight) / 2;
    window.scrollTo({ top: scrollY, behavior: 'smooth' });

    // Initialize Leaflet map
    const philippinesBounds = [[4.5, 116.5], [21.0, 127.0]];
    const map = L.map('propertyMap', {
        worldCopyJump: false,
        maxBoundsViscosity: 1.0,
        maxBounds: philippinesBounds,
        minZoom: 12,
        maxZoom: 10
    }).setView([14.6760, 121.0437], 12);

    map.setMinZoom(5);
    map.setMaxZoom(18);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        noWrap: true,
        bounds: philippinesBounds
    }).addTo(map);

    // Toggleable human location marker
    let locationMarker = null;
    let isLocationVisible = false;
    const locateIndicator = document.getElementById('locateIndicator');
    const locateBtn = document.getElementById('locateBtn');

    locateBtn.addEventListener('click', function () {
        if (!navigator.geolocation) {
            alert('Geolocation not supported.');
            return;
        }

        if (isLocationVisible && locationMarker) {
            map.removeLayer(locationMarker);
            isLocationVisible = false;
            locateBtn.style.backgroundColor = 'rgb(248, 160, 58)';
            locateIndicator.classList.remove('on');
            locateIndicator.classList.add('off');
        } else {
            navigator.geolocation.getCurrentPosition(async function (position) {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;

                const userIcon = L.icon({
                    iconUrl: 'assets/images/human-pin.png',
                    iconSize: [50, 50],
                    iconAnchor: [20, 40],
                    popupAnchor: [0, -40]
                });

                if (locationMarker) map.removeLayer(locationMarker);
                if (locationCircle) map.removeLayer(locationCircle);

                locationMarker = L.marker([userLat, userLng], { icon: userIcon })
                    .addTo(map)
                    .bindPopup("<strong>You are here</strong>")
                    .openPopup();

                locationCircle = L.circle([userLat, userLng], {
                    radius: 500,
                    color: '#FB8C00',
                    weight: 2,
                    fillColor: '#FB8C00',
                    fillOpacity: 0.2
                }).addTo(map);

                map.setView([userLat, userLng], 16);
                isLocationVisible = true;
                locateBtn.style.backgroundColor = 'rgb(248, 160, 58)';
                locateIndicator.classList.remove('off');
                locateIndicator.classList.add('on');

                // Fetch nearby places
                const query = `[out:json];(node(around:500,${userLat},${userLng})[amenity];);out;`;
                const response = await fetch(`https://overpass-api.de/api/interpreter?data=${encodeURIComponent(query)}`);
                const data = await response.json();
                const nearbyList = document.getElementById('nearbyList');
                nearbyList.innerHTML = '';

                if (data.elements.length === 0) {
                    nearbyList.innerHTML = '<li>No nearby places found.</li>';
                } else {
                    let activeNearbyMarker = null;
                    const table = document.createElement('table');
                    table.style.width = '100%';
                    table.style.borderCollapse = 'collapse';
                    table.innerHTML = `<thead>
                        <tr style="background:#FB8C00; color:white;">
                            <th style="padding:8px; text-align:left;">Name</th>
                            <th style="padding:8px; text-align:left;">Type</th>
                        </tr>
                    </thead>`;
                    const tbody = document.createElement('tbody');

                    data.elements.forEach(place => {
                        const name = place.tags.name || 'Unnamed';
                        const typeRaw = place.tags.amenity || 'Unknown';
                        const type = typeRaw.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                        const lat = place.lat;
                        const lon = place.lon;

                        const row = document.createElement('tr');
                        row.style.cursor = 'pointer';
                        row.style.borderBottom = '1px solid #ccc';
                        row.innerHTML = `<td style="padding:6px;">${name}</td><td style="padding:6px;">${type}</td>`;

                        row.addEventListener('click', () => {
                            if (activeNearbyMarker) map.removeLayer(activeNearbyMarker);

                            activeNearbyMarker = L.marker([lat, lon])
                                .addTo(map)
                                .bindPopup(`<strong>${name}</strong><br>${type}`)
                                .openPopup();

                            map.setView([lat, lon], 19);
                        });

                        tbody.appendChild(row);
                    });

                    table.appendChild(tbody);
                    nearbyList.appendChild(table);
                }

                document.getElementById('nearbyPopup').style.display = 'block';
            }, function (error) {
                console.warn("Geolocation error:", error.message);
            });
        }
    });

    // PHP to JS: Properties from server
    var properties = <?php
        $mappedProps = array_filter($properties, function($prop) {
            return isset($prop['latitude']) && isset($prop['longitude']);
        });
        echo json_encode(array_values($mappedProps));
    ?>;

    var houseIcon = L.icon({
        iconUrl: 'assets/images/house-pin.png',
        iconSize: [50, 50],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
    });

properties.forEach(function (property) {
    if (property.latitude && property.longitude) {
        const marker = L.marker([property.latitude, property.longitude], { icon: houseIcon });

        // Create a hover tooltip with basic property info
        const amenities = [];
        if (property.bedrooms) amenities.push(`${property.bedrooms} Beds`);
        if (property.bathrooms) amenities.push(`${property.bathrooms} Baths`);
        if (property.parking) amenities.push(`${property.parking} Parking`);

        const tooltipContent = `
            <div style="font-size: 14px; font-weight: 500; color: #333;">
                <strong>${property.name}</strong><br>
                ${property.location}<br>
                <span style="color: #27ae60; font-weight: 600;">₱${property.price}/month</span><br>
                ${amenities.join(' • ')}
            </div>
        `;

        marker.bindTooltip(tooltipContent, {
            direction: 'top',
            offset: [0, -10],
            opacity: 0.95,
            className: 'property-tooltip'
        });

        // On click -> open sliding sidebar
        marker.on('click', () => openSidebar(property));

        markers.addLayer(marker); // Add marker to cluster
    }
});

    map.addLayer(markers); // Add cluster group to map
};
function openSidebar(property) {
    const sidebar = document.getElementById('propertySidebar');
    const content = document.getElementById('sidebarContent');

    const imgSrc = (property.images && property.images.length > 0) 
        ? property.images[0] 
        : 'userdashboard/uploads/properties/default-image.jpg';

    content.innerHTML = `
        <div class="sidebar-image-container">
            <img src="${imgSrc}" alt="Property Image" class="sidebar-image">
        </div>
        <h3 class="text-xl font-semibold text-gray-900 mt-3">${property.name}</h3>
        <p class="text-gray-600">${property.location}</p>
        <p class="text-green-600 font-bold mt-2 text-lg">₱${property.price}/month</p>
        <div class="amenities mt-2">
            <span><i class="fas fa-bed"></i> ${property.bedrooms}</span>
            <span><i class="fas fa-bath"></i> ${property.bathrooms}</span>
            <span><i class="fas fa-car"></i> ${property.parking}</span>
        </div>
        <a href="viewProperty.php?id=${property.id}" class="view-details-btn mt-4">View More Details</a>
    `;

    sidebar.style.right = "0"; // Slide sidebar in
}

function closeSidebar() {
    document.getElementById('propertySidebar').style.right = "-400px"; // Slide sidebar out
}


</script>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
<script src="assets/js/login.js"></script>
<script src="other/tailwind/js/tailwind.min.js"></script>
<script src="assets/js/isotope.min.js"></script>
<script src="assets/js/owl-carousel.js"></script>
<script src="assets/js/counter.js"></script>
<script src="assets/js/custom.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>


<script>
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
      window.location.href = 'logout.php';
    }
  });
}

function makeDraggable(element) {
    let offsetX = 0, offsetY = 0, isDragging = false;

    element.addEventListener('mousedown', (e) => {
        isDragging = true;
        offsetX = e.clientX - element.offsetLeft;
        offsetY = e.clientY - element.offsetTop;
    });

    window.addEventListener('mousemove', (e) => {
        if (isDragging) {
            element.style.left = (e.clientX - offsetX) + 'px';
            element.style.top = (e.clientY - offsetY) + 'px';
        }
    });

    window.addEventListener('mouseup', () => { isDragging = false; });
}

document.addEventListener('DOMContentLoaded', () => {
    makeDraggable(document.getElementById('nearbyPopup'));
});

</script>


</body>
</html>
