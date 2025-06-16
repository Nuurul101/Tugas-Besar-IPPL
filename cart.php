<?php
session_start();
require_once 'config/database.php';

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle product removal via GET request
if (isset($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    error_log('Attempting to remove product. Product ID: ' . $product_id);
    error_log('Cart before remove: ' . print_r($_SESSION['cart'], true));

    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Produk berhasil dihapus dari keranjang.'
        ];
        error_log('Cart after successful remove: ' . print_r($_SESSION['cart'], true));
    } else {
        error_log('Product ID ' . $product_id . ' not found in cart.');
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Produk tidak ditemukan di keranjang.'
        ];
    }
    header('Location: cart.php');
    exit();
}

// Handle cart actions via POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
                    $product_id = $_POST['product_id'];
                    $quantity = (int)$_POST['quantity'];
                    
                    // Get product details including current stock
                    $stmt = $pdo->prepare("SELECT id, name, price, image_url, stock FROM products WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch();
                    
                    if ($product) {
                        $current_cart_quantity = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id]['quantity'] : 0;
                        $new_total_quantity = $current_cart_quantity + $quantity;
                        
                        if ($new_total_quantity <= $product['stock']) {
                            if (isset($_SESSION['cart'][$product_id])) {
                                $_SESSION['cart'][$product_id]['quantity'] = $new_total_quantity;
                            } else {
                                $_SESSION['cart'][$product_id] = array(
                                    'id' => $product['id'],
                                    'name' => $product['name'],
                                    'price' => $product['price'],
                                    'quantity' => $quantity,
                                    'image_url' => $product['image_url'],
                                    'stock' => $product['stock'] // Store available stock for client-side validation
                                );
                            }
                        } else {
                            $_SESSION['message'] = [
                                'type' => 'danger',
                                'text' => 'Jumlah yang diminta untuk ' . htmlspecialchars($product['name']) . ' melebihi stok yang tersedia. Stok saat ini: ' . $product['stock']
                            ];
                        }
                    }
                }
                break;
                
            case 'update':
                if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
                    $product_id = $_POST['product_id'];
                    $quantity = (int)$_POST['quantity'];
                    
                    // Get product details including current stock
                    $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch();
                    
                    if ($product) {
                        if ($quantity > 0 && $quantity <= $product['stock']) {
                            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                        } elseif ($quantity <= 0) {
                            unset($_SESSION['cart'][$product_id]);
                        } else {
                            $_SESSION['message'] = [
                                'type' => 'danger',
                                'text' => 'Jumlah yang diminta melebihi stok yang tersedia. Stok saat ini: ' . $product['stock']
                            ];
                        }
                    }
                }
                break;
                
            case 'remove':
                // This case is no longer strictly needed if using GET for removal
                // but keeping it won't hurt if there are other forms submitting this action
                if (isset($_POST['product_id'])) {
                    $product_id = $_POST['product_id'];
                    unset($_SESSION['cart'][$product_id]);
                    $_SESSION['message'] = [
                        'type' => 'success',
                        'text' => 'Produk berhasil dihapus dari keranjang (via POST).',
                    ];
                }
                break;
                
            case 'clear':
                $_SESSION['cart'] = array();
                $_SESSION['message'] = [
                    'type' => 'success',
                    'text' => 'Keranjang berhasil dikosongkan.',
                ];
                break;
        }
    }
}

// Calculate total
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Get user info if logged in
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Debug information
error_log('Cart contents at end of script: ' . print_r($_SESSION['cart'], true));
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
                                <form action="checkout.php" method="POST" id="checkoutForm">
                                <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                                    <?php if (is_array($item) && isset($item['name']) && isset($item['price']) && isset($item['quantity'])): ?>
                                        <div class="cart-item" data-product-id="<?php echo $product_id; ?>" data-price="<?php echo $item['price']; ?>" data-quantity="<?php echo $item['quantity']; ?>">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <input type="checkbox" name="selected_products[]" value="<?php echo $product_id; ?>" checked class="form-check-input me-2 product-checkbox">
                                                </div>
                                                <div class="col-auto">
                                                    <img src="<?php echo htmlspecialchars($item['image_url'] ?? ''); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                         class="product-image">
                                                </div>
                                                <div class="col">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                    <span class="stock-badge">Stock: <?php echo $item['stock']; ?></span>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="price mb-2">
                                                        Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="input-group quantity-control">
                                                            <button type="button" class="btn btn-quantity" 
                                                                    onclick="updateQuantity(this, -1)">-</button>
                                                            <input type="number" name="quantity_<?php echo $product_id; ?>" value="<?php echo $item['quantity']; ?>" 
                                                                   min="1" max="<?php echo $item['stock']; ?>" 
                                                                   class="form-control text-center product-quantity-input" onchange="updateOrderSummary();">
                                                            <button type="button" class="btn btn-quantity" 
                                                                    onclick="updateQuantity(this, 1)">+</button>
                                                        </div>
                                                    </div>
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
                                        <!-- Hidden input to carry quantity for selected products -->
                                        <input type="hidden" id="hidden_quantity_<?php echo $product_id; ?>" name="hidden_quantity_<?php echo $product_id; ?>" value="<?php echo $item['quantity']; ?>">
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                </form>
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
                                    <span id="summaryItemCount">Items (<?php echo count($_SESSION['cart']); ?>)</span>
                                    <span id="summaryTotalPrice">Rp <?php echo number_format($total, 0, ',', '.'); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Shipping</span>
                                    <span>Free</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-4">
                                    <strong>Total</strong>
                                    <strong class="price" id="summaryFinalTotal">Rp <?php echo number_format($total, 0, ',', '.'); ?></strong>
                                </div>
                                <div class="d-grid gap-2">
                                    <a href="index.php#marketplace" class="btn btn-outline-primary">
                                        <i class="bi bi-arrow-left"></i> Continue Shopping
                                    </a>
                                    <?php if ($user): ?>
                                        <button type="submit" form="checkoutForm" class="btn btn-checkout text-white">
                                            Proceed to Checkout <i class="bi bi-arrow-right"></i>
                                        </button>
                                    <?php else: ?>
                                        <a href="login.php?redirect=cart.php" class="btn btn-checkout text-white">
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
        const input = button.parentElement.querySelector('.product-quantity-input');
        const productId = input.name.split('_')[1];
        const hiddenInput = document.getElementById('hidden_quantity_' + productId);
        const currentValue = parseInt(input.value);
        const maxValue = parseInt(input.max);
        let newValue = currentValue + change;
        
        if (newValue < 1) newValue = 1;
        if (newValue > maxValue) newValue = maxValue;

        input.value = newValue;
        if (hiddenInput) {
            hiddenInput.value = newValue;
        }
        updateOrderSummary();
    }

    function updateOrderSummary() {
        let totalItems = 0;
        let totalPrice = 0;
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        const summaryItemCountSpan = document.getElementById('summaryItemCount');
        const summaryTotalPriceSpan = document.getElementById('summaryTotalPrice');
        const summaryFinalTotalSpan = document.getElementById('summaryFinalTotal');
        const checkoutButton = document.querySelector('button[form="checkoutForm"]');

        productCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const productId = checkbox.value;
                const cartItem = checkbox.closest('.cart-item');
                const price = parseFloat(cartItem.dataset.price);
                const quantityInput = cartItem.querySelector(`input[name="quantity_${productId}"]`);
                const quantity = parseInt(quantityInput.value) || 0;
                
                totalItems += quantity;
                totalPrice += (price * quantity);
            }
        });

        // Update summary display
        summaryItemCountSpan.textContent = `Items (${totalItems})`;
        summaryTotalPriceSpan.textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;
        summaryFinalTotalSpan.textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;

        // Enable/disable checkout button
        if (checkoutButton) {
            checkoutButton.disabled = totalItems === 0;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initial update
        updateOrderSummary();

        // Add event listeners for checkboxes
        document.querySelectorAll('.product-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updateOrderSummary);
        });

        // Add event listeners for quantity inputs
        document.querySelectorAll('.product-quantity-input').forEach(input => {
            input.addEventListener('change', updateOrderSummary);
            input.addEventListener('input', updateOrderSummary);
        });
    });
    </script>
</body>
</html> 