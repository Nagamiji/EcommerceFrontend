<?php
require('inc/header.php');
$api = new APIClient();

if (!isset($_GET['id']) && !isset($_GET['category'])) {
    header("location: index.php");
} else {
    $foundId = null;
    if (isset($_GET['category'])) {
        $response = $api->callAPI("/public/categories");
        $categories = $response['data'] ?? null;
        $targetName = $_GET['category'];
        
        foreach ($categories as $item) {
            if (strtolower($item['name']) === strtolower($targetName)) {
                $foundId = $item['id'];
                break;
            }
        }
    }
    $c_id = $_GET['id'] ?? $foundId;

    $response = $api->callAPI("/public/categories/$c_id/products");
    if ($response['status_code'] != 200) {
        header("location: index.php");
    } else {
        $p_data = $response['data'] ?? [];
    }
    $response = $api->callAPI("/public/products?per_page=9");
    $p_data_9 = $response['data']['data'] ?? null;
}
?>

<!-- cart mini area start -->
<?php include("inc/cart.php"); ?>
<div class="body-overlay"></div>
<!-- cart mini area end -->

<!-- sidebar area start -->
<div class="sidebar__area">
    <div class="sidebar__wrapper">
        <div class="sidebar__close">
            <button class="sidebar__close-btn" id="sidebar__close-btn">
                <span><i class="fal fa-times"></i></span>
                <span>close</span>
            </button>
        </div>
        <div class="sidebar__content">
            <div class="logo mb-40">
                <a href="index.php">
                    <img class="logo" src="assets/img/logo/Online-store-white.png" alt="logo">
                </a>
            </div>
            <div class="mobile-menu"></div>
            <div class="sidebar__action mt-330">
                <div class="sidebar__login mt-15">
                    <?php if ($auth->isLogin()) {
                        echo "<a href=\"account\"><i class=\"far fa-user\"></i>Account</a>";
                    } else {
                        echo "<a href=\"sign-in.php\"><i class=\"far fa-unlock\"></i> Log In</a>";
                    } ?>
                </div>
                <div class="sidebar__cart mt-20">
                    <a href="javascript:void(0);" class="cart-toggle-btn">
                        <i class="far fa-shopping-cart"></i>
                        <span id="cart-count"><?php echo $cart_count ?? 0; ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- sidebar area end -->
<div class="body-overlay"></div>

<main>
    <!-- bg shape area start -->
    <div class="bg-shape">
        <img src="assets/img/shape/shape-1.png" alt="">
    </div>
    <!-- bg shape area end -->

    <!-- product area start -->
    <section class="product__area po-rel-z1 pt-100 pb-115 grey-bg">
        <div class="container">
            <div class="row">
                <div class="col-xxl-12 col-xl-12 col-lg-12">
                    <div class="row">
                    <?php
                    if ($p_data) {
                        foreach ($p_data as $key => $value) {
                            $product_title = isset($value['product_name']) ? mb_substr($value['product_name'], 0, 30) . ".." : "";
                            $category = (isset($value['category']) ? $value['category'] : ($value['category_name'] ?? 'Category'));
                            $price = $value['priceUSD'] ?? 0;
                            $price = ($price == 0) ? "FREE!" : "$" . $price;

                            echo '<div class="col-xxl-4 col-xl-4 col-lg-4 col-md-4">
                                <div class="product__item white-bg mb-30 wow fadeInUp" data-wow-delay=".3s">
                                    <div class="product__thumb">
                                        <div class="product__thumb-inner fix w-img">
                                            <a href="product-details.php?id=' . $value['product_id'] . '">
                                                <img class="product_img_356" src="' . (isset($value['image']) ? $value['image'] : '') . '" alt="">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="product__content">
                                        <div class="product__meta mb-10 d-flex justify-content-between align-items-center">
                                            <div class="product__tag">
                                                <a href="product.php?category=' . $category . '">' . $category . '</a>
                                            </div>
                                            <div class="product__price">
                                                <span>' . $price . '</span>
                                            </div>
                                        </div>
                                        <h3 class="product__title">
                                            <a href="product-details.php?id=' . $value['product_id'] . '">' . $product_title . '</a>
                                        </h3>
                                        <button class="m-btn m-btn-2 add-to-cart" data-product-id="' . $value['product_id'] . '">Add to Cart</button>
                                    </div>
                                </div>
                            </div>';
                        }
                    }
                    ?>
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
                        <h2 class="section__title">Trending Landmark Software</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                if (isset($p_data_9)) {
                    foreach ($p_data_9 as $key => $value) {
                        $product_title = isset($value['product_name']) ? mb_substr($value['product_name'], 0, 30) . ".." : "";
                        $category = (isset($value['category']) ? $value['category'] : ($value['category_name'] ?? 'Category'));
                        $price = $value['priceUSD'] ?? 0;
                        $price = ($price == 0) ? "FREE!" : "$" . $price;

                        echo '<div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6">
                            <div class="trending__item d-sm-flex white-bg mb-30 wow fadeInUp" data-wow-delay=".3s">
                                <div class="trending__thumb mr-25">
                                    <div class="trending__thumb-inner fix">
                                        <a href="product-details.php?id=' . $value['id'] . '">
                                            <img src="' . (isset($value['image_url']) ? $value['image_url'] : '') . '" alt="" class="product_img_102">
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
                                            <span>' . $price . '</span>
                                        </div>
                                    </div>
                                    <button class="m-btn m-btn-2 add-to-cart" data-product-id="' . $value['id'] . '">Add to Cart</button>
                                </div>
                            </div>
                        </div>';
                    }
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

<!-- Add JavaScript for Cart Functionality -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const API_BASE_URL = '<?php echo API_BASE_URL ?? 'http://localhost:8000'; ?>';
    const token = localStorage.getItem('token') || '<?php echo $_SESSION['token'] ?? ''; ?>';

    // Add to Cart
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', async (e) => {
            const productId = e.target.getAttribute('data-product-id');
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
                const response = await axios.post(`${API_BASE_URL}/api/cart/add`, { product_id: productId, quantity: 1 }, {
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
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.data.message || 'Failed to add to cart'
                    });
                    console.log(response);
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
            if (!token) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Login Required',
                    text: 'Please log in to remove items from your cart.'
                });
                return;
            }
            try {
                const response = await axios.post(`${API_BASE_URL}/api/cart/remove`, { product_id: productId }, {
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
            axios.get(`${API_BASE_URL}/api/cart/count`, {
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
            axios.get(`${API_BASE_URL}/api/cart/view`, {
                headers: { Authorization: `Bearer ${token}`, 'Accept': 'application/json' }
            }).then(response => {
                if (response.data.status_code === 200) {
                    const cartItemsElement = document.getElementById('cart-items');
                    if (cartItemsElement) {
                        cartItemsElement.innerHTML = '';
                        if (response.data.data.length > 0) {
                            response.data.data.forEach(item => {
                                cartItemsElement.innerHTML += `
                                    <div class="cart-item">
                                        <span>${item.product.product_name} (x${item.quantity}) - $${item.total_price}</span>
                                    </div>
                                `;
                            });
                        } else {
                            cartItemsElement.innerHTML = '<p>Cart is empty</p>';
                        }
                    } else {
                        console.error('Cart items element not found!');
                    }
                } else {
                    console.error('Failed to fetch cart view:', response.data);
                }
            }).catch(error => {
                console.error('Cart View Error:', error.response ? error.response.data : error.message);
            });
        }
    }

    // Initial load
    updateCartCount();

    // Toggle Cart View
    document.querySelector('.cart-toggle-btn')?.addEventListener('click', () => {
        const cartMiniArea = document.querySelector('.cart-mini-area');
        if (cartMiniArea) {
            cartMiniArea.style.display = cartMiniArea.style.display === 'block' ? 'none' : 'block';
            updateCartView();
        }
    });
});
</script>
</body>
</html>