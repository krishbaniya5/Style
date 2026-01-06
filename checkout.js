function checkoutCOD(item){
    const data = {
        product_name: item.name,
        product_price: item.price,
        size: item.size,
        quantity: item.qty,
        total: item.price * item.qty,
        shipping_name: document.getElementById('ship_name').value,
        shipping_address: document.getElementById('ship_address').value,
        payment_method: 'COD'
    };

    fetch('check_out.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        if(res.status==='success'){
            alert('Order placed! Order ID: ' + res.order_id);
            window.location='user.php';
        } else {
            alert('Error: ' + res.message);
        }
    });
}
