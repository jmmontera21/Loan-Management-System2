<?php
include 'user_session.php';

// Set timeout duration in seconds (5 minutes = 300 seconds)
$timeout_duration = 300;

// Check if the last activity timestamp is set and calculate the inactivity duration
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: customer_login.php");
    exit();
}

// Update the last activity timestamp
$_SESSION['last_activity'] = time();

// Check if it's the first time the user logs in or a new user indicator
$is_new_user = $_SESSION['is_new_user'] ?? false;

// Retrieve the pensioner's latest loan details
$customer_code = $_SESSION['customer_code']; // Assume customer_code is stored in session
$loan_details = null;

// Database connection
include 'db_connect.php'; // Replace with your actual DB connection file

$sql = "SELECT loan_type, loan_amount, months_to_pay, 
               DATE_FORMAT(withdrawal_date, '%m/%d/%Y') AS date_released, 
               monthly_amortization, 
               total_amount - (monthly_amortization * months_to_pay) AS remaining_balance 
        FROM apply_loan 
        WHERE customer_code = ? 
        ORDER BY date_created DESC 
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $customer_code);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $loan_details = $result->fetch_assoc();
    $loan_details['months_to_pay'] = intval($loan_details['months_to_pay']); // Convert months_to_pay to a whole number
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <img src="img/LMS logo.png" alt="Logo">
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="customer_dashboard.php">Home</a></li>
                    <li><a href="customer_calculator.php">Calculator</a></li>
                    <li><a href="customer_form.php">Apply Loan</a></li>
                    <li><a href="customer_loanhistory.php">Loan History</a></li>
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
                    <h2><?php echo $_SESSION['fullname']; ?></h2>
                </div>
            </header>

            <section class="content">
                <div class="inbox">
                    <h3>Inbox</h3>
                    <?php if ($is_new_user): ?>
                        <p>
                            Welcome, <?php echo $_SESSION['fullname']; ?>, it is a great pleasure that you choose our lending company in assisting with your needs. 
                            We will do our best to achieve the best amount you can borrow for your needs.
                        </p>
                        <?php 
                        $_SESSION['is_new_user'] = false; 
                        ?>
                    <?php else: ?>
                        <p>No new messages.</p>
                    <?php endif; ?>
                </div>

                <div class="loan-details">
                    <h3>Current Loan</h3>
                    <?php if ($loan_details): ?>
                        <p>Type: <span><?php echo htmlspecialchars($loan_details['loan_type']); ?></span></p>
                        <p>Loan Amount: <span><?php echo number_format($loan_details['loan_amount'], 2); ?></span></p>
                        <p>Term: <span><?php echo $loan_details['months_to_pay']; ?> Months</span></p>
                        <p>Date Released: <span><?php echo $loan_details['date_released']; ?></span></p>
                        <p>Amount to Pay: <span class="amount-to-pay"><?php echo number_format($loan_details['monthly_amortization'], 2); ?></span></p>
                        <p>Remaining Balance: <span class="remaining-balance"><?php echo number_format($loan_details['remaining_balance'], 2); ?></span></p>
                    <?php else: ?>
                        <p>No active loans found.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
