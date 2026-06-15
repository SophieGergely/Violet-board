document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#shipping-form');
    const inputs = form.querySelectorAll('input, select');
    const nextButton = document.getElementById('next-button');

    nextButton.disabled = true;

    function validateForm() {
        let isValid = true;

        inputs.forEach(input => {
            const pattern = input.getAttribute('pattern');
            const value = input.value.trim();

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
});
