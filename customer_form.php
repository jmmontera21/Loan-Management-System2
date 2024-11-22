<?php
// Include the user session file to retrieve logged-in user's full name
include 'save_applyloan.php';
include 'db_connect.php';

$query = "SELECT province_id, province_name FROM provinces ORDER BY province_name";
$result = $conn->query($query);

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
    <title>Pensioner's Information Form</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    <li><a href="customer_calculator.php">Calculator</a></li>
                    <li class="active"><a href="customer_form.php">Apply Loan</a></li>
                    <li><a href="customer_loanhistory.php">Loan History</a></li>
                    <li><a href="customer_settings.php">Settings</a></li>
                    <li><a href="customer_logout.php">Log Out</a></li>
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

            <!-- <section class="content"> -->
                <div class="container1">
                    <div class="progress-bar">
                        <!-- You can replace these with actual steps if needed -->
                        <a href="customer_form.php"><div class="step active">1</div></a>
                        <a><div class="step">2</div></a>
                        <a><div class="step">3</div></a>
                    </div>

                    <form action="customer_form.php" id="pensionerForm" class="pensioner-form" method="POST">
                        <input type="hidden" name="apply_loan_id" id="apply_loan_id" value="">
                        <div class="form-group1">
                            <label for="account_number">Account Number</label>
                            <input type="text" id="account_number" name="account_number" value="Generating..." readonly>
                        </div>

                        <div class="form-group1">
                            <label for="customer_code">Customer Code</label>
                            <input type="text" id="customer_code" name="customer_code" value="<?php echo $_SESSION['customer_code']; ?>" readonly>
                        </div>

                        <div class="form-group1">
                            <label for="date">Date</label>
                            <input type="date" id="date" name="date" value="<?php echo date('Y-m-d'); ?>" readonly>
                        </div>

                        <h2 style="color:darkblue">Pensioner's Information</h2>

                        <div class="form-group1">
                            <label for="pensioner_fname">First Name</label>
                            <input type="text" id="pensioner_fname" name="pensioner_fname" placeholder="First Name" 
                                value="<?php echo isset($_SESSION['firstname']) ? $_SESSION['firstname'] : ''; ?>" readonly>
                        </div>

                        <div class="form-group1">
                            <label for="pensioner_mname">Middle Name</label>
                            <input type="text" id="pensioner_mname" name="pensioner_mname" placeholder="Middle Name"
                                value="<?php echo isset($_SESSION['middlename']) ? $_SESSION['middlename'] : ''; ?>" readonly>
                        </div>

                        <div class="form-group1">
                            <label for="pensioner_lname">Last Name</label>
                            <input type="text" id="pensioner_lname" name="pensioner_lname" placeholder="Last Name"
                                value="<?php echo isset($_SESSION['lastname']) ? $_SESSION['lastname'] : ''; ?>" readonly>
                        </div>

                        <div class="form-group1">
                            <label for="pensioner_bday">Birthdate</label>
                            <input type="date" id="pensioner_bday" name="pensioner_bday" value="<?php echo isset($_SESSION['birthdate']) ? $_SESSION['birthdate'] : ''; ?>" readonly>
                        </div>

                        <div class="form-group1">
                            <label for="pensioner_cstatus">Civil Status</label>
                            <select id="pensioner_cstatus" name="pensioner_cstatus" onchange="toggleSpouseInfo()" required>
                                <option value="">Select an option</option>
                                <option value="single">Single</option>
                                <option value="married">Married</option>
                                <option value="divorced">Divorced</option>
                                <option value="widowed">Widowed</option>
                            </select>
                        </div>

                        <div class="form-group1">
                            <label for="sex">Sex</label>
                            <select id="sex" name="sex" required>
                                <option>Select an option</option>
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>

                        <div class="form-group1">
                            <label for="contact_no_pensioner">Contact Number</label>
                            <input type="tel" id="contact_no_pensioner" name="contact_no_pensioner" value="<?php echo isset($_SESSION['contact_no']) ? $_SESSION['contact_no'] : ''; ?>" readonly>
                        </div>

                        <h2 style="text-decoration:none">Address</h2>

                        <div class="form-group1">
                            <label for="pensioner_street_no">House No. / Street</label>
                            <input type="text" id="pensioner_street_no" name="pensioner_street_no" placeholder="House No. / Street" required>
                        </div>

                        <div class="form-group1">
                            <label for="pensioner_province">Province</label>
                            <input type="text" id="pensioner_province" name="pensioner_province" value="<?php echo isset($_SESSION['province_name']) ? $_SESSION['province_name'] : ''; ?>" readonly>
                        </div>

                        <!-- City Dropdown -->
                        <div class="form-group1">
                            <label for="pensioner_municipality">Municipality</label>
                            <input type="text" id="pensioner_municipality" name="pensioner_municipality" value="<?php echo isset($_SESSION['city_name']) ? $_SESSION['city_name'] : ''; ?>" readonly>
                        </div>

                        <!-- Barangay Dropdown -->
                        <div class="form-group1">
                            <label for="pensioner_barangay">Barangay</label>
                            <input type="text" id="pensioner_barangay" name="pensioner_barangay" value="<?php echo isset($_SESSION['barangay_name']) ? $_SESSION['barangay_name'] : ''; ?>" readonly>
                        </div>

                        <div class="form-group1">
                            <label for="zipcode">Zip Code</label>
                            <input type="text" id="zipcode" name="zipcode" placeholder="Zip Code" required>
                        </div>

                        <h2 style="color:darkblue">Spouse's Information</h2>

                        <div class="form-group1">
                            <label for="spouse_name">Name</label>
                            <input type="text" id="spouse_name" name="spouse_name" placeholder="Spouse's Name" required>
                        </div>

                        <div class="form-group1">
                            <label for="spouse_bday">Birthdate</label>
                            <input type="date" id="spouse_bday" name="spouse_bday" required>
                        </div>

                        <div class="form-group1">
                            <label for="spouse_death">Date of Death</label>
                            <input type="date" id="spouse_death" name="spouse_death" required>
                        </div>

                        <h2 style="color:darkblue">Co-Maker's Information</h2>

                        <div class="form-group1">
                            <label for="comaker_fname">First Name</label>
                            <input type="text" id="comaker_fname" name="comaker_fname" placeholder="First Name" required>
                        </div>

                        <div class="form-group1">
                            <label for="comaker_mname">Middle Name</label>
                            <input type="text" id="comaker_mname" name="comaker_mname" placeholder="Middle Name" required>
                        </div>

                        <div class="form-group1">
                            <label for="comaker_lname">Last Name</label>
                            <input type="text" id="comaker_lname" name="comaker_lname" placeholder="Last Name" required>
                        </div>

                        <div class="form-group1">
                            <label for="comaker_bday">Birthdate</label>
                            <input type="date" id="comaker_bday" name="comaker_bday" required>
                        </div>

                        <div class="form-group1">
                            <label for="comaker_cstatus">Civil Status</label>
                            <select id="comaker_cstatus" name="comaker_cstatus" required>
                                <option>Select an option</option>
                                <option>Single</option>
                                <option>Married</option>
                                <option>Widowed</option>
                            </select>
                        </div>

                        <div class="form-group1">
                            <label for="occupation">Occupation</label>
                            <input type="text" id="occupation" name="occupation" placeholder="Occupation" required>
                        </div>

                        <div class="form-group1">
                            <label for="contact_no_comaker">Contact Number</label>
                            <input type="tel" id="contact_no_comaker" name="contact_no_comaker" placeholder="Contact Number" required>
                        </div>

                        <div class="form-group1">
                            <label for="relation_pensioner">Relationship to Pensioner</label>
                            <input type="text" id="relation_pensioner" name="relation_pensioner" placeholder="Relationship to Pensioner" required>
                        </div>

                        <h2 style="text-decoration:none">Address</h2>

                        <div class="form-group1">
                            <label for="comaker_street_no">House No. / Street No.</label>
                            <input type="text" id="comaker_street_no" name="comaker_street_no" placeholder="House No. / Street No." required>
                        </div>

                        <div class="form-group1">
                            <label for="comaker_province">Province</label>
                            <select id="comaker_province" name="comaker_province" required>
                                <option value="">Select an option</option>
                                <option value="Quezon">Quezon</option>
                            </select>
                        </div>

                        <div class="form-group1">
                            <label for="comaker_municipality">Municipality</label>
                            <select id="comaker_municipality" name="comaker_municipality" onchange="updateBarangays()" required>
                                <option value="">Select Municipality / City</option>
                                <option value="Agdangan">Agdangan</option>
                                <option value="Alabat">Alabat</option>
                                <option value="Atimonan">Atimonan</option>
                                <option value="Buenavista">Buenavista</option>
                                <option value="Burdeos">Burdeos</option>
                                <option value="Calauag">Calauag</option>
                                <option value="Candelaria">Candelaria</option>
                                <option value="Catanauan">Catanauan</option>
                                <option value="Dolores">Dolores</option>
                                <option value="General Luna">General Luna</option>
                                <option value="General Nakar">General Nakar</option>
                                <option value="Guinayangan">Guinayangan</option>
                                <option value="Gumaca">Gumaca</option>
                                <option value="Infanta">Infanta</option>
                                <option value="Jomalig">Jomalig</option>
                                <option value="Lopez">Lopez</option>
                                <option value="Lucban">Lucban</option>
                                <option value="Lucena City">Lucena City</option>
                                <option value="Macalelon">Macalelon</option>
                                <option value="Mauban">Mauban</option>
                                <option value="Mulanay">Mulanay</option>
                                <option value="Padre Burgos">Padre Burgos</option>
                                <option value="Pagbilao">Pagbilao</option>
                                <option value="Panukulan">Panukulan</option>
                                <option value="Patnanungan">Patnanungan</option>
                                <option value="Perez">Perez</option>
                                <option value="Pitogo">Pitogo</option>
                                <option value="Plaridel">Plaridel</option>
                                <option value="Polillo">Polillo</option>
                                <option value="Quezon">Quezon</option>
                                <option value="Real">Real</option>  
                                <option value="Sampaloc">Sampaloc</option>
                                <option value="San Andres">San Andres</option>
                                <option value="San Antonio">San Antonio</option>
                                <option value="San Francisco">San Francisco</option>
                                <option value="San Narciso">San Narciso</option>
                                <option value="Sariaya">Sariaya</option>
                                <option value="Tagkawayan">Tagkawayan</option>
                                <option value="Tayabas City">Tayabas City</option>
                                <option value="Tiaong">Tiaong</option>
                                <option value="Unisan">Unisan</option>
                            </select>
                        </div>

                        <div class="form-group1">
                            <label for="comaker_barangay">Barangay</label>
                            <select id="comaker_barangay" name="comaker_barangay" required>
                                <option value="">Select Barangay</option>
                            </select>
                        </div>

                        <h2 style="color:darkblue">Dependent's Information</h2>

                        <!-- Buttons to Add/Remove Dependents -->
                        <div class="dependents-control">
                            <label for="num-dependents" style="margin-top:10px">Number of Dependents:</label>
                            <button type="button" class="decrease" onclick="updateDependents(-1)">-</button>
                            <input type="number" id="num-dependents" value="1" min="1" max="5" readonly>
                            <button type="button" class="increase" onclick="updateDependents(1)">+</button>
                        </div><br>

                        <div id="dependents-container">
                            <h3>Dependent No.1</h3>
                            <div class="form-group1">
                                <label for="dependent_fname">First Name</label>
                                <input type="text" id="dependent_fname" name="dependent_fname" placeholder="First Name">
                            </div>
                            <div class="form-group1">
                                <label for="dependent_mname_">Middle Name</label>
                                <input type="text" id="dependent_mname" name="dependent_mname" placeholder="Middle Name">
                            </div>
                            <div class="form-group1">
                                <label for="dependent_lname">Last Name</label>
                                <input type="text" id="dependent_lname" name="dependent_lname" placeholder="Last Name">
                            </div>
                            <div class="form-group1">
                                <label for="dependent_bday">Birthdate</label>
                                <input type="date" id="dependent_bday" name="dependent_bday">
                            </div>
                            <div class="form-group1">
                                <label for="remarks">Remarks</label>
                                <textarea id="remarks" name="remarks" placeholder="..."></textarea>
                            </div>
                        </div>
                        
                        <div class="form-buttons">
                            <button type="submit" name="submit" class="btn-next" onclick="goToNextPage()">Next</button>
                        </div>
                    </form>
                </div>
        </main>
    </div>

    <script src="dependent.js"></script>
    <script src="scripts.js"></script>
    <script>
    $(document).ready(function() {
        document.getElementById('account_number').value = 'Generating...';
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