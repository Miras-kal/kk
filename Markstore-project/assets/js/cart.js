document.addEventListener('DOMContentLoaded', function() {
    // Санын өзгерту функциясы
    function updateQuantity(productId, quantity) {
        fetch('/cart/update.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartTotals(data.cart);
                updateHeaderCartCount(data.total_items);
            } else {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => console.error('Қате:', error));
    }

    // Тауарды жою функциясы
    function removeItem(productId) {
        fetch('/cart/remove.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector(`tr[data-product-id="${productId}"]`).remove();
                updateCartTotals(data.cart);
                updateHeaderCartCount(data.total_items);
                
                if (data.total_items === 0) {
                    location.reload();
                }
            } else {
                alert(data.message);
            }
        })
        .catch(error => console.error('Қате:', error));
    }

    // Себет құнын жаңарту
    function updateCartTotals(cart) {
        let subtotal = 0;
        
        cart.forEach(item => {
            const row = document.querySelector(`tr[data-product-id="${item.product_id}"]`);
            if (row) {
                const price = parseFloat(item.price);
                const quantity = parseInt(item.quantity);
                const itemTotal = price * quantity;
                
                row.querySelector('.subtotal').textContent = itemTotal.toLocaleString('kz') + ' ₸';
                subtotal += itemTotal;
            }
        });

        document.getElementById('subtotal').textContent = subtotal.toLocaleString('kz') + ' ₸';
        document.getElementById('grand-total').textContent = subtotal.toLocaleString('kz') + ' ₸';
    }

    // Үстіңгі бөлімдегі себет санауышын жаңарту
    function updateHeaderCartCount(count) {
        const cartCount = document.getElementById('cart-count');
        if (cartCount) {
            cartCount.textContent = count;
        }
    }

    // Оқиға тыңдаушыларын қосу
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantity = this.value;
            updateQuantity(productId, quantity);
        });
    });

    document.querySelectorAll('.increase-quantity').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            input.value = parseInt(input.value) + 1;
            updateQuantity(input.dataset.productId, input.value);
        });
    });

    document.querySelectorAll('.decrease-quantity').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                updateQuantity(input.dataset.productId, input.value);
            }
        });
    });

    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Тауарды себеттен шынымен алып тастағыңыз келе ме?')) {
                removeItem(this.dataset.productId);
            }
        });
    });
});