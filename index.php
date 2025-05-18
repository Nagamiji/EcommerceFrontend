<?php require('inc/header.php'); ?>
<!-- header area end -->

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
                    <a href="sign-in.php" id="login-link"><i class="far fa-unlock"></i> Log In</a>
                </div>
                <div class="sidebar__cart mt-20">
                    <a href="javascript:void(0);" class="cart-toggle-btn">
                        <i class="far fa-shopping-cart"></i>
                        <span id="cart-count">0</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- sidebar area end -->
<div class="body-overlay"></div>

<main>
    <!-- hero area start -->
    <section class="hero__area hero__height grey-bg-3 d-flex align-items-center">
        <div class="hero__shape">
            <img class="circle" src="assets/img/icon/hero/hero-circle.png" alt="circle">
            <img class="circle-2" src="assets/img/icon/hero/hero-circle-2.png" alt="circle">
            <img class="square" src="assets/img/icon/hero/hero-square.png" alt="circle">
            <img class="square-2" src="assets/img/icon/hero/hero-square-2.png" alt="circle">
            <img class="dot" src="assets/img/icon/hero/hero-dot.png" alt="circle">
            <img class="triangle" src="assets/img/icon/hero/hero-triangle.png" alt="circle">
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-8 col-sm-8">
                    <div class="hero__content">
                        <h2 class="hero__title">
                            <span>Unlock a Universe </span>
                            of Digital Delights
                        </h2>
                        <p>—Your Next Favorite Software is Just a Click Away.</p>
                        <div class="hero__search">
                            <form id="search-form">
                                <div class="hero__search-inner d-xl-flex">
                                    <div class="hero__search-input">
                                        <span><i class="far fa-search"></i></span>
                                        <input type="text" name="query" id="query" placeholder="Search for products">
                                    </div>
                                    <button type="submit" class="m-btn ml-20"> <span></span> search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6">
                    <div class="hero__thumb-wrapper scene ml-70">
                        <div class="hero__thumb one d-none d-lg-block">
                            <img class="layer" data-depth="0.2" src="https://themepure.net/template/markit/markit/assets/img/hero/hero-1.jpg" alt="">
                        </div>
                        <div class="hero__thumb two">
                            <img class="layer" data-depth="0.3" src="https://themepure.net/template/markit/markit/assets/img/hero/hero-2.jpg" alt="">
                        </div>
                        <div class="hero__thumb three">
                            <img class="layer" data-depth="0.4" src="https://themepure.net/template/markit/markit/assets/img/hero/hero-3.jpg" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- hero area end -->

    <!-- category area start -->
    <section class="category__area pt-105 pb-135">
        <div class="container">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="section__title-wrapper text-center mb-60">
                        <h2 class="section__title">Browse <br> Popular Categories</h2>
                        <p>Find over 70 software accounts and license keys.</p>
                    </div>
                </div>
            </div>
            <div class="row" id="category-list">
                <!-- Categories will be populated by JS -->
            </div>
        </div>
    </section>
    <!-- category area end -->

    <!-- trending area start -->
    <section class="trending__area pt-110 pb-110 grey-bg">
        <div class="container">
            <div class="row align-items-end">
                <div class="col-xxl-6 col-xl-6 col-lg col-md-8">
                    <div class="section__title-wrapper mb-50">
                        <h2 class="section__title">Trending <br> Landmark Software</h2>
                    </div>
                </div>
            </div>
            <div class="row" id="trending-list">
                <!-- Trending products will be populated by JS -->
            </div>
            <div class="row" id="trending-load-more" style="display: none;">
                <div class="col-xxl-12">
                    <div class="product__more text-center mt-30">
                        <a href="javascript:void(0);" id="t-load-more" class="m-btn m-btn-2"> <span></span> Load More </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- trending area end -->

    <!-- product area start -->
    <section class="product__area pt-105 pb-110 grey-bg-2">
        <div class="container">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="section__title-wrapper text-center mb-60">
                        <h2 class="section__title">Latest <br> Products</h2>
                        <p>From Digital Store</p>
                    </div>
                </div>
            </div>
            <div class="row" id="product-list">
                <!-- Products will be populated by JS -->
            </div>
            <div class="row" id="product-load-more" style="display: none;">
                <div class="col-xxl-12">
                    <div class="product__more text-center mt-30">
                        <a href="javascript:void(0);" id="p-load-more" class="m-btn m-btn-2"> <span></span> Load More </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- product area end -->
</main>

<!-- footer area start -->
<?php include("inc/footer.php"); ?>
<!-- footer area end -->
</body>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="assets/js/parallax.min.js"></script>
<script src="assets/js/wow.min.js"></script>
<script>
    // Initialize WOW.js for animations
    new WOW().init();

    // Load categories dynamically
    async function loadCategories() {
        const url = 'http://127.0.0.1:8000/api/public/categories?per_page=10';
        try {
            const response = await axios.get(url);
            const categories = response.data.data;
            const categoryList = document.getElementById('category-list');
            categoryList.innerHTML = '';
            categories.forEach((category, index) => {
                categoryList.innerHTML += `
                    <div class="col-xxl-4 col-xl-4 col-md-6 col-sm-6">
                        <div class="category__item transition-3 text-center white-bg mb-30 wow fadeInUp" data-wow-delay=".${(index + 3) * 2}s">
                            <div class="category__icon mb-25">
                                <a href="product.php?category=${category.id}">
                                    <img src="assets/img/icon/catagory/cat-${index + 1}.png" alt="${category.name}">
                                </a>
                            </div>
                            <div class="category__content">
                                <h3 class="category__title">
                                    <a href="product.php?category=${category.id}">${category.name}</a>
                                </h3>
                                <a href="product.php?category=${category.id}" class="link-btn">
                                    <i class="far fa-long-arrow-right"></i> Learn More
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            });
        } catch (error) {
            console.error('Failed to load categories:', error);
            alert(error.response?.data?.message || 'Failed to load categories');
        }
    }

    // Load trending products
    let tPerPage = 9;
    async function loadTrendingProducts() {
        const url = `http://127.0.0.1:8000/api/public/products?per_page=${tPerPage}`;
        try {
            const response = await axios.get(url);
            const products = response.data.data;
            const trendingList = document.getElementById('trending-list');
            trendingList.innerHTML = '';
            products.forEach((product, index) => {
                const price = product.price || 0;
                const displayPrice = price === 0 ? 'FREE!' : `$${price}`;
                const category = product.category?.name || 'Category';
                const categoryId = product.category?.id || '';
                trendingList.innerHTML += `
                    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6">
                        <div class="trending__item d-sm-flex white-bg mb-30 wow fadeInUp" data-wow-delay=".${(index + 3) * 2}s">
                            <div class="trending__thumb mr-25">
                                <div class="trending__thumb-inner fix">
                                    <a href="product-details.php?id=${product.id}">
                                        <img src="http://127.0.0.1:8000/storage/${product.image_url || 'placeholder.jpg'}" alt="${product.name}" class="product_img_102">
                                    </a>
                                </div>
                            </div>
                            <div class="trending__content">
                                <h3 class="trending__title"><a href="product-details.php?id=${product.id}">${product.name}</a></h3>
                                <p>Click to see full information.</p>
                                <div class="trending__meta d-flex justify-content-between">
                                    <div class="trending__tag"><a href="product.php?category=${categoryId}">${category}</a></div>
                                    <div class="trending__price"><span>${displayPrice}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            document.getElementById('trending-load-more').style.display = response.data.total > tPerPage ? 'block' : 'none';
        } catch (error) {
            console.error('Failed to load trending products:', error);
            alert(error.response?.data?.message || 'Failed to load trending products');
        }
    }

    // Load latest products
    let pPerPage = 6;
    async function loadProducts() {
        const url = `http://127.0.0.1:8000/api/public/products?per_page=${pPerPage}`;
        try {
            const response = await axios.get(url);
            const products = response.data.data;
            const productList = document.getElementById('product-list');
            productList.innerHTML = '';
            products.forEach((product, index) => {
                const price = product.price || 0;
                const displayPrice = price === 0 ? 'FREE!' : `$${price}`;
                const category = product.category?.name || 'Category';
                const categoryId = product.category?.id || '';
                productList.innerHTML += `
                    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6">
                        <div class="trending__item d-sm-flex white-bg mb-30 wow fadeInUp" data-wow-delay=".${(index + 3) * 2}s">
                            <div class="trending__thumb mr-25">
                                <div class="trending__thumb-inner fix">
                                    <a href="product-details.php?id=${product.id}">
                                        <img src="http://127.0.0.1:8000/storage/${product.image_url || 'placeholder.jpg'}" alt="${product.name}" class="product_img_102">
                                    </a>
                                </div>
                            </div>
                            <div class="trending__content">
                                <h3 class="trending__title"><a href="product-details.php?id=${product.id}">${product.name}</a></h3>
                                <p>Click to see full information.</p>
                                <div class="trending__meta d-flex justify-content-between">
                                    <div class="trending__tag"><a href="product.php?category=${categoryId}">${category}</a></div>
                                    <div class="trending__price"><span>${displayPrice}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            document.getElementById('product-load-more').style.display = response.data.total > pPerPage ? 'block' : 'none';
        } catch (error) {
            console.error('Failed to load products:', error);
            alert(error.response?.data?.message || 'Failed to load products');
        }
    }

    // Search functionality
    document.getElementById('search-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const query = document.getElementById('query').value;
        try {
            const response = await axios.get(`http://127.0.0.1:8000/api/search-products?query=${query}`);
            const products = response.data.data;
            const trendingList = document.getElementById('trending-list');
            trendingList.innerHTML = '';
            products.forEach((product, index) => {
                const price = product.price || 0;
                const displayPrice = price === 0 ? 'FREE!' : `$${price}`;
                const category = product.category?.name || 'Category';
                const categoryId = product.category?.id || '';
                trendingList.innerHTML += `
                    <div class="col-xxl-4 col-xl-4 col-lg-6 col-md-6">
                        <div class="trending__item d-sm-flex white-bg mb-30 wow fadeInUp" data-wow-delay=".${(index + 3) * 2}s">
                            <div class="trending__thumb mr-25">
                                <div class="trending__thumb-inner fix">
                                    <a href="product-details.php?id=${product.id}">
                                        <img src="http://127.0.0.1:8000/storage/${product.image_url || 'placeholder.jpg'}" alt="${product.name}" class="product_img_102">
                                    </a>
                                </div>
                            </div>
                            <div class="trending__content">
                                <h3 class="trending__title"><a href="product-details.php?id=${product.id}">${product.name}</a></h3>
                                <p>Click to see full information.</p>
                                <div class="trending__meta d-flex justify-content-between">
                                    <div class="trending__tag"><a href="product.php?category=${categoryId}">${category}</a></div>
                                    <div class="trending__price"><span>${displayPrice}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        } catch (error) {
            console.error('Search failed:', error);
            alert(error.response?.data?.message || 'Search failed');
        }
    });

    // Load more functionality
    document.getElementById('t-load-more').addEventListener('click', () => {
        tPerPage += 3;
        loadTrendingProducts();
    });

    document.getElementById('p-load-more').addEventListener('click', () => {
        pPerPage += 3;
        loadProducts();
    });

    // Initialize on load
    window.addEventListener('load', () => {
        loadCategories();
        loadTrendingProducts();
        loadProducts();
        const token = localStorage.getItem('token');
        if (token) {
            document.getElementById('login-link').innerHTML = '<i class="far fa-user"></i> Account';
            document.getElementById('login-link').href = 'account/index.php';
        }
    });
</script>
</html>