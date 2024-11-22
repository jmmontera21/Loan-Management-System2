<?php
include 'db_connect.php';

if (isset($_POST['address_id']) && isset($_POST['latitude']) && isset($_POST['longitude'])) {
    $address_id = $_POST['address_id'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Update the customer_address table with the new latitude and longitude
    $query = "UPDATE customer_address SET latitude = ?, longitude = ? WHERE address_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ddi", $latitude, $longitude, $address_id);

    if ($stmt->execute()) {
        echo "Location updated successfully.";
    } else {
        echo "Failed to update location.";
    }

    $stmt->close();
}

$conn->close();
?>
