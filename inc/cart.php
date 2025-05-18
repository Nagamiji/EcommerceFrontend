<?php
// Include necessary classes
$auth = new auth();
$api = new APIClient();
$token = $_SESSION['token'] ?? $_COOKIE['token'] ?? null;
$cart_data = $api->callAPI("/cart/view", 'GET', [], $token)['data'] ?? [];

// Calculate cart count and total
$cart_count = count($cart_data);
$cart_total = array_reduce($cart_data, fn($sum, $item) => $sum + ($item['total_price'] ?? 0), 0);

// Prepare cart items for PayPal
$cart_items = $cart_data ? array_map(fn($item, $index) => [
    'id' => $index + 1,
    'quantity' => $item['quantity'],
    'price' => $item['total_price'] / $item['quantity'],
    'name' => $item['product']['product_name']
], $cart_data, array_keys($cart_data)) : [];
?>

<div class="cartmini__area">
    <div class="cartmini__wrapper">
        <div class="cartmini__title"><h4>Shopping cart</h4></div>
        <div class="cartmini__close"><button type="button" class="cartmini__close-btn"><i class="fal fa-times"></i></button></div>
        <div class="cartmini__widget">
            <div class="cartmini__inner">
                <ul>
                    <?php if ($cart_data): ?>
                        <?php foreach ($cart_data as $key => $item): ?>
                            <?php $product = $item['product']; $price = $product['priceUSD']; ?>
                            <li>
                                <div class="cartmini__thumb"><a href="product-details.php"><img src="<?= $product['image'] ?>" alt=""></a></div>
                                <div class="cartmini__content">
                                    <h5><a href="product-details.php?id=<?= $item['product_id'] ?>"><?= $product['product_name'] ?></a></h5>
                                    <div class="product__sm-price-wrapper">
                                        <span><?= $item['quantity'] ?> <i class="fal fa-times"></i></span>
                                        <span class="product__sm-price"><?= is_numeric($price) ? "$" . $price : $price ?></span>
                                    </div>
                                </div>
                                <a href="product-details.php?id=<?= $item['product_id'] ?>&remove_cart=<?= $item['product_id'] ?>&msg=msg" class="cartmini__del"><i class="fal fa-times"></i></a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><p>Cart is empty</p></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="cartmini__checkout">
                <div class="cartmini__checkout-title mb-30">
                    <h4>Subtotal:</h4><span>$<?= $cart_total ?></span>
                </div>
                <div class="cartmini__checkout-btn">
                    <div class="m-btn m-btn-3 w-100" id="<?= $cart_total > 0 ? 'checkout' : '' ?>" data-bs-toggle="modal" data-bs-target="#qrCodeModal"><span></span>checkout</div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="body-overlay"></div>

<!-- sidebar area start -->
<div class="sidebar__area">
    <div class="sidebar__wrapper">
        <div class="sidebar__close">
            <button class="sidebar__close-btn" id="sidebar__close-btn"><span><i class="fal fa-times"></i></span><span>close</span></button>
        </div>
        <div class="sidebar__content">
            <div class="logo mb-40"><a href="index.php"><img class="logo" src="assets/img/logo/Online-store-white.png" alt="logo"></a></div>
            <div class="mobile-menu"></div>
            <div class="sidebar__action mt-330">
                <div class="sidebar__login mt-15">
                    <?= $auth->isLogin() ? '<a href="account"><i class="far fa-user"></i>Account</a>' : '<a href="sign-in.php"><i class="far fa-unlock"></i> Log In</a>' ?>
                </div>
                <div class="sidebar__cart mt-20">
                    <a href="javascript:void(0);" class="cart-toggle-btn"><i class="far fa-shopping-cart"></i><span id="cart-count"><?= $cart_count ?></span></a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content" style="border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); border: none;">
            <div style="background: linear-gradient(90deg, #0070BA, #009CDE); color: white; padding: 1rem; text-align: center; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                <div style="font-size: 1.5rem; font-weight: 600; font-family: 'Helvetica', 'Arial', sans-serif;">Checkout</div>
            </div>
            <div class="modal-body py-4 px-5" style="background-color: #f7f9fc;">
                <div id="qrName" class="px-3" style="font-weight: 600; font-size: 1.1rem; color: #333; font-family: 'Helvetica', 'Arial', sans-serif;">TY KANA</div>
                <div class="text-center"><hr class="m-3" style="margin: 1rem 0; border-color: #ddd;" /><canvas id="qrCodeCanvas" style="max-height: 100% !important;"></canvas></div>
                <div class="payment-options mt-3 text-center">
                    <button class="paypal-btn" data-payment-type="paypal" style="background-color: #FFC439; color: #fff; border: none; padding: 10px 20px; width: 100%; margin-bottom: 10px; font-size: 16px; border-radius: 5px;">PayPal</button>
                    <button class="paylater-btn" data-payment-type="paylater" style="background-color: #FFC439; color: #fff; border: none; padding: 10px 20px; width: 100%; margin-bottom: 10px; font-size: 16px; border-radius: 5px;"><span style="font-size: 24px; vertical-align: middle;">₽</span> Pay Later</button>
                    <button class="card-btn" data-payment-type="card" style="background-color: #333; color: #fff; border: none; padding: 10px 20px; width: 100%; font-size: 16px; border-radius: 5px;">Debit or Credit Card</button>
                    <p style="font-size: 12px; color: #666; margin-top: 10px;">Powered by PayPal</p>
                </div>
                <div id="paypal-button-container" style="display: none;"></div>
            </div>
            <div class="modal-footer justify-content-center" style="border-top: none; background-color: #f7f9fc; padding: 1rem;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 5px; font-family: 'Helvetica', 'Arial', sans-serif; background-color: #6c757d; border: none;">Close</button>
                <button type="button" class="btn btn-primary" onclick="downloadQRCode()" style="border-radius: 5px; font-family: 'Helvetica', 'Arial', sans-serif; background-color: #0070BA; border: none;"><i class="fas fa-download"></i></button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
<script src="https://www.paypal.com/sdk/js?client-id=AY_Aii9RcVehburdrMfI5hS78l4bD59OIyBSsBE74NSKQfA581EZCSzspbXKMThzMcn_Z-dBkThuULD6&currency=USD&enable-funding=paylater,card"></script>
<script>
function downloadQRCode() {
    const canvas = document.getElementById("qrCodeCanvas");
    const link = document.createElement("a");
    link.href = canvas.toDataURL("image/png");
    link.download = "khqr-code.png";
    link.click();
}

function generateQRCode() {
    new QRCode(document.getElementById("qrCodeCanvas"), {
        text: "https://example.com/checkout/" + Math.random().toString(36).substr(2, 9),
        width: 150,
        height: 150,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
}
window.onload = generateQRCode;

document.querySelectorAll('.paypal-btn, .paylater-btn, .card-btn').forEach(button => {
    button.addEventListener('click', async () => {
        const paymentType = button.dataset.paymentType;
        console.log('Button clicked:', paymentType);

        if (paymentType === 'paylater') {
            // Handle Pay Later without PayPal
            try {
                const response = await fetch('http://localhost:8000/api/checkout/paylater', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer <?= $token ?>'
                    },
                    body: JSON.stringify({
                        cart_total: <?= $cart_total ?>,
                        cart_items: <?= json_encode($cart_items) ?>
                    })
                });

                const result = await response.json();
                if (response.ok) {
                    console.log('Pay Later successful:', result);
                    alert('Order placed successfully with Pay Later! Order ID: ' + result.order_id);
                    window.location.reload(); // Refresh to clear cart
                } else {
                    console.error('Pay Later failed:', result);
                    alert('Pay Later failed: ' + result.error);
                }
            } catch (err) {
                console.error('Pay Later error:', err);
                alert('An error occurred during Pay Later checkout. Please try again.');
            }
        } else {
            // Handle PayPal and Card with PayPal SDK
            document.getElementById('paypal-button-container').style.display = 'block';
            paypalButtons(paymentType);
        }
    });
});

function paypalButtons(paymentType) {
    console.log('PayPal initialized for', paymentType);
    const cartTotal = parseFloat('<?= $cart_total ?>');
    if (!cartTotal || cartTotal <= 0) {
        console.error('Invalid cart total:', cartTotal);
        alert('Cart total is invalid. Please add items to your cart.');
        return;
    }

    paypal.Buttons({
        createOrder: (data, actions) => {
            console.log('Creating order with paymentType:', paymentType);
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: cartTotal.toFixed(2),
                        currency_code: 'USD',
                        breakdown: {
                            item_total: { value: cartTotal.toFixed(2), currency_code: 'USD' }
                        }
                    },
                    items: <?= json_encode($cart_items) ?>.map(item => ({
                        name: item.name,
                        unit_amount: { value: item.price.toFixed(2), currency_code: 'USD' },
                        quantity: item.quantity
                    }))
                }],
                application_context: {
                    shipping_preference: 'NO_SHIPPING'
                },
                custom_id: paymentType
            });
        },
        onApprove: async (data, actions) => {
            console.log('Payment approved, capturing order:', data);
            try {
                const details = await actions.order.capture();
                console.log('Capture result:', details);
                const message = `Transaction completed by ${details.payer.name.given_name}${
                    paymentType === 'card' ? ' (Card)' : ''
                }`;
                alert(message);
                window.location.href = `http://localhost:8000/api/order/paypal/success?paymentId=${data.orderID}&PayerID=${details.payer.payer_id}&paymentType=${paymentType}`;
            } catch (err) {
                console.error('Capture failed:', err);
                alert('Payment capture failed. Please try again.');
            }
        },
        onError: (err) => {
            console.error('PayPal Checkout Error:', err);
            alert('An error occurred during checkout. Please try again.');
        },
        onCancel: (data) => {
            console.log('Payment cancelled:', data);
            alert('Payment was cancelled.');
        },
        style: {
            layout: 'vertical',
            color: { paypal: 'blue', paylater: 'gold', card: 'black' }[paymentType] || 'blue',
            shape: 'rect',
            label: 'paypal'
        }
    }).render('#paypal-button-container').catch(err => {
        console.error('PayPal button render failed:', err);
        alert('Failed to load PayPal button. Please refresh the page.');
    });
}
</script>

<style>
.payment-options { display: flex; flex-direction: column; gap: 10px; text-align: center; }
.paypal-btn, .paylater-btn, .card-btn { cursor: pointer; font-family: 'Helvetica', 'Arial', sans-serif; transition: opacity 0.3s; }
.paypal-btn, .paylater-btn, .card-btn:hover { opacity: 0.9; }
</style>