<?php
// Include the database configuration file
include 'db_connect.php';

// Query to fetch all provinces
$query = "SELECT province_id, province_name FROM provinces ORDER BY province_name";
$result = $conn->query($query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $first_name = $_POST['first-name'];
    $middle_name = $_POST['middle-name'];
    $last_name = $_POST['last-name'];
    $birthdate = $_POST['birthdate'];
    $contact_number = $_POST['contact-number'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    
    // Generate customer code by taking the first 4 letters of the last name and first 3 letters of the first name
    $customer_code = strtoupper(substr($last_name, 0, 4)) . strtoupper(substr($first_name, 0, 3));

    // Check if a customer with the same first, middle, and last name already exists
    $check_user_query = "SELECT * FROM customer WHERE firstname = '$first_name' AND middlename = '$middle_name' AND lastname = '$last_name'";
    $check_user_result = $conn->query($check_user_query);

    if ($check_user_result->num_rows > 0) {
        // A user with the same name already exists
        echo "<script>alert('A user with the same name already exists. Please check your details.');</script>";
    } else {
        // Handle profile picture upload
    // Handle profile picture upload
    $profile_picture_path = null; // Default value if no picture is uploaded

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Allowed file extensions
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Define the upload path
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $fileName;

            // Move the file to the destination directory
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile_picture_path = $dest_path; // Save the file path for database insertion
            } else {
                echo "<script>alert('There was an error moving the file to the upload directory.');</script>";
            }
        } else {
            echo "<script>alert('Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions) . "');</script>";
        }
    }

        // Insert into customer table with the generated customer code and profile picture path
        $sql_customer = "INSERT INTO customer (firstname, middlename, lastname, birthdate, contact_no, email, password, date_created, customer_code, profile_picture)
                         VALUES ('$first_name', '$middle_name', '$last_name', '$birthdate', '$contact_number', '$email', '$password', NOW(), '$customer_code', '$profile_picture_path')";

        if ($conn->query($sql_customer) === TRUE) {
            $customer_id = $conn->insert_id; // Get the newly inserted customer ID

            // Retrieve address details from the form
            $province = $_POST['province'];
            $city = $_POST['city'];
            $barangay = $_POST['barangay'];

            // Fetch latitude and longitude for the selected barangay
            $sql_barangay = "SELECT latitude, longitude FROM barangays WHERE barangay_id = '$barangay'";
            $result_barangay = $conn->query($sql_barangay);

            if ($result_barangay->num_rows > 0) {
                $row = $result_barangay->fetch_assoc();
                $latitude = $row['latitude'];
                $longitude = $row['longitude'];

                // Insert into customer_address table
                $sql_address = "INSERT INTO customer_address (customer_id, province, city, barangay, latitude, longitude)
                                VALUES ('$customer_id', '$province', '$city', '$barangay', '$latitude', '$longitude')";

                if ($conn->query($sql_address) === TRUE) {
                    // Success message and redirect to login page
                    echo "<script>
                            alert('Account successfully created!');
                            window.location.href = 'customer_login.php';
                          </script>";
                    exit();
                } else {
                    echo "Error: " . $sql_address . "<br>" . $conn->error;
                }
            }
        } else {
            echo "Error: " . $sql_customer . "<br>" . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Signup</title>
    <link rel="stylesheet" href="css/customer(1).css">
    <style>
    /* Modal styles */
    .modal1 {
        display: none; 
        position: fixed;
        z-index: 1;
        padding-top: 100px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.4);
    }

    .modal-content1 {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 600px;
        max-height: 80vh; /* Limit modal height */
        overflow-y: auto;  /* Scroll within modal */
    }

    .modal-body1, .modal-footer1 {
        padding: 10px;
    }

    .modal-footer1 {
        text-align: right;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* Button styles */
    #agreeBtn {
        background-color: green;
        color: white;
        padding: 10px 20px;
        border: none;
        cursor: pointer;
    }
    </style>
</head>
<body>
    <header>
    <nav>
        <div class="logo">
            <a href="homepage.php"><img src="img/LMS logo.png" alt="Logo"></a>
        </div>
    </nav>
    </header>

    <div class="signup-container">
        <h1>Achieve your goals!</h1>
        <p>Let us start by creating an account</p>

        <form class="signup-form" action="customer_signup.php" method="POST" enctype="multipart/form-data">
            <!-- Two columns for personal details -->
            <div class="form-row">
                <div class="form-group">
                    <label for="first-name">First Name</label>
                    <input type="text" id="first-name" name="first-name" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <label for="middle-name">Middle Name</label>
                    <input type="text" id="middle-name" name="middle-name" placeholder="Middle Name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="last-name">Last Name</label>
                    <input type="text" id="last-name" name="last-name" placeholder="Last Name" required>
                </div>
                <div class="form-group">
                    <label for="birthdate">Birthdate</label>
                    <input type="date" id="birthdate" name="birthdate" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="contact-number">Contact Number</label>
                    <input type="text" id="contact-number" name="contact-number" placeholder="Contact Number" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="province">Province</label>
                    <select id="province" name="province" required>
                        <option value="">Select Province</option>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row['province_id'] . '">' . $row['province_name'] . '</option>';
                            }
                        } else {
                            echo '<option value="">No provinces available</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="city">Municipality / City</label>
                    <select id="city" name="city" disabled required>
                        <option value="">Select Municipality / City</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="barangay">Barangay</label>
                    <select id="barangay" name="barangay" disabled required>
                        <option value="">Select Barangay</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="email@example.com" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="8-12 characters" minlength="8" maxlength="12" required>
                </div>
                <!-- <div class="form-group">
                    <label for="confirm-password">Retry Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="8-12 characters" minlength="8" maxlength="12" required>
                </div> -->
            </div>
                
                <div class="form-group">
                    <label for="profile_picture">Upload Profile Picture:</label>
                    <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*" capture="camera" required>
                </div><br>

                <!-- <div>
                    <input type="checkbox" id="terms" name="terms" disabled>
                    <label for="terms">
                        I agree to the <a href="#" id="openModal">Privacy Policy and Software Services Agreement</a>
                    </label>
                </div> -->

                <button type="submit" class="btn-create-account" id="createAccountBtn">Create Account</button>

                <p class="login-link">Already have an account? <a href="customer_login.php">Log In</a></p>
            </div>

                <!-- The Modal -->
                <div id="policyModal" class="modal1" style="text-align:left">
                    <div class="modal-content1">
                        <!-- <div class="modal-header">
                            <h2>Privacy Policy & Software Services Agreement</h2>
                            <span class="close">&times;</span>
                        </div> -->
                        <div class="modal-body1" id="modalBody">
                            <h2>Privacy Policy for Loan Management System</h2>
                            <p><b>Effective Date:</b> October 1, 2024 <br><br>
                            We value your privacy and are committed to protecting the personal information you share with us. 
                            This privacy policy outlines how we collect, use, share, and protect your personal information when you use our Loan Management System (the “System”).<br>
                            <h2>1. Information We Collect</h2>
                            <b>a. Personal Information</b><br><br>
                            When you use our System, we may collect personal information that you voluntarily provide to us, including but not limited to:<br>
                            <ul>
                                <li>Full name</li>
                                <li>Date of birth</li>
                                <li>Address</li>
                                <li>Contact details (phone number, email address)</li>
                                <li>Employment information</li>
                                <li>Financial information (salary, assets, liabilities)</li>
                                <li>Loan details (loan amount, repayment history, etc.)</li>
                            </ul>
                            <b>b. Automatically Collected Information</b><br><br>
                            When accessing the System, we may automatically collect information about your device, browser, and usage of the system. This includes:<br>
                            <ul>
                                <li>IP address</li>
                                <li>Browser type and version</li>
                                <li>Pages visited within the system</li>
                                <li>Date and time of access</li>
                            </ul>
                            <h2>2. How We Use Your Information</h2>
                            We use the personal information we collect for the following purposes:<br>
                            <ul>
                                <li>To process and manage loan applications</li>
                                <li>To verify customer identity and perform credit checks</li>
                                <li>To communicate with you about your loan and provide customer support</li>
                                <li>To comply with legal and regulatory requirements</li>
                                <li>To improve our services and maintain the functionality of the System</li>
                                <li>To prevent fraud and secure our System</li>
                            </ul>
                            <h2>3. Sharing Your Information</h2>
                            We do not sell, trade, or otherwise transfer your personal information to outside parties, except in the following circumstances:<br>
                            <ul>
                                <li><b>With Service Providers:</b> We may share your information with trusted third-party service providers who assist us in operating the system, conducting business, or servicing your loan.</li>
                                <li><b>For Legal Requirements:</b> We may disclose your information when required by law, in response to a legal process, or to protect our rights, property, and safety or that of others.</li>
                                <li><b>With Your Consent:</b> We may share your information with your explicit consent for specific purposes.</li>
                            </ul>
                            <h2>4. Data Security</h2>
                            We take data security seriously and have implemented appropriate technical and organizational measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction. This includes:<br>
                            <ul>
                                <li>Encryption of sensitive data</li>
                                <li>Secure access controls</li>
                                <li>Regular security audits and monitoring</li>
                            </ul>
                            However, no system can be completely secure, and we cannot guarantee the absolute security of your information.
                            <h2>5. Retention of Information</h2>
                            We retain your personal information only for as long as necessary to fulfill the purposes for which it was collected or to comply with legal obligations. Once the information is no longer required, we will securely dispose of it.
                            <h2>6. Your Rights</h2>
                            You have the following rights concerning your personal information:
                            <ul>
                                <li><b>Access:</b> You can request access to the personal information we hold about you.</li>
                                <li><b>Correction:</b> You can request corrections to any inaccurate or incomplete information.</li>
                                <li><b>Deletion:</b> You can request the deletion of your personal information, subject to legal and contractual limitations.</li>
                                <li><b>Restriction:</b> You can request that we restrict the processing of your personal information.</li>
                                <li><b>Portability:</b> You can request that we transfer your personal information to another entity, where feasible.</li>
                            </ul>
                            To exercise these rights, please contact us using the contact information provided below.
                            <h2>7. Changes to the Privacy Policy</h2>
                            We may update this privacy policy from time to time to reflect changes in our practices or applicable laws. 
                            Any changes will be posted on this page with an updated effective date. We encourage you to review this policy periodically.
                            <h2>8. Contact Us</h2>
                            If you have any questions or concerns about this privacy policy or how we handle your personal information, please contact us at:<br><br>
                            <b>Quezon APC Lending Company, Inc.</b><br>
                            2nd Floor JC Roces Bldg. C.M. Recto St, Brgy. 4, Lucena City, 4301 Quezon<br>
                            09338190272<br><br>
                            ______________________________________________________________<br><br>
                            By using the Loan Management System, you acknowledge that you have read and understood this privacy policy.
                            </p><br><br><br><br>

                            <h2>User Agreement for Loan Management System</h2>
                            <p><b>Effective Date:</b> October 1, 2024 <br><br>
                            This User Agreement ("Agreement") is a legally binding contract between you ("User") and 
                            [Company Name] ("Company") governing your access to and use of the Loan Management System ("System").<br><br>
                            By accessing or using the System, you agree to be bound by the terms and conditions outlined in this 
                            Agreement. If you do not agree to these terms, you must immediately cease using the System.
                            <h2>1. Eligibility</h2>
                            To use the System, you must be at least 18 years of age or the age of majority in your jurisdiction 
                            and have the legal capacity to enter into this Agreement. By using the System, you represent and warrant that you meet these eligibility requirements.
                            <h2>2. Account Registration</h2>
                            To access certain features of the System, you may be required to create an account. You agree to:
                            <ul>
                                <li>Provide accurate, current, and complete information during the registration process.</li>
                                <li>Maintain the security of your account credentials and promptly notify the Company of any unauthorized use of your account.</li>
                                <li>Be responsible for all activities that occur under your account.</li>
                            </ul>
                            <h2>3. Use of the System</h2>
                            The Company grants you a limited, non-exclusive, non-transferable, and revocable license to access and use the System for the sole purpose of managing loans, provided you comply with this Agreement.<br><br>
                            You agree not to:
                            <ul>
                                <li>Use the System for any illegal or unauthorized purpose.</li>
                                <li>Interfere with or disrupt the operation of the System, including its servers and networks.</li>
                                <li>Attempt to gain unauthorized access to any portion of the System or any other accounts, systems, or networks.</li>
                            </ul>
                            <h2>4. User Content</h2>
                            You are solely responsible for any data, information, or content that you upload, submit, or store 
                            on the System ("User Content"). You grant the Company a worldwide, royalty-free, non-exclusive 
                            license to use, store, and process your User Content solely for the purpose of providing the services
                            in the System.<br><br>
                            You represent and warrant that:
                            <ul>
                                <li>You have all necessary rights to submit User Content to the System.</li>
                                <li>Your User Content does not infringe upon the rights of any third party, including intellectual property rights or privacy rights.</li>
                            </ul>
                            <h2>5. Data Privacy</h2>
                            The Company values your privacy and will handle your personal information in accordance with our 
                            <b>Privacy Policy</b>. By using the System, you consent to the collection, use, and disclosure of your 
                            personal information as outlined in the Privacy Policy.
                            <h2>6. Fees</h2>
                            Certain features or services of the System may require payment of fees. If you choose to access 
                            paid services, you agree to pay all applicable fees as outlined at the time of payment. Failure 
                            to pay any applicable fees may result in the suspension or termination of your access to the System.
                            <h2>7. Intellectual Property</h2>
                            All intellectual property rights in the System, including but not limited to software, trademarks, 
                            and logos, are owned by or licensed to the Company. You agree not to:
                            <ul>
                                <li>Copy, modify, distribute, or create derivative works of the System.</li>
                                <li>Use the Company's trademarks or logos without prior written permission.</li>
                            </ul>
                            <h2>8. Termination</h2>
                            The Company may, in its sole discretion, suspend or terminate your access to the System at any time, with or 
                            without cause or notice. Upon termination, your right to access and use the System will immediately cease.<br><br>
                            You may terminate this Agreement by discontinuing your use of the System and closing your account. Any provisions 
                            of this Agreement that, by their nature, should survive termination (e.g., indemnification, limitations of liability) shall survive.
                            <h2>9. Disclaimers</h2>
                            <ul>
                                <li><b>No Warranty</b>: The System is provided "as-is" and "as available," without any warranty of any kind. The Company makes no warranties, express or implied, regarding the System, including but not limited to its accuracy, completeness, reliability, or fitness for a particular purpose.</li>
                                <li><b>No Guarantee of Availability</b>: The Company does not guarantee that the System will be available at all times or without interruption. Maintenance, upgrades, or unforeseen circumstances may result in temporary service outages.</li>
                            </ul>
                            <h2>10. Limitation of Liability</h2>
                            To the maximum extent permitted by law:
                            <ul>
                                <li>The Company shall not be liable for any indirect, incidental, special, consequential, or 
                                punitive damages arising out of or related to your use of the System, even if advised of the possibility of such damages.</li>
                                <li>The Company's total liability to you for any claims arising from or related to your use of 
                                the System shall not exceed the amount you paid to the Company for accessing the System in the 12 months preceding the claim.</li>
                            </ul>
                            <h2>11. Indemnification</h2>
                            You agree to indemnify, defend, and hold harmless the Company, its affiliates, and its directors, 
                            officers, employees, and agents from and against any claims, liabilities, damages, losses, and 
                            expenses (including reasonable legal fees) arising out of or related to your use of the System or breach of this Agreement.
                            <h2>12. Modifications to the Agreement</h2>
                            The Company reserves the right to modify this Agreement at any time. If we make material changes, 
                            we will provide notice by posting the updated Agreement on the System or through other communication. 
                            Your continued use of the System after the effective date of the updated Agreement constitutes your acceptance of the modified terms.
                            <h2>13. Governing Law and Dispute Resolution</h2>
                            <ul>
                                <li><b>Governing Law</b>: This Agreement shall be governed by and construed in accordance with the laws of 
                                [Jurisdiction], without regard to its conflict of laws principles.</li>
                                <li><b>Dispute Resolution</b>: Any disputes arising out of or related to this Agreement shall be resolved 
                                through [negotiation/mediation/arbitration] in [Location]. If not resolved, disputes will be brought before the courts of [Jurisdiction].</li>
                            </ul>
                            <h2>14. Miscellaneous</h2>
                            <ul>
                                <li><b>Entire Agreement</b>: This Agreement constitutes the entire understanding between you and the Company regarding your use of the System.</li>
                                <li><b>Assignment</b>: You may not assign or transfer this Agreement or any rights or obligations hereunder without the prior written consent of the Company.</li>
                                <li><b>Severability</b>: If any provision of this Agreement is found to be invalid or unenforceable, the remaining provisions will remain in full force and effect.</li>
                                <li><b>Waiver</b>: The failure of the Company to enforce any right or provision of this Agreement will not be deemed a waiver of such right or provision.</li>
                            </ul>
                            ______________________________________________________________<br><br>
                            By using the Loan Management System, you acknowledge that you have read, understood, and agreed to be bound by this User Agreement.<br><br>
                            <b>Quezon APC Lending Company, Inc.</b><br>
                            2nd Floor JC Roces Bldg. C.M. Recto St, Brgy. 4, Lucena City, 4301 Quezon
                            09338190272<br><br>
                            ______________________________________________________________<br><br>
                            This User Agreement sets the terms for users accessing your Loan Management System, covering key aspects like user responsibilities, data privacy, intellectual property, and legal protections for both parties.
                            </p>
                        </div>
                        <div class="modal-footer1">
                            <button id="agreeBtn">I Agree</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="signup.js"></script>
</body>
</html>

