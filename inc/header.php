<?php
session_start();
require_once 'inc/config.php'; // Added to ensure API_BASE_URL is defined
require_once 'functions.php';
require_once 'inc/APIClient.php';

$auth = new auth();
$file = new files();

if (!$auth->isLogin()) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = rndmString(3);
        $_SESSION['user_type'] = 0;
    }
}

$user_id = $_SESSION['user_id'] ?? null;
$user_type = $_SESSION['user_type'] ?? 0;

// Fetch user name if logged in
$api = new APIClient();
$token = $_SESSION['token'] ?? $_COOKIE['token'] ?? null;
$user_name = 'Guest';
$is_logged_in = false;

if ($token) {
    $response = $api->callAPI("/profile", "GET", [], $token);
    if (isset($response['error'])) {
        error_log("Profile API Error: " . $response['error']);
    }
    if (isset($response['status_code']) && $response['status_code'] == 200 && isset($response['data']['email'])) {
        $_SESSION['user_name'] = $response['data']['email']; // Use email as name
        $user_name = htmlspecialchars($_SESSION['user_name']);
        $is_logged_in = true;
    } else {
        error_log("Profile API Response: " . json_encode($response));
        error_log("Token used: " . $token);
    }
} else {
    error_log("No token found. Session: " . json_encode($_SESSION));
}

$query = new query();
$shopAction = new shopAction();

$response = $api->callAPI("/public/categories");
$categories = $response['data'] ?? null;
$get_id = $_GET['id'] ?? null;

if (isset($_GET['action']) && $_GET['action'] === "checkout") {
    $order_hash = rndmString(13, "gd_order_");
} elseif (isset($_GET['action']) && $_GET['action'] === "logout") {
    $token = $_SESSION['token'] ?? $_COOKIE['token'] ?? null;
    $response = $api->callAPI("/logout", 'POST', [], $token) ?: [];
    if (($response['status_code'] ?? null) == 200) {
        $msg = $response['message'] ?? 'Logged out successfully';
        $msg_class = "alert-success";
        $st_msg = "Congratulations";
        session_destroy();
        $_SESSION = [];
        if (isset($_COOKIE['token'])) {
            setcookie('token', '', time() - 3600, '/');
        }
    }
}

if (isset($_GET['download'])) {
    $p_id = $_GET['download'];
    $file->download($p_id);
}

// Handle add-to-cart via GET
if (isset($_GET['add_to_cart'])) {
    $pid = (int) $_GET['add_to_cart'];
    $data = ['product_id' => $pid, 'quantity' => 1];
    $resp = $api->callAPI('/cart/add', 'POST', $data, $token) ?: [];
    if (($resp['status_code'] ?? 0) === 200) {
        $msg = 'Product added to your cart successfully';
        $msg_class = 'alert-success';
        $st_msg = 'Congratulations';
    } else {
        $msg = $resp['message'] ?? 'Failed to add product to cart';
        $msg_class = 'alert-danger';
        $st_msg = 'Sorry';
    }
}

// Handle remove-from-cart via GET
if (isset($_GET['remove_cart'])) {
    $pid = (int) $_GET['remove_cart'];
    $data = ['product_id' => $pid];
    $resp = $api->callAPI('/cart/remove', 'POST', $data, $token) ?: [];
    if (($resp['status_code'] ?? 0) === 200) {
        $msg = 'Product removed from your cart';
        $msg_class = 'alert-success';
        $st_msg = 'Done';
    } else {
        $msg = $resp['message'] ?? 'Failed to remove product from cart';
        $msg_class = 'alert-danger';
        $st_msg = 'Sorry';
    }
    header('Location: ' . $_SERVER['PHP_SELF'] . '?msg=msg');
    exit;
}

$token = $_SESSION['token'] ?? $_COOKIE['token'] ?? null;
// Fetch cart contents & compute badge count
$cartResp = $api->callAPI('/cart/view', 'GET', [], $token);
$cart_data = $cartResp['data'] ?? [];
$cart_count = array_sum(array_column($cart_data, 'quantity'));
?>

<!doctype html>
<html class="no-js" lang="zxx">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Digital Store</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/Online-store.png">
    <link rel="stylesheet" href="assets/css/preloader.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/meanmenu.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/backToTop.css">
    <link rel="stylesheet" href="assets/css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="assets/css/fontAwesome5Pro.css">
    <link rel="stylesheet" href="assets/css/elegantFont.css">
    <link rel="stylesheet" href="assets/css/default.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        .btn-outline-secondary {
            background-color: white;
            color: #6c757d;
            border-color: #6c757d;
        }
        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            color: #6c757d;
        }
        .dropdown-menu {
            background-color: white;
            border: 1px solid #dee2e6;
            display: none;
        }
        .dropdown-menu.show {
            display: block !important;
        }
        .dropdown-item {
            color: #212529;
            display: block;
        }
        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #212529;
        }
    </style>

    <script src="./assets/js/khqr-1.0.16.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>

<body>
    <!--[if lte IE 9]>
      <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
      <![endif]-->

    <!-- pre loader area start -->
    <div id="loading">
        <div id="loading-center">
            <div id="loading-center-absolute">
                <div class="object" id="object_one"></div>
                <div class="object" id="object_two" style="left:20px;"></div>
                <div class="object" id="object_three" style="left:40px;"></div>
                <div class="object" id="object_four" style="left:60px;"></div>
                <div class="object" id="object_five" style="left:80px;"></div>
            </div>
        </div>
    </div>
    <!-- pre loader area end -->

    <!-- back to top start -->
    <div class="progress-wrap">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>
    <!-- back to top end -->

    <!-- header area start -->
    <header>
        <div class="header__area white-bg" id="header-sticky">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-4 col-6">
                        <div class="logo">
                            <a href="index.php">
                                <img class="logo" src="assets/img/logo/Online-store.png" alt="logo">
                            </a>
                        </div>
                    </div>
                    <div class="col-xxl-8 col-xl-8 col-lg-8 d-none d-lg-block">
                        <div class="main-menu">
                            <nav id="mobile-menu">
                                <ul>
                                    <li class="has-dropdown- <?php echo ($get_id == null ? 'active' : ''); ?>">
                                        <a href="index.php">Home</a>
                                    </li>
                                    <?php
                                    if ($categories) {
                                        foreach ($categories as $key => $value) {
                                            $category_id = $value['id'] ?? null;
                                            $name = $value['name'] ?? 'category_name';
                                            echo '<li class="' . ($get_id == $category_id ? 'active' : '') . '"><a href="product.php?id=' . $value['id'] . '">' . $name . '</a></li>';
                                        }
                                    } else {
                                        echo '<li class="has-dropdown">
                                                <a href="product.php">Themes</a>
                                                <ul class="submenu">
                                                    <li><a href="product.php">Product</a></li>
                                                    <li><a href="product-details.php">Product Details</a></li>
                                                </ul>
                                            </li>
                                            <li><a href="product.php">HTML</a></li>
                                            <li class="has-dropdown">
                                                <a href="product.php">pages</a>
                                                <ul class="submenu">
                                                    <li><a href="about.php">About</a></li>
                                                    <li><a href="documentation.php">Documentation</a></li>
                                                    <li><a href="pricing.php">Pricing</a></li>
                                                    <li><a href="sign-up.php">Sign Up</a></li>
                                                    <li><a href="sign-in.php">Log In</a></li>
                                                </ul>
                                            </li>';
                                    }
                                    ?>
                                    <li><a href="contact.php" class="">Contact</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-xl-2 col-lg-2 col-md-8 col-6">
                        <div class="header__action d-flex align-items-center justify-content-end">
                            <div class="header__login d-none d-sm-block">
                                <?php if ($is_logged_in): ?>
                                  <div class="user-account d-flex align-items-center">
                                    <span class="mr-2">Welcome, <strong><?= htmlspecialchars($user_name, ENT_QUOTES) ?></strong></span>
                                    <div class="dropdown">
                                      <button
                                        class="btn btn-outline-secondary btn-sm dropdown-toggle"
                                        type="button"
                                        id="userMenuButton"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                      >
                                        <i class="far fa-user-circle"></i>
                                      </button>
                                      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userMenuButton">
                                        <a class="dropdown-item" href="?action=logout">
                                          <i class="fas fa-sign-out-alt mr-1"></i> Logout
                                        </a>
                                      </div>
                                    </div>
                                  </div>
                                <?php else: ?>
                                  <a href="sign-in.php" class="btn btn-outline-primary btn-sm">
                                    <i class="far fa-unlock mr-1"></i> Log In
                                  </a>
                                <?php endif; ?>
                            </div>
                            <div class="header__cart d-none d-sm-block">
                                <a href="javascript:void(0);" class="cart-toggle-btn">
                                    <i class="far fa-shopping-cart"></i>
                                    <span><?php echo $cart_count; ?></span>
                                </a>
                            </div>
                            <div class="sidebar__menu d-lg-none">
                                <div class="sidebar-toggle-btn" id="sidebar-toggle">
                                    <span class="line"></span>
                                    <span class="line"></span>
                                    <span class="line"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <?php
    if (isset($_GET['msg'])) {
        if ($_GET["msg"] != "msg") {
            $msg = $_GET["msg"];
            $msg_class = "alert-success";
        }

        if ($_GET['msg'] == "order successful") {
            $token = $_SESSION['token'] ?? $_COOKIE['token'] ?? null;
            $response = $api->callAPI("/cart/place-order", 'POST', [], $token);
            if ($response && $response['status_code'] === 200) {
                header("location: index.php?msg=msg");
                $_SESSION['msg'] = $msg = "Order successfully. Thank you for order, View more in your account.";
                $_SESSION['msg_class'] = $msg_class = "alert-success";
                $_SESSION['st_msg'] = $st_msg = "Congratulations";
            } else {
                header("location: index.php?msg=msg");
                $_SESSION['msg'] = $msg = "Order failed";
                $_SESSION['msg_class'] = $msg_class = "alert-danger";
                $_SESSION['st_msg'] = $st_msg = "Sorry";
            }
        }

        $st_msg = $st_msg ?? (isset($_SESSION['st_msg']) ? $_SESSION['st_msg'] : '');
        $msg_class = $msg_class ?? (isset($_SESSION['msg_class']) ? $_SESSION['msg_class'] : '');
        $msg = $msg ?? (isset($_SESSION['msg']) ? $_SESSION['msg'] : '');

        $html = "<div class=\"alert {$msg_class} alert-dismissible\">
            <button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>
            <strong>{$st_msg}!</strong> {$msg}
          </div>";
        echo $html;
    }
    ?>
    <script src="./assets/js/khqr-1.0.16.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>