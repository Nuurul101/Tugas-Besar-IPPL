<?php
session_start();
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Get user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Calculate total
$total = 0;
$item_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
    $item_count += $item['quantity'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // Create order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address, created_at) VALUES (?, ?, 'pending', ?, NOW())");
        $stmt->execute([
            $_SESSION['user_id'],
            $total,
            $_POST['shipping_address']
        ]);
        $order_id = $pdo->lastInsertId();

        // Create order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $stmt->execute([
                $order_id,
                $product_id,
                $item['quantity'],
                $item['price']
            ]);

            // Update product stock
            $stmt2 = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt2->execute([$item['quantity'], $product_id]);
        }

        $pdo->commit();

        // Clear cart and redirect to success page
        $_SESSION['cart'] = [];
        header('Location: order_success.php?order_id=' . $order_id);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'An error occurred while processing your order. Please try again.';
        error_log($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - BanyumaSportHub</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        body {
            background-color: #e6f0f8;
        }
        .checkout-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .order-summary {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .btn-checkout {
            background: #1e3a8a;
            border-color: #1e3a8a;
            width: 100%;
            padding: 12px;
            font-size: 16px;
            font-weight: 500;
        }
        .btn-checkout:hover {
            background: #1e40af;
            border-color: #1e40af;
        }
        .price {
            color: #1e3a8a;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8">
                <div class="checkout-container">
                    <h2 class="mb-4">Checkout</h2>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-4">
                            <h5>Shipping Information</h5>
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Shipping Address</label>
                                <textarea class="form-control" name="shipping_address" rows="3" required></textarea>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>Order Items</h5>
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?>
                                    </div>
                                    <div class="price">
                                        Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn btn-checkout text-white">Place Order</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="order-summary">
                    <h5 class="mb-4">Order Summary</h5>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Items (<?php echo $item_count; ?>)</span>
                        <span>Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping</span>
                        <span>Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total</strong>
                        <strong class="price">Rp <?php echo number_format($total, 0, ',', '.'); ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html> 