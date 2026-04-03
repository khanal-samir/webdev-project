<?php
require_once 'auth.php';
require_once 'db.php';
requireLogin();

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare('SELECT available, quantity FROM books WHERE id = ?');
    $stmt->execute([$id]);
    $book = $stmt->fetch();
    
    if ($book) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('DELETE FROM borrowed_books WHERE book_id = ?');
            $stmt->execute([$id]);
            
            $stmt = $pdo->prepare('DELETE FROM books WHERE id = ?');
            $stmt->execute([$id]);
            
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
        }
    }
}

header('Location: dashboard.php');
exit;