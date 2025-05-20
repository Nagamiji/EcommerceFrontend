<?php
require('inc/header.php');
$api = new APIClient();

// Validate product ID
$p_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;
if ($p_id <= 0) {
    header("Location: index.php");
    exit;
}

// Fetch product details via API
$response = $api->callAPI("/public/products/$p_id");
if (!is_array($response) || !isset($response['status_code']) || $response['status_code'] != 200) {
    header("Location: index.php?error=" . urlencode($response['message'] ?? 'Product not found'));
    exit;
}

$p_data = $response['data'] ?? [];

// Fetch related products
$response = $api->callAPI("/public/products");
$related_products = $response['data'] ?? [];

$title = isset($p_data['name']) ? htmlspecialchars($p_data['name']) : 'Product Not Found';
$price = isset($p_data['price']) ? number_format($p_data['price'], 2) : '0.00';
$description = isset($p_data['description']) ? htmlspecialchars($p_data['description']) : 'No description available.';
$seller = isset($p_data['user_id']) ? "User ID: " . htmlspecialchars($p_data['user_id']) : 'Justin Case';
$category = isset($p_data['category']['name']) ? htmlspecialchars($p_data['category']['name']) : 'N/A';
$date = isset($p_data['created_at']) ? date("F j, Y", strtotime($p_data['created_at'])) : 'N/A';
$update_date = isset($p_data['updated_at']) ? date("F j, Y", strtotime($p_data['updated_at'])) : 'N/A';
$image = isset($p_data['image_url']) ? "http://127.0.0.1:8000/storage/" . htmlspecialchars($p_data['image_url']) : 'assets/img/banner/sidebar-banner.jpg';
?>

<!-- cart mini area start -->
<?php include("inc/cart.php"); ?>
<!-- sidebar area end -->
<div class="body-overlay"></div>

<main>
    <!-- bg shape area start -->
    <div class="bg-shape">
        <img src="assets/img/shape/shape-1.png" alt="">
    </div>
    <!-- bg shape area end -->

    <!-- page title area -->
    <section class="page__title-area pt-85">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="page__title-content mb-50 text-center">
                        <h2 class="page__title text-white"><?php echo $title; ?></h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb justify-content-center">
                                <li class="breadcrumb-item"><a href="index.php" class="text-white">Home</a></li>
                                <li class="breadcrumb-item"><a href="product.php" class="text-white">Product</a></li>
                                <li class="breadcrumb-item active text-primary" aria-current="page">Current</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- page title end -->

    <!-- product area start -->
    <section class="product__area pb-120">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="product__image-wrapper">
                        <div class="product__main-image">
                            <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" class="img-fluid rounded shadow-sm">
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="product__details-sidebar p-4 bg-light rounded">
                        <h3 class="product__title mb-3"><?php echo $title; ?></h3>
                        <div class="product__price mb-4 text-success fw-bold fs-4">$<?php echo $price; ?></div>
                        <div class="product__seller mb-3">
                            <p class="mb-1"><strong>Seller:</strong> <?php echo $seller; ?></p>
                            <a href="#" class="text-primary">View Profile</a>
                        </div>
                        <div class="product__meta mb-4">
                            <p class="mb-1"><strong>Released On:</strong> <?php echo $date; ?></p>
                            <p><strong>Last Updated:</strong> <?php echo $update_date; ?></p>
                        </div>
                        <p class="product__description mb-4"><?php echo $description; ?></p>
                        <div class="product__actions">
                            <button class="m-btn add-to-cart mb-2" data-product-id="<?php echo $p_id; ?>">Add to Cart</button>
                            <button class="m-btn remove-from-cart" data-product-id="<?php echo $p_id; ?>" style="display: none;">Remove from Cart</button>
                            <p class="text-muted small mt-2">Pay once and get lifetime free updates.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- product area end -->

    <!-- trending area start -->
    <section class="trending__area pt-100 pb-100 bg-gray-100">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section__title text-center mb-60">
                        <h2 class="section__title-text">Trending Products</h2>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <?php
                foreach (array_slice($related_products, 0, 3) as $key => $value) { // Limit to 3 related products
                    $product_title = isset($value['name']) ? mb_substr($value['name'], 0, 30) . ".." : "Untitled";
                    $price = isset($value['price']) ? number_format($value['price'], 2) : '0.00';
                    $category = isset($value['category']['name']) ? htmlspecialchars($value['category']['name']) : 'N/A';
                    $image_url = isset($value['image_url']) ? "http://127.0.0.1:8000/storage/" . htmlspecialchars($value['image_url']) : 'assets/img/banner/sidebar-banner.jpg';

                    echo '<div class="col-md-4">
                        <div class="trending__item bg-white p-3 rounded shadow-sm">
                            <div class="trending__thumb mb-3">
                                <a href="product-details.php?id=' . $value['id'] . '">
                                    <img src="' . $image_url . '" alt="' . $product_title . '" class="img-fluid rounded">
                                </a>
                            </div>
                            <h4 class="trending__title mb-2"><a href="product-details.php?id=' . $value['id'] . '">' . $product_title . '</a></h4>
                            <p class="text-muted mb-2">Click to see full information.</p>
                            <div class="trending__meta d-flex justify-content-between align-items-center">
                                <span class="trending__category text-primary">' . $category . '</span>
                                <span class="trending__price text-success">$' . $price . '</span>
                            </div>
                            <button class="m-btn w-100 mt-2 add-to-cart" data-product-id="' . $value['id'] . '">Add to Cart</button>
                        </div>
                    </div>';
                }
                ?>
            </div>
        </div>
    </section>
    <!-- trending area end -->

    <!-- subscribe area start -->
    <section class="subscribe__area pt-100 pb-100" style="background-image: url('assets/img/bg/subscribe-bg.jpg');">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="subscribe__content text-center text-white">
                        <h3 class="subscribe__title mb-3">Want to be a seller? <br> Create your account now.</h3>
                        <p class="mb-4">Try our website for FREE!</p>
                        <div class="subscribe__form">
                            <form action="#">
                                <button type="submit" class="m-btn m-btn-black"><span></span> Register</button>
                            </form>
                            <p class="text-white-50 mt-2">Join 20+ other sellers in our Markit community.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- subscribe area end -->
</main>

<!-- footer area start -->
<?php include("inc/footer.php"); ?>
<!-- footer area end -->

<!-- Cart Mini-Widget -->
<div class="cartmini__wrapper" id="cartMiniWrapper">
    <div class="cartmini__title">
        <h4>Shopping Cart</h4>
    </div>
    <div class="cartmini__close">
        <button type="button" class="cartmini__close-btn"><i class="fal fa-times"></i></button>
    </div>
    <div class="cartmini__widget">
        <div class="cartmini__inner">
            <ul id="cartItemsList">
                <!-- Cart items will be populated via JavaScript -->
            </ul>
        </div>
        <div class="cartmini__checkout">
            <div class="cartmini__checkout-title mb-30">
                <h4>Subtotal:</h4>
                <span id="cartSubtotal">$0.00</span>
            </div>
            <div class="cartmini__checkout-btn">
                <a href="checkout.php" class="m-btn m-btn-3 w-100" id="checkoutBtn">Checkout</a>
            </div>
        </div>
    </div>
</div>

<!-- Add JavaScript for Cart Functionality -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://kit.fontawesome.com/your-kit-id.js" crossorigin="anonymous"></script> <!-- Add Font Awesome for spinner -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const API_BASE_URL = '<?php echo API_BASE_URL ?? 'http://127.0.0.1:8000/api'; ?>';
    const token = localStorage.getItem('token') || '<?php echo $_SESSION['token'] ?? ''; ?>';
    const cartMiniWrapper = document.getElementById('cartMiniWrapper');
    const cartItemsList = document.getElementById('cartItemsList');
    const cartSubtotal = document.getElementById('cartSubtotal');
    const checkoutBtn = document.getElementById('checkoutBtn');

    // Toggle Cart Visibility
    document.querySelector('.cart-toggle-btn')?.addEventListener('click', () => {
        cartMiniWrapper.classList.toggle('opened');
        if (cartMiniWrapper.classList.contains('opened')) {
            updateCartView();
        }
    });

    document.querySelector('.cartmini__close-btn')?.addEventListener('click', () => {
        cartMiniWrapper.classList.remove('opened');
    });

    // Add to Cart
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', async (e) => {
            const productId = e.target.getAttribute('data-product-id');
            if (!productId) {
                console.error('No product ID found on button');
                Swal.fire({ icon: 'error', title: 'Error', text: 'No product ID found.' });
                return;
            }
            if (!token) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Login Required',
                    text: 'You need to log in to add items to your cart. Do you want to log in now?',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'sign-in.php';
                    }
                });
                return;
            }
            e.target.disabled = true;
            e.target.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            try {
                const response = await axios.post(`${API_BASE_URL}/cart/add`, { product_id: productId, quantity: 1 }, {
                    headers: { Authorization: `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' }
                });
                if (response.data && response.data.status_code === 200) {
                    Swal.fire({ icon: 'success', title: 'Success!', text: response.data.message || 'Product added to cart successfully' });
                    e.target.style.backgroundColor = '#28a745';
                    updateCartCount();
                    updateCartView();
                    const removeBtn = button.nextElementSibling;
                    if (removeBtn && removeBtn.classList.contains('remove-from-cart')) {
                        removeBtn.style.display = 'inline-block';
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.data.message || 'Failed to add to cart' });
                }
            } catch (error) {
                console.error('Add to Cart Error:', error.response ? error.response.data : error.message);
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred. Please check the console for details.' });
            } finally {
                e.target.disabled = false;
                e.target.innerHTML = 'Add to Cart';
                e.target.style.backgroundColor = '';
            }
        });
    });

    // Remove from Cart
    document.querySelectorAll('.remove-from-cart').forEach(button => {
        button.addEventListener('click', async (e) => {
            const productId = e.target.getAttribute('data-product-id');
            if (!productId) {
                console.error('No product ID found on remove button');
                return;
            }
            if (!token) {
                Swal.fire({ icon: 'warning', title: 'Login Required', text: 'Please log in to remove items from your cart.' });
                return;
            }
            try {
                const response = await axios.post(`${API_BASE_URL}/cart/remove`, { product_id: productId }, {
                    headers: { Authorization: `Bearer ${token}`, 'Accept': 'application/json', 'Content-Type': 'application/json' }
                });
                if (response.data && response.data.status_code === 200) {
                    Swal.fire({ icon: 'success', title: 'Success!', text: response.data.message || 'Product removed from cart successfully' });
                    updateCartCount();
                    updateCartView();
                    const addBtn = button.previousElementSibling;
                    if (addBtn && addBtn.classList.contains('add-to-cart')) {
                        button.style.display = 'none';
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: response.data.message || 'Failed to remove from cart' });
                }
            } catch (error) {
                console.error('Remove from Cart Error:', error.response ? error.response.data : error.message);
                Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred. Please check the console for details.' });
            }
        });
    });

    // Update Cart Count
    function updateCartCount() {
        if (token) {
            axios.get(`${API_BASE_URL}/cart/count`, {
                headers: { Authorization: `Bearer ${token}`, 'Accept': 'application/json' }
            }).then(response => {
                if (response.data && response.data.status_code === 200) {
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = response.data.count;
                    } else {
                        console.error('Cart count element not found!');
                    }
                } else {
                    console.error('Failed to fetch cart count:', response.data);
                }
            }).catch(error => {
                console.error('Cart Count Error:', error.response ? error.response.data : error.message);
            });
        }
    }

    // Update Cart View
    function updateCartView() {
        if (token) {
            axios.get(`${API_BASE_URL}/cart/view`, {
                headers: { Authorization: `Bearer ${token}`, 'Accept': 'application/json' }
            }).then(response => {
                if (response.data && response.data.status_code === 200) {
                    cartItemsList.innerHTML = '';
                    let subtotal = 0;
                    if (response.data.data.length > 0) {
                        response.data.data.forEach(item => {
                            const price = item.product.priceUSD ? parseFloat(item.product.priceUSD) : 0;
                            const quantity = item.quantity || 1;
                            subtotal += price * quantity;
                            cartItemsList.innerHTML += `
                                <li class="d-flex align-items-center mb-3">
                                    <div class="cartmini__thumb me-3">
                                        <a href="product-details.php?id=${item.product_id}">
                                            <img src="${item.product.image || 'assets/img/banner/sidebar-banner.jpg'}" alt="${item.product.product_name || 'Product'}" class="img-fluid rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        </a>
                                    </div>
                                    <div class="cartmini__content flex-grow-1">
                                        <h5 class="mb-1"><a href="product-details.php?id=${item.product_id}">${item.product.product_name || 'Unnamed Product'}</a></h5>
                                        <div class="product__sm-price-wrapper">
                                            <span>${quantity} <i class="fal fa-times"></i></span>
                                            <span class="product__sm-price text-success">$${price.toFixed(2)}</span>
                                        </div>
                                    </div>
                                    <a href="product-details.php?id=${item.product_id}&remove_cart=${item.product_id}&msg=msg" class="cartmini__del text-danger"><i class="fal fa-times"></i></a>
                                </li>
                            `;
                        });
                    } else {
                        cartItemsList.innerHTML = '<li class="text-center py-2">Cart is empty</li>';
                    }
                    cartSubtotal.textContent = `$${subtotal.toFixed(2)}`;
                    checkoutBtn.disabled = subtotal === 0;
                } else {
                    console.error('Failed to fetch cart view:', response.data);
                    cartItemsList.innerHTML = '<li class="text-center py-2">Failed to load cart</li>';
                    cartSubtotal.textContent = '$0.00';
                    checkoutBtn.disabled = true;
                }
            }).catch(error => {
                console.error('Cart View Error:', error.response ? error.response.data : error.message);
                cartItemsList.innerHTML = '<li class="text-center py-2">Error loading cart</li>';
                cartSubtotal.textContent = '$0.00';
                checkoutBtn.disabled = true;
            });
        } else {
            cartItemsList.innerHTML = '<li class="text-center py-2">Please log in to view cart</li>';
            cartSubtotal.textContent = '$0.00';
            checkoutBtn.disabled = true;
        }
    }

    // Initial load
    updateCartCount();
    if (cartMiniWrapper.classList.contains('opened')) {
        updateCartView();
    }

    // Handle URL parameters for add/remove
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('add_to_cart') || urlParams.has('remove_cart')) {
        updateCartView();
    }
});
</script>

<style>
:root {
    --primary-color: #6f42c1;
    --success-color: #28a745;
    --error-color: #dc3545;
    --text-color: #333;
    --font-family: 'Helvetica', 'Arial', sans-serif;
    --bg-gray: #f8f9fa;
}

.bg-shape img {
    width: 100%;
    height: auto;
    opacity: 0.1;
}

.page__title-area {
    background: linear-gradient(90deg, #6f42c1, #8e6de6);
    padding: 60px 0;
}

.page__title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.breadcrumb {
    background: transparent;
}

.breadcrumb-item a, .breadcrumb-item.active {
    color: inherit;
    text-decoration: none;
}

.product__area {
    padding: 60px 0;
}

.product__image-wrapper {
    border: 1px solid #eee;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.product__main-image img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product__main-image img:hover {
    transform: scale(1.05);
}

.product__details-sidebar {
    border: 1px solid #eee;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.product__title {
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--text-color);
}

.product__price {
    font-size: 1.5rem;
}

.product__actions .m-btn {
    background-color: var(--primary-color);
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease;
}

.product__actions .m-btn:hover {
    background-color: darken(var(--primary-color), 10%);
}

.trending__area {
    padding: 60px 0;
    background-color: var(--bg-gray);
}

.trending__item {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.trending__item:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.trending__title {
    font-size: 1.2rem;
    font-weight: 500;
}

.trending__meta {
    margin-top: 10px;
}

.subscribe__area {
    background-size: cover;
    background-position: center;
    position: relative;
    color: white;
}

.subscribe__area::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 0;
}

.subscribe__content {
    position: relative;
    z-index: 1;
}

.subscribe__title {
    font-size: 2rem;
    font-weight: 700;
}

.m-btn-black {
    background-color: #000;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.m-btn-black:hover {
    background-color: #333;
}

.cartmini__wrapper {
    display: none;
    position: fixed;
    top: 10px;
    right: 10px;
    width: 320px;
    background: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 0 15px rgba(0,0,0,0.2);
    z-index: 1000;
    padding: 15px;
    border-radius: 8px;
}

.cartmini__wrapper.opened {
    display: block;
}

.cartmini__close-btn {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
    float: right;
}

.cartmini__del {
    color: var(--error-color);
    cursor: pointer;
    font-size: 18px;
}

.m-btn-3 {
    background: var(--primary-color);
    color: #fff;
    padding: 10px;
    text-align: center;
    border: none;
    cursor: pointer;
    width: 100%;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.m-btn-3:hover {
    background: darken(var(--primary-color), 10%);
}

.m-btn-3:disabled {
    background: #ccc;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .product__main-image img {
        height: 300px;
    }
    .product__details-sidebar {
        margin-top: 20px;
    }
    .cartmini__wrapper {
        width: 90%;
        right: 5%;
    }
    .trending__item {
        margin-bottom: 20px;
    }
    .subscribe__title {
        font-size: 1.5rem;
    }
}
</style>
</body>
</html>