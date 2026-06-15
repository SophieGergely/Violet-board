document.addEventListener('DOMContentLoaded', () => {
    const radioCard   = document.getElementById('radio-card');
    const radioCash   = document.getElementById('radio-cash');
    const optionCard  = document.getElementById('option-card');
    const optionCash  = document.getElementById('option-cash');
    const cardFields  = document.getElementById('card-fields');
    const cardNumber  = document.getElementById('card-number');
    const cardExpiry  = document.getElementById('card-expiry');
    const cardCvc     = document.getElementById('card-cvc');
    const submitBtn   = document.getElementById('submit-btn');
    const modal       = document.getElementById('dakujemeModal');
    const form        = document.getElementById('paymentForm');

    let currentMethod = 'card';

    window.selectPayment = function (option) {
        currentMethod = option;

        radioCard.classList.remove('selected');
        radioCash.classList.remove('selected');
        optionCard.classList.remove('active');
        optionCash.classList.remove('active');

        document.getElementById(`radio-${option}`).classList.add('selected');
        document.getElementById(`option-${option}`).classList.add('active');

        const isCard = option === 'card';
        cardFields.style.display = isCard ? 'block' : 'none';

        [cardNumber, cardExpiry, cardCvc].forEach(input => {
            input.disabled = !isCard;
            input.classList.remove('is-invalid', 'is-valid');
        });
    };

    function showError(input, errId, msg) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        document.getElementById(errId).textContent = msg;
    }

    function clearError(input) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
    }

    function validateCard() {
        let valid = true;

        const cardNumClean = cardNumber.value.replace(/\s+/g, '');
        if (!/^\d{16}$/.test(cardNumClean)) {
            showError(cardNumber, 'err-number', 'Číslo karty musí mať presne 16 číslic.');
            valid = false;
        } else {
            clearError(cardNumber);
        }

        const expiryRegex = /^(0[1-9]|1[0-2])\/\d{2}$/;
        if (!expiryRegex.test(cardExpiry.value)) {
            showError(cardExpiry, 'err-expiry', 'Formát dátumu: MM/RR (napr. 05/27)');
            valid = false;
        } else {
            const [mm, yy] = cardExpiry.value.split('/').map(Number);
            if (new Date(2000 + yy, mm - 1) < new Date()) {
                showError(cardExpiry, 'err-expiry', 'Platnosť karty vypršala.');
                valid = false;
            } else {
                clearError(cardExpiry);
            }
        }

        if (!/^\d{3}$/.test(cardCvc.value)) {
            showError(cardCvc, 'err-cvc', 'CVC musí mať presne 3 číslice.');
            valid = false;
        } else {
            clearError(cardCvc);
        }

        return valid;
    }

    cardNumber.addEventListener('input', () => {
        let value = cardNumber.value.replace(/\D/g, '');
        value = value.replace(/(.{4})(?=.)/g, '$1 ');
        cardNumber.value = value;
        cardNumber.classList.remove('is-invalid', 'is-valid');
    });

    [cardExpiry, cardCvc].forEach(input => {
        input.addEventListener('input', () => input.classList.remove('is-invalid', 'is-valid'));
    });

    submitBtn.addEventListener('click', (e) => {
        e.preventDefault();

        if (currentMethod === 'card' && !validateCard()) return;

        modal.style.display = 'flex';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: new FormData(form)
        });
    });
});
