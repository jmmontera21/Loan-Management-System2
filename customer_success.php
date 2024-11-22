<?php
// Include the user session file to retrieve logged-in user's full name
include 'user_session.php';

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
    <title>Loan Application Success</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        /* Styles for the party popper animation */
        .party-popper {
            position: relative;
            width: 100%;
            height: 100px;
            overflow: hidden;
        }
        .confetti {
            position: absolute;
            width: 10px;
            height: 10px;
            background-color: #FFD700;
            animation: popper 1s ease-out forwards;
        }
        @keyframes popper {
            from {
                opacity: 1;
                transform: translateY(0) rotate(0deg);
            }
            to {
                opacity: 0;
                transform: translateY(100px) rotate(720deg);
            }
        }
    </style>
</head>
<body onload="showPartyPopper()">

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
                    <h2><?php echo $_SESSION['fullname']; ?></h2>
                </div>
            </header>

            <div class="container1">
                <div id="partyPopperContainer" class="party-popper"></div>
                <h1>Congratulations!</h1>
                <p>Your Loan Application Form has been successfully submitted!</p>
                <p>Please visit our office to finalize the loan transaction. <a href="contact.php">Find our location here.</a></p>
                <div class="form-buttons">
                    <a href="customer_dashboard.php"><button type="button" class="btn-prev">Back to Home Page</button></a>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Party popper animation
        function showPartyPopper() {
            const container = document.getElementById('partyPopperContainer');
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.classList.add('confetti');
                confetti.style.left = `${Math.random() * 100}%`;
                confetti.style.backgroundColor = `hsl(${Math.random() * 360}, 100%, 50%)`;
                container.appendChild(confetti);

                // Remove the confetti after animation ends
                confetti.addEventListener('animationend', () => confetti.remove());
            }
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
