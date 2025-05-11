<?php
session_start();
require_once 'config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log('POST data received: ' . print_r($_POST, true));
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                if (isset($_POST['product_id'])) {
                    try {
                        $product_id = (int)$_POST['product_id'];
                        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
                        
                        error_log("Adding product ID: $product_id, Quantity: $quantity");
                        
                        // Get product details
                        $stmt = $pdo->prepare("SELECT id, name, price, image_url, stock FROM products WHERE id = ?");
                        $stmt->execute([$product_id]);
                        $product = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        error_log('Product details: ' . print_r($product, true));
                        
                        if (!$product) {
                            throw new Exception('Product not found');
                        }
                        
                        // Add to cart
                        if (isset($_SESSION['cart'][$product_id])) {
                            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                        } else {
                            $_SESSION['cart'][$product_id] = [
                                'id' => $product['id'],
                                'name' => $product['name'],
                                'price' => $product['price'],
                                'quantity' => $quantity,
                                'image_url' => $product['image_url'],
                                'stock' => $product['stock']
                            ];
                        }
                        
                        error_log('Cart after adding: ' . print_r($_SESSION['cart'], true));
                        
                        // Redirect to checkout if user is logged in
                        if (isset($_SESSION['user_id'])) {
                            header('Location: checkout.php');
                            exit();
                        }
                        
                    } catch (Exception $e) {
                        error_log('Error adding to cart: ' . $e->getMessage());
                        $_SESSION['message'] = [
                            'type' => 'error',
                            'text' => $e->getMessage()
                        ];
                    }
                }
                break;
                
            case 'update':
                if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
                    $product_id = (int)$_POST['product_id'];
                    $quantity = (int)$_POST['quantity'];
                    
                    if ($quantity < 1) {
                        unset($_SESSION['cart'][$product_id]);
                    } else {
                        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                    }
                }
                break;
                
            case 'clear':
                $_SESSION['cart'] = [];
                break;
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['remove'])) {
        $product_id = (int)$_GET['remove'];
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
}

// Calculate total
$total = 0;
$item_count = 0;
if (is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $item) {
        if (is_array($item) && isset($item['price']) && isset($item['quantity'])) {
            $total += (float)$item['price'] * (int)$item['quantity'];
            $item_count += (int)$item['quantity'];
        }
    }
}

// Get user info if logged in
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Debug information
error_log('Cart contents: ' . print_r($_SESSION['cart'], true));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - BanyumaSportHub</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <style>
        body {
            background-color: #e6f0f8;
        }
        .cart-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .cart-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            transition: all 0.3s ease;
        }
        .cart-item:hover {
            background-color: #f8f9fa;
        }
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        .quantity-control {
            width: 100px;
        }
        .btn-quantity {
            width: 28px;
            height: 28px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .btn-quantity:hover {
            background: #e9ecef;
        }
        .cart-summary {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .cart-empty {
            text-align: center;
            padding: 40px 20px;
        }
        .cart-empty i {
            font-size: 48px;
            color: #1e3a8a;
            margin-bottom: 20px;
        }
        .btn-checkout {
            background: #1e3a8a;
            border-color: #1e3a8a;
        }
        .btn-checkout:hover {
            background: #1e40af;
            border-color: #1e40af;
        }
        .price {
            color: #1e3a8a;
            font-weight: bold;
        }
        .stock-badge {
            background: #e6f0f8;
            color: #1e3a8a;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
        }
        .btn-outline-primary {
            color: #1e3a8a;
            border-color: #1e3a8a;
        }
        .btn-outline-primary:hover {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
            color: #fff;
        }
        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
    </style>
</head>
<body>
    <!-- Cart Section -->
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Shopping Cart</h2>
                
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['message']['text']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>
                
                <?php if (empty($_SESSION['cart'])): ?>
                    <div class="cart-container">
                        <div class="cart-empty">
                            <i class="bi bi-cart-x"></i>
                            <h4>Your cart is empty</h4>
                            <p class="text-muted">Looks like you haven't added any items to your cart yet.</p>
                            <a href="index.php#marketplace" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="cart-container">
                                <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                                    <?php if (is_array($item) && isset($item['name']) && isset($item['price']) && isset($item['quantity'])): ?>
                                        <div class="cart-item">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <img src="<?php echo htmlspecialchars($item['image_url'] ?? ''); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                         class="product-image">
                                                </div>
                                                <div class="col">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                    <span class="stock-badge">Stock: <?php echo $item['stock'] ?? 0; ?></span>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="price mb-2">
                                                        Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                                    </div>
                                                    <form action="" method="POST" class="d-flex align-items-center">
                                                        <input type="hidden" name="action" value="update">
                                                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                                        <div class="input-group quantity-control">
                                                            <button type="button" class="btn btn-quantity" 
                                                                    onclick="updateQuantity(this, -1)">-</button>
                                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" 
                                                                   min="1" max="<?php echo $item['stock'] ?? 999; ?>" 
                                                                   class="form-control text-center" onchange="this.form.submit()">
                                                            <button type="button" class="btn btn-quantity" 
                                                                    onclick="updateQuantity(this, 1)">+</button>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="price mb-2">
                                                        Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                                                    </div>
                                                    <a href="?remove=<?php echo $product_id; ?>" class="btn btn-sm btn-outline-danger" 
                                                       onclick="return confirm('Are you sure you want to remove this item?')">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="mt-3">
                                <form action="" method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="clear">
                                    <button type="submit" class="btn btn-outline-danger" 
                                            onclick="return confirm('Are you sure you want to clear your cart?')">
                                        <i class="bi bi-trash"></i> Clear Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 mt-4 mt-lg-0">
                            <div class="cart-summary">
                                <h5 class="card-title mb-4">Order Summary</h5>
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
                                <div class="d-grid gap-2">
                                    <a href="index.php#marketplace" class="btn btn-outline-primary">
                                        <i class="bi bi-arrow-left"></i> Continue Shopping
                                    </a>
                                    <?php if ($user): ?>
                                        <a href="checkout.php" class="btn btn-checkout text-white">
                                            Proceed to Checkout <i class="bi bi-arrow-right"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="login.php?redirect=checkout.php" class="btn btn-checkout text-white">
                                            Login to Checkout <i class="bi bi-arrow-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
    function updateQuantity(button, change) {
        const input = button.parentElement.querySelector('input');
        const currentValue = parseInt(input.value);
        const maxValue = parseInt(input.max);
        const newValue = currentValue + change;
        
        if (newValue >= 1 && newValue <= maxValue) {
            input.value = newValue;
            input.form.submit();
        }
    }
    </script>
</body>
</html> 