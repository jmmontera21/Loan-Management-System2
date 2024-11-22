<?php
include 'db_connect.php';  // Include database connection file

// Retrieve loan ID from the URL
$apply_loan_id = isset($_GET['apply_loan_id']) ? intval($_GET['apply_loan_id']) : 1;

// Fetch loan details
$sql = "SELECT * FROM apply_loan WHERE apply_loan_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $apply_loan_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Loan details not found.";
    exit();
}

$loan = $result->fetch_assoc();
$account_number = $loan['account_no'];
$total_amount = $loan['total_amount'];
$months_to_pay = $loan['months_to_pay'];
$monthly_payment = $loan['monthly_amortization'];

// Calculate remaining balance (initially, it's the total amount)
$remaining_balance = $total_amount;

// Check if the first 4-6 payments have been made on time
$payment_status_check = "Paid";  // Assuming "Paid" is the status for on-time payments
$on_time_payments = 0;

// Query to get payment status for the first 6 months
$payment_check_query = "SELECT payment_status FROM loan_payments WHERE apply_loan_id = ? AND payment_date <= ? LIMIT 6";
$stmt_check = $conn->prepare($payment_check_query);

$start_date = new DateTime();  // Start date for payments

for ($month = 1; $month <= min(6, $months_to_pay); $month++) {
    $due_date = $start_date->modify("+1 month")->format('Y-m-d');
    
    $stmt_check->bind_param("is", $apply_loan_id, $due_date);
    $stmt_check->execute();
    $payment_result = $stmt_check->get_result();
    
    // Check if the payment was made on time (assuming 'Paid' is the on-time status)
    if ($payment_result->num_rows > 0) {
        $payment = $payment_result->fetch_assoc();
        if ($payment['payment_status'] === $payment_status_check) {
            $on_time_payments++;
        }
    }
}

$stmt_check->close();

// If at least 4-6 payments were made on time, show the trigger button
$show_reloan_button = ($on_time_payments >= 4);

// Display the loan information and payment details
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pension Payment Details</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f2f2f2; }
        .reloan-button { background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .reloan-button:hover { background-color: #45a049; }
    </style>
</head>
<body>

<h2>Pension Payment Details</h2>

<div>
    <h3>Account Information</h3>
    <p><strong>Account Number:</strong> <?php echo htmlspecialchars($account_number); ?></p>
    <p><strong>Total Amount:</strong> Php <?php echo number_format($total_amount, 2); ?></p>
    <p><strong>Months to Pay:</strong> <?php echo $months_to_pay; ?> months</p>
    <p><strong>Monthly Payment:</strong> Php <?php echo number_format($monthly_payment, 2); ?></p>
    <p><strong>Remaining Balance:</strong> Php <?php echo number_format($remaining_balance, 2); ?></p>
</div>

<h3>Payment Distribution</h3>
<table>
    <thead>
        <tr>
            <th>Month</th>
            <th>Due Date</th>
            <th>Remaining Balance</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $start_date = new DateTime();  // Start date for payments

        for ($month = 1; $month <= $months_to_pay; $month++) {
            $due_date = $start_date->modify("+1 month")->format('Y-m-d');
            $remaining_balance -= $monthly_payment;
            $remaining_balance = max($remaining_balance, 0);  // Ensure no negative balance
        ?>
            <tr>
                <td>Month <?php echo $month; ?></td>
                <td><?php echo $due_date; ?></td>
                <td>Php <?php echo number_format($remaining_balance, 2); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- Show the reloan button if the condition is met -->
<?php if ($show_reloan_button): ?>
    <form action="process_reloan.php" method="POST">
        <input type="hidden" name="apply_loan_id" value="<?php echo $apply_loan_id; ?>" />
        <button type="submit" class="reloan-button">Trigger Reloan</button>
    </form>
<?php endif; ?>

</body>
</html>

<?php
$conn->close();  // Close database connection
?>
