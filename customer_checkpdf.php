<?php
include 'user_session.php';

// Check if apply_loan_id is set in the session
if (!isset($_SESSION['apply_loan_id'])) {
    // Redirect to an error page or show a message
    die("No application ID found. Please make sure you have submitted the application.");
}

$apply_loan_id = $_SESSION['apply_loan_id']; // Ensure this is set correctly after the form submission

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pensioner's Information Form</title>
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
                    <li><a href="customer_dashboard.php">Home</a></li>
                    <li><a href="customer_calculator.php">Calculator</a></li>
                    <li class="active"><a href="customer_form.php">Apply Loan</a></li>
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
                    <h2><?php echo htmlspecialchars($_SESSION['fullname']); ?></h2>
                </div>
            </header>

            <div class="container1">
                <div class="progress-bar">
                    <a><div class="step">1</div></a>
                    <a href="customer_checkpdf.php"><div class="step active">2</div></a>
                    <a><div class="step">3</div></a>
                </div>

                <h1>Check Application Form</h1>

                <!-- Container for displaying the PDF -->
                <div class="pdf-container">
                    <iframe id="pdfIframe" src="generate_pdf.php?apply_loan_id=<?php echo urlencode($apply_loan_id); ?>" width="90%" height="1050px"></iframe>
                </div>

                <div class="form-buttons">
                    <button type="button" class="btn-prev" onclick="goToPreviousPage()">Previous</button>
                    <button type="submit" class="btn-next" onclick="goToNextPage()">Next</button>
                </div>
            </div>
        </main>
    </div>

    <script>
        function goToPreviousPage() {
            window.location.href = 'customer_form.php';
        }

        function goToNextPage() {
            window.location.href = 'customer_picture.php';
        }

        // Reset the session timeout timer on user activity
        let inactivityTimer;

        function resetTimer() {
            clearTimeout(inactivityTimer);
            // Ping the server to reset the PHP session activity timestamp
            fetch("reset_timer.php");
            inactivityTimer = setTimeout(logOut, 300000); // 5 minutes = 300000 ms
        }

        // Automatically log out the user if there's no activity
        function logOut() {
            alert("You have been logged out due to inactivity.");
            window.location.href = "customer_login.php"; // Redirect to homepage (login) page
        }

        // Track various user activities to reset the timer
        window.onload = resetTimer;
        window.onmousemove = resetTimer;
        window.onkeypress = resetTimer;
        window.onscroll = resetTimer;
    </script>
</body>
</html>
