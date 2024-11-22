<?php
// Start the session and set the timezone at the beginning
session_start();
date_default_timezone_set("Etc/GMT+8");

// Include the database connection only once
include 'db_connect.php'; 

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit;
}

// Retrieve the user_id from the session
$user_id = $_SESSION['user_id'];

// Query to get the user's name and user type from the database
$user_query = "SELECT name, user_type FROM user WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc(); // Fetch user details as an associative array
$stmt->close();

// Fetch all addresses with latitude and longitude and full address details
$query = "
    SELECT ca.address_id, c.firstname, c.lastname,
           b.barangay_name, ci.city_name, p.province_name,
           ca.latitude, ca.longitude
    FROM customer_address ca
    INNER JOIN customer c ON ca.customer_id = c.customer_id
    INNER JOIN barangays b ON ca.barangay = b.barangay_id
    INNER JOIN cities ci ON ca.city = ci.city_id
    INNER JOIN provinces p ON ca.province = p.province_id
";
$result = $conn->query($query);

$locations = [];
$existingCoordinates = [];
$offset = 0.0001; // Smaller offset value for distinguishing pins

// Check if there are results and process them
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $latitude = $row['latitude'];
        $longitude = $row['longitude'];
        
        // Check if there are other pins with the same coordinates
        $key = $latitude . ',' . $longitude;
        if (isset($existingCoordinates[$key])) {
            // Apply jitter to create a slight random offset for each pin
            $latitude += (rand(-100, 100) / 100000);  // Random offset between -0.001 and 0.001 degrees
            $longitude += (rand(-100, 100) / 100000);
        }

        // Construct the full address
        $full_address = $row['barangay_name'] . ', ' . $row['city_name'] . ', ' . $row['province_name'];
        
        // Store the location data
        $locations[] = [
            'address_id' => $row['address_id'],
            'name' => $row['firstname'] . ' ' . $row['lastname'],
            'latitude' => $latitude,
            'longitude' => $longitude,
            'full_address' => $full_address
        ];
        
        // Mark this location as used
        $existingCoordinates[$key][] = true;
    }
}
?>

<?php date_default_timezone_set("Etc/GMT+8");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Management System Dashboard</title>
    <link rel="stylesheet" href="css/employee.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-heat@0.3.0/dist/leaflet-heat.js"></script> <!-- Add Leaflet Heatmap Plugin -->
    <style>
    #map {
        height: 600px;
        width: 100%;
    }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header" onclick="toggleSidebar()">
        <span class="role">PANEL</span>
        <span class="dropright-icon"><</span>
    </div>
    <ul class="nav-links">
        <li class="nav-item">
            <a href="admin_home.php" class="nav-link">
                <img src="img/dashboard.png" alt="Home">
                <span class="text">Home</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="admin_customers.php" class="nav-link">
                <img src="img/customer.png" alt="Customers">
                <span class="text">Customers</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="admin_loans.php" class="nav-link">
                <img src="img/loans.png" alt="Loans">
                <span class="text">Loans</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="admin_payments.php" class="nav-link">
                <img src="img/credit-card.png" alt="Payments">
                <span class="text">Payments</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="admin_map.php" class="nav-link active">
                <img src="img/map.png" alt="Map">
                <span class="text">Map</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="loan_plans.php" class="nav-link">
                <img src="img/plans.png" alt="Plans">
                <span class="text">Loan Plans</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="accounts.php" class="nav-link">
                <img src="img/accs.png" alt="Accounts">
                <span class="text">Accounts</span>
            </a>
        </li>
    </ul>
</div>

    <div class="main-content">
        <header>
            <h1><b>Loan Management System</b></h1>
            <div class="user-wrapper">
                <div class="notification">
                    <span class="icon" onclick="showNotifications()" style="cursor: pointer;">🔔</span>
                </div>
                <div class="user" onclick="toggleDropdown()">
                    <span class="dropdown-icon">▼</span>
                    <span class="username">
                        <?php echo htmlspecialchars($user['name']); ?> <!-- Display the user's name -->
                    </span>
                    <div class="dropdown-content">
                        <a href="index.php" class="logout-btn">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <main>
            <h2>Map</h2>
            <div class="search-btn" style="display: flex; align-items: center; justify-content: flex-end;">
                <input type="text" id="search-bar" placeholder="Search Pensioner's Address" style="padding: 8px; width: 250px; margin-right: 5px;">
                <button onclick="searchPensioner()" style="margin-right:20px;">Search</button>
                <!-- Buttons to switch map view -->
                <button onclick="toggleHeatMap()">Show Heat Map</button>
                <button onclick="showMarkers()">Show Markers</button>
            </div>
            <div class="cards">
                <div id="map"></div>
            </div>
        </main>
    </div>
<script src="scripts.js"></script>
<!-- Leaflet JS -->
<script>
    var map = L.map('map').setView([14.1640, 121.5512], 10); // Center the map on Quezon Province

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // PHP array passed to JavaScript with full address information
    var locations = <?php echo json_encode($locations); ?>;

    var markers = [];
    locations.forEach(function(location) {
        var marker = L.marker([location.latitude, location.longitude], {draggable: true})
            .addTo(map)
            .bindPopup('<b>' + location.name + '</b><br>' + location.full_address); // Display full address

        markers.push(marker);

        marker.on('dragend', function (e) {
            var newLatLng = e.target.getLatLng(); // Get the new latitude and longitude
            var latitude = newLatLng.lat;
            var longitude = newLatLng.lng;

            // Send the updated coordinates to the server via AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_coordinates.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("address_id=" + location.address_id + "&latitude=" + latitude + "&longitude=" + longitude);

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert("Location updated successfully!");
                }
            };
        });
    });

    var heatmapLayer;

    // Function to toggle the heat map on/off
    function toggleHeatMap() {
        if (heatmapLayer) {
            map.removeLayer(heatmapLayer); // Remove the heatmap layer if it's already there
            heatmapLayer = null;
        } else {
            var heatData = locations.map(function(location) {
                return [location.latitude, location.longitude, 0.5]; // Add intensity as 0.5 for now
            });

            heatmapLayer = L.heatLayer(heatData, {
                radius: 20,
                blur: 15,
                maxZoom: 17
            }).addTo(map);
        }
    }

    // Function to show the markers
    function showMarkers() {
        markers.forEach(function(marker) {
            marker.addTo(map); // Add the marker back to the map
        });
        if (heatmapLayer) {
            map.removeLayer(heatmapLayer); // Remove heatmap if markers are shown
        }
    }

    // Function to search for a pensioner
    function searchPensioner() {
        var searchQuery = document.getElementById("search-bar").value.trim().toLowerCase();
        var found = false;

        locations.forEach(function(location) {
            var fullName = location.name.toLowerCase();

            if (fullName.includes(searchQuery)) {
                map.setView([location.latitude, location.longitude], 16); // Zoom to the marker
                L.popup()
                    .setLatLng([location.latitude, location.longitude])
                    .setContent('<b>' + location.name + '</b><br>' + location.full_address) // Show full address in search popup
                    .openOn(map);
                found = true;
            }
        });

        if (!found) {
            alert("Pensioner not found.");
        }
    }

</script>
</body>
</html>
