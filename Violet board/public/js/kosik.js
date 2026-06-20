function checkCart() {
    const cart = window.cartData || {};
    let total = 0;

    for (const id in cart) {
        const item = cart[id];
        total += item.price * item.quantity;
    }

    if (Object.keys(cart).length === 0 || total === 0) {
        // Use the theme-matching, dismissible notification instead of the
        // native browser alert (defined inline in kosik.blade.php).
        if (typeof window.showThemedAlert === 'function') {
            window.showThemedAlert('Košík je prázdny.');
        } else {
            alert('Košík je prázdny.');
        }
        return;
    }

    window.location.href = '/boxcollect';
}
