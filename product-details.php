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
$additional_images = isset($p_data['images']) ? $p_data['images'] : [];
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
                <div class="col-xxl-12">
                    <div class="page__title-content mb-50">
                        <h2 class="page__title"><?php echo $title; ?></h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                                <li class="breadcrumb-item"><a href="product.php">Product</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Current</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- page title end -->

    <!-- product area start -->
    <section class="product__area pb-115">
        <div class="container">
            <div class="row">
                <div class="col-xxl-8 col-xl-8 col-lg-8">
                    <div class="product__wrapper">
                        <div class="product__details-thumb w-img mb-30">
                            <img src="<?php echo $image; ?>" alt="product-details">
                        </div>
                        <?php if (!empty($additional_images)): ?>
                            <div class="row">
                                <?php foreach ($additional_images as $img): ?>
                                    <div class="col-4">
                                        <img class="img-fluid" src="http://127.0.0.1:8000/storage/<?php echo htmlspecialchars($img['image_url']); ?>" alt="Additional Image">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="product__details-content">
                            <div class="product__tab mb-40">
                                <ul class="nav nav-tabs" id="proTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="product__tab-content">
                                <div class="tab-content" id="proTabContent">
                                    <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                                        <div class="product__overview">
                                            <h3 class="product__overview-title">Product Details</h3>
                                            <p><?php echo $description; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-4 col-xl-4 col-lg-4">
                    <div class="product__details-sidebar ml-30">
                        <div class="product__proprietor white-bg mb-30">
                            <div class="product__proprietor-head mb-25">
                                <div class="product__prorietor-info mb-20 d-flex justify-content-between">
                                    <div class="product__proprietor-avater d-flex align-items-center">
                                        <div class="product__proprietor-thumb">
                                            <img src="assets/img/product/proprietor/profile.png" alt="">
                                        </div>
                                        <div class="product__proprietor-name">
                                            <h5><a href="#"><?php echo $seller; ?></a></h5>
                                            <a href="#">View Profile</a>
                                        </div>
                                    </div>
                                    <div class="product__proprietor-price">
                                        <span class="d-flex align-items-start">$<?php echo $price; ?></span>
                                    </div>
                                </div>
                                <div class="product__proprietor-text">
                                    <p>Pay once and get lifetime free updates.</p>
                                </div>
                            </div>
                            <div class="product__proprietor-body fix">
                                <ul class="mb-10 fix">
                                    <li>
                                        <h6>Released On:</h6>
                                        <span><?php echo $date; ?></span>
                                    </li>
                                    <li>
                                        <h6>Last Update:</h6>
                                        <span><?php echo $update_date; ?></span>
                                    </li>
                                </ul>
                                <button class="m-btn m-btn-2 add-to-cart" data-product-id="<?php echo $p_id; ?>">Add to Cart</button>
                                <button class="m-btn m-btn-2 remove-from-cart" data-product-id="<?php echo $p_id; ?>" style="display: none;">Remove from Cart</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- product area end -->

    <!-- trending area start -->
    <section class="trending__area pt-110 pb-110 grey-bg">
        <div class="container">
            <div class="row align-items-end">
                <div class="col-xxl-6 col-xl-6 col-lg col-md-8">
                    <div class="section__title-wrapper mb-50">
                        <h2 class="section__title">Trending Products</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                foreach ($related_products as $key => $value) {
                    $product_title = isset($value['name']) ? mb_substr($value['name'], 0, 30) . ".." : "Untitled";
                    $price = isset($value['price']) ? number_format($value['price'], 2) : '0.00';
                    $category = isset($value['category']['name']) ? htmlspecialchars($value['category']['name']) : 'N/A';
                    $image_url = isset($value['image_url']) ? "http://127.0.0.1:8000/storage/" . htmlspecialchars($value['image_url']) : 'assets/img/banner/sidebar-banner.jpg';

                    echo '<div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6">
                        <div class="trending__item d-sm-flex white-bg mb-30 wow fadeInUp" data-wow-delay=".3s">
                            <div class="trending__thumb mr-25">
                                <div class="trending__thumb-inner fix">
                                    <a href="product-details.php?id=' . $value['id'] . '">
                                        <img src="' . $image_url . '" alt="" class="product_img_102">
                                    </a>
                                </div>
                            </div>
                            <div class="trending__content">
                                <h3 class="trending__title"><a href="product-details.php?id=' . $value['id'] . '">' . $product_title . '</a></h3>
                                <p>Click to see full information.</p>
                                <div class="trending__meta d-flex justify-content-between">
                                    <div class="trending__tag">
                                        <a href="product.php?category=' . $category . '">' . $category . '</a>
                                    </div>
                                    <div class="trending__price">
                                        <span>$' . $price . '</span>
                                    </div>
                                </div>
                                <button class="m-btn m-btn-2 add-to-cart" data-product-id="' . $value['id'] . '">Add to Cart</button>
                            </div>
                        </div>
                    </div>';
                }
                ?>
            </div>
        </div>
    </section>
    <!-- trending area end -->

    <!-- subscribe area start -->
    <section class="subscribe__area p-relative pt-100 pb-110" data-background="assets/img/bg/subscribe-bg.jpg">
        <div class="subscribe__icon">
            <img class="ps" src="assets/img/icon/subscribe/ps.png" alt="">
            <img class="wp" src="assets/img/icon/register/pr.png" alt="">
            <img class="html" src="assets/img/icon/register/AI.png" alt="">
            <img class="f" src="assets/img/icon/subscribe/f.png" alt="">
            <img class="man" src="assets/img/icon/subscribe/man.png" alt="">
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="subscribe__content text-center wow fadeInUp" data-wow-delay=".5s">
                        <h3 class="subscribe__title">Want to be a seller? <br> Create your account now.</h3>
                        <p>Try our website for FREE!</p>
                        <div class="subscribe__form wow fadeInUp" data-wow-delay=".7s">
                            <form action="#">
                                <button type="submit" class="m-btn m-btn-black"><span></span> register</button>
                            </form>
                            <p>Join 20+ other sellers in our Markit community.</p>
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
            try {
                const response = await axios.post(`${API_BASE_URL}/cart/add`, { product_id: productId, quantity: 1 }, {
                    headers: { 
                        Authorization: `Bearer ${token}`, 
                        'Accept': 'application/json', 
                        'Content-Type': 'application/json' 
                    }
                });
                if (response.data.status_code === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.data.message
                    });
                    updateCartCount();
                    updateCartView();
                    // Show Remove button after adding
                    const removeBtn = button.nextElementSibling;
                    if (removeBtn && removeBtn.classList.contains('remove-from-cart')) {
                        removeBtn.style.display = 'inline-block';
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.data.message || 'Failed to add to cart'
                    });
                }
            } catch (error) {
                console.error('Add to Cart Error:', error.response ? error.response.data : error.message);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please check the console for details.'
                });
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
                Swal.fire({
                    icon: 'warning',
                    title: 'Login Required',
                    text: 'Please log in to remove items from your cart.'
                });
                return;
            }
            try {
                const response = await axios.post(`${API_BASE_URL}/cart/remove`, { product_id: productId }, {
                    headers: { 
                        Authorization: `Bearer ${token}`, 
                        'Accept': 'application/json', 
                        'Content-Type': 'application/json' 
                    }
                });
                if (response.data.status_code === 200) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.data.message
                    });
                    updateCartCount();
                    updateCartView();
                    // Hide Remove button after removing
                    const addBtn = button.previousElementSibling;
                    if (addBtn && addBtn.classList.contains('add-to-cart')) {
                        button.style.display = 'none';
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.data.message || 'Failed to remove from cart'
                    });
                }
            } catch (error) {
                console.error('Remove from Cart Error:', error.response ? error.response.data : error.message);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred. Please check the console for details.'
                });
            }
        });
    });

    // Update Cart Count
    function updateCartCount() {
        if (token) {
            axios.get(`${API_BASE_URL}/cart/count`, {
                headers: { Authorization: `Bearer ${token}`, 'Accept': 'application/json' }
            }).then(response => {
                if (response.data.status_code === 200) {
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement) {
                        cartCountElement.textContent = response.data.data.count;
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
                if (response.data.status_code === 200) {
                    cartItemsList.innerHTML = '';
                    let subtotal = 0;
                    if (response.data.data.length > 0) {
                        response.data.data.forEach(item => {
                            const price = item.product.priceUSD ? parseFloat(item.product.priceUSD) : 0;
                            const quantity = item.quantity || 1;
                            subtotal += price * quantity;
                            cartItemsList.innerHTML += `
                                <li>
                                    <div class="cartmini__thumb">
                                        <a href="product-details.php?id=${item.product_id}">
                                            <img src="${item.product.image || 'assets/img/banner/sidebar-banner.jpg'}" alt="${item.product.product_name || 'Product'}">
                                        </a>
                                    </div>
                                    <div class="cartmini__content">
                                        <h5><a href="product-details.php?id=${item.product_id}">${item.product.product_name || 'Unnamed Product'}</a></h5>
                                        <div class="product-quantity mt-10 mb-10">${quantity}x</div>
                                        <div class="product__sm-price-wrapper">
                                            <span>${quantity} <i class="fal fa-times"></i></span>
                                            <span class="product__sm-price">$${price.toFixed(2)}</span>
                                        </div>
                                    </div>
                                    <a href="product-details.php?id=${item.product_id}&remove_cart=${item.product_id}&msg=msg" class="cartmini__del"><i class="fal fa-times"></i></a>
                                </li>
                            `;
                        });
                    } else {
                        cartItemsList.innerHTML = '<li><p>Cart is empty</p></li>';
                    }
                    cartSubtotal.textContent = `$${subtotal.toFixed(2)}`;
                    checkoutBtn.disabled = subtotal === 0;
                } else {
                    console.error('Failed to fetch cart view:', response.data);
                    cartItemsList.innerHTML = '<li><p>Failed to load cart</p></li>';
                    cartSubtotal.textContent = '$0.00';
                    checkoutBtn.disabled = true;
                }
            }).catch(error => {
                console.error('Cart View Error:', error.response ? error.response.data : error.message);
                cartItemsList.innerHTML = '<li><p>Error loading cart</p></li>';
                cartSubtotal.textContent = '$0.00';
                checkoutBtn.disabled = true;
            });
        } else {
            cartItemsList.innerHTML = '<li><p>Please log in to view cart</p></li>';
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
.cartmini__wrapper {
    display: none;
    position: fixed;
    top: 10px;
    right: 10px;
    width: 300px;
    background: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    z-index: 1000;
    padding: 15px;
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
    color: red;
    cursor: pointer;
    font-size: 18px;
}
.m-btn-3 {
    background: #6f42c1;
    color: #fff;
    padding: 10px;
    text-align: center;
    border: none;
    cursor: pointer;
    width: 100%;
}
.m-btn-3:hover {
    background: #5a2d9e;
}
.m-btn-3:disabled {
    background: #ccc;
    cursor: not-allowed;
}
</style>
</body>
</html>