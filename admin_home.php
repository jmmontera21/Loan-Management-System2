<?php 
date_default_timezone_set("Etc/GMT+8");
include 'db_connect.php'; // Include your database connection file

// Start the session
session_start();

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

// Get the total number of customers
$totalCustomersQuery = "SELECT COUNT(*) AS total FROM customer";
$totalCustomersResult = mysqli_query($conn, $totalCustomersQuery);
$totalCustomersRow = mysqli_fetch_assoc($totalCustomersResult);
$totalCustomers = $totalCustomersRow['total'];

$customerStatusCounts = [
    'Approved' => 0,
    'Released' => 0,
    'For Approval' => 0,
    'Rejected' => 0
];

// Get status counts from apply_loan
$sql = "SELECT status, COUNT(*) as total FROM apply_loan WHERE loan_type IN ('SSS', 'GSIS', 'PVAO') GROUP BY status";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $customerStatusCounts[$row['status']] = $row['total'];
    }
}

// Get customer count by loan type
$loanTypes = ['sss', 'gsis', 'pvao'];
$customerCountByLoanType = array_fill_keys($loanTypes, 0);

$sql = "SELECT loan_type, COUNT(*) as count FROM apply_loan WHERE status IN ('Approved', 'Released', 'For Approval', 'Rejected') GROUP BY loan_type";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $customerCountByLoanType[$row['loan_type']] = $row['count'];
    }
}

// Get specific counts for active loans, released loans, rejected loans, and approvals
$sqlReleased = "SELECT 
                    SUM(status = 'Approved') as approvedCount,
                    SUM(status = 'Released') as releasedCount,
                    SUM(status = 'Rejected') as rejectedCount,
                    SUM(status = 'For Approval') as approvalCount
                FROM apply_loan";
$result = mysqli_query($conn, $sqlReleased);
$row = mysqli_fetch_assoc($result);
$activeLoansCount = $row['approvedCount'] ?? 0;
$releasedLoansCount = $row['releasedCount'] ?? 0;
$rejectedCount = $row['rejectedCount'] ?? 0;
$approvalCount = $row['approvalCount'] ?? 0;

// Close the connection after all queries
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Management System Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="css/employee.css">
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header" onclick="toggleSidebar()">
        <span class="role">PANEL</span>
        <span class="dropright-icon"><</span>
    </div>
    <ul class="nav-links">
        <li class="nav-item">
            <a href="admin_home.php" class="nav-link active">
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
            <h2><b>Dashboard</b></h2>
            <div class="cards">
                <div class="cards-container">
                    <div class="card" style="background-color: #4a69bd;">
                        <h3>Customers</h3>
                        <h2><?php echo $totalCustomers; ?></h2>
                    </div>
                    <div class="card" style="background-color: #e1b12c;">
                        <h3>Active Loans</h3>
                        <h2><?php echo $releasedLoansCount; ?></h2>
                    </div>
                    <div class="card" style="background-color: #44bd32;">
                        <h3>For Approval</h3>
                        <h2><?php echo $approvalCount; ?></h2>
                    </div>
                    <div class="card" style="background-color: purple;">
                        <h3>Rejected</h3>
                        <h2><?php echo $rejectedCount; ?></h2>
                    </div>
                </div>
            </div>
            <div class="cards">
                <div class="charts-container">
                    <div class="chart">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="chart">
                        <canvas id="loanTypeChart"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="scripts.js"></script>
    <script>
    var customerStatusCounts = <?php echo json_encode($customerStatusCounts); ?>;

    var ctx1 = document.getElementById('statusChart').getContext('2d');
    var statusChart = new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: Object.keys(customerStatusCounts),
        datasets: [{
            label: 'Number of Customers',
            data: Object.values(customerStatusCounts),
            backgroundColor: [
                'rgba(255, 206, 86, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(86, 255, 153, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderColor: [
                'rgba(255, 206, 86, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(86, 255, 153, 1)',
                'rgba(255, 159, 64, 1)'
            ],
        }]
    },
    options: {
        plugins: {
            title: {
                display: true,
                text: 'Customer Status Distribution'
            },
            legend: {
                labels: {
                    color: 'gray' 
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                stepSize: 1, // Display only whole number values
                precision: 0 // No decimals for the y-axis labels
            }
        }
    }
});

    var customerCountByLoanType = <?php echo json_encode($customerCountByLoanType); ?>;

    var ctx2 = document.getElementById('loanTypeChart').getContext('2d');
    var loanTypeChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: Object.keys(customerCountByLoanType),
            datasets: [{
                label: 'Number of Customers',
                data: Object.values(customerCountByLoanType),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Customer Count by Loan Type'
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            var label = loanTypeChart.data.labels[tooltipItem.index];
                            return label;
                        }
                    }
                }
            },
            responsive: true,
        }
    });
    </script>
</body>
</html>
