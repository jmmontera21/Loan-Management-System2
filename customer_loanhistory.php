<?php
// Include the user session file and database connection
include 'user_session.php';
include 'db_connect.php'; // Replace with your actual database connection file

// Set timeout duration in seconds (5 minutes = 300 seconds)
$timeout_duration = 300;

// Check if the last activity timestamp is set and calculate the inactivity duration
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // If the inactivity duration is greater than the timeout duration, destroy the session and redirect to login page
    session_unset();
    session_destroy();
    header("Location: customer_login.php");
    exit();
}

// Update the last activity timestamp
$_SESSION['last_activity'] = time();

// Fetch loan history for the logged-in customer
$customer_code = $_SESSION['customer_code']; // Assuming customer_code is stored in session

// Modify query to handle status dynamically
$loan_history_query = "
    SELECT 
        loan_type,
        DATE_FORMAT(date_created, '%m/%d/%Y') AS start_date,
        DATE_FORMAT(DATE_ADD(date_created, INTERVAL months_to_pay MONTH), '%m/%d/%Y') AS end_date,
        CEIL(months_to_pay) AS duration, -- Convert months_to_pay to a whole number
        loan_amount,
        CASE 
            WHEN status = 'Repaid' THEN 'Repaid'
            WHEN NOW() < DATE_ADD(date_created, INTERVAL months_to_pay MONTH) THEN 'Ongoing'
            ELSE 'Overdue'
        END AS status
    FROM apply_loan
    WHERE customer_code = ?
    ORDER BY date_created DESC
";

$stmt = $conn->prepare($loan_history_query);
$stmt->bind_param("s", $customer_code);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Loan History</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        /* Additional CSS for loan history table */
        .loan-table {
            text-align: center;
            border-collapse: collapse;
            width: 100%;
            max-width: 1050px;
            margin-top: 40px;
            margin-left: 50px;
            background-color: #f3f3f3;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .loan-table th, .loan-table td {
            padding: 15px 15px;
        }
        .loan-table thead th {
            background-color: #b3b3b3;
            color: #333;
            font-weight: bold;
            font-size: 14px;
        }
        .loan-table tbody td {
            background-color: #ffffff;
            font-size: 14px;
            color: #333;
        }
        .loan-table tbody tr:nth-child(even) td {
            background-color: #f9f9f9;
        }
        .loan-table tbody tr:last-child td {
            border-bottom: none;
        }
        .loan-table td {
            border-bottom: 1px solid #e0e0e0;
        }
        .loan-table tbody tr td:last-child {
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <img src="img/LMS logo.png" alt="Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="customer_dashboard.php">Home</a></li>
                    <li><a href="customer_calculator.php">Calculator</a></li>
                    <li><a href="customer_form.php">Apply Loan</a></li>
                    <li class="active"><a href="customer_loanhistory.php">Loan History</a></li>
                    <li><a href="customer_settings.php">Settings</a></li>
                    <li><a href="homepage.php">Log Out</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="profile-header">
                <div class="profile-pic">
                    <img src="<?php echo $_SESSION['profile_picture']; ?>" alt="Profile Picture" onerror="this.onerror=null; this.src='img/default-profile.png';">
                </div>
                <div class="profile-name">
                    <!-- Display the full name of the logged-in customer -->
                    <h2><?php echo $_SESSION['fullname']; ?></h2>
                </div>
            </header>

            <!-- Loan History Table -->
            <section class="loan-history">
                <table class="loan-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Duration</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['loan_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['end_date'] ?? '--/--/----'); ?></td>
                                <td><?php echo htmlspecialchars($row['duration']); ?> Months</td>
                                <td><?php echo number_format($row['loan_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($result->num_rows === 0): ?>
                            <tr>
                                <td colspan="6">No loan history available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
