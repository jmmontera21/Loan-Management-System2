<?php
include 'db_connect.php'; // Database connection

if (isset($_POST['province_id'])) {
    $province_id = $_POST['province_id'];

    // Query to fetch cities based on the selected province
    $query = "SELECT city_id, city_name FROM cities WHERE province_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $province_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<option value="">Select City</option>';
        while ($row = $result->fetch_assoc()) {
            echo '<option value="' . $row['city_id'] . '">' . $row['city_name'] . '</option>';
        }
    } else {
        echo '<option value="">No cities available</option>';
    }
    $stmt->close();
}
?>
