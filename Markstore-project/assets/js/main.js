// Себет санауышын жаңарту
function updateCartCount() {
    fetch('/cart/count.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('cart-count').textContent = data.count;
        });
}

// Тауарды себетке қосу
function addToCart(productId) {
    fetch('/cart/add.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Тауар себетке қосылды!');
            updateCartCount();
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Қате:', error));
}

// Документ жүктелгенде
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    
    // Себетке қосу батырмаларына тыңдаушы қосу
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            addToCart(productId);
        });
    });
    
    // Хабарлау батырмаларына тыңдаушы қосу
    document.querySelectorAll('.notify-me').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            alert('Бұл тауар қоймаға түскенде хабарлаймыз!');
        });
    });
});