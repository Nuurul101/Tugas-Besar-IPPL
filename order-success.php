<?php
session_start();
require_once 'config/database.php';

if (!isset($_GET['order_id'])) {
    header('Location: marketplace.php');
    exit();
}

$order_id = $_GET['order_id'];

// Get order details
$stmt = $pdo->prepare("
    SELECT o.*, u.full_name, u.email, u.phone 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: marketplace.php');
    exit();
}

// Get order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name, p.image_url 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$order_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - BanyumaSportHub</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container mt-5">
        <div class="text-center mb-4">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            <h2 class="mt-3">Order Successful!</h2>
            <p class="lead">Thank you for your purchase. Your order has been received.</p>
        </div>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4>Order Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5>Order Information</h5>
                                <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                                <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                                <p><strong>Status:</strong> <span class="badge bg-primary"><?php echo ucfirst($order['status']); ?></span></p>
                            </div>
                            <div class="col-md-6">
                                <h5>Customer Information</h5>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                            </div>
                        </div>

                        <h5>Order Items</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td>
                                                <img src="<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>" style="width: 50px;">
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </td>
                                            <td><?php echo $item['quantity']; ?></td>
                                            <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                            <td>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td><strong>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="text-center mt-4">
                            <a href="marketplace.php" class="btn btn-primary">Continue Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html> 