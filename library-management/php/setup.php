<?php
require_once 'db.php';

try {
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password_hash TEXT NOT NULL
    )');
    
    $pdo->exec('CREATE TABLE IF NOT EXISTS books (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        category TEXT,
        cover_url TEXT,
        quantity INTEGER DEFAULT 1,
        available INTEGER DEFAULT 1,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )');
    
    $pdo->exec('CREATE TABLE IF NOT EXISTS borrowed_books (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        book_id INTEGER NOT NULL,
        borrower_name TEXT NOT NULL,
        borrow_date TEXT DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (book_id) REFERENCES books(id)
    )');
    
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
    $stmt->execute(['admin']);
    if ($stmt->fetchColumn() == 0) {
        $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
        $stmt->execute(['admin', $passwordHash]);
    }
    
    echo '<!DOCTYPE html>
<html>
<head>
    <title>Setup - Prithvi Narayan Campus Library</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="main-content" style="text-align: center; padding: 50px;">
            <h1 style="color: #27ae60;">Setup Complete!</h1>
            <p>Database has been initialized successfully.</p>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; display: inline-block; text-align: left;">
                <p><strong>Username:</strong> admin</p>
                <p><strong>Password:</strong> admin123</p>
            </div>
            <br>
            <a href="index.php" class="btn" style="margin-top: 20px;">Go to Login</a>
        </div>
    </div>
</body>
</html>';
} catch (PDOException $e) {
    die('Setup failed: ' . $e->getMessage());
}