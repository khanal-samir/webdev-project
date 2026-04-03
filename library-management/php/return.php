<?php
require_once 'auth.php';
require_once 'db.php';
requireLogin();

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare('UPDATE books SET status = "available", borrower_name = NULL, borrow_date = NULL WHERE id = ?');
    $stmt->execute([$id]);
}

header('Location: dashboard.php?success=returned');
exit;