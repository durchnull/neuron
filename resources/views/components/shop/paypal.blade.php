@props(['data' => []])
<div id="paypal-button-container"></div>
<script>
    paypal.Buttons({
        createOrder() {
            return fetch("/my-server/create-paypal-order", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({
                    cart: [
                        {
                            sku: "YOUR_PRODUCT_STOCK_KEEPING_UNIT",
                            quantity: "YOUR_PRODUCT_QUANTITY",
                        },
                    ]
                })
            })
                .then((response) => response.json())
                .then((order) => order.id);
        }
    }).render('#paypal-button-container');
</script>
