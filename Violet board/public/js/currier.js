document.addEventListener('DOMContentLoaded', () => {
    const form       = document.querySelector('#shipping-form');
    const inputs     = form.querySelectorAll('input, select');
    const nextButton = document.getElementById('next-button');

    nextButton.disabled = true;

    function validateForm() {
        let isValid = true;

        inputs.forEach(input => {
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

        nextButton.disabled = !isValid;
        return isValid;
    }

    inputs.forEach(input => {
        input.addEventListener('input', () => {
            input.classList.remove('is-invalid', 'is-valid');
            validateForm();
        });
        input.addEventListener('change', () => {
            validateForm();
        });
    });

    // Run on load so pre-filled (logged-in) values enable the button immediately
    validateForm();

    // Navigate to checkout, passing all delivery fields as query params
    nextButton.addEventListener('click', () => {
        if (!validateForm()) return;

        const params = new URLSearchParams({
            first_name: (form.querySelector('[name="first_name"]')?.value ?? '').trim(),
            last_name:  (form.querySelector('[name="last_name"]')?.value ?? '').trim(),
            email:      (form.querySelector('[name="email"]')?.value ?? '').trim(),
            phone:      (form.querySelector('[name="phone"]')?.value ?? '').trim(),
            street:     (document.getElementById('street')?.value ?? '').trim(),
            city:       (document.getElementById('city')?.value ?? '').trim(),
            state:      (document.getElementById('state')?.value ?? '').trim(),
        });

        window.location.href = '/checkout?' + params.toString();
    });
});
