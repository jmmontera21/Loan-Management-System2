<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Password is correct, start a new session
            session_start();
            $_SESSION['email'] = $email;
            $_SESSION['type'] = $row['type']; // Store user type in session

            // Redirect based on user type
            if ($row['type'] == 'admin') {
                header("Location: admin_home.php");
            } elseif ($row['type'] == 'employee') {
                header("Location: employee_home.php");
            } else {
                // Handle other types if needed
                header("Location: employee_home.php"); // Default redirect
            }
            exit();
        } else {
            // Incorrect password
            echo "Invalid password";
        }
    } else {
        // User not found
        echo "User not found";
    }
}
$conn->close();
?>
