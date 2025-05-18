<?php
require('boss/wthfetch.php');
require 'vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Create Logger
function MyLog($string = null) {
    $log = new Logger('my_app');
    $log->pushHandler(new StreamHandler('logs/app.log', Logger::INFO));
    $log->info($string);
}

function rndmString($length = 5, $prefix = "", $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $prefix . $randomString;
}

class query {
    // Add a connect method (example using PDO)
    protected function connect() {
        try {
            $dsn = "mysql:host=localhost;dbname=your_database_name;charset=utf8mb4";
            $username = "root";
            $password = "";
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            MyLog("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    function fetchData($table, $fields = "*", $condition = "", $order = "", $sort = "", $limit = "") {
        try {
            $con = $this->connect();
            $q = "SELECT $fields FROM $table";
            if ($condition != "") {
                $q .= " WHERE $condition ";
            }
            if ($order != "" && $sort != "") {
                $q .= " ORDER BY $order $sort ";
            }
            if ($limit != "") {
                $q .= " LIMIT $limit ";
            }
            $stmt = $con->prepare($q);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            MyLog("Fetch data failed: " . $e->getMessage());
            return [];
        }
    }

    function insertData($table, $data) {
        try {
            $fields = array_keys($data);
            $placeholders = array_map(fn($field) => ":$field", $fields);
            $fieldsStr = "`" . implode("`,`", $fields) . "`";
            $placeholdersStr = implode(",", $placeholders);

            $con = $this->connect();
            $q = "INSERT INTO $table ($fieldsStr) VALUES ($placeholdersStr)";
            MyLog("Insert query: $q");
            $stmt = $con->prepare($q);
            $stmt->execute($data);
            return $con->lastInsertId();
        } catch (PDOException $e) {
            MyLog("Insert failed: " . $e->getMessage());
            throw new Exception("Insert failed: " . $e->getMessage());
        }
    }

    function dropData($table, $condition) {
        try {
            $con = $this->connect();
            $q = "DELETE FROM `$table` WHERE $condition";
            MyLog("Delete query: $q");
            $stmt = $con->prepare($q);
            $stmt->execute();
        } catch (PDOException $e) {
            MyLog("Delete failed: " . $e->getMessage());
            throw new Exception("Delete failed: " . $e->getMessage());
        }
    }

    function updateData($table, $data, $condition) {
        try {
            $con = $this->connect();
            $q = "UPDATE `$table` SET $data WHERE $condition";
            MyLog("Update query: $q");
            $stmt = $con->prepare($q);
            $stmt->execute();
        } catch (PDOException $e) {
            MyLog("Update failed: " . $e->getMessage());
            throw new Exception("Update failed: " . $e->getMessage());
        }
    }

    function getProducts($limit = "") {
        $url = 'http://127.0.0.1:8000/api/public/products' . ($limit ? "?per_page=$limit" : '');

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            MyLog("Get products failed: $error");
            return ['error' => $error];
        }

        $responseData = json_decode($response, true);
        curl_close($curl);

        if (!is_array($responseData)) {
            MyLog("Get products failed: Invalid JSON response");
            return ['error' => 'Invalid JSON response'];
        }

        if (isset($responseData['status_code']) && $responseData['status_code'] == 200) {
            return isset($responseData['data']) ? $responseData['data'] : [];
        }

        MyLog("Get products failed: " . json_encode($responseData));
        return [];
    }
}

class shopAction extends query {
    protected function getUserId() {
        // Use localStorage user_id (set by sign-in.php or sign-up.php)
        // Since this is PHP, we can't access localStorage directly
        // We'll rely on a cookie set during login/register
        return $_COOKIE['user_id'] ?? null;
    }

    function createOrder() {
        $query = new query();
        $user_id = $this->getUserId();
        if (!$user_id) {
            throw new Exception("User not logged in");
        }

        $user_cart = $query->fetchData("cart", "*", "user_id='$user_id'");
        $order_total = 0;
        foreach ($user_cart as $value) {
            $order_total += $value['price'];
        }
        $order_hash = rndmString(13, "gd_order_");
        $data = [
            "customer_id" => $user_id,
            "order_status" => "pending",
            "order_hash" => $order_hash,
            "order_total" => $order_total
        ];
        $order_id = $query->insertData("orders", $data);

        foreach ($user_cart as $value) {
            $data = [
                "customer_id" => $user_id,
                "parent_id" => $order_id,
                "product_id" => $value['product_id'],
                "price" => $value['price']
            ];
            $query->insertData("orders_meta", $data);
        }
        return $order_hash;
    }

    function checkout() {
        $auth = new auth();
        $user_id = $this->getUserId();
        if ($auth->isLogin() && $user_id) {
            $order_hash = $this->createOrder();
            header("location: razorpay/pay.php?order=$order_hash");
        } else {
            header("location: sign-in.php?gb=http://localhost:4000/index.php?action=checkout");
        }
    }

    function isPurchased($product_id) {
        $query = new query();
        $user_id = $this->getUserId();
        if (!$user_id) return false;

        $data = $query->fetchData("orders_meta", "parent_id", "customer_id='$user_id' AND product_id='$product_id'");
        if (empty($data)) return false;

        foreach ($data as $item) {
            $order_id = $item['parent_id'];
            $order_data = $query->fetchData("orders", "*", "ID='$order_id' AND customer_id='$user_id' AND (order_status='completed' OR order_status='wc-completed')");
            if (!empty($order_data)) return true;
        }
        return false;
    }

    function isPaid($order_id) {
        $order_status = $this->fetchData("orders", "payment_id,order_status", "ID='$order_id'");
        if (empty($order_status)) return false;

        $order_status = $order_status[0];
        return !($order_status['payment_id'] == "Pending" || $order_status['order_status'] != "completed" || $order_status['payment_id'] == "");
    }

    function minPrice($product_id) {
        $price = $this->fetchData('product_meta', "min_price", "product_id='$product_id'");
        return $price[0]['min_price'] ?? 0;
    }

    function orderData() {
        $user_id = $this->getUserId();
        if (!$user_id) return [];
        return $this->fetchData("orders", "*", "customer_id='$user_id'");
    }

    function orderProducts($order_id) {
        return $this->fetchData("orders_meta", "product_id", "parent_id='$order_id'");
    }

    function availableDownloads() {
        $user_id = $this->getUserId();
        if (!$user_id) return [];

        $order_data = $this->fetchData("orders_meta", "`product_id`,`parent_id`", "customer_id='$user_id'");
        $downloads = [];
        foreach ($order_data as $item) {
            if ($this->isPaid($item['parent_id'])) {
                $downloads[] = $item['product_id'];
            }
        }
        return $downloads;
    }

    function productTitleById($id) {
        $product = $this->fetchData("products", "product_title", "id='$id'");
        return $product[0]["product_title"] ?? '';
    }
}

class auth extends query {
    public function isLogin() {
        $token = $_SESSION['token'] ?? $_COOKIE['token'] ?? null;
        if ($token && isset($_SESSION['user_login'])) {
            return true;
        }
        return false;
    }

    function loginUser($email, $password) {
        $data = ["email" => $email, "password" => $password];
        $jsonData = json_encode($data);

        $url = 'http://127.0.0.1:8000/api/login';
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Content-Length: " . strlen($jsonData)
        ]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            $msg = curl_error($curl);
            MyLog("Login failed: $msg");
        } else {
            $responseData = json_decode($response, true);
            $res = (object)$responseData;

            if ($httpCode == 200) {
                $user_id = $res->user['id'] ?? 0;
                $token = $res->token ?? 'null';
                setcookie("user_id", $user_id, time() + (60*60));
                setcookie("token", $token, time() + (60*60));
                $msg = "login successful";
            } else {
                $msg = $res->message ?? 'Wrong email or password';
                MyLog("Login failed: $msg");
            }
        }

        curl_close($curl);
        return $msg;
    }

    function registerUser($data) {
        $jsonData = json_encode($data);

        $url = 'http://127.0.0.1:8000/api/register';
        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Content-Length: " . strlen($jsonData)
        ]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            $msg = curl_error($curl);
            MyLog("Register failed: $msg");
        } else {
            $responseData = json_decode($response, true);
            $res = (object)$responseData;

            if ($httpCode == 201) {
                $user_id = $res->user['id'] ?? 0;
                $token = $res->token ?? 'null';
                setcookie("user_id", $user_id, time() + (60*60));
                setcookie("token", $token, time() + (60*60));
                $msg = "register successful";
            } else {
                $msg = $res->message ?? 'register failed';
                MyLog("Register failed: $msg");
            }
        }

        curl_close($curl);
        return $msg;
    }

    function logedInuser() {
        if (!$this->isLogin()) {
            return [];
        }

        $token = $_COOKIE['token'] ?? '';
        $url = 'http://127.0.0.1:8000/api/user'; // Updated to local backend

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer {$token}"
        ]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            MyLog("Fetch user failed: $error");
            return ['error' => $error];
        }

        $responseData = json_decode($response, true);
        curl_close($curl);

        if (!is_array($responseData)) {
            MyLog("Fetch user failed: Invalid JSON response");
            return ['error' => 'Invalid JSON response'];
        }

        if (isset($responseData['status_code']) && $responseData['status_code'] == 200) {
            return isset($responseData['data']) ? $responseData['data'] : [];
        }

        MyLog("Fetch user failed: " . json_encode($responseData));
        return [];
    }

    function encPass($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    function verifyPass($pass, $hash) {
        return password_verify($pass, $hash);
    }

    function resetLink($email) {
        $key = rndmString(15, "gd_auth_");
        $this->updateData("users", "user_activation_key='$key'", "user_email='$email'");
    }

    function resetPass($key, $pass) {
        $validKey = $this->fetchData("users", "user_activation_key", "user_activation_key='$key'");
        if (!empty($validKey)) {
            $pass = $this->encPass($pass);
            $this->updateData("users", "user_pass='$pass'", "user_activation_key='$key'");
            return "Password updated successfully";
        }
        return "Password reset link is expired";
    }
}

class files extends shopAction {
    function download($product_id) {
        $query = new query();
        $auth = new auth();
        if ($auth->isLogin()) {
            if ($this->minPrice($product_id) == 0 || $this->isPurchased($product_id)) {
                $file = $query->fetchData("files", "url", "parent_id='$product_id'");
                if (!empty($file)) {
                    header("location: " . $file[0]['url']);
                    exit;
                } else {
                    return "File not found";
                }
            } else {
                return "This product is not purchased by you";
            }
        } else {
            return "You must be logged in to download the file";
        }
    }
}