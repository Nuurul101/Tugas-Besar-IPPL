<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get user details
$stmt = $pdo->prepare("
    SELECT u.*, 
           COUNT(DISTINCT o.id) as total_orders,
           SUM(o.total_amount) as total_spent,
           MAX(o.created_at) as last_order_date
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id
    WHERE u.id = ?
    GROUP BY u.id
");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: users.php');
    exit();
}

// Get user's order history
$stmt = $pdo->prepare("
    SELECT o.*, 
           COUNT(oi.id) as total_items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details - BanyumaSportHub</title>
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">User Details</h1>
                    <a href="users.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Users
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">User Information</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                                <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>
                                <p><strong>Role:</strong> 
                                    <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </p>
                                <p><strong>Joined:</strong> <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Order Statistics</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Total Orders:</strong> <?php echo $user['total_orders']; ?></p>
                                <p><strong>Total Spent:</strong> 
                                    <?php if ($user['total_spent']): ?>
                                        Rp <?php echo number_format($user['total_spent'], 0, ',', '.'); ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </p>
                                <p><strong>Last Order:</strong> 
                                    <?php if ($user['last_order_date']): ?>
                                        <?php echo date('M d, Y', strtotime($user['last_order_date'])); ?>
                                    <?php else: ?>
                                        No orders yet
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Order History</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($orders): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td>#<?php echo $order['id']; ?></td>
                                                <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                                <td><?php echo $order['total_items']; ?> items</td>
                                                <td>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo match($order['status']) {
                                                            'pending' => 'warning',
                                                            'processing' => 'info',
                                                            'shipped' => 'primary',
                                                            'delivered' => 'success',
                                                            'cancelled' => 'danger',
                                                            default => 'secondary'
                                                        };
                                                    ?>">
                                                        <?php echo ucfirst($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="order-details.php?id=<?php echo $order['id']; ?>" 
                                                       class="btn btn-sm btn-primary">
                                                        <i class="bi bi-eye"></i> View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">No orders found for this user.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html> 