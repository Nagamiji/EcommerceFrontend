<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Digital Store</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .thankyou-container {
            text-align: center;
            padding: 50px;
            font-family: 'Helvetica', 'Arial', sans-serif;
        }
        .thankyou-container h1 {
            color: #0070BA;
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        .thankyou-container p {
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 30px;
        }
        .thankyou-container a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0070BA;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .thankyou-container a:hover {
            background-color: #005EA6;
        }
    </style>
</head>
<body>
    <?php include 'inc/header.php'; ?>
    <div class="thankyou-container">
        <h1>Thank You for Your Purchase!</h1>
        <p>Your payment was successful. We appreciate your business!</p>
        <a href="index.php">Return to Home</a>
    </div>
    <?php include 'inc/footer.php'; ?>
</body>
</html>