<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pensioner's Information Form</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .modal-header {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .modal-body {
            max-height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .modal-footer button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-cancel {
            background-color: #f44336;
            color: white;
        }
        .btn-confirm {
            background-color: #4caf50;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Your form content -->
        <form id="pensionerForm" class="pensioner-form" method="POST">
            <!-- Include all your form fields here -->
            <div class="form-group1">
                <label for="pensioner_fname">First Name</label>
                <input type="text" id="pensioner_fname" name="pensioner_fname" placeholder="First Name" required>
            </div>
            <div class="form-group1">
                <label for="pensioner_lname">Last Name</label>
                <input type="text" id="pensioner_lname" name="pensioner_lname" placeholder="Last Name" required>
            </div>
            <div class="form-group1">
                <label for="contact_no_pensioner">Contact Number</label>
                <input type="tel" id="contact_no_pensioner" name="contact_no_pensioner" placeholder="Contact Number" required>
            </div>
            <!-- Add other fields as needed -->

            <div class="form-buttons">
                <button type="button" class="btn-next" onclick="showConfirmation()">Next</button>
            </div>
        </form>

        <!-- Confirmation Modal -->
        <div id="confirmationModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">Confirm Your Information</div>
                <div class="modal-body" id="modalContent"></div>
                <div class="modal-footer">
                    <button class="btn-cancel" onclick="closeModal()">Cancel</button>
                    <button class="btn-confirm" onclick="submitForm()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to show confirmation modal
        function showConfirmation() {
            const formData = new FormData(document.getElementById('pensionerForm'));
            let modalContent = '<ul>';

            // Loop through form data and generate a summary
            for (const [key, value] of formData.entries()) {
                if (value.trim()) {
                    const fieldLabel = document.querySelector(`label[for=${key}]`)?.innerText || key;
                    modalContent += `<li><strong>${fieldLabel}:</strong> ${value}</li>`;
                }
            }
            modalContent += '</ul>';

            document.getElementById('modalContent').innerHTML = modalContent;
            document.getElementById('confirmationModal').style.display = 'flex';
        }

        // Close the modal
        function closeModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }

        // Submit the form
        function submitForm() {
            document.getElementById('pensionerForm').submit();
        }
    </script>
</body>
</html>
