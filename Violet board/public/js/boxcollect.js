document.addEventListener('DOMContentLoaded', function () {
    const form      = document.getElementById('shipping-form');
    const submitBtn = document.getElementById('submit-btn');
    const inputs    = form.querySelectorAll('input, select');

    submitBtn.disabled = true;

    function validateForm() {
        let isValid = true;

        inputs.forEach(input => {
            // Skip hidden inputs – they are populated automatically
            if (input.type === 'hidden') return;

            const pattern = input.getAttribute('pattern');
            const value   = input.value.trim();

            if (input.required && value === '') {
                isValid = false;
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
            } else if (pattern && !(new RegExp(pattern).test(value))) {
                isValid = false;
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
            } else {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            }
        });

        submitBtn.disabled = !isValid;
        return isValid;
    }

    // When a box location is selected, populate the hidden street/city fields
    const boxSelect = form.querySelector('[name="box_location"]');
    const boxStreet = document.getElementById('box-street');
    const boxCity   = document.getElementById('box-city');

    if (boxSelect && boxStreet && boxCity) {
        boxSelect.addEventListener('change', () => {
            const opt = boxSelect.options[boxSelect.selectedIndex];
            boxStreet.value = opt.dataset.street ?? '';
            boxCity.value   = opt.dataset.city   ?? '';
            validateForm();
        });
    }

    inputs.forEach(input => {
        if (input.type === 'hidden') return;
        input.addEventListener('input',  () => { input.classList.remove('is-invalid', 'is-valid'); validateForm(); });
        input.addEventListener('change', () => { validateForm(); });
    });

    // Run on load so pre-filled values enable the button immediately
    validateForm();
});
