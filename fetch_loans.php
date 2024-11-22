<?php
include 'db_connect.php';

if (isset($_GET['apply_loan_id'])) {
    $apply_loan_id = intval($_GET['apply_loan_id']);

    $sql = "SELECT account_no, loan_type, loan_amount, application_status, payment_schedule FROM apply_loan WHERE apply_loan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $apply_loan_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $loanDetails = $result->fetch_assoc();

    if ($loanDetails) {
        echo json_encode(["loanDetails" => $loanDetails]);
    } else {
        echo json_encode(["error" => "Loan details not found."]);
    }
} else {
    echo json_encode(["error" => "apply_loan_id is missing."]);
}

$conn->close();
?>
