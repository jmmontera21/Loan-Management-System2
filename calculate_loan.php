<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the values from the AJAX request
    $monthly_pension = $_POST['monthly_pension'];
    $loan_term = $_POST['loan_term'];

    // Function to compute possible loan amount
    function computeLoanAmount($monthly_pension, $loan_term) {
        $interest_rate = 0.20; // 20% interest rate
        $min_loan_amount = $monthly_pension * $loan_term;
        $max_loan_amount = $min_loan_amount * 2;
        $min_loan_amount_with_interest = $min_loan_amount + ($min_loan_amount * $interest_rate);
        $max_loan_amount_with_interest = $max_loan_amount + ($max_loan_amount * $interest_rate);

        return [
            'min_amount' => $min_loan_amount_with_interest,
            'max_amount' => $max_loan_amount_with_interest
        ];
    }

    // Calculate the loan amounts
    $loan_amount = computeLoanAmount($monthly_pension, $loan_term);

    // Return the formatted loan amount range as a string
    echo "PHP " . number_format($loan_amount['min_amount'], 2) . " - PHP " . number_format($loan_amount['max_amount'], 2);
}
?>
