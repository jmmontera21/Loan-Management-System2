<?php
session_start(); // Start the session if not already started
include 'db_connect.php'; // Include database connection file

// Check if the user is already logged in
if (isset($_SESSION['email'])) {
    // Fetch user information based on the email stored in the session
    $email = $_SESSION['email'];
    loadUserData($conn, $email);
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle login form submission if not logged in
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM customer WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $hashed_password = $user['password'];

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['email'] = $user['email'];
            loadUserData($conn, $email); // Load user data after successful login
            header('Location: customer_dashboard.php');
            exit;
        } else {
            echo "Invalid email or password.";
        }
    } else {
        echo "Invalid email or password.";
    }
}

function loadUserData($conn, $email) {
    $sql = "
        SELECT 
            customer.firstname, 
            customer.middlename, 
            customer.lastname, 
            customer.profile_picture, 
            customer.contact_no,
            customer.birthdate,
            customer_address.province, 
            customer_address.city, 
            customer_address.barangay,
            provinces.province_name, 
            cities.city_name, 
            barangays.barangay_name 
        FROM 
            customer 
        JOIN 
            customer_address 
        ON 
            customer.customer_id = customer_address.customer_id 
        LEFT JOIN 
            provinces 
        ON 
            customer_address.province = provinces.province_id 
        LEFT JOIN 
            cities 
        ON 
            customer_address.city = cities.city_id 
        LEFT JOIN 
            barangays 
        ON 
            customer_address.barangay = barangays.barangay_id 
        WHERE 
            customer.email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pensioner = $result->fetch_assoc();

        $_SESSION['firstname'] = $pensioner['firstname'];
        $_SESSION['middlename'] = $pensioner['middlename'];
        $_SESSION['lastname'] = $pensioner['lastname'];
        $_SESSION['profile_picture'] = $pensioner['profile_picture'];
        $_SESSION['contact_no'] = $pensioner['contact_no'];
        $_SESSION['birthdate'] = $pensioner['birthdate'];
        $_SESSION['fullname'] = $pensioner['firstname'] . " " . $pensioner['middlename'] . " " . $pensioner['lastname'];
        $_SESSION['province_name'] = $pensioner['province_name'];
        $_SESSION['city_name'] = $pensioner['city_name'];
        $_SESSION['barangay_name'] = $pensioner['barangay_name'];
        $_SESSION['loggedin'] = true;

        // Generate and store customer code
        $customer_code = strtoupper(substr($pensioner['lastname'], 0, 4)) . strtoupper(substr($pensioner['firstname'], 0, 3));
        $_SESSION['customer_code'] = $customer_code;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>
