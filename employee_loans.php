<?php
session_start(); // Start the session at the beginning

date_default_timezone_set("Etc/GMT+8"); // Set timezone

include 'db_connect.php'; // Include the database connection only once

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to login if not logged in
    exit;
}

// Retrieve the user_id from the session
$user_id = $_SESSION['user_id'];

// Query to get the user's name and user type from the database
$user_query = "SELECT name, user_type FROM user WHERE user_id = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc(); // Fetch user details as an associative array
$stmt->close();

$message = '';

// Fetch customers data 
$query = "SELECT apply_loan_id, account_no, p_firstname, p_middlename, p_lastname, contact_no_pensioner, 
          p_street_no, p_barangay, p_municipality, p_province, customer_code, status,
          loan_type, loan_amount, application_status, payment_schedule 
          FROM apply_loan";
$result = $conn->query($query);


// Fetch account numbers for dropdown in modal
$account_query = "SELECT account_no, p_firstname, p_middlename, p_lastname FROM apply_loan";
$account_result = $conn->query($account_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Management System Dashboard</title>
    <link rel="stylesheet" href="css/employee.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <style>
        /* Modal styling */
        .modal2 {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content2 {
            margin: 1% 0 0 25%;
            background-color: #fff;
            padding: 20px;
            width: 80%;
            max-width: 700px;
            max-height: 96vh; /* Set a max height */
            overflow-y: auto; /* Enable vertical scrolling */
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header" onclick="toggleSidebar()">
        <span class="role">PANEL</span>
        <span class="dropright-icon">&#60;</span>
    </div>
    <ul class="nav-links">
        <li class="nav-item">
            <a href="employee_home.php" class="nav-link">
                <img src="img/dashboard.png" alt="Home">
                <span class="text">Home</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="employee_customers.php" class="nav-link">
                <img src="img/customer.png" alt="Customers">
                <span class="text">Customers</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="employee_loans.php" class="nav-link active">
                <img src="img/loans.png" alt="Loans">
                <span class="text">Loans</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="employee_payments.php" class="nav-link">
                <img src="img/credit-card.png" alt="Payments">
                <span class="text">Payments</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="employee_map.php" class="nav-link">
                <img src="img/map.png" alt="Map">
                <span class="text">Map</span>
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <header>
        <h1>Loan Management System</h1>
        <div class="user-wrapper">
            <div class="notification">
                <span class="icon" onclick="showNotifications()" style="cursor: pointer;">🔔</span>
            </div>
            <div class="user" onclick="toggleDropdown()">
                <span class="dropdown-icon">▼</span>
                <span class="username">
                    <?php echo htmlspecialchars($user['name']); ?> <!-- Display the user's name -->
                </span>
                <div class="dropdown-content">
                    <a href="index.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Logout
                    </a
                </div>
            </div>
        </div>
    </header>

    <main>
        <h2>Loans</h2>
        <button type="button" onclick="openAddLoanModal()">+ Add Loan Detail</button>
        <div class="cards">
            <div class="table-container">
                <div class="table-wrapper">
                    <table id="customersTable" class="display">
                        <thead>
                        <!-- Payment Detail (Add Payment Info - Button)
                        Status Options (Active, Paid/Done, Unpaid)
                        Action (View More, Archive) -->
                            <tr>
                                <th>Account No.</th>
                                <th>Borrower</th>
                                <th>Loan Detail</th>
                                <th>Payment Detail</th>
                                <th>Status</th> 
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php  
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $full_address = $row["p_street_no"] . ", Brgy. " . $row["p_barangay"] . ", " . $row["p_municipality"] . ", " . $row["p_province"];
                                        echo "<tr>";
                                        echo "<td>" . $row["account_no"] . "</td>";
                                        echo "<td>Name: " . $row["p_firstname"] . " " . $row["p_middlename"] . " " . $row["p_lastname"] . "<br>Contact: " . $row["contact_no_pensioner"] . "<br>Address: " . $full_address . "</td>";
                                        
                                        // Loan Detail column populated with database values
                                        echo "<td>";
                                        if (!empty($row["loan_type"]) || !empty($row["loan_amount"])) {
                                            echo "Loan Type: " . $row["loan_type"] . "<br>";
                                            echo "Amount: ₱" . $row["loan_amount"] . "<br>";
                                            echo "Application Status: " . $row["application_status"] . "<br>";
                                            echo "Payment Schedule: " . $row["payment_schedule"];
                                        } else {
                                            echo "No details available";
                                        }
                                        echo "</td>";
                                        
                                        // Other columns
                                        echo '<td><div class="btns">';
                                        if ($row["status"] === "Released") {
                                            echo '<button class="edit-btn" onclick="openModal(' . $row['apply_loan_id'] . ')">View</button>';
                                        }
                                        echo '</div></td>';
                                        echo "<td>" . (isset($row["status"]) ? $row["status"] : "") . "</td>";
                                        echo '<td><div class="btns">
                                                <button class="edit-btn"><a href="generate_pdf.php?apply_loan_id=' . $row["apply_loan_id"] . '" target="_blank">View PDF</a></button> 
                                                <button class="add-btn" onclick="openUpdateLoanModal(' . $row['apply_loan_id'] . ')">Update</button>
                                                <button class="delete-btn" onclick="archiveLoan(' . $row['apply_loan_id'] . ')">Archive</button>
                                            </div></td>';
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No loans found</td></tr>";
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Placeholder for customers content -->
        </div>
    </main>
</div>

<!-- Add Loan Modal -->
<div id="addLoanModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAddLoanForm()">&times;</span>
        <form id="addLoanForm" class="pensioner-form" method="POST">
        <input type="hidden" name="apply_loan_id" id="apply_loan_id" value="">
            <div class="form-group1">
                <label for="account_no">Select Account No.</label>
                <select id="account_no" name="account_no" required>
                    <option value="">Select an Account No.</option>
                    <?php 
                    if ($account_result->num_rows > 0) {
                        while($account_row = $account_result->fetch_assoc()) {
                            echo "<option value='" . $account_row['account_no'] . "'>" . $account_row['account_no'] . " - " . $account_row['p_firstname'] . " " . $account_row['p_middlename'] . " " . $account_row['p_lastname'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <h2 style="color:darkblue; text-decoration:none">Payment Information</h2>
            <div class="form-group1">
                <label for="bank_name">Bank Name</label>
                <input type="text" id="bank_name" name="bank_name" placeholder="Bank Name">
            </div>
            <div class="form-group1">
                <label for="bank_branch">Bank Branch</label>
                <input type="text" id="bank_branch" name="bank_branch" placeholder="Branch Location">
            </div>

            <div class="checkbox-group" required>
                <label>
                    <input type="checkbox" name="loan_type" value="SSS" onclick="onlyOneCheckbox(this, 'loan_type')" />
                    SSS
                </label>
                <label>
                    <input type="checkbox" name="loan_type" value="PVAO" onclick="onlyOneCheckbox(this, 'loan_type')" />
                    PVAO
                </label>
                <label>
                    <input type="checkbox" name="loan_type" value="GSIS" onclick="onlyOneCheckbox(this, 'loan_type')" />
                    GSIS
                </label>
            </div>

            <div class="form-group1">
                <label for="card_number">Card No.</label>
                <input type="text" id="card_number" name="card_number" placeholder="Card Number" required>
            </div>

            <div class="form-group1">
                <label for="savings_account_no">Savings Account No.</label>
                <input type="text" id="savings_account_no" name="savings_account_no" placeholder="Savings Account Number">
            </div>
                        
            <div class="form-group1">
                <label for="claim_type">Claim Type</label>
                <select id="claim_type" name="claim_type" required>
                    <option value">Select an option</option>
                    <option value="Personal">Personal Loan</option>
                    <option value="Emergency">Emergency Loan</option>
                    <option value="Business">Business Loan</option>
                </select>
            </div>

            <div class="form-group1">
                <label for="withdrawal_date">Withdrawal Date</label>
                <input type="date" id="withdrawal_date" name="withdrawal_date" placeholder="mm/dd/yyyy" required>
            </div>
                        
            <div class="form-group1">
                <label for="sss_sp">If SSS SP, Remaining Months to RUN:</label>
                <input type="text" id="sss_sp" name="sss_sp" placeholder="Remaining Months">
            </div><br>

            <h2 style="color:darkblue; text-decoration:none">Loan Information</h2>
            <div class="checkbox-group" required>
                <label>
                    <input type="checkbox" name="application_status" value="new" onclick="onlyOneCheckbox(this, 'application_status')" />
                    New
                </label>
                <label>
                    <input type="checkbox" name="application_status" value="renewal" onclick="onlyOneCheckbox(this, 'application_status')" />
                    Renewal
                </label>
                <label>
                    <input type="checkbox" name="application_status" value="return" onclick="onlyOneCheckbox(this, 'application_status')" />
                    Return
                </label>
            </div>

            <div class="form-group1">
                <label for="monthly_pension">Monthly Pension (₱)</label>
                <input type="text" id="monthly_pension" name="monthly_pension" placeholder="Enter Monthly Pension" required oninput="calculateAmortization()">
            </div>

            <div class="form-group1">
                <label for="loan_amount">Loan Amount (₱)</label>
                <input type="number" id="loan_amount" name="loan_amount" placeholder="Enter Loan Amount" required oninput="calculateAmortization()">
            </div>

            <div class="form-group1">
                <label for="monthly_amortization">Monthly Amortization (₱)</label>
                <input type="number" id="monthly_amortization" name="monthly_amortization" placeholder="Enter Desired Amount" required oninput="calculateAmortization()">
            </div>

            <div class="form-group1">
                <label for="months_to_pay">Months To Pay Off:</label>
                <input type="number" id="months_to_pay" name="months_to_pay" readonly>
            </div>

            <div class="form-group1">
                <label for="sukli">Excess Over Payment (Sukli)</label>
                <input type="number" id="sukli" name="sukli" readonly>
            </div>

            <div class="form-group1">
                <label for="net_cashout">Net Cash Out (₱)</label>
                <input type="number" id="net_cashout" name="net_cashout" readonly>
            </div>

            <div class="form-group1">
                <label for="total_amount">Total Amount To Pay (₱)</label>
                <input type="number" id="total_amount" name="total_amount" readonly>
            </div>

            <div class="form-group1">
                <label for="voucher_number">Voucher Number <span style="color:gray">(optional)</span></label>
                <input type="number" id="voucher_number" name="voucher_number" placeholder="Enter Voucher Number">
            </div>

            <div class="form-group1">
                <label for="payment_schedule">Payment Schedule</label>
                <select id="payment_schedule" name="payment_schedule" required>
                    <option value="1st_day">1st Day of the Month</option>
                    <option value="16th_day">16th Day of the Month</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>

            <div id="loanStatusDiv" class="form-group1" style="display: none;">
                <label for="loan_status">Loan Status</label>
                <select id="loan_status" name="loan_status">
                    <option value="">Select Loan Status</option>
                    <option value="Released">Released</option>
                    <option value="Denied">Denied</option>
                    <option value="On_hold">On Hold</option>
                </select>
            </div>

            <div class="form-buttons" style="justify-content: flex-end;">
                <button type="submit" class="btn-next">Submit Loan Info</button>
            </div>
        </form>
    </div>
</div>

<div id="paymentDetailsModal" class="modal2">
    <div class="modal-content2">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="modalContent">
            <!-- AJAX will load content here -->
        </div>
    </div>
</div>

<!-- DataTables JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<!-- Your custom script -->
<script src="scripts.js"></script>
<script>
    $(document).ready(function() {
        $('#customersTable').DataTable();
    });

    // Open modal and set apply_loan_id if provided
    function openAddLoanModal(applyLoanId) {
        document.getElementById("addLoanModal").style.display = "block";
        if (applyLoanId) {
            document.getElementById("apply_loan_id").value = applyLoanId;
        } else {
            document.getElementById("apply_loan_id").value = "";
        }
    }

    // Function to close the first form modal
    function closeAddLoanForm() {
        document.getElementById('addLoanModal').style.display = 'none';
    }

    // Set apply_loan_id based on selected account_no and add debugging
    document.getElementById("account_no").addEventListener("change", function () {
        const selectedAccount = this.value;
        if (!selectedAccount) {
            console.error("No account selected.");
            return;
        }

        // Fetch apply_loan_id associated with the selected account
        fetch(`get_apply_loan_id.php?account_no=${selectedAccount}`)
            .then(response => response.json())
            .then(data => {
                if (data.apply_loan_id) {
                    document.getElementById("apply_loan_id").value = data.apply_loan_id;
                    console.log("apply_loan_id set to:", data.apply_loan_id); // Debugging log
                } else {
                    console.error("apply_loan_id not found for selected account.");
                    alert("Could not retrieve apply_loan_id for the selected account.");
                }
            })
            .catch(error => console.error('Error fetching apply_loan_id:', error));
    });

    // Function to allow only one checkbox to be selected
    function onlyOneCheckbox(checkbox, name) {
        const checkboxes = document.getElementsByName(name);
        checkboxes.forEach((cb) => {
            if (cb !== checkbox) cb.checked = false;
        });
    }

    // Attach event listeners for input changes and calculations
    document.getElementById('loan_amount').addEventListener('input', calculateAmortization);
    document.getElementById('months_to_pay').addEventListener('change', calculateAmortization);

    // Function to calculate amortization and other loan details
    function calculateAmortization() {
        const monthlyPension = parseFloat(document.getElementById('monthly_pension').value) || 0;
        const loanAmount = parseFloat(document.getElementById('loan_amount').value) || 0;
        const monthlyAmortization = parseFloat(document.getElementById('monthly_amortization').value) || 0;

        if (!loanAmount || !monthlyAmortization) return; // Exit if inputs are invalid

        const processingFee = 200; // 1% service fee as a fixed amount
        const interestRate = 0.20; // 20% interest rate
        const interestAmount = loanAmount * interestRate; // Calculate the interest amount
        const totalWithInterest = loanAmount + interestAmount; // Net cash after fees and interest
        const netCashout = loanAmount - processingFee;

        // Calculate the number of months to pay off the loan
        const monthsToPay = totalWithInterest / monthlyAmortization;

        // Calculate the remaining balance (Sukli)  
        const sukli = monthlyPension - monthlyAmortization;

        // Set values in the respective fields
        document.getElementById('months_to_pay').value = monthsToPay.toFixed(2);
        document.getElementById('net_cashout').value = netCashout.toFixed(2);
        document.getElementById('sukli').value = sukli.toFixed(2);
        document.getElementById('total_amount').value = totalWithInterest.toFixed(2);
    }

// AJAX submission of form data with debugging
document.getElementById("addLoanForm").addEventListener("submit", function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const applyLoanId = formData.get("apply_loan_id");

    if (!applyLoanId) {
        console.error("apply_loan_id is missing in form submission.");
        alert("apply_loan_id is missing. Please select an account again.");
        return;
    }

    console.log("Submitting form with apply_loan_id:", applyLoanId); // Debugging

    fetch("submit_loan.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Loan information saved successfully!");
            closeAddLoanForm();
            this.reset();
            fetchUpdatedLoans(applyLoanId);
        } else {
            alert(data.error || data.info || "An error occurred. Please try again.");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An unexpected error occurred.");
    });
});

function fetchUpdatedLoans(applyLoanId) {
    fetch(`fetch_loans.php?apply_loan_id=${applyLoanId}`)
        .then(response => response.json())
        .then(data => {
            if (data.loanDetails) {
                // Locate the row for the updated loan and update the Loan Detail column
                const tableRows = document.querySelectorAll("#customersTable tbody tr");
                tableRows.forEach(row => {
                    const accountNoCell = row.cells[0];
                    if (accountNoCell && accountNoCell.textContent === data.loanDetails.account_no) {
                        // Update Loan Detail column with retrieved information
                        const loanDetailCell = row.cells[2]; // Assuming Loan Detail is the third column
                        loanDetailCell.innerHTML = `
                            Loan Type: ${data.loanDetails.loan_type}<br>
                            Amount: ${data.loanDetails.loan_amount}<br>
                            Application Status: ${data.loanDetails.application_status}<br>
                            Payment Schedule: ${data.loanDetails.payment_schedule}
                        `;
                    }
                });
            } else {
                console.error("Loan details not found for the specified apply_loan_id.");
            }
        })
        .catch(error => console.error('Error fetching updated loan details:', error));
}

function openUpdateLoanModal(applyLoanId) {
    // Show the modal
    document.getElementById('addLoanModal').style.display = 'block';

    // Show the loan status dropdown (only for updating)
    document.getElementById('loanStatusDiv').style.display = 'block';

    // Fetch loan details and populate the form fields
    fetch(`get_loan_details.php?apply_loan_id=${applyLoanId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('apply_loan_id').value = data.apply_loan_id;
            document.getElementById('account_no').value = data.account_no;
            document.getElementById('bank_name').value = data.bank_name;
            document.getElementById('bank_branch').value = data.bank_branch;
            document.querySelector(`input[name="loan_type"][value="${data.loan_type}"]`).checked = true;
            document.getElementById('card_number').value = data.card_number;
            document.getElementById('savings_account_no').value = data.savings_account_no;
            document.getElementById('claim_type').value = data.claim_type;
            document.getElementById('withdrawal_date').value = data.withdrawal_date;
            document.getElementById('sss_sp').value = data.sss_sp;
            document.querySelector(`input[name="application_status"][value="${data.application_status}"]`).checked = true;
            document.getElementById('monthly_pension').value = data.monthly_pension;
            document.getElementById('loan_amount').value = data.loan_amount;
            document.getElementById('monthly_amortization').value = data.monthly_amortization;
            document.getElementById('months_to_pay').value = data.months_to_pay;
            document.getElementById('sukli').value = data.sukli;
            document.getElementById('net_cashout').value = data.net_cashout;
            document.getElementById('voucher_number').value = data.voucher_number;
            document.getElementById('payment_schedule').value = data.payment_schedule;

            // Set the loan status dropdown to the current status
            document.getElementById('loan_status').value = data.loan_status;
        })
        .catch(error => console.error('Error fetching loan details:', error));
}

function closeAddLoanForm() {
    document.getElementById('addLoanModal').style.display = 'none';
    document.getElementById('addLoanForm').reset();

    // Hide the loan status dropdown for new loan entries
    document.getElementById('loanStatusDiv').style.display = 'none';
}

function archiveLoan(applyLoanId) {
    if (confirm("Are you sure you want to archive this loan?")) {
        fetch(`archive_loan.php?apply_loan_id=${applyLoanId}`, {
            method: 'GET'
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Display success or error message
            location.reload(); // Reload the page to reflect changes
        })
        .catch(error => console.error('Error archiving loan:', error));
    }
}

function openModal(applyLoanId) {
        // Show modal
        document.getElementById("paymentDetailsModal").style.display = "block";
        
        // Fetch loan details with AJAX
        const xhr = new XMLHttpRequest();
        xhr.open("GET", `payment_details.php?apply_loan_id=${applyLoanId}`, true);
        xhr.onload = function() {
            if (this.status === 200) {
                document.getElementById("modalContent").innerHTML = this.responseText;
            } else {
                document.getElementById("modalContent").innerHTML = "Error loading details.";
            }
        };
        xhr.send();
    }

    function closeModal() {
        document.getElementById("paymentDetailsModal").style.display = "none";
    }
</script>
</body>
</html>

