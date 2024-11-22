function updateDependents(change) {
    const numDependents = document.getElementById('num-dependents');
    let count = parseInt(numDependents.value) + change;
    if (count < 1) count = 1;
    if (count > 5) count = 5;
    numDependents.value = count;

    const dependentsContainer = document.getElementById('dependents-container');
    dependentsContainer.innerHTML = '';

    for (let i = 1; i <= count; i++) {
        dependentsContainer.innerHTML += `
            <h3>Dependent No.${i}</h3>
            <div class="form-group1">
                <label for="dependent_fname_${i}">First Name</label>
                <input type="text" id="dependent_fname_${i}" name="dependent_fname[]" placeholder="First Name">
            </div>
            <div class="form-group1">
                <label for="dependent_mname_${i}">Middle Name</label>
                <input type="text" id="dependent_mname_${i}" name="dependent_mname[]" placeholder="Middle Name">
            </div>
            <div class="form-group1">
                <label for="dependent_lname_${i}">Last Name</label>
                <input type="text" id="dependent_lname_${i}" name="dependent_lname[]" placeholder="Last Name">
            </div>
            <div class="form-group1">
                <label for="dependent_bday_${i}">Birthdate</label>
                <input type="date" id="dependent_bday_${i}" name="dependent_bday[]">
            </div>
            <div class="form-group1">
                <label for="remarks_${i}">Remarks</label>
                <textarea id="remarks_${i}" name="remarks[]" placeholder="..."></textarea>
            </div>`;
    }
}
