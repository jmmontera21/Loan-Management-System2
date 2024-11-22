<?php
include 'db_connect.php'; // Include your database connection file
session_start(); // Start session to store login info

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the form data
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; // No need to escape since it won't be used in the SQL query directly
    
    // Query to find the user by email
    $sql = "SELECT * FROM customer WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch user data
        $user = $result->fetch_assoc();
        $hashed_password = $user['password'];  // Get hashed password from the database

        // Debug: Print the fetched hash and entered password
        echo "Stored hash: " . $hashed_password . "<br>";
        echo "Entered password: " . $password . "<br>";

        // Verify the entered password with the hashed password
        if (password_verify($password, $hashed_password)) {
            // Debug: Password verified successfully
            echo "Password verified successfully.<br>";

            // Password is correct, set session variables or redirect
            $_SESSION['email'] = $user['email'];
            header('Location: customer_dashboard.php');
            exit;
        } else {
            // Invalid password
            echo "Invalid email or password.";
        }
    } else {
        // No user found with this email
        echo "Invalid email or password.";
    }
}

// Close the connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
    <link rel="stylesheet" href="css/customer(1).css">
</head>
<body>
    <header>
    <nav>
        <div class="logo">
            <a href="homepage.php"><img src="img/LMS logo.png" alt="Logo"></a>
        </div>
    </nav>
    </header>

    <!-- Middle Section (Form) -->
     <div class="middle-section" style="margin-top:75px;">
        <div class="form-container">
            <h1>Welcome</h1>
            <form action="customer_login.php" method="post">
                <div class="form-group2">
                    <label for="email">Email <span>*</span></label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group2">
                    <label for="password">Password <span>*</span></label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn-create-account">Log In</button>
            </form>
            <p class="login-link">Don't have an account yet? <a href="customer_signup.php">Sign Up</a></p>
        </div>
    </div>

    <!-- Bottom Section -->
    <div class="bottom-section">
        <!-- You can add more content here if needed -->
    </div>
</body>
</html>