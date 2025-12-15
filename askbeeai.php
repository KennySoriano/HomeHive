<?php

// === PHP Part (AJAX search endpoint) ===
if (isset($_GET['action']) && $_GET['action'] === 'search') {
    require_once 'config.php';

    $location = isset($_GET['location']) ? trim($_GET['location']) : '';
    $price    = isset($_GET['price']) ? (float)$_GET['price'] : 0;
    $type     = isset($_GET['type']) ? trim($_GET['type']) : '';

    $type = strtolower($type);

    // main query: include first image
    $sql = "
    SELECT p.id, p.name, p.location, p.price, p.type,
           (SELECT image_path FROM apartmentimages ai 
            WHERE ai.apartment_id = p.id 
            LIMIT 1) AS image_path
    FROM properties p 
    WHERE p.status='Approved'";
    $result = $conn->query($sql);

    $properties = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $row['image_path'] = convertImagePath($row['image_path']);
            $pLoc = strtolower($row['location']);
            $pType = strtolower($row['type']);
            $match = true;

            if ($location !== '') {
                $locKeywords = explode(' ', strtolower($location));
                $match = false;
                foreach ($locKeywords as $kw) {
                    if (strpos($pLoc, $kw) !== false) {
                        $match = true;
                        break;
                    }
                }
            }

            if ($match && $price > 0 && $row['price'] > $price) $match = false;
            if ($match && $type !== '' && strpos($pType, $type) === false) $match = false;

            if ($match) $properties[] = $row;
        }
    }

    $suggestions = [];
    if (empty($properties)) {
        $sql2 = "
        SELECT p.id, p.name, p.location, p.price, p.type,
               (SELECT image_path FROM apartmentimages ai 
                WHERE ai.apartment_id = p.id 
                LIMIT 1) AS image_path
        FROM properties p 
        WHERE p.status='Approved'
        ORDER BY ABS(p.price - $price) ASC 
        LIMIT 3";
        $res2 = $conn->query($sql2);
        while ($res2 && $row2 = $res2->fetch_assoc()) {
            $row2['image_path'] = convertImagePath($row2['image_path']);
            $suggestions[] = $row2;
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['results' => $properties, 'suggestions' => $suggestions]);
    exit;
}

function convertImagePath($path) {
    if (!$path) return null;
    $path = str_replace('\\', '/', $path);

    if (strpos($path, '/public_html/') !== false) {
        $relative = substr($path, strpos($path, '/public_html/') + strlen('/public_html/'));
        if (strpos($relative, 'userdashboard/') === false) $relative = 'userdashboard/' . ltrim($relative, '/');
        return 'https://homehiveph.site/' . ltrim($relative, '/');
    }

    if (strpos($path, '/opt/lampp/htdocs/HomeHiveOfficial/') === 0) {
        $relative = str_replace('/opt/lampp/htdocs/HomeHiveOfficial/', '', $path);
        if (strpos($relative, 'userdashboard/') === false) $relative = 'userdashboard/' . ltrim($relative, '/');
        return 'https://homehiveph.site/' . ltrim($relative, '/');
    }

    if (strpos($path, 'uploads/') === 0) {
        $relative = $path;
        if (strpos($relative, 'userdashboard/') === false) $relative = 'userdashboard/' . ltrim($relative, '/');
        return 'https://homehiveph.site/' . ltrim($relative, '/');
    }

    if (filter_var($path, FILTER_VALIDATE_URL)) return $path;

    return 'https://homehiveph.site/userdashboard/' . ltrim($path, '/');
}
?>
<div id="askBeeAI">
  <img src="https://homehiveph.site/AI/chat-bot/beeai.gif" 
       alt="Bee Icon" 
       class="bee-icon">
  <span class="bee-text">BeeAI</span>
  <span class="bee-click">Click Me!</span>
</div>

<style>
#askBeeAI {
    position: fixed;
    top: 30%;
    right: 20px;
    transform: translateY(-50%);
    background: #FB8C00;
    color: #fff;
    padding: 0;
    border-radius: 50px;
    font-weight: bold;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    z-index: 9999;
    display: flex;
    align-items: center;
    height: 50px;
    width: 50px;
    overflow: visible;
    transition: width 0.3s ease, padding 0.3s ease, box-shadow 0.3s ease;
}

#askBeeAI.expanded {
    width: 160px;
    padding: 0 12px;
    animation: shake 0.5s ease-in-out infinite;
    box-shadow: 0 0 12px #FFB347, 0 0 20px #FF6600;
}

#askBeeAI .bee-icon {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 50%;
    flex-shrink: 0;
    transform: scale(1.2);
    transform-origin: center;
    pointer-events: none;
    transition: transform 0.3s ease;
}

#askBeeAI.expanded .bee-icon {
    transform: scale(1.2);
}

#askBeeAI .bee-text {
    margin-left: 10px;
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.3s ease;
}

#askBeeAI.expanded .bee-text {
    opacity: 1;
}

#askBeeAI .bee-click {
    position: absolute;
    right: 100%;
    top: 50%;
    transform: translateY(-50%) translateX(10px);
    background: #FF6600;
    color: #fff;
    padding: 2px 6px;
    border-radius: 12px;
    font-weight: bold;
    font-size: 12px;
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

#askBeeAI.expanded .bee-click {
    opacity: 1;
    transform: translateY(-50%) translateX(0);
}

@keyframes shake {
  0% { transform: translateY(-50%) translateX(0); }
  25% { transform: translateY(-50%) translateX(-3px); }
  50% { transform: translateY(-50%) translateX(3px); }
  75% { transform: translateY(-50%) translateX(-3px); }
  100% { transform: translateY(-50%) translateX(0); }
}

@media (max-width: 480px) {
    #askBeeAI.expanded { width: 140px; }
    #askBeeAI { height: 45px; width: 45px; }
    #askBeeAI .bee-icon { width: 45px; height: 45px; }
    #askBeeAI .bee-click { font-size: 10px; padding: 1px 4px; }
}
/* SweetAlert2 Steps 1-2-3 Recolor */
.swal2-progress-steps .swal2-progress-step,
.swal2-progress-steps .swal2-progress-step-line {
    background-color: #FF6600 !important; /* inactive steps and lines */
    border-color: #FF6600 !important;
    color: #fff !important;
}

.swal2-progress-steps .swal2-progress-step.swal2-active-progress-step {
    background-color: #5D4037 !important; /* active step */
    color: #fff !important;
}

.swal2-progress-steps .swal2-progress-step-line {
    border-top-color: #FF6600 !important; /* connecting lines */
}

</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const messages = [
  "Need Help?", "Start Here!", "Let’s Find!", "Your Home!", "Choose Smart!", "Explore Now!", "Find Quickly!", "I Can Help!"
];
let msgIndex = 0;

const beeButton = document.getElementById('askBeeAI');
const beeClickText = beeButton.querySelector(".bee-click");
let animationPaused = false;

// Message rotation
setInterval(() => {
    if(animationPaused) return;
    beeClickText.textContent = messages[msgIndex];
    msgIndex = (msgIndex + 1) % messages.length;
    beeButton.classList.add('expanded');
    setTimeout(() => beeButton.classList.remove('expanded'), 3000);
}, 8000);

beeButton.addEventListener('click', async function() {
    animationPaused = true;
    beeButton.classList.add('paused');

    // Step 1: Introduction greeting
    const { isConfirmed } = await Swal.fire({
        title: `<img src="https://homehiveph.site/AI/chat-bot/beeai.gif" alt="BeeAI" style="display:block; margin:0 auto; width:80px;"> Ask BeeAI`,
        html: `<div style="color:#3B2A24; font-weight:bold; margin-top:10px;">
                I’ll help you find your perfect home on HomeHive.<br><br>
                <small>Please note: BeeAI can make mistakes and may not find a result if there are few listings.</small><br><br>
                Ready to begin?</div>`,
        confirmButtonText: 'Get Started',
        cancelButtonText: 'Cancel',
        showCancelButton: true,
        reverseButtons: true,
        background: '#FFF8F3',
        confirmButtonColor: '#FF6600',
        color: '#3B2A24',
    });

    animationPaused = false;
    beeButton.classList.remove('paused');
    if(!isConfirmed) return;

    // Step 2: Location
    const { value: location } = await Swal.fire({
        title: 'Step 1: Choose Location',
        input: 'text',
        inputPlaceholder: 'Enter city or area',
        confirmButtonText: 'Next',
        showCancelButton: true,
        progressSteps: ['1','2','3'],
        currentProgressStep: 0,
        reverseButtons: true,
        background: '#FFF8F3',
        confirmButtonColor: '#FF6600',
        didOpen: () => {
            const inputEl = Swal.getInput();
            const confirmBtn = Swal.getConfirmButton();
            confirmBtn.disabled = true;
            inputEl.addEventListener('input', () => { confirmBtn.disabled = inputEl.value.trim() === ''; });
        }
    });
    if(!location) return;

    // Step 3: Budget
    const { value: price } = await Swal.fire({
        title: 'Step 2: Enter Max Price',
        input: 'number',
        inputPlaceholder: 'Ex: 15000',
        confirmButtonText: 'Next',
        showCancelButton: true,
        reverseButtons: true,
        progressSteps: ['1','2','3'],
        currentProgressStep: 1,
        background: '#FFF8F3',
        confirmButtonColor: '#FF6600',
        didOpen: () => {
            const inputEl = Swal.getInput();
            const confirmBtn = Swal.getConfirmButton();
            confirmBtn.disabled = true;
            inputEl.addEventListener('input', () => { confirmBtn.disabled = inputEl.value.trim() === ''; });
        }
    });
    if(!price) return;

    // Step 4: Property Type
    const { value: type } = await Swal.fire({
        title: 'Step 3: Choose Property Type',
        input: 'select',
        inputOptions: {
            'House': 'House',
            '2 Bedroom': '2 Bedroom',
            '3 Bedroom': '3 Bedroom',
            'Studio': 'Studio',
            'Condo': 'Condo',
            'Townhouse': 'Townhouse',
            'Apartment': 'Apartment'
        },
        inputPlaceholder: 'Select a type',
        confirmButtonText: 'Search',
        showCancelButton: true,
        reverseButtons: true,
        progressSteps: ['1','2','3'],
        currentProgressStep: 2,
        background: '#FFF8F3',
        confirmButtonColor: '#FF6600',
        didOpen: () => {
            const inputEl = Swal.getInput();
            const confirmBtn = Swal.getConfirmButton();
            confirmBtn.disabled = true;
            inputEl.addEventListener('change', () => { confirmBtn.disabled = !inputEl.value; });
        }
    });
    if(!type) return;

    // AJAX search
    fetch('askbeeai.php?action=search&location=' + encodeURIComponent(location) +
          '&price=' + encodeURIComponent(price) +
          '&type=' + encodeURIComponent(type))
    .then(res => res.json())
    .then(data => {
        let html = `<div style="text-align:left; max-height:300px; overflow-y:auto; padding-right:10px;">`;

        function propertyCard(p) {
            const imageURL = p.image_path ? p.image_path : 'https://ralfvanveen.com/wp-content/uploads/2021/06/Placeholder-_-Glossary.svg';
            return `
            <a href="https://homehiveph.site/viewProperty?id=${p.id}" target="_blank" 
               style="display:block; background:#fff; border:1px solid #ddd; border-radius:8px; margin-bottom:12px; overflow:hidden; box-shadow:0 2px 4px rgba(0,0,0,0.1); text-decoration:none;">
             <img src="${imageURL}" loading="lazy" alt="Property Image" style="width:100%; height:180px; object-fit:cover;">

              <div style="padding:10px;">
                <div style="font-weight:bold;color:#3B2A24;">${p.name}</div>
                <div style="color:#3B2A24;">${p.location} - ₱${p.price}</div>
              </div>
            </a>`;
        }

        if (data.results.length === 0) {
          html += `<div style="margin-bottom:8px;font-weight:bold;">No results, yet these homes are waiting for you:</div>`;
          data.suggestions.forEach(p => html += propertyCard(p));
        } else {
          data.results.forEach(p => html += propertyCard(p));
        }

        html += '</div>';

        Swal.fire({
          title: 'Properties for You',
          html: html,
          showConfirmButton: true,
          confirmButtonText: 'Close',
          background: '#FFF8F3',
          confirmButtonColor: '#FF6600',
          color: '#3B2A24',
          width: '600px'
        });
    });
});
</script>
