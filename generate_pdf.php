<?php
// Check if the session is already started before starting it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require('fpdf.php');
require('fpdi/src/autoload.php'); // Load FPDI
include 'db_connect.php'; // Include your DB connection

// Function to convert date format from 'yyyy-mm-dd' to 'mm/dd/yyyy'
function formatDate($date) {
    if (empty($date)) return '';
    try {
        $dateObj = new DateTime($date);
        return $dateObj->format('m/d/Y');
    } catch (Exception $e) {
        return $date; // Return original if formatting fails
    }
}

// Use either `apply_loan_id` from the session or from URL parameters
$apply_loan_id = $_GET['apply_loan_id'] ?? ($_SESSION['apply_loan_id'] ?? null);

// Check if apply_loan_id is available
if (!$apply_loan_id) {
    echo "No loan application ID found.";
    exit; // Terminate if no ID is found
}

// Fetch the pensioner and co-maker information from the database
$query = "SELECT * FROM apply_loan WHERE apply_loan_id = ?"; // Use apply_loan_id for the query
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo "Error preparing the statement: " . $conn->error;
    exit;
}

$stmt->bind_param("i", $apply_loan_id); // Assuming apply_loan_id is an integer
if (!$stmt->execute()) {
    echo "Error executing the statement: " . $stmt->error;
    exit;
}

$result = $stmt->get_result();

// Check if the data exists
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();

    // Initialize FPDI for PDF template handling
    $pdf = new \setasign\Fpdi\Fpdi();
    $pdf->AddPage();

    // Import the template PDF
    $pdf->setSourceFile('Pensioners-Application-Form.pdf'); // Path to your built-in PDF
    $templateId = $pdf->importPage(1);
    $pdf->useTemplate($templateId, 0, 0, 210, 297); // A4 size dimensions

    // Set font
    $pdf->SetFont('Arial', '', 11);  // Use Arial instead of Helvetica

    // Format dates
    $date_created_formatted = formatDate($data['date_created']);
    $pensioner_bday_formatted = formatDate($data['p_birthdate']);
    $spouse_bday_formatted = formatDate($data['spouse_bday']);
    $spouse_death_formatted = formatDate($data['spouse_death']);
    $comaker_bday_formatted = formatDate($data['c_birthdate']);

    //Concatenate Co-Maker Name and Address
    $co_maker_fullname = $data['c_firstname'] . " " . $data['c_middlename'] . " " . $data['c_lastname'];
    $co_maker_fulladdress = $data['c_street_no'] . ", " . $data['c_barangay'] . ", " . $data['c_municipality'] . ", " . $data['c_province'];

    // Positioning for Pensioner Data
    $pdf->SetXY(40, 21);
    $pdf->Cell(40, 10, $data['account_no']); // Account No.

    $pdf->SetXY(110, 21);
    $pdf->Cell(40, 10, $data['customer_code']); // Customer Code

    $pdf->SetXY(164.3, 20.8);
    $pdf->Cell(40, 10, $date_created_formatted); // Date Created

    $pdf->SetXY(35, 28);
    $pdf->Cell(40, 10, $data['p_lastname']); // Pensioner Last Name

    $pdf->SetXY(100, 28);
    $pdf->Cell(40, 10, $data['p_firstname']); // Pensioner First Name

    $pdf->SetXY(170, 28);
    $pdf->Cell(40, 10, $data['p_middlename']); // Pensioner Middle Name

    $pdf->SetXY(38, 36);
    $pdf->Cell(40, 10, $pensioner_bday_formatted); // Pensioner Birthday

    $pdf->SetXY(100, 36);
    $pdf->Cell(40, 10, $data['sex']); // Pensioner Sex

    $pdf->SetXY(170, 36);
    $pdf->Cell(40, 10, $data['p_civil_status']); // Pensioner Civil Status

    $pdf->SetXY(35, 43);
    $pdf->Cell(40, 10, $data['p_street_no']); // Street No.

    $pdf->SetXY(100, 43);
    $pdf->Cell(40, 10, $data['p_barangay']); // Barangay

    $pdf->SetXY(35, 49);
    $pdf->Cell(40, 10, $data['p_municipality']); // Municipality

    $pdf->SetXY(100, 49);
    $pdf->Cell(40, 10, $data['p_province']); // Province

    $pdf->SetXY(35, 59);
    $pdf->Cell(40, 10, $data['spouse_name']); // Spouse Name

    $pdf->SetXY(92, 53);
    $pdf->Cell(40, 10, $spouse_bday_formatted); // Spouse Birthday

    $pdf->SetXY(125, 53);
    $pdf->Cell(40, 10, $data['zipcode']); // Zipcode

    $pdf->SetXY(92, 59);
    $pdf->Cell(40, 10, $spouse_death_formatted); // Spouse Death Date

    $pdf->SetXY(160, 59);
    $pdf->Cell(40, 10, $data['contact_no_pensioner']); // Pensioner Contact No.

    // Positioning for Co-Maker Data
    $pdf->SetXY(38, 68);
    $pdf->Cell(40, 10, $co_maker_fullname); // Co-Maker Fullname

    $pdf->SetXY(160, 68);
    $pdf->Cell(40, 10, $data['contact_no_comaker']); // Co-Maker Contact No.

    $pdf->SetXY(45, 74.5);
    $pdf->Cell(40, 10, $co_maker_fulladdress); // Co-Maker Fulladdress

    $pdf->SetXY(180, 74.5);
    $pdf->Cell(40, 10, $data['relationship']); // Relation to Pensioner

    $pdf->SetXY(35, 81.5);
    $pdf->Cell(40, 10, $data['occupation']); // Co-Maker Occupation

    $pdf->SetXY(100, 81.5);
    $pdf->Cell(40, 10, $comaker_bday_formatted); // Co-Maker Birthday

    $pdf->SetXY(170, 81.5);
    $pdf->Cell(40, 10, $data['c_civil_status']); // Co-Maker Civil Status

    //Positioning for Payment/Loan Information
    $pdf->SetXY(35, 128);
    $pdf->Cell(40, 10, $data['bank_name']); // Bank Name

    $pdf->SetXY(100, 128);
    $pdf->Cell(40, 10, $data['savings_account_no']); // Savings Account No.

    $pdf->SetXY(176, 128);
    $pdf->Cell(40, 10, $data['loan_amount']); // Loan Amount

    $pdf->SetXY(35, 135);
    $pdf->Cell(40, 10, $data['bank_branch']); // Bank Branch

    $pdf->SetXY(100, 135);
    $pdf->Cell(40, 10, $data['card_no']); // Card No.

    $pdf->SetXY(176, 134.5);
    $pdf->Cell(40, 10, $data['months_to_pay']); // Months To Pay

    // $pdf->SetXY(48, 140);
    // $pdf->Cell(40, 10, $data['loan_type']); // SSS/PVAO/GSIS Number

    $pdf->SetXY(115, 139.5);
    $pdf->Cell(40, 10, $data['monthly_pension']); // Monthly Pension 

    $pdf->SetXY(172, 140.5);
    $pdf->Cell(40, 10, $data['voucher_no']); // Voucher Number

    $pdf->SetXY(25, 148);
    $pdf->Cell(40, 10, $data['claim_type']); // Claim Type

    $pdf->SetXY(48, 147);
    $pdf->Cell(40, 10, $data['withdrawal_date']); // Withdrawable Date

    $pdf->SetXY(115, 148);
    $pdf->Cell(40, 10, $data['monthly_amortization']); // Monthly Amortization 

    $pdf->SetXY(176, 147.5);
    $pdf->Cell(40, 10, $data['net_cashout']); // Net Cash Out

    $pdf->SetXY(55, 158);
    $pdf->Cell(40, 10, $data['sss_sp']); // SSS-SP

    $pdf->SetXY(108, 154);
    $pdf->Cell(40, 10, $data['application_status']); // Application Status 

    // $pdf->SetXY(172, 151);
    // $pdf->Cell(40, 10, $data['referral']); // Referral

    // Output the PDF in the browser
    $pdf->Output('I', 'Filled_Pensioners_Loan_Application_Form.pdf');

} else {
    echo "No application data found for this loan ID.";
}

$stmt->close();
$conn->close();
?>
