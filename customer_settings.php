<?php
// Include the session and database connection files
include 'user_session.php';
include 'db_connect.php';

// Get the current user's customer_code from the session
$customer_code = $_SESSION['customer_code'];

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

// Query the database to get the user information
$sql = "SELECT firstname, middlename, lastname, contact_no, email, profile_picture FROM customer WHERE customer_code = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $customer_code);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the user data
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User data not found.";
    exit();
}

// Process form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact_no = $_POST['contact_number'];
    $email = $_POST['email'];
    $new_password = $_POST['password'];
    $retry_password = $_POST['retry_password'];

    // Validate passwords
    if ($new_password && $new_password === $retry_password) {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the database with the new password and other info
        $update_sql = "UPDATE customer SET contact_no = ?, email = ?, password = ? WHERE customer_code = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssss", $contact_no, $email, $hashed_password, $customer_code);
    } else {
        // Update without changing the password
        $update_sql = "UPDATE customer SET contact_no = ?, email = ? WHERE customer_code = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sss", $contact_no, $email, $customer_code);
    }

    if ($update_stmt->execute()) {
        // Set a session variable to show a success message after redirect
        $_SESSION['update_success'] = true;
        header("Location: customer_settings.php");
        exit();
    } else {
        echo "Error updating profile.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Settings</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        /* Form Container Styles */
        .form-container {
            width: 400px;
            padding: 30px 30px 15px 30px;
            margin-left: 300px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            border-radius: 8px;
            margin-top: 20px;
        }
        .form-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 95%;
            padding: 10px;
            font-size: 14px;
            color: #333;
            border: none;
            border-bottom: 1px solid #ccc;
            outline: none;
            background: transparent;
        }
        .form-group input[type="text"]::placeholder,
        .form-group input[type="email"]::placeholder,
        .form-group input[type="password"]::placeholder {
            color: #aaa;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .button-group button {
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .cancel-button {
            background-color: #d9534f;
            color: #fff;
        }
        .submit-button {
            background-color: #5cb85c;
            color: #fff;
        }
        .alert-success {
            display: none;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #dff0d8;
            color: #3c763d;
            border-radius: 5px;
        }
    </style>
    <script>
        window.onload = function() {
            <?php if (isset($_SESSION['update_success'])) : ?>
                // Display the success message if the session variable is set
                document.getElementById('success-alert').style.display = 'block';
                // Hide the alert after 3 seconds
                setTimeout(function() {
                    document.getElementById('success-alert').style.display = 'none';
                }, 3000);
                <?php unset($_SESSION['update_success']); ?>
            <?php endif; ?>
        };
    </script>
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
                    <li><a href="customer_loanhistory.php">Loan History</a></li>
               <li class="active"><a href="customer_settings.php">Settings</a></li>
                    <li><a href="homepage.php">Log Out</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="profile-header">
                <div class="profile-pic">
                    <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'img/default-profile.png'); ?>" alt="Profile Picture" onerror="this.onerror=null; this.src='img/default-profile.png';">
                </div>
                <div class="profile-name">
                    <!-- Display the full name of the logged-in customer -->
                    <h2><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['middlename'] . ' ' . $user['lastname']); ?></h2>
                </div>
            </header>

            <!-- Success Alert Message -->
            <div id="success-alert" class="alert-success">
                Profile updated successfully.
            </div>

            <!-- Edit Profile Form -->
            <div class="form-container">
                <h1>Edit Profile</h1>
                <form method="POST">
                    <div class="form-group">
                        <label for="contact-number">Contact Number</label>
                        <input type="text" id="contact-number" name="contact_number" value="<?php echo htmlspecialchars($user['contact_no']); ?>" placeholder="ex. 09XX-XXX-XXXX">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="example@gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" placeholder="8-12 characters">
                    </div>
                    <div class="form-group">
                        <label for="retry-password">Retry New Password</label>
                        <input type="password" id="retry-password" name="retry_password" placeholder="8-12 characters">
                    </div>
                    <div class="button-group">
                        <button type="button" class="cancel-button">Cancel</button>
                        <button type="submit" class="submit-button">Submit Changes</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
<script>
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
