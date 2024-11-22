<?php
session_start(); // Start the session at the very beginning
date_default_timezone_set("Etc/GMT+8"); // Set the timezone only once

// Include the database connection
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

// Query to select all users
$sql = "SELECT * FROM user";
$result = $conn->query($sql);

if ($result === FALSE) {
    die("Error executing query: " . $conn->error);
}

// You can use $result to fetch and display user data as needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Management System Dashboard</title>
    <link rel="stylesheet" href="css/employee.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
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
            <a href="admin_map.php" class="nav-link">
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
            <a href="accounts.php" class="nav-link active">
                <img src="img/accs.png" alt="Accounts">
                <span class="text">Accounts</span>
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
        <h2>Accounts</h2>
        <button type="submit" onclick="showAddCustomerForm()">+ Add Account</button>
        <div class="cards">
            <div class="table-container">
                <table id="customersTable" class="display">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Contact No.</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                if ($row['user_type'] == 'employee') {
                                    $type = 'Employee';
                                } elseif ($row['user_type'] == 'admin') {
                                    $type = 'Admin';
                                } else {
                                    continue;
                                }
                                echo "<tr>
                                        <td>{$row['name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>********</td> <!-- Hide password -->
                                        <td>{$row['contact_no']}</td>
                                        <td>{$type}</td>
                                        <td><div class='btns'>
                                            <button class='edit-btn' onclick=\"editUser(" . htmlspecialchars(json_encode($row)) . ")\">Edit</button>
                                            <button class='delete-btn' onclick=\"deleteUser({$row['user_id']})\">Delete</button>
                                        </div></td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No records found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div id="addCustomerModal" class="modal">
    <div class="modal-content2">
        <span class="close" onclick="closeAddCustomerForm()">&times;</span>
        <form method="post" action="save_user.php" onsubmit="return validateForm()">
            <input type="hidden" id="userId" name="userId" value="">
            <div class="form-group2">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group2">
                <label for="Email">Email</label>
                <input type="text" id="email" name="email" required>
            </div>
            <div class="form-group2">
                <label for="password">Password</label>
                <input type="text" id="password" name="password" required>
            </div>
            <div class="form-group2">
                <label for="contact">Contact No.</label>
                <input type="number" id="contact" name="contact" required>
            </div>
            <div class="form-group2">
                <label for="type">User Type</label>
                <select id="type" name="type" required>
                    <option value="admin">Admin</option>
                    <option value="employee">Employee</option>
                </select>
            </div>
            <div class="form-group2 button-group" style="justify-content: flex-end; align-items: flex-end;">
                <button type="submit" name="submit" class="save-changes2">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div id="alert" class="alert"></div>
<script src="scripts.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#customersTable').DataTable();
    });

    function showAddCustomerForm() {
        document.getElementById('userId').value = '';
        document.getElementById('name').value = '';
        document.getElementById('email').value = '';
        document.getElementById('password').value = '';
        document.getElementById('contact').value = '';
        document.getElementById('type').value = 'admin';
        var modal = document.getElementById('addCustomerModal');
        modal.style.display = "block";
    }

    window.onclick = function(event) {
        var modal = document.getElementById('addCustomerModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function editUser(user) {
        document.getElementById('userId').value = user.user_id;
        document.getElementById('name').value = user.name;
        document.getElementById('email').value = user.email;
        document.getElementById('password').value = '';
        document.getElementById('contact').value = user.contact_no;
        document.getElementById('type').value = user.user_type;
        var modal = document.getElementById('addCustomerModal');
        modal.style.display = "block";
    }

    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            $.post('save_user.php', { deleteUser: true, userId: userId }, function(data) {
                location.reload();
            });
        }
    }
</script>
</body>
</html>
