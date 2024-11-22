<?php
// Include the user session file to retrieve logged-in user's full name
include 'user_session.php';

// Set timeout duration in seconds (5 minutes = 300 seconds)
$timeout_duration = 300;

// Check if the last activity timestamp is set and calculate the inactivity duration
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // If the inactivity duration is greater than the timeout duration, destroy the session and redirect to login page
    session_unset();
    session_destroy();
    header("Location: customer_login.php");
    exit();
}

// Update the last activity timestamp
$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Loan Calculator</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        button {
            background-color: #10184f;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0d153d;
        }

        /* Ensure that the calculator form itself becomes scrollable */
        .scrollable-calculator {
            max-height: 325px; /* Adjust height to your needs */
        }   

        /* Floating box for result details */
        .result-details-box {
            display: none;
            position: absolute;
            top: 284px; /* Adjust to position relative to the question mark */
            left: 765px; /* Adjust to position relative to the question mark */
            width: 250px;
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }

        /* Close button styles */
        #close-details {
            cursor: pointer;
            font-size: 24px;
            color: #333;
        }

        /* Add transition effect */
        .result-details-box p {
            margin: 5px 0;
        }

        #pension-amount {
            width: 35px; /* Adjust based on design */
            padding-right: 5px; /* Add some spacing between input and fixed zeros */
        }

        .fixed-zeros {
            padding-left: 5px;
            font-size: 16px;
            color: #000;
            background-color: #f0f0f0; /* Optional: To distinguish the fixed part */
            border: none;
            pointer-events: none; /* Makes it non-editable */
            user-select: none; /* Prevent user from selecting the text */
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <img src="img/LMS logo.png" alt="Logo">
            </div>
            <nav>
                <ul>
                    <li><a href="customer_dashboard.php">Home</a></li>
                    <li class="active"><a href="customer_calculator.php">Calculator</a></li>
                    <li><a href="customer_form.php">Apply Loan</a></li>
                    <li><a href="customer_loanhistory.php">Loan History</a></li>
                    <li><a href="customer_settings.php">Settings</a></li>
                    <li><a href="homepage.php">Log Out</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="profile-header">
                <div class="profile-pic">
                    <img src="<?php echo $_SESSION['profile_picture']; ?>" alt="Profile Picture" onerror="this.onerror=null; this.src='img/default-profile.png';">
                </div>
                <div class="profile-name">
                    <!-- Display the full name of the logged-in customer -->
                    <h2><?php echo $_SESSION['fullname']; ?></h2>
                </div>
            </header>

            <section class="content1">
                <div class="inbox2">
                    <div>
                        <h1>Calculator</h1>
                        <p class="subtitle">Calculate the maximum amount you can borrow.</p>

                        <div class="scrollable-calculator">
                            <form id="calculator-form" class="calculator-form">
                                <div class="form-group">
                                    <label for="pension-amount">Monthly Pension Amount</label>
                                    <div class="pension-input-container">
                                        <input type="number" id="pension-amount" name="monthly_pension" min="5" max="50" step="1" required>
                                        <span class="fixed-zeros">00.00</span>
                                        <small style="margin-left:60px">Please enter value from 5 to 50 only.</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="loan-term">Loan Term</label>
                                    <select id="loan-term" name="loan_term" required>
                                        <option value="">Choose loan term</option>
                                        <option value="6">6 months</option>
                                        <option value="12">12 months</option>
                                        <option value="18">18 months</option>
                                        <option value="24">24 months</option>
                                        <option value="30">30 months</option>
                                        <option value="36">36 months</option>
                                    </select>
                                </div>

                                <button type="submit">Calculate</button>

                                <!-- Update the HTML to include a floating details box -->
                                <div class="result">
                                    <p>Expected Loan Amount 
                                        <button type="button" id="show-details" style="border:none; background:none;">
                                            <i class="fas fa-question-circle" style="color:#10184f;"><span style="font-size: smaller;"> More Details</span></i>
                                        </button>
                                    </p>
                                    <span class="loan-amount">0.00</span>
                                </div>

                                <!-- Floating box for showing result details -->
                                <div class="result-details-box">
                                    <button type="button" id="close-details" style="float:right; border:none; background:none; font-size:20px;">&times;</button>
                                    <h4 style="color:#10184f">Loan Computation</h4>
                                    <p>Amount to be Received: ₱<span class="received-amount" style="font-weight:bold">0.00</span></p>
                                    <p>Payable in: <span class="payable-months" style="font-weight:bold">0.00</span><span style="font-weight:bold"> months</span></p>
                                    <p>Monthly Payment: ₱<span class="monthly-payment" style="font-weight:bold">0.00</span></p>
                                    <p>Interest Amount: ₱<span class="company-interest" style="font-weight:bold">0.00</span></p>
                                    <p>Total Loan Amount To Pay: ₱<span class="loan-interest" style="font-weight:bold">0.00</span></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
    $(document).ready(function() {
        // Ensure default value is "5" on page load
        $('#pension-amount').val('5');

        // Hide the question mark icon initially
        $('#show-details').hide();

        // Handle form submission
        $('#calculator-form').on('submit', function(e) {
            e.preventDefault();

            // Get form data
            var monthlyPension = parseFloat($('#pension-amount').val()) * 100; // Append two zeros by multiplying by 100
            var loanTerm = parseInt($('#loan-term').val());

            // Check if the fields are not empty and within range
            if (monthlyPension && loanTerm && monthlyPension >= 500 && monthlyPension <= 5000) {
                // Calculate the total loanable amount excluding interest
                var totalPension = monthlyPension * loanTerm;
                var actualLoanAmount = totalPension * 0.83333333; // 83.3333% of the total pension
                var interestAmount = actualLoanAmount * 0.20; // 20% interest on the loan amount

                // Display the results
                $('.loan-amount').text(actualLoanAmount.toFixed(2));
                $('.interest-amount').text(interestAmount.toFixed(2));

                // Show the question mark icon for showing details
                $('#show-details').show(); // Display the question mark only after calculation
            } else {
                alert('Please enter a valid monthly pension amount between 5 and 50.');
            }
        });

        // Handle showing the computation details when clicking the question mark
        $('#show-details').on('click', function() {
            var monthlyPension = parseFloat($('#pension-amount').val()) * 100; // Convert input to real amount (e.g., 500)
            var loanTerm = parseInt($('#loan-term').val());

            if (monthlyPension && loanTerm && monthlyPension >= 500 && monthlyPension <= 5000) {
                var totalPension = monthlyPension * loanTerm;
                var actualLoanAmount = totalPension * 0.83333333;
                var monthlyPayment = totalPension / loanTerm; // Monthly payment the pensioner will make
                var interestAmount = actualLoanAmount * 0.20;
                var companyInterest = interestAmount; // Company's interest earnings
                var interestPerMonth = interestAmount / loanTerm;
                var netCashOut = actualLoanAmount - 200;

                // Display the additional details
                $('.monthly-payment').text(monthlyPayment.toFixed(2));
                $('.company-interest').text(companyInterest.toFixed(2));
                $('.loan-interest').text(totalPension.toFixed(2));
                $('.payable-months').text(loanTerm);
                $('.received-amount').text(netCashOut.toFixed(2));

                // Show the result details box beside the question mark
                $('.result-details-box').show();
            } else {
                alert('Please calculate the loan amount first.');
            }
        });

        // Handle closing the result details box
        $('#close-details').on('click', function() {
            $('.result-details-box').hide();
        });

        // Ensure user input stays within 5-50 range and updates correctly
        $('#pension-amount').on('input', function() {
            var value = $(this).val();
            $(this).val(value); // Keep the user's input visible as 5-50
        });
    });

    // Reset the session timeout timer on user activity
    let inactivityTimer;

    function resetTimer() {
        clearTimeout(inactivityTimer);
        // Ping the server to reset the PHP session activity timestamp
        fetch("reset_timer.php");
        inactivityTimer = setTimeout(logOut, 300000); // 5 minutes = 300000 ms
    }

    // Automatically log out the user if there's no activity
    function logOut() {
        alert("You have been logged out due to inactivity.");
        window.location.href = "customer_login.php"; // Redirect to homepage (login) page
    }

    // Track various user activities to reset the timer
    window.onload = resetTimer;
    window.onmousemove = resetTimer;
    window.onkeypress = resetTimer;
    window.onscroll = resetTimer;
    </script>
</body>
</html>
