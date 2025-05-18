<?php require('inc/header.php'); ?>
<!-- header area end -->

<!-- cart mini area start -->
<?php require('inc/cart.php'); ?>
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
    <!-- bg shape area start -->
    <div class="bg-shape">
        <img src="assets/img/shape/shape-1.png" alt="">
    </div>
    <!-- bg shape area end -->

    <!-- sign in area start -->
    <section class="signup__area po-rel-z1 pt-100 pb-145">
        <div class="sign__shape">
            <img class="man-1" src="assets/img/icon/sign/man-1.png" alt="">
            <img class="man-2" src="assets/img/icon/sign/man-2.png" alt="">
            <img class="circle" src="assets/img/icon/sign/circle.png" alt="">
            <img class="zigzag" src="assets/img/icon/sign/zigzag.png" alt="">
            <img class="dot" src="assets/img/icon/sign/dot.png" alt="">
            <img class="bg" src="assets/img/icon/sign/sign-up.png" alt="">
        </div>
        <div class="container">
            <div class="row">
                <div class="col-xxl-8 offset-xxl-2 col-xl-8 offset-xl-2">
                    <div class="page__title-wrapper text-center mb-55">
                        <h2 class="page__title-2">Sign in to <br> recharge direct.</h2>
                        <p>if you don't have an account you can <a href="sign-up.php">Register here!</a></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xxl-6 offset-xxl-3 col-xl-6 offset-xl-3 col-lg-8 offset-lg-2">
                    <div class="sign__wrapper white-bg">
                        <div class="sign__header mb-35">
                            <div class="sign__in text-center">
                                <p id="sign-in-message"><span>........</span> <a href="sign-in.php">sign in</a> with your email<span> ........</span></p>
                            </div>
                        </div>
                        <div class="sign__form">
                            <form id="login-form">
                                <div class="sign__input-wrapper mb-25">
                                    <h5>Work email</h5>
                                    <div class="sign__input">
                                        <input type="email" name="email" id="email" placeholder="e-mail address" required>
                                        <i class="fal fa-envelope"></i>
                                    </div>
                                </div>
                                <div class="sign__input-wrapper mb-10">
                                    <h5>Password</h5>
                                    <div class="sign__input">
                                        <input type="password" name="password" id="password" placeholder="Password" required>
                                        <i class="fal fa-lock"></i>
                                    </div>
                                </div>
                                <div class="sign__action d-sm-flex justify-content-between mb-30">
                                    <div class="sign__agree d-flex align-items-center">
                                        <input class="m-check-input" type="checkbox" id="m-agree">
                                        <label class="m-check-label" for="m-agree">Keep me signed in</label>
                                    </div>
                                    <div class="sign__forgot">
                                        <a href="#">Forgot your password?</a>
                                    </div>
                                </div>
                                <button type="submit" class="m-btn m-btn-4 w-100"> <span></span> Sign In</button>
                                <div class="sign__new text-center mt-20">
                                    <p>New to Markit? <a href="sign-up.php">Sign Up</a></p>
                                </div>
                            </form>
                            <div id="error-message" class="text-danger mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- sign in area end -->
</main>

<!-- footer area start -->
<?php include("inc/footer.php"); ?>
<!-- footer area end -->
</body>
<script src="assets/js/vendor/jquery-3.5.1.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.getElementById('login-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await axios.post('http://127.0.0.1:8000/api/login', { email, password }, {
                timeout: 8000 // 5 seconds timeout
            });
            console.log('Login Response:', response.data);
            const token = response.data.token || response.data.access_token;
            if (!token) {
                throw new Error('Token not found in response: ' + JSON.stringify(response.data));
            }
            localStorage.setItem('token', token);
            const userId = response.data.user?.id || '';
            localStorage.setItem('user_id', userId);
            document.cookie = `token=${token}; path=/; max-age=${60*60}`;
            document.cookie = `user_id=${userId}; path=/; max-age=${60*60}`;
            document.getElementById('login-link').innerHTML = '<i class="far fa-user"></i> Account';
            document.getElementById('sign-in-message').innerHTML = '<span>........</span> Login successful <span>........</span>';
            setTimeout(() => window.location.href = 'index.php', 1000);
        } catch (error) {
            console.error('Login Error:', error.response || error.message || error);
            const errorMsg = error.response?.data?.message || error.message || 'Login failed. Please ensure the backend server is running at http://127.0.0.1:8000.';
            document.getElementById('error-message').innerText = errorMsg;
        }
    });

    window.addEventListener('load', () => {
        const token = localStorage.getItem('token');
        if (token) {
            document.getElementById('login-link').innerHTML = '<i class="far fa-user"></i> Account';
            document.getElementById('login-link').href = 'account/index.php';
        }
    });
</script>
</html>