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
    <title>Pensioner's Information Form</title>
    <link rel="stylesheet" href="css/dashboard.css">
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
                    <li class="active"><a href="customer_loan.php">Apply Loan</a></li>
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

            <div class="container1">
                <div class="progress-bar">
                    <!-- You can replace these with actual steps if needed -->
                    <a><div class="step">1</div></a>
                    <a><div class="step">2</div></a>
                    <a href="customer_picture.php"><div class="step active">3</div></a>
                </div>

                <div class="selfie-container">
                    <h2>Take a picture of your ID front and back.</h2>
                    <h3 style="text-align:left; color: #2c3e50;">Front</h3>

                    <div class="content">
                        <div class="upload-box">
                            <label for="upload-front-image">
                                <div class="upload-placeholder">
                                    <i class="camera-icon"></i>
                                    <img id="frontSelfiePreview" style="display:none;" />
                                </div>
                            </label>
                            <input type="file" id="upload-front-image" accept="image/*" capture="environment" style="display:none;" onchange="previewImage(event, 'front')" required>
                        </div>

                        <div class="example">
                            <img src="img/homepage1.jpg" alt="Example Image">
                        </div>
                    </div>

                    <div style="text-align:left;">
                        <input type="file" class="form-control" id="front-picture" name="front-picture" accept="image/*" capture="camera" required>
                    </div><br>

                    <h3 style="text-align:left; color: #2c3e50;">Back</h3>
                    <div class="content">
                        <div class="upload-box">
                            <label for="upload-back-image">
                                <div class="upload-placeholder">
                                    <i class="camera-icon"></i>
                                    <img id="backSelfiePreview" style="display:none;" />
                                </div>
                            </label>
                            <input type="file" id="upload-back-image" accept="image/*" capture="environment" style="display:none;" onchange="previewImage(event, 'back')" required>
                        </div>

                        <div class="example">
                            <img src="img/homepage1.jpg" alt="Example Image">
                        </div>
                    </div>

                    <div style="text-align:left;">
                        <input type="file" class="form-control" id="back-picture" name="back-picture" accept="image/*" capture="camera" required>
                    </div><br>

                    <h2>Take a picture while holding your ID.</h2>
                    <div class="content">
                        <div class="upload-box">
                            <label for="upload-selfie-image">
                                <div class="upload-placeholder">
                                    <i class="camera-icon"></i>
                                    <img id="selfiePreview" style="display:none;" />
                                </div>
                            </label>
                            <input type="file" id="upload-selfie-image" accept="image/*" capture="environment" style="display:none;" onchange="previewImage(event, 'selfie')" required>
                        </div>

                        <div class="example">
                            <img src="img/homepage1.jpg" alt="Example Image">
                        </div>
                    </div>

                    <div style="text-align:left;">
                        <input type="file" class="form-control" id="selfie-picture" name="selfie-picture" accept="image/*" capture="camera" required>
                    </div><br>

                    <div class="buttons">
                        <button class="prev-button" onclick="goToPreviousPage()">Previous</button>
                        <button class="submit-button" onclick="validateForm(event)" disabled>Submit</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function goToPreviousPage() {
            window.location.href = 'customer_checkpdf.php';
        }

        function previewImage(event, type) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (type === 'front') {
                        document.getElementById('frontSelfiePreview').style.display = 'block';
                        document.getElementById('frontSelfiePreview').src = e.target.result;
                    } else if (type === 'back') {
                        document.getElementById('backSelfiePreview').style.display = 'block';
                        document.getElementById('backSelfiePreview').src = e.target.result;
                    } else if (type === 'selfie') {
                        document.getElementById('selfiePreview').style.display = 'block';
                        document.getElementById('selfiePreview').src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
            checkImages();  // Check if all images are uploaded after each file selection
        }

        function checkImages() {
            let submitButton = document.querySelector('.submit-button');
            let frontPicture = document.getElementById('front-picture');
            let backPicture = document.getElementById('back-picture');
            let selfiePicture = document.getElementById('selfie-picture');

            if (frontPicture.files.length > 0 && backPicture.files.length > 0 && selfiePicture.files.length > 0) {
                submitButton.disabled = false;  // Enable the submit button if all images are uploaded
            } else {
                submitButton.disabled = true;   // Disable the submit button if any image is missing
            }
        }

        function validateForm(event) {
            event.preventDefault();  // Prevent form submission

            let frontPicture = document.getElementById('front-picture');
            let backPicture = document.getElementById('back-picture');
            let selfiePicture = document.getElementById('selfie-picture');

            if (!frontPicture.files.length) {
                alert('Please upload the front picture of your ID.');
                frontPicture.scrollIntoView({ behavior: 'smooth' });
            } else if (!backPicture.files.length) {
                alert('Please upload the back picture of your ID.');
                backPicture.scrollIntoView({ behavior: 'smooth' });
            } else if (!selfiePicture.files.length) {
                alert('Please upload the selfie holding your ID.');
                selfiePicture.scrollIntoView({ behavior: 'smooth' });
            } else {
                // If all images are uploaded, proceed with form submission
                window.location.href = 'customer_success.php';  // Or replace with the actual submission logic
            }
        }

        // Initialize the check when the page loads
        checkImages();
    </script>
</body>
</html>
