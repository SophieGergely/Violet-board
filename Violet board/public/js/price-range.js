document.addEventListener('DOMContentLoaded', () => {
    const slider = document.querySelector('.price-range-slider');
    if (!slider) return;

    const minInput = slider.querySelector('.price-range-input-min');
    const maxInput = slider.querySelector('.price-range-input-max');
    const display = document.getElementById('priceRangeDisplay');
    const minHidden = document.getElementById('minPriceHidden');
    const maxHidden = document.getElementById('maxPriceHidden');

    const boundsMin = parseFloat(slider.dataset.min);
    const boundsMax = parseFloat(slider.dataset.max);

    function update() {
        const minVal = parseFloat(minInput.value);
        const maxVal = parseFloat(maxInput.value);

        if (display) {
            display.textContent = Math.round(minVal) + ' € – ' + Math.round(maxVal) + ' €';
        }
        if (minHidden) minHidden.value = Math.round(minVal);
        if (maxHidden) maxHidden.value = Math.round(maxVal);
    }

    minInput.addEventListener('input', () => {
        if (parseFloat(minInput.value) > parseFloat(maxInput.value)) {
            minInput.value = maxInput.value;
        }
        update();
    });

    maxInput.addEventListener('input', () => {
        if (parseFloat(maxInput.value) < parseFloat(minInput.value)) {
            maxInput.value = minInput.value;
        }
        update();
    });

    update();

    document.querySelectorAll('.filter-field-clear').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (btn.dataset.clear === 'price') {
                minInput.value = boundsMin;
                maxInput.value = boundsMax;
                update();
            } else if (btn.dataset.clearInput) {
                const target = document.querySelector('[name="' + btn.dataset.clearInput + '"]');
                if (target) target.value = '';
            }
        });
    });
});
