<?php
include 'db_connect.php'; // Ensure this file connects to the database

if (isset($_GET['account_no'])) {
    $accountNo = $_GET['account_no'];

    // Query to get the apply_loan_id associated with the account_no
    $query = "SELECT apply_loan_id FROM apply_loan WHERE account_no = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $accountNo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo json_encode(["apply_loan_id" => $row['apply_loan_id']]);
    } else {
        echo json_encode(["apply_loan_id" => null]);
    }
    $stmt->close();
} else {
    echo json_encode(["apply_loan_id" => null]);
}

$conn->close();
?>
