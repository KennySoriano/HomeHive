<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';
session_start();


// AJAX email availability check
if (isset($_POST['email_check'])) {
    $emailToCheck = trim($_POST['email_check']);
    $stmt = $conn->prepare("SELECT email FROM accountsdb WHERE email = ?");
    $stmt->bind_param("s", $emailToCheck);
    $stmt->execute();
    $stmt->store_result();

    echo json_encode([
        "status" => "success",
        "exists" => $stmt->num_rows > 0
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Generate unique userID
    $userID = "1000" . mt_rand(100000, 999999);

    // Set default roles and profile picture
    $pRole = "Tenant";
    $sRole = "None";
    $defaultProfilePic = "default_profile.jpg";

    // Assign POST values to variables
    $Lname = trim($_POST['Lname']);
    $Fname = trim($_POST['Fname']);
    $Mname = trim($_POST['Mname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $Birthdate = trim($_POST['Birthdate']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $streetAddress = trim($_POST['streetAddress']);
    $postal = trim($_POST['postal']);
    $UploadedIDType = trim($_POST['UploadedIDType']);
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Handle ID photo upload
    $UploadIDPhoto = null;
    $uploadDir = "userdashboard/uploads/ids/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (isset($_FILES['UploadIDPhoto']) && $_FILES['UploadIDPhoto']['error'] === UPLOAD_ERR_OK) {
        $idFileName = basename($_FILES['UploadIDPhoto']['name']);
        $idTargetPath = $uploadDir . uniqid("id_") . "_" . $idFileName;

        if (move_uploaded_file($_FILES['UploadIDPhoto']['tmp_name'], $idTargetPath)) {
            $UploadIDPhoto = $idTargetPath;
        } else {
            die("Error: Failed to move uploaded ID photo.");
        }
    } else {
        die("Error: ID photo is required and must be uploaded successfully.");
    }

    // Prepare and bind
$stmt = $conn->prepare("INSERT INTO accountsdb (
    Lname, Fname, Mname, email, phone, Birthdate,
    city, state, streetAddress, postal,
    UploadedIDType, UploadIDPhoto, ProfilePic,
    pRole, sRole, userID, password, latitude, longitude
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param(
    "sssssssssssssssssss", // all are strings
    $Lname, $Fname, $Mname, $email, $phone, $Birthdate,
    $city, $state, $streetAddress, $postal,
    $UploadedIDType, $UploadIDPhoto, $defaultProfilePic,
    $pRole, $sRole, $userID, $password, $latitude, $longitude
);

    // Execute
 if ($stmt->execute()) {
    // 🔹 MANUAL PH TIME
    date_default_timezone_set('Asia/Manila');
    $created_at = date('Y-m-d H:i:s');

    /** 
     * INSERT into inbox → Welcome message
     */
    $inbox_id = (int)("5000" . str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT));
    $title = "Welcome to HomeHive";
$message = "Hello {$Fname}, welcome to HomeHive! We're excited to have you on board. 
With your new account, you can browse verified properties, submit rental applications, track your leases, 
communicate securely with property owners, and manage your account settings easily. 
We aim to make your renting experience simple, safe, and transparent. Enjoy exploring your new HomeHive journey!
If you have any questions or need assistance, feel free to contact us at email@homehive.site.";



    $inbox_stmt = $conn->prepare("
        INSERT INTO inbox (inbox_id, `from`, receiver_id, title, message, created_at, status, type)
        VALUES (?, 'system', ?, ?, ?, ?, 'unread', 'primary')
    ");
    $inbox_stmt->bind_param("iisss", $inbox_id, $userID, $title, $message, $created_at);
    $inbox_stmt->execute();

    // Continue with existing logic
    $_SESSION['registration_success'] = true;
    header("Location: welcome.php");
    exit();
} else {
    echo "Database Error: " . $stmt->error;
}


$stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <?php include 'meta.php'; ?>
    <title>HomeHive - Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <style>
        :root {
            --primary-color: #FB8C00;
            --secondary-color: #F57C00;
            --accent-color: #5D4037;
            --light-color: #FFF8E1;
            --dark-color: #5D4037;
            --s: 37px; /* control the size*/
            --s: 44px; /* control the size*/ --c1: #e6973d;
            --c2: #272c20; 
            --c:#0000,var(--c1) .5deg 119.5deg,#0000 120deg; 
            --g1:conic-gradient(from 60deg at 56.25% calc(425%/6),var(--c));
            --g2:conic-gradient(from 180deg at 43.75% calc(425%/6),var(--c)); 
            --g3:conic-gradient(from -60deg at 50% calc(175%/12),var(--c));
            
        }
        
      body {
background: var(--g1),var(--g1) var(--s) calc(1.73*var(--s)), var(--g2),var(--g2) var(--s) calc(1.73*var(--s)), var(--g3) var(--s) 0,var(--g3) 0 calc(1.73*var(--s)) var(--c2); 
background-size: calc(2*var(--s)) calc(3.46*var(--s));

    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

        .signup-container {
            max-width: 900px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .signup-header {
            background: linear-gradient(135deg, var(--dark-color), var(--primary-color));
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .signup-header h2 {
            margin: 0;
            font-weight: 700;
        }
        
        .signup-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #262626;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(245, 124, 0, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .location-btn {
            background-color: var(--light-color);
            border: 1px solid #ddd;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .location-btn:hover {
            background-color:var(--primary-color);
        }
        
        #map {
            height: 250px;
            border-radius: 8px;
            margin-top: 10px;
            display: none;
        }
        
        .password-strength {
            height: 5px;
            background-color: #e9ecef;
            border-radius: 5px;
            margin-top: 5px;
            overflow: hidden;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s;
        }
        
        .terms-link {
            color: var(--primary-color);
            cursor: pointer;
            text-decoration: underline;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 5px;
        }
        
        .valid-feedback {
            color: #28a745;
            font-size: 0.875em;
            margin-top: 5px;
        }
        
        .input-group-text {
            background-color: #FFF8E1;
        }
        
.form-check-input {
    width: 18px;
    height: 18px;
    accent-color: #F57C00; 
    border: 2px solid #F57C00;
    border-radius: 3px;
    cursor: pointer;
    transition: box-shadow 0.2s ease;
}

.form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(245, 124, 0, 0.25); 
    outline: none;
}
i.fas, i.far, i.fab {
    color: #F57C00;
}
i.fas:hover,
i.far:hover,
i.fab:hover {
    color: #FB8C00; 
    transition: color 0.2s ease;
}
.back-button {
    position: fixed;
    top: 20px;
    left: 20px;
    background-color: #F57C00;
    color: #fff;
    padding: 10px 16px;
    font-size: 14px;
    font-weight: bold;
    text-decoration: none;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    transition: background-color 0.2s ease;
}

.back-button:hover {
    background-color: #FB8C00; 
}

.red {
    color:red;
}
input[readonly] {
  border: none;
  background: transparent;
  color: #555;
  font-weight: bold;
}


    </style>
</head>

<body>
    <?php include 'includes/disclaimer-pictures.php' ?>
    <a href="loginuser.php" class="back-button" title="Go Back">
  &#8592; Back
</a>

    <div class="container-fluid">
        <div class="signup-container">
            <div class="signup-header">
               <h2>
  <img src="/assets/logo/HomeHiveIcon-White-removebg.png" alt="Home icon" style="width: 100px; height: 100px; margin-right: 8px; vertical-align: middle;"><br>
  Create Your HomeHive Account
</h2>

                <p class="mb-0">Join our community today</p>
            </div>
            
            <div class="signup-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form id="signupForm" method="POST" enctype="multipart/form-data">
                    <div class="row">
                    <h5 class="mb-3"><i class="fas fa-user me-2"></i> Personal Information</h5>
                    <p class="text-muted mb-3" style="font-size: 0.95rem;">
                        Please provide accurate personal details to ensure smooth processing of your registration and verification. 
                        <br>
                      <small><strong>We keep your information private and only use it to check your identity and set up your account.</strong></small>

                    </p>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="Fname" class="form-label">First Name <span class ="red"> *</span></span></label>
                                <input type="text" class="form-control" id="Fname" name="Fname" maxlength="50" required>
                                <div class="error-message" id="Fname-error"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="Mname" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="Mname" name="Mname" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="Lname" class="form-label">Last Name <span class ="red"> *</span></label>
                                <input type="text" class="form-control" id="Lname" name="Lname" maxlength="50" required>
                                <div class="error-message" id="Lname-error"></div>
                            </div>
                        </div>
                    </div>
                    
         <div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="email" class="form-label">Email Address <span class ="red"> *</span></label>
            <small id="valid-emails" style="cursor:pointer; color:#FB8C00;">(View valid emails)</small>
            <input type="email" class="form-control" id="email" name="email" placeholder="example@gmail.com" required>
            <div class="error-message" id="email-error"></div>
            <div class="valid-feedback" id="email-valid"></div>
        </div>
    </div>

<div class="col-md-6">
  <div class="form-group">
    <label for="phone" class="form-label">Phone Number <span class="red"> *</span></label>
    <div class="input-group">
      <span class="input-group-text">
        <img src="https://upload.wikimedia.org/wikipedia/commons/9/99/Flag_of_the_Philippines.svg" 
             alt="PH" 
             style="width:24px; height:16px; margin-right:4px;">
        +63
      </span>
      <input 
        type="tel" 
        class="form-control" 
        id="phone" 
        name="phone" 
        maxlength="10" 
        inputmode="numeric" 
        pattern="[0-9]*" 
        oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
        placeholder="Your Phone Number"
        required>
    </div>
    <div class="error-message" id="phone-error"></div>
  </div>
</div>


                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="Birthdate" class="form-label">Birthdate <span class ="red"> *</span></label>
                                <input type="date" class="form-control" id="Birthdate" name="Birthdate" required>
                                <div class="error-message" id="Birthdate-error"></div>
                            </div>
                        </div>
                   
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="UploadedIDType" class="form-label">ID Type <span class ="red"> *</span></label>
<select class="form-control" id="UploadedIDType" name="UploadedIDType" required>
    <option value="">Select ID Type</option>
    <optgroup label="Suggested IDs">
        <option value="Passport" title="Government-issued international travel document">Passport</option>
        <option value="Driver's License" title="Official license for driving vehicles">Driver's License</option>
        <option value="PhilSys National ID" title="Philippine Identification System National ID card">PhilSys National ID</option>
        <option value="UMID Card" title="Unified Multi-Purpose ID for government employees and beneficiaries">UMID Card</option>
        <option value="Voter's ID" title="Certificate issued to registered voters by Comelec">Voter's ID</option>
    </optgroup>
    <optgroup label="Other Valid IDs">
        <option value="GSIS ID" title="Government Service Insurance System identification card">GSIS ID</option>
        <option value="PRC ID" title="Professional Regulation Commission license ID">PRC ID</option>
        <option value="Postal ID" title="Government-issued Postal identification card">Postal ID</option>
    </optgroup>
</select>

                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="UploadIDPhoto" class="form-label">Upload ID Photo <span class ="red"> *</span></label>
                        <input type="file" class="form-control" id="UploadIDPhoto" name="UploadIDPhoto" accept="image/*" required>
                        <small class="text-muted">Upload a clear photo of your valid ID. You can click to select a file or drag and drop it here.</small>

                        <div class="error-message" id="UploadIDPhoto-error"></div>
                    </div>
                    
                   <hr class="my-4">

<h5 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i> Address Information</h5>
<p class="text-muted mb-3" style="font-size: 0.95rem;">
    For greater accuracy, we recommend using the <strong>"Use My Location"</strong> feature to automatically fill in your address based on your current location. For best results, please avoid using VPNs or location-changing tools.
    <br>
    <small><strong>Your location is used only to improve form accuracy and is not shared.</strong></small>
</p>


<div class="mb-4">
    <button type="button" id="useLocationBtn" class="btn btn-outline-secondary w-100 location-btn">
        <i class="fas fa-location-arrow me-2"></i> Use My Location
    </button>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="streetAddress" class="form-label">Street Address <span class="red"> *</span></label>
            <input type="text" class="form-control" id="streetAddress" name="streetAddress" required>
            <div class="error-message" id="streetAddress-error"></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="city" class="form-label">City <span class="red"> *</span></label>
            <input type="text" class="form-control" id="city" name="city" required>
            <div class="error-message" id="city-error"></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 col-lg-4">
        <div class="form-group">
            <label for="state" class="form-label">State/Province <span class="red"> *</span></label>
            <input type="text" class="form-control" id="state" name="state" required>
            <div class="error-message" id="state-error"></div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4">
        <div class="form-group">
            <label for="postal" class="form-label">Postal Code <span class="red"> *</span></label>
            <input type="text" class="form-control" id="postal" name="postal"
                maxlength="4" 
                inputmode="numeric" 
                pattern="[0-9]*"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                required>
            <div class="error-message" id="postal-error"></div>
        </div>
    </div>
</div>
<div id="address-error" class="error-message" style="color: #dc3545; margin-top: 0.25rem;"></div>

<div id="map" class="mb-3"></div>
<label for="latitude">Latitude</label>
<input type="text" id="latitude" name="latitude" readonly>

<label for="longitude">Longitude</label>
<input type="text" id="longitude" name="longitude" readonly>

<hr class="my-4">
                    
                                  <h5 class="mb-3"><i class="fas fa-lock me-2"></i> Account Security</h5>
                    <p class="text-muted mb-3" style="font-size: 0.95rem;">
                        Create a strong password to protect your account and personal information. Use a mix of letters, numbers, and symbols for added security.
                        <br>
                        <small><strong>Your password is securely encrypted and never stored in plain text.</strong></small>
                    </p>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label">Password <span class ="red"> *</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <span class="input-group-text toggle-password" style="cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <div class="password-strength mt-2">
                                    <div class="password-strength-bar" id="password-strength-bar"></div>
                                </div>
                                <small class="text-muted">Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character.</small>
                                <div class="error-message" id="password-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirmPassword" class="form-label">Confirm Password <span class ="red"> *</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" required>
                                    <span class="input-group-text toggle-password" style="cursor: pointer;">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <div class="error-message" id="confirmPassword-error"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group form-check mt-4">
                        <input type="checkbox" class="form-check-input" id="termsCheck" required>
                        <label class="form-check-label" for="termsCheck">I have read and agree to the <span class="terms-link" id="termsLink">Terms and Conditions</span> <span class ="red"> *</span></label>
                        <div class="error-message" id="termsCheck-error"></div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                           Create Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_detection@0.4/face_detection.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils@0.3/camera_utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils@0.3/drawing_utils.js"></script>

    
    <script>
        document.getElementById("UploadIDPhoto").addEventListener("change", async function (e) {
  const file = e.target.files[0];
  if (!file) return;

  const reader = new FileReader();
  reader.onload = async () => {
    const imageData = reader.result;

    Swal.fire({
      title: "Validating ID...",
      text: "Please wait while we check your ID.",
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
    });

    try {
      // Run OCR with Tesseract
      const { data: { text } } = await Tesseract.recognize(imageData, "eng");
      const detectedID = detectIDType(text);

      // Run face detection
      const facesDetected = await detectFaces(imageData);

      Swal.close();

      if (facesDetected > 0 && detectedID) {
        Swal.fire({
          icon: "success",
          title: "ID Verified Successfully",
          html: `ID recognized as <b>${detectedID}</b>.`,
          confirmButtonColor: "#ffb84d"
        });
      } else {
        e.target.value = ""; // remove invalid file
        let reason = "❌ Invalid ID or no face detected.";
 if (facesDetected === 0 && detectedID)
  reason = "Invalid ID. Please upload a clearer image.";
if (facesDetected > 0 && !detectedID)
  reason = "Invalid ID. The text could not be recognized.";

        Swal.fire({
          icon: "error",
          title: "Validation Failed",
          html: reason,
          confirmButtonColor: "#ffb84d"
        });
      }
    } catch (err) {
      console.error(err);
      e.target.value = "";
      Swal.close();
      Swal.fire("Error", "Something went wrong during validation. Please try again.", "error");
    }
  };
  reader.readAsDataURL(file);
});

// === Detect ID Type (OCR keywords) ===
function detectIDType(text) {
  text = text.toLowerCase();
  if (text.includes("pasaporte") || text.includes("pilipinas pasaporte")) return "Philippine Passport";
  if (text.includes("umid") || text.includes("unified-multipurpose id")) return "SSS UMID CARD/ID";
  if (text.includes("philhealth") || text.includes("philippine health insurance corporation")) return "PhilHealth ID";
  if (text.includes("pag-ibig") || text.includes("hdmf")) return "Pag-IBIG ID";
  if (text.includes("driver") || text.includes("lto") || text.includes("non-professional driver's license") || text.includes("department of transportation") || text.includes("land transportation office")) return "Driver’s License";
  if (text.includes("philsys") || text.includes("national id") || text.includes("national identification") || text.includes("pambansang pagkakakilanlan")) return "PhilSys National ID";
  if (text.includes("postal") || text.includes("post office") || text.includes("postal identity card")) return "Postal ID";
  if (text.includes("voter") || text.includes("comelec") || text.includes("commission on elections")) return "Voter’s ID";
  return null;
}

// === Face Detection ===
async function detectFaces(imageData) {
  const img = new Image();
  img.src = imageData;
  await new Promise((resolve, reject) => {
    img.onload = resolve;
    img.onerror = reject;
  });

  return new Promise((resolve) => {
    const faceDetection = new FaceDetection({
      locateFile: (file) => `https://cdn.jsdelivr.net/npm/@mediapipe/face_detection@0.4/${file}`,
    });

    faceDetection.setOptions({
      model: "short",
      minDetectionConfidence: 0.3,
    });

    faceDetection.onResults((results) => {
      const count = results.detections ? results.detections.length : 0;
      resolve(count);
    });

    const canvas = document.createElement("canvas");
    canvas.width = img.width;
    canvas.height = img.height;
    const ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
    const frame = ctx.getImageData(0, 0, canvas.width, canvas.height);

    faceDetection.send({ image: frame });
  });
}
   function formatName(input) {
    let words = input.value.split(' ').filter(word => word.length > 0);
    let formatted = words.map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase());
    input.value = formatted.join(' ');
  }

  document.addEventListener('DOMContentLoaded', () => {
    const nameFields = ['Fname', 'Mname', 'Lname'];
    nameFields.forEach(id => {
      const input = document.getElementById(id);
      input.addEventListener('input', () => formatName(input));
    });
  });
  
const validEmails = [
  "@gmail.com",
  "@yahoo.com"
];

document.getElementById('valid-emails').addEventListener('click', () => {
  Swal.fire({
    title: 'Valid HomeHive Emails',
    html: `
      <p style="text-align:left; margin-bottom:10px;">
        Please use only personal email addresses for registration. Corporate, work, or temporary emails are not allowed.  
        Personal emails help us verify your identity, ensure account security, and allow you to receive important notifications directly.
      </p>
      <ul style="text-align:left;">
        ${validEmails.map(email => `<li>${email}</li>`).join('')}
      </ul>
      <p style="text-align:left; margin-top:10px; font-size:0.9em; color:#555;">
        Using a personal email keeps your HomeHive account safe and ensures you don’t miss important messages about your listings or contracts.
      </p>
    `,
    icon: 'info',
    confirmButtonText: 'I understand',
    didOpen: () => {
      const btn = Swal.getConfirmButton();
      btn.style.backgroundColor = '#FB8C00'; // HomeHive orange
      btn.style.color = '#fff';
      btn.style.fontWeight = 'bold';
      btn.style.borderRadius = '6px';
      btn.style.padding = '8px 20px';
      btn.style.outline = 'none';
      btn.style.boxShadow = 'none';
    }
  });
});

  
$('#UploadIDPhoto').on('change', function() {
    const fileInput = this;
    const file = fileInput.files[0];
    const errorDiv = $('#UploadIDPhoto-error');
    errorDiv.text('');

    if (!file) {
        errorDiv.text('Please select an ID photo.');
        return;
    }

    const maxSize = 5 * 1024 * 1024;
    if (file.size > maxSize) {
        errorDiv.text('File size must be less than 5MB.');
        fileInput.value = ''; 

        Swal.fire({
            icon: 'error',
            title: 'File Too Large',
            text: 'The ID photo must be less than 5MB. Please select a smaller file.',
            confirmButtonColor: '#F57C00'
        });

        return;
    }

    // Continue with other validations or upload...
});
    document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.getElementById('email');
    const errorDiv = document.getElementById('email-error');
    const validDiv = document.getElementById('email-valid');

    emailInput.addEventListener('input', function () {
        const email = emailInput.value.trim();

        // Reset styles for short or empty input
        if (email.length < 5 || !email.includes('@')) {
            errorDiv.textContent = '';
            validDiv.textContent = '';
            emailInput.classList.remove('is-valid', 'is-invalid');
            return;
        }

        // Regex to allow only Gmail and Yahoo emails
        const personalEmailRegex = /^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com)$/i;
        if (!personalEmailRegex.test(email)) {
            emailInput.classList.add('is-invalid');
            emailInput.classList.remove('is-valid');
            errorDiv.textContent = 'Please use a valid personal email.';
            validDiv.textContent = '';
            return;
        }

        // Optional: check availability via server
        fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'email_check=' + encodeURIComponent(email)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                if (data.exists) {
                    emailInput.classList.add('is-invalid');
                    emailInput.classList.remove('is-valid');
                    errorDiv.textContent = 'Email is already registered.';
                    validDiv.textContent = '';
                } else {
                    emailInput.classList.add('is-valid');
                    emailInput.classList.remove('is-invalid');
                    errorDiv.textContent = '';
                    validDiv.textContent = 'Email is available.';
                }
            } else {
                console.error(data.message);
            }
        })
        .catch(error => console.error('Fetch error:', error));
    });
});

//Validation Birthdate
  document.getElementById('Birthdate').addEventListener('change', function () {
        const birthdate = new Date(this.value);
        const today = new Date();

        // Calculate age
        let age = today.getFullYear() - birthdate.getFullYear();
        const m = today.getMonth() - birthdate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthdate.getDate())) {
            age--;
        }

        const errorDiv = document.getElementById('Birthdate-error');
        if (age < 18) {
            errorDiv.textContent = 'You must be at least 18 years old.';
            this.setCustomValidity('You must be at least 18 years old.');
        } else {
            errorDiv.textContent = '';
            this.setCustomValidity('');
        }
    });
        $(document).ready(function() {
            // Initialize map
            let map = L.map('map');
            let marker;
            
            // Toggle password visibility
            $('.toggle-password').click(function() {
                const input = $(this).parent().find('input');
                const icon = $(this).find('i');
                
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });
            
  
            // Use current location
            $('#useLocationBtn').click(function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                $('#latitude').val(lat);
                $('#longitude').val(lng);

                // Initialize map if not already done
               if (!map._loaded) {
    map.setView([lat, lng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    $('#map').show();

}

                // Reset marker before adding a new one
                if (marker) {
                    map.removeLayer(marker);
                    marker = null;
                }

                // Add new marker
                marker = L.marker([lat, lng]).addTo(map)
                    .bindPopup('Your location').openPopup();

                // Reverse geocode to fill address
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.address) {
                            $('#streetAddress').val(data.address.road || '');
                            $('#city').val(data.address.city || data.address.town || data.address.village || '');
                            $('#state').val(data.address.state || '');
                            $('#postal').val(data.address.postcode || '');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            },
            function(error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Location Error',
                    text: 'Unable to retrieve your location. Please enter your address manually.'
                });
            }
        );
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Not Supported',
            text: 'Geolocation is not supported by your browser.'
        });
    }
});

    // Manully Location 
            function fetchLatLngFromAddress() {
    const street = $('#streetAddress').val().trim();
    const city = $('#city').val().trim();
    const state = $('#state').val().trim();
    const postal = $('#postal').val().trim();

    if (!street && !city && !state && !postal) return;

    const fullAddress = `${street}, ${city}, ${state}, ${postal}`;
    
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullAddress)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const lat = data[0].lat;
                const lon = data[0].lon;

                $('#latitude').val(lat);
                $('#longitude').val(lon);

                // Update map and marker if available
             if (!map._loaded) {
    map.setView([lat, lon], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    $('#map').show();


}

                if (marker) {
                    marker.setLatLng([lat, lon]);
                } else {
                    marker = L.marker([lat, lon]).addTo(map)
                        .bindPopup(fullAddress).openPopup();
                }
            }
        })
        .catch(err => console.error("Geocoding error:", err));
}

// Trigger geocoding when address fields change
$('#streetAddress, #city, #state, #postal').on('blur', function() {
    fetchLatLngFromAddress();
});
            
            // Password strength checker
            $('#password').on('input', function() {
                const password = $(this).val();
                let strength = 0;
                
                // Length check
                if (password.length >= 8) strength += 25;
                
                // Contains uppercase
                if (/[A-Z]/.test(password)) strength += 25;
                
                // Contains number
                if (/\d/.test(password)) strength += 25;
                
                // Contains special character
                if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength += 25;
                
                // Update strength bar
                $('#password-strength-bar').css('width', strength + '%');
                
                // Update color
                if (strength < 50) {
                    $('#password-strength-bar').css('background-color', '#dc3545');
                } else if (strength < 75) {
                    $('#password-strength-bar').css('background-color', '#ffc107');
                } else {
                    $('#password-strength-bar').css('background-color', '#28a745');
                }
            });
            
            // Confirm password match
            $('#confirmPassword').on('input', function() {
                if ($(this).val() !== $('#password').val()) {
                    $('#confirmPassword-error').text('Passwords do not match.');
                } else {
                    $('#confirmPassword-error').text('');
                }
            });
            
            // Terms and conditions modal
            $('#termsLink').click(function() {
                $.get('terms.txt', function(data) {
                   Swal.fire({
    title: 'Terms and Conditions',
    html: `<div style="text-align: left; max-height: 60vh; overflow-y: auto;">${data.replace(/\n/g, '<br>')}</div>`,
    width: '800px',
    confirmButtonText: 'I Understand',
    confirmButtonColor: '#F57C00' 
});
                }).fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Could not load terms and conditions.'
                    });
                });
            });
            
            // Form validation before submission
   async function validateAddress() {
    const street = $('#streetAddress').val().trim();
    const city = $('#city').val().trim();
    const state = $('#state').val().trim();
    const postal = $('#postal').val().trim();

    if (!street && !city && !state && !postal) {
        // Empty address, maybe block or handle differently
        return false;
    }

    const fullAddress = `${street}, ${city}, ${state}, ${postal}`;
    
    try {
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(fullAddress)}`);
        const data = await response.json();
        return data.length > 0; // true if found, false if not
    } catch (error) {
        console.error("Address validation error:", error);
        return false; // fail safe
    }
}

// Inside your submit handler
$('#signupForm').submit(async function(e) {
    e.preventDefault();
    
    let isValid = true;

    // Reset error messages
    $('.error-message').text('');

    // Validate required fields
    const requiredFields = ['Fname', 'Lname', 'email', 'phone', 'Birthdate', 'streetAddress', 'city', 'state', 'postal', 'password', 'confirmPassword'];
    requiredFields.forEach(field => {
        if (!$('#' + field).val()) {
            $('#' + field + '-error').text('This field is required.');
            Swal.fire({
                icon: 'error',
                title: 'Missing Field',
                text: 'Please complete all required fields.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#F57C00'
            });
            isValid = false;
            return false; // Break out of forEach
        }
    });
    
    // Validate name lengths
    if ($('#Fname').val().length > 50) {
        $('#Fname-error').text('First name must be 50 characters or less.');
        Swal.fire({
            icon: 'error',
            title: 'Invalid First Name',
            text: 'First name must be 50 characters or less.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#F57C00'
        });
        isValid = false;
    }

    if ($('#Mname').val().length > 50) {
        $('#Mname-error').text('Middle name must be 50 characters or less.');
        Swal.fire({
            icon: 'error',
            title: 'Invalid Middle Name',
            text: 'Middle name must be 50 characters or less.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#F57C00'
        });
        isValid = false;
    }

    if ($('#Lname').val().length > 50) {
        $('#Lname-error').text('Last name must be 50 characters or less.');
        Swal.fire({
            icon: 'error',
            title: 'Invalid Last Name',
            text: 'Last name must be 50 characters or less.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#F57C00'
        });
        isValid = false;
    }


    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test($('#email').val())) {
        $('#email-error').text('Please enter a valid email address.');
        Swal.fire({
            icon: 'error',
            title: 'Invalid Email',
            text: 'Please enter a valid email address.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#F57C00'
        });
        isValid = false;
    }

if ($('#email').hasClass('is-invalid')) {
    Swal.fire({
        icon: 'error',
        title: 'Email Error',
        text: 'Check your email. It might already be used or not a personal email. See valid emails above the email field.',
        confirmButtonText: 'OK',
        confirmButtonColor: '#F57C00'
    });
    isValid = false;
}


  const lat = parseFloat($('#latitude').val());
const lng = parseFloat($('#longitude').val());

if (isNaN(lat) || isNaN(lng)) {
    Swal.fire({
        icon: 'error',
        title: 'Invalid Location',
        text: 'Please use the "Use My Location" button or enter a valid address with coordinates.',
        confirmButtonText: 'OK',
        confirmButtonColor: '#F57C00'
    });
    isValid = false;
}
const addressValid = await validateAddress();

    if (!addressValid) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid Address',
            text: 'The address you entered does not seem to be valid or found. Please check and try again.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#F57C00'
        });
        return false; // prevent form submission
    }
    // Validate password requirements
    const password = $('#password').val();
    if (password.length < 8) {
        $('#password-error').text('Password must be at least 8 characters long.');
        Swal.fire({
            icon: 'error',
            title: 'Weak Password',
            text: 'Password must be at least 8 characters long.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#F57C00'
        });
        isValid = false;
    } else if (!/[A-Z]/.test(password)) {
        $('#password-error').text('Password must contain at least one uppercase letter.');
        Swal.fire({
            icon: 'error',
            title: 'Weak Password',
            text: 'Password must contain at least one uppercase letter.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#F57C00'
        });
        isValid = false;
    } else if (!/\d/.test(password)) {
        $('#password-error').text('Password must contain at least one number.');
        Swal.fire({
            icon: 'error',
            title: 'Weak Password',
            text: 'Password must contain at least one number.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#F57C00'
        });
        isValid = false;
    } else if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
        $('#password-error').text('Password must contain at least one special character.');
        Swal.fire({
            icon: 'error',
            title: 'Weak Password',
            text: 'Password must contain at least one special character.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#F57C00'
        });
        isValid = false;
    }

    // Validate password match
    if ($('#password').val() !== $('#confirmPassword').val()) {
        $('#confirmPassword-error').text('Passwords do not match.');
        Swal.fire({
            icon: 'error',
            title: 'Password Mismatch',
            text: 'Passwords do not match.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#F57C00'
        });
        isValid = false;
    }
    
    // Validate terms checkbox
    if (!$('#termsCheck').is(':checked')) {
        $('#termsCheck-error').text('You must accept the terms and conditions.');
        Swal.fire({
            icon: 'error',
            title: 'Terms Required',
            text: 'You must accept the terms and conditions to register.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#F57C00'
        });
        isValid = false;
    }
                
                
               if (isValid) {
    Swal.fire({
        title: 'Confirm Your Information',
        html: `
            <div style="text-align: left;">
                <p><strong>Name:</strong> ${$('#Fname').val()} ${$('#Mname').val()} ${$('#Lname').val()}</p>
                <p><strong>Email:</strong> ${$('#email').val()}</p>
                <p><strong>Phone:</strong> ${$('#phone').val()}</p>
                <p><strong>Birthdate:</strong> ${$('#Birthdate').val()}</p>
                <p><strong>Address:</strong> ${$('#streetAddress').val()}, ${$('#city').val()}, ${$('#state').val()} ${$('#postal').val()}</p>
                <p><strong>ID Type:</strong> ${$('#UploadedIDType').val()}</p>
            </div>
        `,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, everything is correct',
        cancelButtonText: 'No, I need to make changes',
        buttonsStyling: false,
        didOpen: () => {
            const confirmBtn = Swal.getConfirmButton();
            const cancelBtn = Swal.getCancelButton();

            if (confirmBtn) {
                confirmBtn.style.backgroundColor = '#F57C00'; 
                confirmBtn.style.color = '#fff';
                confirmBtn.style.border = 'none';
                confirmBtn.style.padding = '8px 16px';
                confirmBtn.style.borderRadius = '4px';
                confirmBtn.style.fontWeight = '500';
                confirmBtn.style.cursor = 'pointer';
            }

            if (cancelBtn) {
                cancelBtn.style.backgroundColor = '#5D4037'; 
                cancelBtn.style.color = '#fff';
                cancelBtn.style.border = 'none';
                cancelBtn.style.padding = '8px 16px';
                cancelBtn.style.borderRadius = '4px';
                cancelBtn.style.fontWeight = '500';
                cancelBtn.style.marginLeft = '10px';
                cancelBtn.style.cursor = 'pointer';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Creating your account...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    document.getElementById('signupForm').submit();
                }
            });
        }
    });
}
            });
        });
    </script>
</body>
</html>