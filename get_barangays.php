<?php
include 'db_connect.php'; // Database connection

if (isset($_POST['city_id'])) {
    $city_id = $_POST['city_id'];

    // Query to fetch barangays based on the selected city
    $query = "SELECT barangay_id, barangay_name FROM barangays WHERE city_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $city_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<option value="">Select Barangay</option>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['barangay_id'] . '">' . $row['barangay_name'] . '</option>';
        }
    } else {
        echo '<option value="">No barangays available</option>';
    }
    $stmt->close();
}
?>
