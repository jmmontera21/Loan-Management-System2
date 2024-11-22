window.onload = function () {
    const isFormEmpty = !localStorage.getItem('pensionerFormData');
    // Load data only if there is saved form data, otherwise start with an empty form
    if (!isFormEmpty) {
        loadFormData();
    }
};


function saveFormData() {
    // Pensioner's Part //
    const formData = JSON.parse(localStorage.getItem('pensionerFormData')) || {}; // Get existing data
    formData.account_number = document.getElementById('account_number').value;
    formData.customer_code = document.getElementById('customer_code').value;
    formData.pensioner_fname = document.getElementById('pensioner_fname').value;
    formData.pensioner_mname = document.getElementById('pensioner_mname').value;
    formData.pensioner_lname = document.getElementById('pensioner_lname').value;
    formData.pensioner_bday = document.getElementById('pensioner_bday').value;
    formData.pensioner_cstatus = document.getElementById('pensioner_cstatus').value;
    formData.sex = document.getElementById('sex').value;
    formData.contact_no_pensioner = document.getElementById('contact_no_pensioner').value;
    formData.pensioner_street_no = document.getElementById('pensioner_street_no').value;
    formData.pensioner_barangay = document.getElementById('pensioner_barangay').value;
    formData.pensioner_municipality = document.getElementById('pensioner_municipality').value;
    formData.pensioner_province = document.getElementById('pensioner_province').value;
    formData.zipcode = document.getElementById('zipcode').value;
    formData.spouse_name = document.getElementById('spouse_name').value;
    formData.spouse_bday = document.getElementById('spouse_bday').value;
    formData.spouse_death = document.getElementById('spouse_death').value;

    // Comaker's Form //
    formData.comaker_fname = document.getElementById('comaker_fname').value;
    formData.comaker_mname = document.getElementById('comaker_mname').value;
    formData.comaker_lname = document.getElementById('comaker_lname').value;
    formData.comaker_bday = document.getElementById('comaker_bday').value;
    formData.comaker_cstatus = document.getElementById('comaker_cstatus').value;
    formData.occupation = document.getElementById('occupation').value;
    formData.contact_no_comaker = document.getElementById('contact_no_comaker').value;
    formData.relation_pensioner = document.getElementById('relation_pensioner').value;
    formData.address = {
        comaker_street_no: document.getElementById('comaker_street_no').value,
        comaker_barangay: document.getElementById('comaker_barangay').value,
        comaker_municipality: document.getElementById('comaker_municipality').value,
        comaker_province: document.getElementById('comaker_province').value
    };

    // Dependent's data
    const numDependents = document.getElementById('num-dependents').value;
    formData.dependents = [];
    for (let i = 1; i <= numDependents; i++) {
        const dependent = {
            fname: document.getElementById(`dependent_fname_${i}`).value,
            lname: document.getElementById(`dependent_lname_${i}`).value,
            bday: document.getElementById(`dependent_bday_${i}`).value,
            relation: document.getElementById(`dependent_relation_${i}`).value
        };
        formData.dependents.push(dependent);
    }
    localStorage.setItem('pensionerFormData', JSON.stringify(formData));
}

function loadFormData() {
    const formData = JSON.parse(localStorage.getItem('pensionerFormData'));
    if (formData) {
        // Pensioner's Part //
        document.getElementById('account_number').value = formData.account_number || '';
        document.getElementById('customer_code').value = formData.customer_code || '';
        document.getElementById('pensioner_fname').value = formData.pensioner_fname || '';
        document.getElementById('pensioner_mname').value = formData.pensioner_mname || '';
        document.getElementById('pensioner_lname').value = formData.pensioner_lname || '';
        document.getElementById('pensioner_bday').value = formData.pensioner_bday || '';
        document.getElementById('pensioner_cstatus').value = formData.pensioner_cstatus || '';
        document.getElementById('sex').value = formData.sex || '';
        document.getElementById('contact_no_pensioner').value = formData.contact_no_pensioner || '';
        document.getElementById('pensioner_street_no').value = formData.pensioner_street_no || '';
        document.getElementById('pensioner_barangay').value = formData.pensioner_barangay || '';
        document.getElementById('pensioner_municipality').value = formData.pensioner_municipality || '';
        document.getElementById('pensioner_province').value = formData.pensioner_province || '';
        document.getElementById('zipcode').value = formData.zipcode || '';
        document.getElementById('spouse_name').value = formData.spouse_name || '';
        document.getElementById('spouse_bday').value = formData.spouse_bday || '';
        document.getElementById('spouse_death').value = formData.spouse_death || '';

        // Comaker's Part //
        document.getElementById('account_number').value = formData.account_number || '';
        document.getElementById('customer_code').value = formData.customer_code || '';
        document.getElementById('comaker_fname').value = formData.comaker_fname || '';
        document.getElementById('comaker_mname').value = formData.comaker_mname || '';
        document.getElementById('comaker_lname').value = formData.comaker_lname || '';
        document.getElementById('comaker_bday').value = formData.comaker_bday || '';
        document.getElementById('comaker_cstatus').value = formData.comaker_cstatus || '';
        document.getElementById('occupation').value = formData.occupation || '';
        document.getElementById('contact_no_comaker').value = formData.contact_no_comaker || '';
        document.getElementById('relation_pensioner').value = formData.relation_pensioner || '';
        document.getElementById('comaker_street_no').value = formData.address?.comaker_street_no || '';
        document.getElementById('comaker_barangay').value = formData.address?.comaker_barangay || '';
        document.getElementById('comaker_municipality').value = formData.address?.comaker_municipality || '';
        document.getElementById('comaker_province').value = formData.address?.comaker_province || '';

        // Load dependent data
        if (formData.dependents && formData.dependents.length > 0) {
            document.getElementById('num-dependents').value = formData.dependents.length;
            updateDependents(0); // Adjust the number of dependent forms
            formData.dependents.forEach((dependent, index) => {
                document.getElementById(`dependent_fname_${index + 1}`).value = dependent.fname || '';
                document.getElementById(`dependent_lname_${index + 1}`).value = dependent.lname || '';
                document.getElementById(`dependent_bday_${index + 1}`).value = dependent.bday || '';
                document.getElementById(`dependent_relation_${index + 1}`).value = dependent.relation || '';
            });
        }
    }
}

function goToNextPage() {
    saveFormData(); // Save form data before navigating to the next page
    window.location.href = 'customer_checkpdf.php'; // Redirect to the next page
}

function goToPreviousPage() {
    saveFormData();
    window.location.href = 'customer_form.php'; // Redirect to the previous page
}