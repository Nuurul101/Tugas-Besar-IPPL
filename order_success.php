<?php
session_start();
require_once 'config/database.php';

// Check if order_id exists in URL
if (!isset($_GET['order_id'])) {
    header('Location: index.php');
    exit();
}

$order_id = $_GET['order_id'];

// Get order details
$stmt = $pdo->prepare("SELECT o.*, u.full_name, u.email 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       WHERE o.id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

// If order not found, redirect to home
if (!$order) {
    header('Location: index.php');
    exit();
}

// Get order items
$stmt = $pdo->prepare("SELECT oi.*, p.name, p.price 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - BanyumaSportHub</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .success-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .success-icon {
            font-size: 64px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .order-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .item-list {
            list-style: none;
            padding: 0;
        }
        .item-list li {
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .item-list li:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container text-center">
            <i class="bi bi-check-circle-fill success-icon"></i>
            <h1 class="mb-4">Order Success!</h1>
            <p class="lead">Thank you for your purchase. Your order has been received.</p>
            
            <div class="order-details text-start">
                <h4 class="mb-3">Order Details</h4>
                <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                <p><strong>Date:</strong> <?php echo date('d F Y H:i', strtotime($order['created_at'])); ?></p>
                <p><strong>Total Amount:</strong> Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></p>
                <p><strong>Status:</strong> <span class="badge bg-success">Paid</span></p>
                
                <h5 class="mt-4 mb-3">Items Ordered:</h5>
                <ul class="item-list">
                    <?php foreach ($items as $item): ?>
                    <li>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                <br>
                                <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                            </div>
                            <div>
                                Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="mt-4">
                <a href="index.php" class="btn btn-primary me-2">
                    <i class="bi bi-house-door"></i> Back to Home
                </a>
                <a href="orders.php" class="btn btn-outline-primary">
                    <i class="bi bi-list-ul"></i> View All Orders
                </a>
            </div>
        </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html> 