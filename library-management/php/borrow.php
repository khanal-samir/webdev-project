<?php
require_once 'auth.php';
require_once 'db.php';
requireLogin();

$id = $_GET['id'] ?? null;
$error = '';
$book = null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM books WHERE id = ?');
    $stmt->execute([$id]);
    $book = $stmt->fetch();
    
    if (!$book || $book['available'] < 1) {
        header('Location: dashboard.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $borrower_name = trim($_POST['borrower_name'] ?? '');
    
    if (empty($borrower_name)) {
        $error = 'Borrower name is required';
    } else {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('INSERT INTO borrowed_books (book_id, borrower_name) VALUES (?, ?)');
            $stmt->execute([$id, $borrower_name]);
            
            $stmt = $pdo->prepare('UPDATE books SET available = available - 1 WHERE id = ?');
            $stmt->execute([$id]);
            
            $pdo->commit();
            header('Location: dashboard.php');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Failed to borrow book';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Borrow Book - Prithvi Narayan Campus Library</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <div class="header">
                <h1><img src="https://prnc.tu.edu.np/assets/logo.png" alt="PNC" style="height: 40px; vertical-align: middle; margin-right: 10px;">Borrow Book</h1>
                <div class="nav-links">
                    <a href="dashboard.php">← Back to Dashboard</a>
                </div>
            </div>
            
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            
            <?php if ($book): ?>
                <div class="book-info">
                    <p><strong>Title:</strong> <?php echo htmlspecialchars($book['title']); ?></p>
                    <p><strong>Semester:</strong> <?php echo htmlspecialchars($book['category']); ?></p>
                    <p><strong>Available Copy:</strong> <?php echo $book['available']; ?> / <?php echo $book['quantity']; ?></p>
                </div>
                
                <form method="POST" onsubmit="return verifyBorrow()">
                    <div class="form-group">
                        <label>Borrower Name *</label>
                        <input type="text" name="borrower_name" id="borrower_name" placeholder="Enter borrower's name" required autofocus>
                    </div>
                    <button type="submit" class="btn-success">Confirm Borrow</button>
                    <a href="dashboard.php" style="margin-left: 10px;">Cancel</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
    <script src="../js/script.js"></script>
    <script>
        function verifyBorrow() {
            var borrower = document.getElementById('borrower_name').value.trim();
            if (borrower === '') {
                alert('Please enter the borrower name.');
                return false;
            }
            return confirm('Confirm borrowing for: ' + borrower + '?');
        }
    </script>
</body>
</html>