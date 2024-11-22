<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Page</title>
    <link rel="stylesheet" href="css/customer.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <style>
        /* Map Styling */
        #map {
            margin-top: 85px;
            height: 100%;
            width: 100%;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <img src="img/LMS logo.png" alt="Logo">
            </div>
            <div class="auth-buttons">
                <ul class="nav-links">
                    <li><a href="homepage.php">Home</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
                <a href="customer_login.php"><button class="login">Log In</button></a>
                <span>or</span>
                <a href="customer_signup.php"><button class="signup">Sign Up</button></a>
            </div>
        </nav>
    </header>

    <section class="contact-info">
        <div class="map">
            <div id="map"></div> <!-- Correct map container -->
        </div>
        <div class="contact-details">
            <h2>You can find us here!</h2>
            <p>2nd Floor JC Roces Bldg, C.M. Recto St, Brgy. 4, Lucena City, 4301 Quezon</p>
            <hr>
            <h2>You can call us!</h2>
            <p>09388190272</p>
        </div>
    </section>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        var map = L.map('map').setView([13.9374, 121.6170], 13);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add a marker
        L.marker([13.9374, 121.6170]).addTo(map)
            .bindPopup('A pretty CSS3 popup.<br> Easily customizable.')
            .openPopup();
    </script>
</body>
</html>
