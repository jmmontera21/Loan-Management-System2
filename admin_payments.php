<?php
session_start(); // Start the session at the beginning

date_default_timezone_set("Etc/GMT+8"); // Set the timezone once at the top

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

// Fetch loan application records
$data = [];
$sql = "SELECT apply_loan_id, account_no, p_firstname, p_middlename, p_lastname, status 
        FROM apply_loan";
$result = $conn->query($sql);
if ($result) {
    $data = $result->fetch_all(MYSQLI_ASSOC);
}

// Fetch payment records for displaying in the table
$payments = [];
$payment_query = "SELECT p.payment_id, p.apply_loan_id, p.payment_date, p.amount_paid, p.penalty, 
                  a.account_no, a.p_firstname, a.p_middlename, a.p_lastname 
                  FROM loan_payments p 
                  JOIN apply_loan a ON p.apply_loan_id = a.apply_loan_id";
$payment_result = $conn->query($payment_query);
if ($payment_result) {
    $payments = $payment_result->fetch_all(MYSQLI_ASSOC);
}

// Check for a success or error message in the URL
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>

<?php date_default_timezone_set("Etc/GMT+8");?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Management System Dashboard</title>
    <link rel="stylesheet" href="css/employee.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body>
<div class="sidebar">
    <div class="sidebar-header" onclick="toggleSidebar()">
        <span class="role">PANEL</span>
        <span class="dropright-icon"><</span>
    </div>
    <ul class="nav-links">
        <li class="nav-item">
            <a href="admin_home.php" class="nav-link">
                <img src="img/dashboard.png" alt="Home">
                <span class="text">Home</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="admin_customers.php" class="nav-link">
                <img src="img/customer.png" alt="Customers">
                <span class="text">Customers</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="admin_loans.php" class="nav-link">
                <img src="img/loans.png" alt="Loans">
                <span class="text">Loans</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="admin_payments.php" class="nav-link active">
                <img src="img/credit-card.png" alt="Payments">
                <span class="text">Payments</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="admin_map.php" class="nav-link">
                <img src="img/map.png" alt="Map">
                <span class="text">Map</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="loan_plans.php" class="nav-link">
                <img src="img/plans.png" alt="Plans">
                <span class="text">Loan Plans</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="accounts.php" class="nav-link">
                <img src="img/accs.png" alt="Accounts">
                <span class="text">Accounts</span>
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
            <h2>Payments</h2>
            <button type="submit" onclick="showAddCustomerForm()">+ New Payment</button>
            <div class="cards">
                <div class="table-container">
                    <div class="table-wrapper">
                        <table id="paymentsTable" class="display">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Account No.</th>
                                    <th>Payment Date</th>
                                    <th>Amount</th>
                                    <th>Penalty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $index => $payment): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($payment['account_no']) ?></td>
                                        <td><?= htmlspecialchars($payment['payment_date']) ?></td>
                                        <td><?= htmlspecialchars($payment['amount_paid']) ?></td>
                                        <td><?= htmlspecialchars($payment['penalty']) ?></td>
                                        <td>Paid</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <div id="addCustomerModal" class="modal">
        <div class="modal-content2">
            <span class="close" onclick="closeAddCustomerForm()">&times;</span>
            <form method="post" action="submit_loan_payment.php">
                <div class="form-group2">
                    <label for="borrower">Borrower</label>
                    <select id="borrower" name="borrower" required>
                        <option value="">Select an option</option>
                        <?php foreach ($data as $row): ?>
                            <option value="<?= htmlspecialchars($row['apply_loan_id']) ?>">
                                <?= htmlspecialchars($row['account_no']) ?> - 
                                <?= htmlspecialchars($row['p_firstname']) ?> 
                                <?= htmlspecialchars($row['p_middlename']) ?> 
                                <?= htmlspecialchars($row['p_lastname']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group2">
                    <label for="payee">Payee</label>
                    <input type="text" id="payee" name="payee" required>
                </div>
                <div class="form-group2">
                    <label for="payableAmount">Payable Amount</label>
                    <input type="number" id="payableAmount" name="payableAmount" readonly>
                </div>
                <div class="form-group2">
                    <label for="monthlyPayment">Monthly Payment</label>
                    <input type="number" id="monthlyPayment" name="monthlyPayment" readonly>
                </div>
                <div class="form-group2">
                    <label for="amountPaid">Amount Paid</label>
                    <input type="number" id="amountPaid" name="amountPaid" required>
                </div>
                <div class="form-group2">
                    <label for="overduePenalty">Overdue Penalty</label>
                    <input type="number" id="overduePenalty" name="overduePenalty" required>
                </div>
                <div class="form-group2 button-group" style="justify-content: flex-end; align-items: flex-end;">
                    <button name="submit" class="save-changes2">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

<div id="alert" class="alert"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="scripts.js"></script>

<script>
$(document).ready(function () {
    $('#paymentsTable').DataTable(); // Initialize DataTables for payments table

    $('#borrower').on('change', function () {
        const borrowerId = $(this).val();
        if (borrowerId) {
            $.ajax({
                url: 'fetch_pensioner_details.php',
                type: 'POST',
                data: { apply_loan_id: borrowerId },
                dataType: 'json',
                success: function (data) {
                    if (!data.error) {
                        $('#payableAmount').val(data.total_amount);
                        $('#monthlyPayment').val(data.monthly_amortization);
                        $('#remainingBalance').val(data.total_amount - data.monthly_amortization);
                    } else {
                        alert(data.error);
                    }
                },
                error: function () {
                    alert('An error occurred while fetching loan details.');
                }
            });
        }
    });
});

<?php if (!empty($message)) : ?>
    window.onload = function() {
        showAlert('<?php echo $message; ?>');
    };
<?php endif; ?>
</script>

</body>
</html>