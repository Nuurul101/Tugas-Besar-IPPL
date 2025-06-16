<?php
session_start();
require_once 'config/database.php';

// Debugging: Log POST data received by checkout.php
error_log('Checkout.php received POST data: ' . print_r($_POST, true));

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

$items_to_checkout = [];
$source = ''; // To track if checkout is from direct purchase or cart

// Try to load items from session first (for page refreshes or final form submission)
if (isset($_SESSION['checkout_items']) && !empty($_SESSION['checkout_items'])) {
    $items_to_checkout = $_SESSION['checkout_items'];
    $source = $_SESSION['checkout_source'] ?? ''; // Retain the original source
} else {
    // If no items in session, determine from current request
    // Handle direct checkout from product page (GET request)
    if (isset($_GET['product_id']) && isset($_GET['quantity'])) {
        $product_id = (int)$_GET['product_id'];
        $quantity = (int)$_GET['quantity'];
        $source = 'direct';

        // Get product details
        $stmt = $pdo->prepare("SELECT id, name, price, image_url, stock FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product && $quantity > 0 && $quantity <= $product['stock']) {
            $items_to_checkout[$product_id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity,
                'image_url' => $product['image_url'],
                'stock' => $product['stock']
            ];
        } else {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Produk tidak valid atau jumlah melebihi stok yang tersedia.'
            ];
            header('Location: index.php');
            exit();
        }
    } 
    // Handle cart checkout (POST request from cart.php)
    else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_products'])) {
        $selected_product_ids = $_POST['selected_products'];
        $source = 'cart';

        if (empty($_SESSION['cart'])) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Keranjang Anda kosong.'
            ];
            header('Location: cart.php');
            exit();
        }

        foreach ($selected_product_ids as $product_id) {
            // Get quantity from the hidden input sent by cart.php
            $quantity_key = 'hidden_quantity_' . $product_id;
            $selected_quantity = isset($_POST[$quantity_key]) ? (int)$_POST[$quantity_key] : 0;

            if (isset($_SESSION['cart'][$product_id]) && $selected_quantity > 0) {
                $item_from_session = $_SESSION['cart'][$product_id];
                $items_to_checkout[$product_id] = [
                    'id' => $item_from_session['id'],
                    'name' => $item_from_session['name'],
                    'price' => $item_from_session['price'],
                    'quantity' => $selected_quantity,
                    'image_url' => $item_from_session['image_url'],
                    'stock' => $item_from_session['stock']
                ];
            }
        }

        if (empty($items_to_checkout)) {
            $_SESSION['message'] = [
                'type' => 'danger',
                'text' => 'Tidak ada produk yang dipilih untuk checkout atau produk tidak valid/kuantitas nol.'
            ];
            header('Location: cart.php');
            exit();
        }
    }
}

// If after all attempts, no items are set for checkout, redirect
if (empty($items_to_checkout)) {
    $_SESSION['message'] = [
        'type' => 'danger',
        'text' => 'Tidak ada produk untuk diproses. Silakan pilih produk dari keranjang atau langsung dari halaman produk.'
    ];
    header('Location: cart.php');
    exit();
}

// Store items to checkout in session for subsequent POST requests (e.g., final order placement)
// This ensures items are persisted across refreshes or the final form submission.
$_SESSION['checkout_items'] = $items_to_checkout;
$_SESSION['checkout_source'] = $source;

// Get user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Calculate total for items to checkout
$total = 0;
$item_count = 0;
foreach ($items_to_checkout as $item) {
    $total += $item['price'] * $item['quantity'];
    $item_count += $item['quantity'];
}

// Handle order placement (POST request from checkout form itself)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shipping_address'])) {
    // Debugging: Log items_to_checkout right before processing
    error_log('Checkout.php - items_to_checkout before order processing: ' . print_r($items_to_checkout, true));

    try {
        $pdo->beginTransaction();

        // Validate stock for all items to checkout before proceeding
        foreach ($items_to_checkout as $product_id => $item) {
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product_db = $stmt->fetch();

            if (!$product_db || $product_db['stock'] < $item['quantity']) {
                throw new Exception("Stok tidak mencukupi untuk produk: " . htmlspecialchars($item['name']) . ". Stok tersedia: " . ($product_db['stock'] ?? 0));
            }
        }

        // Create order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status, shipping_address, created_at) VALUES (?, ?, 'pending', ?, NOW())");
        $stmt->execute([
            $_SESSION['user_id'],
            $total,
            $_POST['shipping_address']
        ]);
        $order_id = $pdo->lastInsertId();

        // Create order items and update product stock for items to checkout
        $stmt_order_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt_update_stock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

        foreach ($items_to_checkout as $product_id => $item) {
            // Insert order item
            $stmt_order_item->execute([
                $order_id,
                $product_id,
                $item['quantity'],
                $item['price']
            ]);

            // Update product stock
            $stmt_update_stock->execute([$item['quantity'], $product_id]);
        }

        $pdo->commit();

        // Remove only the purchased items from the cart if checkout originated from cart
        if ($source === 'cart') {
            foreach ($items_to_checkout as $product_id => $item) {
                unset($_SESSION['cart'][$product_id]);
            }
        }

        // Clear the stored checkout items from session
        unset($_SESSION['checkout_items']);
        unset($_SESSION['checkout_source']);

        header('Location: order_success.php?order_id=' . $order_id);
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => $e->getMessage()
        ];
        header('Location: checkout.php');
        exit();
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
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Shipping Address</label>
                                <textarea class="form-control" name="shipping_address" rows="3" required><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5>Order Items</h5>
                            <?php foreach ($items_to_checkout as $item): ?>
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