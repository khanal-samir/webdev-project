<?php
require_once 'auth.php';
require_once 'db.php';
requireLogin();

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM books WHERE id = ?');
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: dashboard.php');
    exit;
}

$borrowedBooks = $pdo->prepare('SELECT * FROM borrowed_books WHERE book_id = ? ORDER BY borrow_date DESC');
$borrowedBooks->execute([$id]);
$borrowed = $borrowedBooks->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_id'])) {
    $returnId = $_POST['return_id'];
    
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('DELETE FROM borrowed_books WHERE id = ? AND book_id = ?');
        $stmt->execute([$returnId, $id]);
        
        $stmt = $pdo->prepare('UPDATE books SET available = available + 1 WHERE id = ?');
        $stmt->execute([$id]);
        
        $pdo->commit();
        header('Location: borrowed.php?id=' . $id);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Borrowed Books - Prithvi Narayan Campus Library</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <div class="header">
                <h1><img src="https://prnc.tu.edu.np/assets/logo.png" alt="PNC" style="height: 40px; vertical-align: middle; margin-right: 10px;">Borrowed Copy</h1>
                <div class="nav-links">
                    <a href="dashboard.php">← Back to Dashboard</a>
                </div>
            </div>
            
            <div class="book-info">
                <p><strong>Title:</strong> <?php echo htmlspecialchars($book['title']); ?></p>
                <p><strong>Semester:</strong> <?php echo htmlspecialchars($book['category']); ?></p>
                <p><strong>Total Copy:</strong> <?php echo $book['quantity']; ?></p>
                <p><strong>Available:</strong> <?php echo $book['available']; ?> | <strong>Borrowed:</strong> <?php echo $book['quantity'] - $book['available']; ?></p>
            </div>
            
            <?php if (empty($borrowed)): ?>
                <div class="empty-state">
                    <p>No copy currently borrowed</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Borrower Name</th>
                            <th>Date Borrowed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($borrowed as $i => $b): ?>
                            <tr>
                                <td><?php echo $i + 1; ?></td>
                                <td><?php echo htmlspecialchars($b['borrower_name']); ?></td>
                                <td><?php echo date('F j, Y \a\t g:i A', strtotime($b['borrow_date'])); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Confirm return?')">
                                        <input type="hidden" name="return_id" value="<?php echo $b['id']; ?>">
                                        <button type="submit" class="btn-small btn-success">Return</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>