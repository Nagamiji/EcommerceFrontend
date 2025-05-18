<?php
// No need for session_start() here; it should be in header.php
include("header.php");

// Initialize APIClient
$api = new APIClient();
$token = $_SESSION['token'] ?? $_COOKIE['token'] ?? null;

// Fetch user data if token exists
$user = null;
if ($token) {
    $userResponse = $api->callAPI("/api/user", 'GET', [], $token);
    if (isset($userResponse['data']['user'])) {
        $user = $userResponse['data']['user'];
    } else {
        unset($_SESSION['token']);
        setcookie('token', '', time() - 3600, '/');
        $token = null;
    }
}

// Fetch orders if user is logged in
$orderData = [];
if ($token) {
    $response = $api->callAPI("/cart/orders", 'GET', [], $token);
    $orderData = $response['data'] ?? [];
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['token']);
    setcookie('token', '', time() - 3600, '/');
    header("Location: ../sign-in.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
    <!-- Root-relative paths -->
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/fontAwesome5Pro.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <!-- Breadcrumb Area -->
    <div class="breadcrumb-area section-padding-1 breadcrumb-ptb-1">
        <div class="container-fluid">
            <div class="breadcrumb-content text-center">
                <h2>My Account</h2>
            </div>
        </div>
    </div>

    <!-- My Account Area -->
    <div class="my-account-area pb-120">
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar Menu -->
                <div class="col-lg-3 col-md-4">
                    <div class="myaccount-tab-menu nav" role="tablist">
                        <a href="#dashboad" class="active" data-bs-toggle="tab">Dashboard</a>
                        <a href="#orders" data-bs-toggle="tab">Orders History</a>
                        <a href="#account-info" data-bs-toggle="tab">Account Details</a>
                        <a href="?action=logout">Logout</a>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="col-lg-9 col-md-8">
                    <div class="tab-content" id="myaccountContent">
                        <!-- Dashboard Tab -->
                        <div class="tab-pane fade show active" id="dashboad" role="tabpanel">
                            <div class="myaccount-content">
                                <h3>Dashboard</h3>
                                <div class="welcome">
                                    <?php if ($user): ?>
                                        <p>Hello, <strong><?php echo htmlspecialchars($user['name'] ?? 'Guest'); ?></strong> (If not <strong><?php echo htmlspecialchars($user['name'] ?? 'Guest'); ?>!</strong> <a href="?action=logout" class="logout">Logout</a>)</p>
                                    <?php else: ?>
                                        <p>Please <a href="../sign-in.php">log in</a> to view your dashboard.</p>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-0">From your account dashboard, you can easily check & view your recent orders,
                                    manage your shipping and billing addresses, and edit your password and account details.
                                </p>
                            </div>
                        </div>

                        <!-- Orders History Tab -->
                        <div class="tab-pane fade" id="orders" role="tabpanel">
                            <div class="myaccount-content">
                                <h3>Orders History</h3>
                                <div class="myaccount-table table-responsive text-center">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Order</th>
                                                <th>Product</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Price</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 1;
                                            if (!empty($orderData)) {
                                                foreach ($orderData as $key => $value) {
                                                    $date = !empty($value['ordered_at']) ? date("M d, Y", strtotime($value['ordered_at'])) : 'N/A';
                                                    $status = $value['order_status'] ?? 'Completed';
                                                    $price = number_format($value['price'] ?? 0, 2);
                                                    $order = $value['order_id'] ?? $i;
                                                    $formattedOrder = '#' . sprintf('%04d', $order);
                                                    $product = $value['product_name'] ?? 'N/A';
                                                    echo "<tr>
                                                            <td>{$formattedOrder}</td>
                                                            <td>{$product}</td>
                                                            <td>{$date}</td>
                                                            <td>{$status}</td>
                                                            <td>\${$price}</td>
                                                            <td><a href=\"claim.php?id={$i}\" class=\"check-btn sqr-btn\">View</a></td>
                                                        </tr>";
                                                    $i++;
                                                }
                                            } else {
                                                echo "<tr><td colspan='6'>No orders found.</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Account Details Tab -->
                        <div class="tab-pane fade" id="account-info" role="tabpanel">
                            <div class="myaccount-content">
                                <h3>Account Details</h3>
                                <div class="account-details-form">
                                    <form id="account-details-form">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="single-input-item">
                                                    <label for="first-name" class="required">First Name</label>
                                                    <input type="text" id="first-name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="single-input-item">
                                                    <label for="last-name" class="required">Last Name</label>
                                                    <input type="text" id="last-name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="single-input-item">
                                            <label for="display-name" class="required">Display Name</label>
                                            <input type="text" id="display-name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" />
                                        </div>
                                        <div class="single-input-item">
                                            <label for="email" class="required">Email Address</label>
                                            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly />
                                        </div>
                                        <fieldset>
                                            <legend>Password Change</legend>
                                            <div class="single-input-item">
                                                <label for="current-pwd" class="required">Current Password</label>
                                                <input type="password" id="current-pwd" />
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="single-input-item">
                                                        <label for="new-pwd" class="required">New Password</label>
                                                        <input type="password" id="new-pwd" />
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="single-input-item">
                                                        <label for="confirm-pwd" class="required">Confirm Password</label>
                                                        <input type="password" id="confirm-pwd" />
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <div class="single-input-item">
                                            <button type="submit" class="check-btn sqr-btn">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Vendor, plugins JS -->
    <script src="/assets/js/vendor/modernizr-3.11.7.min.js"></script>
    <script src="/assets/js/vendor/jquery-3.5.1.min.js"></script> <!-- Corrected from jquery.min.js -->
    <script src="/assets/js/vendor/jquery-migrate-3.3.2.min.js"></script>
    <script src="/assets/js/vendor/popper.min.js"></script>
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/plugins/select2.min.js"></script>
    <script src="/assets/js/plugins/jquery.nice-select.min.js"></script>
    <script src="/assets/js/plugins/wow.min.js"></script>
    <script src="/assets/js/plugins/scrollup.js"></script>
    <script src="/assets/js/plugins/swiper.min.js"></script>
    <script src="/assets/js/plugins/waypoints.min.js"></script>
    <script src="/assets/js/plugins/counterup.js"></script>
    <script src="/assets/js/plugins/smoothscroll.js"></script>
    <script src="/assets/js/plugins/mouse-parallax.js"></script>
    <script src="/assets/js/plugins/slinky.min.js"></script>
    <script src="/assets/js/plugins/easyzoom.js"></script>
    <script src="/assets/js/plugins/magnific-popup.js"></script>
    <script src="/assets/js/plugins/images-loaded.js"></script>
    <script src="/assets/js/plugins/isotope.js"></script>
    <script src="/assets/js/plugins/jquery-ui.js"></script>
    <script src="/assets/js/plugins/jquery-ui-touch-punch.js"></script>
    <script src="/assets/js/plugins/jquery.meanmenu.js"></script>
    <script src="/assets/js/plugins/jquery.mb.ytplayer.min.js"></script>
    <script src="/assets/js/plugins/ajax-mail.js"></script>
    <script src="/assets/js/main.js"></script>

    <!-- JavaScript to Handle Account Details Update -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        document.getElementById('account-details-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const token = '<?php echo addslashes($token); ?>';
            if (!token) {
                alert('Please log in to update your details.');
                window.location.href = '../sign-in.php';
                return;
            }

            const firstName = document.getElementById('first-name').value;
            const lastName = document.getElementById('last-name').value;
            const displayName = document.getElementById('display-name').value;
            const currentPassword = document.getElementById('current-pwd').value;
            const newPassword = document.getElementById('new-pwd').value;
            const confirmPassword = document.getElementById('confirm-pwd').value;

            if (newPassword && newPassword !== confirmPassword) {
                alert('New password and confirm password do not match.');
                return;
            }

            try {
                const data = {
                    first_name: firstName,
                    last_name: lastName,
                    name: displayName
                };
                if (newPassword) {
                    data.current_password = currentPassword;
                    data.password = newPassword;
                }

                const response = await axios.put('http://127.0.0.1:8000/api/user', data, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                alert('Account details updated successfully!');
                window.location.reload();
            } catch (error) {
                console.error('Update Error:', error.response || error.message);
                alert(error.response?.data?.message || 'Failed to update account details.');
            }
        });
    </script>
</body>
</html>