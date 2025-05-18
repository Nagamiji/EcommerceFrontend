<?php
session_start();
require("../functions.php");

$auth = new auth();
$api = new APIClient();
$token = $_SESSION['token'] ?? $_COOKIE['token'] ?? null;

// Fetch user data if token exists but not logged in
if ($token && !$auth->isLogin()) {
    $userResponse = $api->callAPI("/user", 'GET', [], $token);
    if (isset($userResponse['data']['user'])) {
        $_SESSION['user_login'] = $userResponse['data']['user']['name'] ?? 'User';
        $_SESSION['user_id'] = $userResponse['data']['user']['id'] ?? null;
    } else {
        unset($_SESSION['token']);
        setcookie('token', '', time() - 3600, '/');
    }
}

// Redirect to sign-in if not logged in
if (!$auth->isLogin()) {
    header("Location: ../sign-in.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Digital Store</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/slick.css">
    <link rel="stylesheet" href="/assets/css/meanmenu.css">
    <link rel="stylesheet" href="/assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="/assets/css/animate.min.css">
    <link rel="stylesheet" href="/assets/css/backToTop.css">
    <link rel="stylesheet" href="/assets/css/jquery.fancybox.min.css">
    <link rel="stylesheet" href="/assets/css/fontAwesome5Pro.css">
    <link rel="stylesheet" href="/assets/css/elegantFont.css">
    <link rel="stylesheet" href="/assets/css/default.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Header Content (e.g., navigation, logo) -->
    <header>
        <div class="header-top">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="header-contact">
                            <ul>
                                <li><i class="far fa-envelope"></i> support@digitalstore.com</li>
                                <li><i class="far fa-phone"></i> +1-123-456-7890</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="header-action">
                            <ul>
                                <li><a href="/account">My Account</a></li>
                                <li><a href="?action=logout">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-2">
                        <div class="logo">
                            <a href="/index.php"><img src="/assets/img/logo/Online-store.png" alt="Logo"></a>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <!-- Navigation Menu (if any) -->
                    </div>
                    <div class="col-lg-3">
                        <div class="header-action">
                            <a href="cart.php" class="cart-toggle-btn"><i class="far fa-shopping-cart"></i> <span id="cart-count">0</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</body>
</html>