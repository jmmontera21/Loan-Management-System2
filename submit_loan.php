<?php
include 'db_connect.php';

function updateLoanInformation($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $apply_loan_id = isset($_POST['apply_loan_id']) ? intval($_POST['apply_loan_id']) : null;

        if (!$apply_loan_id) {
            echo json_encode(["error" => "apply_loan_id is required. Please select an account and try again."]);
            return;
        }

        // Retrieve other form inputs and sanitize as needed
        $bank_name = $_POST['bank_name'] ?? null;
        $bank_branch = $_POST['bank_branch'] ?? null;
        $loan_type = $_POST['loan_type'] ?? null;
        $card_number = $_POST['card_number'] ?? null;
        $savings_account_no = $_POST['savings_account_no'] ?? null;
        $claim_type = $_POST['claim_type'] ?? null;
        $withdrawal_date = $_POST['withdrawal_date'] ?? null;
        $sss_sp = $_POST['sss_sp'] ?? null;
        $application_status = $_POST['application_status'] ?? null;
        $monthly_pension = $_POST['monthly_pension'] ?? null;
        $loan_amount = $_POST['loan_amount'] ?? null;
        $monthly_amortization = $_POST['monthly_amortization'] ?? null;
        $months_to_pay = $_POST['months_to_pay'] ?? null;
        $sukli = $_POST['sukli'] ?? null;
        $net_cashout = $_POST['net_cashout'] ?? null;
        $total_amount = $_POST['total_amount'] ?? null;
        $voucher_number = $_POST['voucher_number'] ?? null;
        $payment_schedule = $_POST['payment_schedule'] ?? null;
        $loan_status = $_POST['loan_status'] ?? null;

        // SQL update statement with placeholders for parameters
        $sql = "UPDATE apply_loan SET 
                    bank_name = ?, 
                    bank_branch = ?, 
                    loan_type = ?, 
                    card_no = ?, 
                    savings_account_no = ?, 
                    claim_type = ?, 
                    withdrawal_date = ?, 
                    sss_sp = ?, 
                    application_status = ?, 
                    loan_amount = ?, 
                    monthly_pension = ?, 
                    monthly_amortization = ?, 
                    months_to_pay = ?, 
                    sukli = ?, 
                    net_cashout = ?, 
                    total_amount = ?, 
                    voucher_no = ?, 
                    payment_schedule = ?, 
                    status = ?
                WHERE apply_loan_id = ?";

        // Prepare the SQL statement and bind parameters
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param(
                "sssssssssssssssssssi",
                $bank_name, 
                $bank_branch, 
                $loan_type, 
                $card_number, 
                $savings_account_no, 
                $claim_type, 
                $withdrawal_date, 
                $sss_sp, 
                $application_status, 
                $loan_amount, 
                $monthly_pension, 
                $monthly_amortization, 
                $months_to_pay, 
                $sukli, 
                $net_cashout, 
                $total_amount,
                $voucher_number, 
                $payment_schedule,
                $loan_status,
                $apply_loan_id
            );

            // Execute the statement
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(["success" => "Loan information updated successfully."]);
                } else {
                    echo json_encode(["info" => "No changes made. Data may be identical to existing values."]);
                }
            } else {
                echo json_encode(["error" => "Error updating loan information: " . htmlspecialchars($stmt->error)]);
            }
        } else {
            echo json_encode(["error" => "Error preparing the statement: " . htmlspecialchars($conn->error)]);
        }
    }
}

// Call the function to handle the update operation
updateLoanInformation($conn);
$conn->close();
?>
