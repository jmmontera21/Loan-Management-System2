<?php
// Start the session and set timezone
session_start();
date_default_timezone_set("Etc/GMT+8");

// Include database connection file
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

// Initialize message variable
$message = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        $months = $_POST['months'];
        $interest = $_POST['interest'];
        $penalty = $_POST['penalty'];
        $lplan_id = $_POST['lplan_id'] ?? null;

        if ($lplan_id) {
            // Update existing loan plan
            $stmt = $conn->prepare("UPDATE loan_plan SET lplan_month=?, lplan_interest=?, lplan_penalty=? WHERE lplan_id=?");
            $stmt->bind_param("idii", $months, $interest, $penalty, $lplan_id);
        } else {
            // Insert new loan plan
            $stmt = $conn->prepare("INSERT INTO loan_plan (lplan_month, lplan_interest, lplan_penalty) VALUES (?, ?, ?)");
            $stmt->bind_param("idd", $months, $interest, $penalty);
        }

        if ($stmt->execute()) {
            $message = 'The loan plan information has been saved successfully';
        } else {
            $message = 'Error: ' . $stmt->error;
        }
        $stmt->close();
    }

    if (isset($_POST['delete'])) {
        $lplan_id = $_POST['lplan_id'];
        $stmt = $conn->prepare("DELETE FROM loan_plan WHERE lplan_id=?");
        $stmt->bind_param("i", $lplan_id);

        if ($stmt->execute()) {
            $message = 'Loan plan has been deleted successfully';
        } else {
            $message = 'Error: ' . $stmt->error;
        }
        $stmt->close();
    }
}
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
            <a href="loan_plans.php" class="nav-link active">
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
        <h2>Loan Plans</h2>
        <div class="cards2">
            <div class="form-container">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" id="lplan_id" name="lplan_id">
                    <label for="months">Months</label>
                    <input type="number" id="months" name="months" required>
                    <label for="interest">Interest (%)</label>
                    <input type="number" id="interest" name="interest" required>
                    <label for="penalty">Overdue Penalty (%)</label>
                    <input type="number" id="penalty" name="penalty" required>
                    <button type="submit" name="submit">Save</button>
                </form>
            </div>
            <div class="table-container2">
                <table>
                    <thead>
                        <tr>
                            <th>Months</th>
                            <th>Interest (%)</th>
                            <th>Overdue Penalty / Month (%)</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = "SELECT * FROM loan_plan";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr data-lplan-id='" . $row['lplan_id'] . "'>";
                            echo "<td>" . $row['lplan_month'] . "</td>";
                            echo "<td>" . $row['lplan_interest'] . "</td>";
                            echo "<td>" . $row['lplan_penalty'] . "</td>";
                            echo "<td>";
                            echo "<div class='btns'><button class='edit-btn' onclick='editLoanPlan(" . $row['lplan_id'] . ")'>Edit</button></div>";
                            echo "<div class='btns'><button class='delete-btn' onclick='deleteLoanPlan(" . $row['lplan_id'] . ")'>Delete</button></div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No loan plans found</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div id="alert" class="alert"></div>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    function toggleSidebar() {
        var sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('minimized');
        var toggleIcon = document.querySelector('.toggle-icon');
        toggleIcon.innerHTML = sidebar.classList.contains('minimized') ? '>' : '<';
    }

    function toggleDropdown() {
        var dropdownContent = document.querySelector('.user .dropdown-content');
        dropdownContent.classList.toggle('show');
    }

    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches('.user') && !event.target.matches('.dropdown-icon')) {
            var dropdowns = document.getElementsByClassName('dropdown-content');
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }

    $(document).ready(function() {
        $('#loanPlansTable').DataTable();
    });

    function editLoanPlan(lplan_id) {
        var row = $('tr[data-lplan-id=' + lplan_id + ']');
        var months = row.find('td:eq(0)').text();
        var interest = row.find('td:eq(1)').text();
        var penalty = row.find('td:eq(2)').text();

        $('#lplan_id').val(lplan_id);
        $('#months').val(months);
        $('#interest').val(interest);
        $('#penalty').val(penalty);
    }

    function deleteLoanPlan(lplan_id) {
        if (confirm('Are you sure you want to delete this loan plan?')) {
            $('<form method="post" action="loan_plans.php"><input type="hidden" name="lplan_id" value="' + lplan_id + '"><input type="hidden" name="delete" value="1"></form>').appendTo('body').submit();
        }
    }

    <?php if (!empty($message)) : ?>
    window.onload = function() {
        showAlert('<?php echo $message; ?>');
    };
    <?php endif; ?>

    function showAlert(message) {
        var alertBox = document.getElementById('alert');
        alertBox.innerHTML = message;
        alertBox.className = "alert show";
        setTimeout(function() {
            alertBox.className = alertBox.className.replace("show", "");
        }, 3000);
    }

    function showNotifications() {
        // Example function, replace with actual implementation if needed
            alert('No notifications available');
        }  
    </script>
    <script src="scripts.js"></script>
</body>
</html>
