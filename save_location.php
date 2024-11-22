<?php
include 'db_connect.php';

$province_id = $_POST['province'];
$city_id = $_POST['city'];
$barangay_id = $_POST['barangay'];

// Get barangay latitude and longitude
$query = "SELECT latitude, longitude FROM barangays WHERE barangay_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $barangay_id);
$stmt->execute();
$result = $stmt->get_result();
$barangay = $result->fetch_assoc();

$latitude = $barangay['latitude'];
$longitude = $barangay['longitude'];

// Save the selected location and coordinates in the database, for example linking to a loan application
$query = "INSERT INTO selected_locations (province_id, city_id, barangay_id, latitude, longitude) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiidd", $province_id, $city_id, $barangay_id, $latitude, $longitude);
$stmt->execute();

echo "Location saved successfully!";
?>
