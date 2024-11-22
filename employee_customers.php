<?php
session_start(); // Start the session at the beginning

date_default_timezone_set("Etc/GMT+8"); // Set timezone

include 'db_connect.php'; // Include the database connection only once

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

// Fetch customer data, joining with the address, province, city, and barangay tables to get names
$query = "
    SELECT 
        c.firstname, c.middlename, c.lastname, c.contact_no, c.email, c.customer_code, 
        ca.barangay AS barangay_id, ca.city AS city_id, ca.province AS province_id,
        p.province_name, ci.city_name, b.barangay_name, c.profile_picture, c.birthdate
    FROM customer c
    JOIN customer_address ca ON c.customer_id = ca.customer_id
    JOIN provinces p ON ca.province = p.province_id
    JOIN cities ci ON ca.city = ci.city_id
    JOIN barangays b ON ca.barangay = b.barangay_id";
$result = $conn->query($query);

$message = '';
?>

<!DOCTYPE html>
<html lang="en">    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Management System Dashboard</title>
    <link rel="stylesheet" href="css/employee.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        /* Modal Styles */
        .modal {
            display: none; 
            position: fixed;
            justify-content: center;
            z-index: 100; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(40, 40, 70, 0.7);
        }
        .modal-content {
            background-color: white;
            margin-top: 100px;
            padding: 20px;
            border: 2px solid #888;
            width: 50%;
            text-align:center;
            line-height: 1.8;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header" onclick="toggleSidebar()">
        <span class="role">PANEL</span>
        <span class="dropright-icon">&#60;</span>
    </div>
    <ul class="nav-links">
        <li class="nav-item">
            <a href="employee_home.php" class="nav-link">
                <img src="img/dashboard.png" alt="Home">
                <span class="text">Home</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="employee_customers.php" class="nav-link active">
                <img src="img/customer.png" alt="Customers">
                <span class="text">Customers</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="employee_loans.php" class="nav-link">
                <img src="img/loans.png" alt="Loans">
                <span class="text">Loans</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="employee_payments.php" class="nav-link">
                <img src="img/credit-card.png" alt="Payments">
                <span class="text">Payments</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="employee_map.php" class="nav-link">
                <img src="img/map.png" alt="Map">
                <span class="text">Map</span>
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <header>
        <h1>Loan Management System</h1>
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
        <h2>Customers</h2>
        <div class="cards">
            <div class="table-container">
                <table id="customersTable" class="display">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Contact No.</th>
                            <th>Address</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Customer Code</th>
                            <th>Other Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            // Output data as table rows
                            while($row = $result->fetch_assoc()) {
                                $full_address = "" . $row["barangay_name"] . ", " . $row["city_name"] . ", " . $row["province_name"];
                                echo "<tr>";
                                echo "<td>" . $row['firstname'] . " " . $row['middlename'] . " " . $row['lastname'] . "</td>";
                                echo "<td>" . $row['contact_no'] . "</td>";
                                echo "<td> $full_address </td>"; 
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td> ***** </td>"; // Masking password
                                echo "<td>" . $row['customer_code'] . "</td>";
                                echo '<td><div class="btns">
                                            <button class="view-btn" 
                                                data-customer-code="' . $row['customer_code'] . '" 
                                                data-name="' . $row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'] . '" 
                                                data-birthdate="' . $row['birthdate'] . '" 
                                                data-contact-no="' . $row['contact_no'] . '" 
                                                data-email="' . $row['email'] . '" 
                                                data-address="' . $full_address . '" 
                                                data-profile-picture=" ' . $row['profile_picture'] . '">View</button>
                                        </div></td>';
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7'>No customers found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<!-- Modal Structure -->
<div id="customerModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Customer Details</h3><br>
        <div id="modal-body">
            <p><strong>Profile Picture:</strong></p>
            <img id="modal-profile-picture" src="" alt="Profile Picture" style="width: 150px; height: 150px;">
            <p><strong>Customer Code:</strong> <b><span id="modal-customer-code"></span></b></p>
            <p><strong>Name:</strong> <span id="modal-full-name"></span></p>
            <p><strong>Birthdate:</strong> <span id="modal-birthdate"></span></p>
            <p><strong>Contact No.:</strong> <span id="modal-contact-no"></span></p>
            <p><strong>Email:</strong> <span id="modal-email"></span></p>
            <p><strong>Address:</strong> <span id="modal-address"></span></p>
        </div>
    </div>
</div>

<div id="alert" class="alert"></div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="scripts.js"></script>
<script>
    $(document).ready(function() {
        $('#customersTable').DataTable();
    });

    // Modal script
    $('.view-btn').click(function() {
        var customerCode = $(this).data('customer-code');
        var name = $(this).data('name');
        var birthdate = $(this).data('birthdate');
        var contactNo = $(this).data('contact-no');
        var email = $(this).data('email');
        var address = $(this).data('address');
        var profilePicture = $(this).data('profile-picture');
        
        // Populate modal fields
        $('#modal-customer-code').text(customerCode);
        $('#modal-full-name').text(name);
        $('#modal-birthdate').text(birthdate);
        $('#modal-contact-no').text(contactNo);
        $('#modal-email').text(email);
        $('#modal-address').text(address);
        $('#modal-profile-picture').attr('src', profilePicture);
        
        // Show modal
        $('#customerModal').css('display', 'block');
    });

    // Close the modal when the "x" is clicked
    $('.close').click(function() {
        $('#customerModal').css('display', 'none');
    });

    // Close modal when clicking outside of it
    $(window).click(function(event) {
        if (event.target == $('#customerModal')[0]) {
            $('#customerModal').css('display', 'none');
        }
    });
</script>
</body>
</html>
