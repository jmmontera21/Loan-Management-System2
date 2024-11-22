<?php
include 'db_connect.php';

// Get the payment details from the form
$borrowerId = $_POST['borrower']; // Apply Loan ID
$payee = $_POST['payee'];
$payableAmount = $_POST['payableAmount'];
$monthlyPayment = $_POST['monthlyPayment'];
$overduePenalty = $_POST['overduePenalty'];
$paymentStatus = $_POST['payment_status'];

// Get the loan details for the borrower
$query = "SELECT apply_loan_id, total_amount, monthly_amortization, months_to_pay, status FROM apply_loan WHERE apply_loan_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $borrowerId);
$stmt->execute();
$result = $stmt->get_result();
$loan = $result->fetch_assoc();
$stmt->close();

if ($loan) {
    // Deduct the payment from the total amount and reduce months_to_pay by 1
    $newTotalAmount = $loan['total_amount'] - $monthlyPayment;
    $newMonthsToPay = $loan['months_to_pay'] - 1;

    // Update the loan details with the new values
    $updateQuery = "UPDATE apply_loan SET total_amount = ?, months_to_pay = ? WHERE apply_loan_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("dii", $newTotalAmount, $newMonthsToPay, $borrowerId);
    $stmt->execute();
    $stmt->close();

    // If loan is fully paid, archive it
    if ($newTotalAmount <= 0 && $newMonthsToPay <= 0) {
        // Archive the loan in the loan_ledger table
        $archiveQuery = "INSERT INTO loan_ledger (apply_loan_id, account_no, loan_amount, monthly_amortization, months_to_pay, status) 
                         SELECT apply_loan_id, account_no, loan_amount, monthly_amortization, months_to_pay, payment_status 
                         FROM apply_loan WHERE apply_loan_id = ?";
        $stmt = $conn->prepare($archiveQuery);
        $stmt->bind_param("i", $borrowerId);
        $stmt->execute();
        $stmt->close();

        // Delete the loan from the active apply_loan table
        $deleteQuery = "DELETE FROM apply_loan WHERE apply_loan_id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $borrowerId);
        $stmt->execute();
        $stmt->close();
    }

    // Record the payment
    $paymentQuery = "INSERT INTO loan_payments (apply_loan_id, payment_date, paid_amount, penalty_charge, payment_status, added_from_employee_page) 
    VALUES (?, NOW(), ?, ?, ?, TRUE)";
    $stmt = $conn->prepare($paymentQuery);
    $stmt->bind_param("idss", $borrowerId, $monthlyPayment, $overduePenalty, $paymentStatus);
    $stmt->execute();
    $stmt->close();

    // Redirect back to the payments page with a success message
    header("Location: employee_payments.php?message=Payment%20Successfully%20Processed");
    exit; // Ensure no further code is executed
}
?>
