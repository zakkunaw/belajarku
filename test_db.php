<?php

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Test database connection
try {
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3306;dbname=belajarku',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "✅ Database connection successful!\n";
    
    // Try to create tables if they don't exist
    $sql = "SHOW TABLES LIKE 'users'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "✅ Tables already exist\n";
    } else {
        echo "⚠️  Tables need to be created. Running migrations...\n";
        system('php artisan migrate --force');
    }
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
    echo "🔧 Trying to create database...\n";
    
    // Try to create database
    try {
        $pdo = new PDO(
            'mysql:host=127.0.0.1;port=3306',
            'root',
            '',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        $pdo->exec("CREATE DATABASE IF NOT EXISTS belajarku");
        echo "✅ Database 'belajarku' created successfully!\n";
        echo "🔧 Now running migrations...\n";
        system('php artisan migrate --force');
    } catch (PDOException $e2) {
        echo "❌ Failed to create database: " . $e2->getMessage() . "\n";
    }
}
