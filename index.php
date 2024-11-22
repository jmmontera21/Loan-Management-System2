<?php
include 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $error = null;

    // Prepared statement for retrieving user data
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        
        // Verifying password
        if (password_verify($password, $row['password'])) {
            // Correct password, start session
            $_SESSION['user_id'] = $row['user_id']; // Store user_id in session
            $_SESSION['type'] = $row['user_type'];

            if ($row['user_type'] == 'admin') {
                header("Location: admin_home.php");
                exit();
            } elseif ($row['user_type'] == 'employee') {
                header("Location: employee_home.php");
                exit();
            }
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Account not found";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <div class="logo">
                <img src="img/LMS logo.png" alt="Loan Logo">
            </div>
            <h1>WELCOME</h1>
            <p>Login to continue</p>
            <form action="index.php" method="POST">
                <label for="email">Email<span>*</span></label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                
                <label for="password">Password<span>*</span></label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <span style="display:flex; justify-content:center">
                <button type="submit">LOGIN</button>
                </span>
                <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
            </form>
        </div>
    </div>
</body>
</html>
