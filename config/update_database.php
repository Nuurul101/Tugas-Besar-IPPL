<?php
require_once 'database.php';

try {
    // Add role column to users table if it doesn't exist
    $pdo->exec("
        ALTER TABLE users 
        ADD COLUMN IF NOT EXISTS role ENUM('user', 'admin') DEFAULT 'user'
    ");

    // Set existing users as regular users
    $pdo->exec("UPDATE users SET role = 'user' WHERE role IS NULL");

    // Create default admin user if it doesn't exist
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute(['admin@banyumasporthub.com']);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("
            INSERT INTO users (full_name, email, password, role) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            'Admin',
            'admin@banyumasporthub.com',
            password_hash('admin123', PASSWORD_DEFAULT),
            'admin'
        ]);
    }

    echo "Database updated successfully!";
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
} 