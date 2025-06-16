<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Handle product deletion
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    
    try {
        $pdo->beginTransaction();

        // First, delete related order items
        $stmt_delete_order_items = $pdo->prepare("DELETE FROM order_items WHERE product_id = ?");
        $stmt_delete_order_items->execute([$product_id]);

        // Then, delete the product
        $stmt_delete_product = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt_delete_product->execute([$product_id]);

        $pdo->commit();
        $_SESSION['success'] = "Product and related order items deleted successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error deleting product: " . $e->getMessage();
    }

    header('Location: products.php');
    exit();
}

// Get all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management - BanyumaSportHub</title>
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <style>
        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .product-image:hover {
            transform: scale(1.05);
        }
        .image-preview-modal .modal-dialog {
            max-width: 800px;
        }
        .image-preview-modal .modal-body {
            padding: 0;
        }
        .image-preview-modal img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
                    <h1 class="h2">Products Management</h1>
                    <a href="add-product.php" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add New Product
                    </a>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['success'];
                        unset($_SESSION['success']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php 
                        echo $_SESSION['error'];
                        unset($_SESSION['error']);
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td>
                                                <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                     class="product-image"
                                                     data-bs-toggle="modal"
                                                     data-bs-target="#imagePreviewModal"
                                                     data-image="../<?php echo htmlspecialchars($product['image_url']); ?>"
                                                     data-name="<?php echo htmlspecialchars($product['name']); ?>">
                                            </td>
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                            <td><?php echo $product['stock']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($product['created_at'])); ?></td>
                                            <td>
                                                <a href="edit-product.php?id=<?php echo $product['id']; ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this product? All associated order history for this product will also be deleted.');">
                                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                    <button type="submit" name="delete_product" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade image-preview-modal" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="" alt="" id="previewImage">
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize image preview modal
        document.addEventListener('DOMContentLoaded', function() {
            const imagePreviewModal = document.getElementById('imagePreviewModal');
            const modalTitle = imagePreviewModal.querySelector('.modal-title');
            const previewImage = imagePreviewModal.querySelector('#previewImage');
            
            // Add click event to all product images
            document.querySelectorAll('.product-image').forEach(img => {
                img.addEventListener('click', function() {
                    const imageUrl = this.getAttribute('data-image');
                    const productName = this.getAttribute('data-name');
                    
                    modalTitle.textContent = productName;
                    previewImage.src = imageUrl;
                    previewImage.alt = productName;
                });
            });
        });
    </script>
</body>
</html> 