<?php
// Include the user session file to retrieve logged-in user's full name
include 'user_session.php';

// Include your database connection file
include 'db_connect.php';  // Replace this with your actual DB connection file

if (isset($_POST['submit'])) {
    // Collect all form data
    $customer_code = $_POST['customer_code'];
    $date_created = $_POST['date'];
    $pensioner_firstname = $_POST['pensioner_fname'];
    $pensioner_middlename = $_POST['pensioner_mname'];
    $pensioner_lastname = $_POST['pensioner_lname'];
    $pensioner_birthdate = $_POST['pensioner_bday'];
    $pensioner_civil_status = $_POST['pensioner_cstatus'];
    $sex = $_POST['sex'];
    $pensioner_contact_number = $_POST['contact_no_pensioner'];
    $pensioner_street_no = $_POST['pensioner_street_no'];
    $pensioner_barangay = $_POST['pensioner_barangay'];
    $pensioner_municipality = $_POST['pensioner_municipality'];
    $pensioner_province = $_POST['pensioner_province'];
    $zipcode = $_POST['zipcode'];

    // Spouse Information
    $spouse_name = $_POST['spouse_name'];
    $spouse_birthday = $_POST['spouse_bday'];
    $spouse_death = $_POST['spouse_death'];

    // Co-Maker Information
    $comaker_firstname = $_POST['comaker_fname'];
    $comaker_middlename = $_POST['comaker_mname'];
    $comaker_lastname = $_POST['comaker_lname'];
    $comaker_birthdate = $_POST['comaker_bday'];
    $comaker_civil_status = $_POST['comaker_cstatus'];
    $occupation = $_POST['occupation'];
    $comaker_contact_number = $_POST['contact_no_comaker'];
    $relation_pensioner = $_POST['relation_pensioner'];
    $comaker_street_no = $_POST['comaker_street_no'];
    $comaker_barangay = $_POST['comaker_barangay'];
    $comaker_municipality = $_POST['comaker_municipality'];
    $comaker_province = $_POST['comaker_province'];

    // Generate or retrieve account number
    $prefix = "550-";  // Static prefix for account number
    $query = "SELECT MAX(account_no) AS last_account_number FROM apply_loan";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastAccountNumber = $row['last_account_number'];
        $lastNumericPart = intval(substr($lastAccountNumber, 4));
        $newNumericPart = $lastNumericPart + 1;
        $newAccountNumber = $prefix . str_pad($newNumericPart, 5, "0", STR_PAD_LEFT);
    } else {
        $newAccountNumber = $prefix . "00001";
    }

    // Check if record with apply_loan_id already exists
    $apply_loan_id = $_POST['apply_loan_id']; // Assuming apply_loan_id is provided when editing
    $check_query = "SELECT * FROM apply_loan WHERE apply_loan_id = ?";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bind_param("i", $apply_loan_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Record exists, update the row
        $sql_apply_loan = "UPDATE apply_loan SET 
                            account_no = ?, date_created = ?, p_firstname = ?, p_middlename = ?, p_lastname = ?, 
                            p_birthdate = ?, p_civil_status = ?, sex = ?, contact_no_pensioner = ?, 
                            p_street_no = ?, p_barangay = ?, p_municipality = ?, p_province = ?, zipcode = ?, 
                            spouse_name = ?, spouse_bday = ?, spouse_death = ?, c_firstname = ?, c_middlename = ?, 
                            c_lastname = ?, c_birthdate = ?, c_civil_status = ?, occupation = ?, 
                            contact_no_comaker = ?, relationship = ?, c_street_no = ?, c_barangay = ?, 
                            c_municipality = ?, c_province = ? WHERE apply_loan_id = ?";

        $stmt_apply_loan = $conn->prepare($sql_apply_loan);
        $stmt_apply_loan->bind_param(
            "ssssssssssssssssssssssssssssssi", 
            $newAccountNumber, $date_created, $pensioner_firstname, $pensioner_middlename, 
            $pensioner_lastname, $pensioner_birthdate, $pensioner_civil_status, $sex, 
            $pensioner_contact_number, $pensioner_street_no, $pensioner_barangay, 
            $pensioner_municipality, $pensioner_province, $zipcode, $spouse_name, 
            $spouse_birthday, $spouse_death, $comaker_firstname, $comaker_middlename, 
            $comaker_lastname, $comaker_birthdate, $comaker_civil_status, $occupation, 
            $comaker_contact_number, $relation_pensioner, $comaker_street_no, 
            $comaker_barangay, $comaker_municipality, $comaker_province, $apply_loan_id
        );
    } else {
        // Record does not exist, insert a new row
        $sql_apply_loan = "INSERT INTO apply_loan (account_no, customer_code, date_created, p_firstname, p_middlename, p_lastname, p_birthdate, p_civil_status, sex, contact_no_pensioner, p_street_no, p_barangay, p_municipality, p_province, zipcode, spouse_name, spouse_bday, spouse_death, c_firstname, c_middlename, c_lastname, c_birthdate, c_civil_status, occupation, contact_no_comaker, relationship, c_street_no, c_barangay, c_municipality, c_province)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_apply_loan = $conn->prepare($sql_apply_loan);
        $stmt_apply_loan->bind_param(
            "ssssssssssssssssssssssssssssss", 
            $newAccountNumber, $customer_code, $date_created, $pensioner_firstname, 
            $pensioner_middlename, $pensioner_lastname, $pensioner_birthdate, 
            $pensioner_civil_status, $sex, $pensioner_contact_number, $pensioner_street_no, 
            $pensioner_barangay, $pensioner_municipality, $pensioner_province, $zipcode, 
            $spouse_name, $spouse_birthday, $spouse_death, $comaker_firstname, 
            $comaker_middlename, $comaker_lastname, $comaker_birthdate, $comaker_civil_status, 
            $occupation, $comaker_contact_number, $relation_pensioner, $comaker_street_no, 
            $comaker_barangay, $comaker_municipality, $comaker_province
        );
    }

    // Execute the statement
    if ($stmt_apply_loan->execute()) {
        // If a new application is created, set the session variable
        if (empty($apply_loan_id)) { // Only set if it's a new application
            $_SESSION['apply_loan_id'] = $conn->insert_id; // Store the new application ID in session
        } else {
            $_SESSION['apply_loan_id'] = $apply_loan_id; // Update the session if editing
        }

        echo("Loan information saved successfully!");
        header("Location: customer_checkpdf.php");
        exit();
    } else {
        echo "Error: " . $stmt_apply_loan->error;
    }

    // Close the prepared statements
    $stmt_check->close();
    $stmt_apply_loan->close();
}

// Close the connection
$conn->close();
?>
