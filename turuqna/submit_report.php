<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if (isset($_POST['submit_report'])) {
    $user_id = $_SESSION['user_id'];
    $desc = $_POST['description'];
    $lat = $_POST['latitude'];
    $lng = $_POST['longitude'];
    
    // 1. Check Location
    if(empty($lat) || empty($lng)) {
        $message = "Error: Location is missing. Please click on the map.";
        $msg_color = "red";
    } else {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        
        $image_path = "";
        $uploadOk = 1;

        // 2. Logic: Camera Data OR File Upload
        if (!empty($_POST['camera_image_data'])) {
            $data = $_POST['camera_image_data'];
            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $file_name = time() . "_cam.png";
            $target_file = $target_dir . $file_name;
            file_put_contents($target_file, $data);
            $image_path = $target_file;

        } elseif (!empty($_FILES["traffic_image"]["name"])) {
            $image_name = basename($_FILES["traffic_image"]["name"]);
            $target_file = $target_dir . time() . "_" . $image_name;
            if (move_uploaded_file($_FILES["traffic_image"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
            } else {
                $message = "Failed to upload file."; 
                $msg_color = "red"; 
                $uploadOk = 0;
            }
        } else {
            $message = "Error: You must provide a photo."; 
            $msg_color = "red"; 
            $uploadOk = 0;
        }

        // 3. Save to Database
        if ($uploadOk && $image_path != "") {
            $stmt = $conn->prepare("INSERT INTO reports (user_id, description, latitude, longitude, image_path) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("isdds", $user_id, $desc, $lat, $lng, $image_path);
            
            if ($stmt->execute()) { 
                $message = "Report submitted successfully!"; 
                $msg_color = "green";
            } else { 
                $message = "Database Error: " . $conn->error; 
                $msg_color = "red";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Submit Report - Turuqna</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    
    <style>
        /* --- COMPACT LAYOUT CSS --- */
        .main-content {
            background-color: #f4f6f8;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .report-card {
            background: white;
            width: 100%;
            max-width: 900px;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            resize: none;
            height: 80px;
        }

        /* --- ACTION BUTTONS (Select Photo | Use Camera) --- */
        .action-row {
            display: flex;
            gap: 10px;
            margin-top: 5px;
        }

        .action-btn {
            flex: 1; /* Equal width */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            background-color: white;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            color: #555;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .action-btn:hover { background-color: #f5f5f5; }

        /* Specific Colors */
        .btn-select-photo { border-color: #00695C; color: #00695C; background-color: #e0f2f1; }
        .btn-use-camera { border-color: #15356A; color: #15356A; background-color: #e8eaf6; }

        /* --- HIDDEN INPUT --- */
        #real-file-input { display: none; }
        
        #file-name-display {
            font-size: 12px;
            color: green;
            margin-top: 5px;
            font-weight: bold;
            display: none; /* Hidden until file selected */
        }

        /* --- CAMERA STYLING --- */
        #camera-wrapper {
            background: #000;
            padding: 5px;
            border-radius: 6px;
            text-align: center;
            display: none; 
            margin-top: 10px;
        }
        #video-feed { width: 100%; height: 200px; object-fit: cover; border-radius: 4px; }
        
        .cam-controls { display: flex; justify-content: center; gap: 10px; margin-top: 5px; }
        .snap-btn { background: #2ecc71; color: white; border: none; padding: 6px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;}
        .close-cam-btn { background: #e74c3c; color: white; border: none; padding: 6px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;}

        /* --- MAP STYLING --- */
        #map {
            width: 100%;
            height: 100%;
            min-height: 320px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        /* --- SUBMIT BUTTON --- */
        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #00695C;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        .submit-btn:hover { background-color: #004D40; }

        label { font-weight: bold; font-size: 14px; color: #333; display: block; margin-bottom: 5px;}
        .msg-box { padding: 10px; text-align: center; border-radius: 5px; margin-bottom: 15px; font-weight: bold; }
        .msg-green { background: #d4edda; color: #155724; }
        .msg-red { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body style="display: block;">

<div class="dashboard-container">
    <div class="sidebar">
        <h2>Turuqna</h2>
        <a href="dashboard_citizen.php">Live Map</a>
        <a href="submit_report.php" class="active">Submit Report</a>
        <a href="my_reports.php">My Reports</a>
			<a href="notifications.php">Notifications</a>
		<a href="profile.php">My Profile</a>
		
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <div class="main-content">
        <div class="report-card">
            <h2 style="text-align: center; margin: 0; color: #00695C;">Submit Traffic Report</h2>

            <?php if($message): ?>
                <div class="msg-box <?php echo ($msg_color == 'green') ? 'msg-green' : 'msg-red'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data" id="reportForm">
                
                <div class="form-grid">
                    
                    <!-- LEFT COLUMN -->
                    <div class="left-col">
                        <label>Description</label>
                        <textarea name="description" placeholder="Describe the congestion..." required></textarea>

                        <label style="margin-top: 15px;">Evidence Photo</label>
                        
                        <!-- NEW ACTION ROW: Select Photo | Use Camera -->
                        <div class="action-row">
                            <button type="button" class="action-btn btn-select-photo" onclick="triggerFileUpload()">
                                 Select Photo
                            </button>
                            
                            <button type="button" class="action-btn btn-use-camera" onclick="openCameraUI()">
                                 Use Camera
                            </button>
                        </div>

                        <!-- 1. HIDDEN FILE INPUT -->
                        <input type="file" name="traffic_image" id="real-file-input" accept="image/*" onchange="handleFileSelect()">
                        <div id="file-name-display"></div>

                        <!-- 2. CAMERA SECTION (Hidden by default) -->
                        <div id="camera-wrapper">
                            <video id="video-feed" autoplay playsinline></video>
                            <div class="cam-controls">
                                <button type="button" class="snap-btn" onclick="takeSnapshot()">Snap</button>
                                <button type="button" class="close-cam-btn" onclick="closeCameraUI()">Close</button>
                            </div>
                            <canvas id="canvas" style="display:none;"></canvas>
                        </div>

                        <!-- PREVIEW IMAGE -->
                        <img id="captured-preview" style="display:none; width: 100%; height: 150px; object-fit: cover; border-radius: 6px; margin-top: 10px; border: 2px solid #00695C;">
                        
                        <!-- HIDDEN DATA -->
                        <input type="hidden" name="camera_image_data" id="camera-data">
                    </div>

                    <!-- RIGHT COLUMN: Map -->
                    <div class="right-col">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <label style="margin:0;">Location</label>
                            <span id="location-status" style="font-size: 12px; color: #00695C; font-weight: bold;">Detecting GPS...</span>
                        </div>
                        <div id="map"></div>
                        <input type="hidden" name="latitude" id="lat" required>
                        <input type="hidden" name="longitude" id="lng" required>
                    </div>

                </div> 

                <button type="submit" name="submit_report" class="submit-btn">Submit Report</button>
            </form>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
    // --- MAP LOGIC ---
    var map = L.map('map').setView([26.2172, 50.1971], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    var marker;

    function updateLocation(lat, lng) {
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], 15);
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
        document.getElementById('location-status').innerHTML = "Location Locked ✅";
        document.getElementById('location-status').style.color = "green";
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(p) { updateLocation(p.coords.latitude, p.coords.longitude); }, 
            function(e) { 
                document.getElementById('location-status').innerHTML = "GPS Failed. Click Map."; 
                document.getElementById('location-status').style.color = "red";
            }
        );
    }
    map.on('click', function(e) { updateLocation(e.latlng.lat, e.latlng.lng); });

    // --- UPLOAD LOGIC ---
    function triggerFileUpload() {
        // Reset camera data if present
        document.getElementById('camera-data').value = '';
        closeCameraUI();
        document.getElementById('real-file-input').click();
    }

    function handleFileSelect() {
        const fileInput = document.getElementById('real-file-input');
        const nameDisplay = document.getElementById('file-name-display');
        const preview = document.getElementById('captured-preview');
        
        if (fileInput.files.length > 0) {
            nameDisplay.innerHTML = "✅ Selected: " + fileInput.files[0].name;
            nameDisplay.style.display = 'block';
            preview.style.display = 'none'; // Hide camera preview if exists
        }
    }

    // --- CAMERA LOGIC ---
    const camWrapper = document.getElementById('camera-wrapper');
    const preview = document.getElementById('captured-preview');
    const camData = document.getElementById('camera-data');
    const video = document.getElementById('video-feed');
    const canvas = document.getElementById('canvas');
    let stream = null;

    function openCameraUI() {
        // Reset file input
        document.getElementById('real-file-input').value = ''; 
        document.getElementById('file-name-display').style.display = 'none';
        preview.style.display = 'none';
        
        camWrapper.style.display = 'block';
        startCamera();
    }

    function closeCameraUI() {
        camWrapper.style.display = 'none';
        stopCamera();
    }

    async function startCamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
        } catch (err) { alert("Camera Error: " + err); }
    }

    function stopCamera() {
        if (stream) { stream.getTracks().forEach(track => track.stop()); stream = null; }
    }

    function takeSnapshot() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        
        let dataURL = canvas.toDataURL('image/png');
        preview.src = dataURL;
        preview.style.display = 'block';
        camData.value = dataURL;
        
        closeCameraUI(); // Hide camera and show preview
    }
</script>

</body>
</html>