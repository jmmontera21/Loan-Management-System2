$(document).ready(function(){
    // Handle province selection
    $('#province').change(function(){
        var province_id = $(this).val();
        if(province_id){
            $.ajax({
                type: 'POST',
                url: 'get_cities.php',
                data: {province_id: province_id},
                success: function(html){
                    $('#city').html(html);
                    $('#city').prop('disabled', false);
                    $('#barangay').html('<option value="">Select Barangay</option>').prop('disabled', true);
                }
            });
        }else{
            $('#city').html('<option value="">Select Municipality / City</option>').prop('disabled', true);
            $('#barangay').html('<option value="">Select Barangay</option>').prop('disabled', true);
        }
    });

    // Handle city selection
    $('#city').change(function(){
        var city_id = $(this).val();
        if(city_id){
            $.ajax({
                type: 'POST',
                url: 'get_barangays.php',
                data: {city_id: city_id},
                success: function(html){
                    $('#barangay').html(html);
                    $('#barangay').prop('disabled', false);
                }
            });
        }else{
            $('#barangay').html('<option value="">Select Barangay</option>').prop('disabled', true);
        }
    });

    // Password confirmation check
    $('form').submit(function(event) {
        var password = $('#password').val();
        var confirmPassword = $('#confirm-password').val();

        // if (password !== confirmPassword) {
        //     alert('Passwords do not match.');
        //     event.preventDefault(); // Prevent form submission if passwords don't match
        // }

        // // Ensure terms and conditions checkbox is checked
        // if (!$('#terms').is(':checked')) {
        //     alert('You must agree to the terms and conditions.');
        //     event.preventDefault(); // Prevent form submission
        // }
    });

    // Modal logic
    handleModal();
});

function handleModal() {
    // Get modal elements
    const modal = document.getElementById('policyModal');
    const openModalBtn = document.getElementById('openModal');
    const closeModalBtn = document.querySelector('.close');
    const agreeBtn = document.getElementById('agreeBtn');
    const termsCheckbox = document.getElementById('terms');
    const createAccountBtn = document.getElementById('createAccountBtn');

    // Open modal when Privacy Policy link is clicked
    openModalBtn.addEventListener('click', function() {
        modal.style.display = 'block';
    });

    // Enable checkbox and close modal when 'I Agree' button is clicked
    agreeBtn.addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default button behavior
        termsCheckbox.disabled = false;  // Enable checkbox
        termsCheckbox.checked = true;    // Check the checkbox
        createAccountBtn.disabled = false; // Enable 'Create Account' button
        modal.style.display = 'none'; // Close modal
    });
}
