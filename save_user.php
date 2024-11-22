<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['deleteUser']) && $_POST['deleteUser'] == true) {
        $user_id = $_POST['userId'];
        $sql = "DELETE FROM user WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            echo "User deleted successfully";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
        exit();
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact = $_POST['contact'];
    $type = $_POST['type'];

    if (isset($_POST['userId']) && $_POST['userId'] != '') {
        $user_id = $_POST['userId'];
        $sql = "UPDATE user SET name=?, email=?, password=?, contact_no=?, user_type=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $password, $contact, $type, $user_id);
    } else {
        $sql = "INSERT INTO user (name, email, password, contact_no, user_type) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $password, $contact, $type);
    }

    if ($stmt->execute()) {
        echo "User saved successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
header("Location: accounts.php");
exit();
?>
