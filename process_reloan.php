<?php
include 'db_connect.php';  // Include database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_loan_id'])) {
    $apply_loan_id = intval($_POST['apply_loan_id']);

    // Fetch the loan details
    $sql = "SELECT * FROM apply_loan WHERE apply_loan_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $apply_loan_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "Loan not found.";
        exit();
    }

    $loan = $result->fetch_assoc();

    // Reset the loan amount and months to pay (you can adjust this logic)
    $new_total_amount = $loan['total_amount'];  // Reset to original amount or some logic
    $new_months_to_pay = $loan['months_to_pay'];  // Reset to original number of months

    // Update the loan with new values
    $update_query = "UPDATE apply_loan SET total_amount = ?, months_to_pay = ? WHERE apply_loan_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("dii", $new_total_amount, $new_months_to_pay, $apply_loan_id);
    $stmt->execute();

    echo "Reloan processed successfully.";
    exit();
}

?>
