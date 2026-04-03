<?php
require_once 'auth.php';
require_once 'db.php';
requireLogin();

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: dashboard.php');
    exit;
}

$semesters = ['First Semester', 'Second Semester', 'Third Semester', 'Fourth Semester', 'Fifth Semester', 'Sixth Semester', 'Seventh Semester', 'Eighth Semester'];
$quantities = [1, 2, 3, 4, 5];

$stmt = $pdo->prepare('SELECT * FROM books WHERE id = ?');
$stmt->execute([$id]);
$book = $stmt->fetch();

if (!$book) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $pdo->prepare('SELECT COUNT(*) FROM borrowed_books WHERE book_id = ?');
$stmt->execute([$id]);
$borrowedCount = $stmt->fetchColumn();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'First Semester');
    $cover_url = trim($_POST['cover_url'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 1);
    
    if (empty($title)) {
        $error = 'Title is required';
    } elseif ($quantity < 1 || $quantity > 5) {
        $error = 'Copy must be between 1 and 5';
    } elseif ($quantity < $borrowedCount) {
        $error = "Cannot set copy below $borrowedCount (currently borrowed)";
    } else {
        $available = $quantity - $borrowedCount;
        $stmt = $pdo->prepare('UPDATE books SET title = ?, category = ?, cover_url = ?, quantity = ?, available = ? WHERE id = ?');
        if ($stmt->execute([$title, $category, $cover_url, $quantity, $available, $id])) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Failed to update book';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Book - Prithvi Narayan Campus Library</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <div class="header">
                <h1><img src="https://prnc.tu.edu.np/assets/logo.png" alt="PNC" style="height: 40px; vertical-align: middle; margin-right: 10px;">Edit Book</h1>
                <div class="nav-links">
                    <a href="dashboard.php">← Back to Dashboard</a>
                </div>
            </div>
            
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            
            <div class="book-info">
                <p><strong>Borrowed:</strong> <?php echo $borrowedCount; ?> | <strong>Available:</strong> <?php echo $book['available']; ?></p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Semester</label>
                    <select name="category">
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?php echo $sem; ?>" <?php echo $book['category'] === $sem ? 'selected' : ''; ?>><?php echo $sem; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Copy</label>
                    <select name="quantity">
                        <?php foreach ($quantities as $q): ?>
                            <option value="<?php echo $q; ?>" <?php echo $book['quantity'] == $q ? 'selected' : ''; ?> <?php echo $q < $borrowedCount ? 'disabled' : ''; ?>><?php echo $q; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small style="color: #666; display: block; margin-top: 5px;">
                        Min: <?php echo max($borrowedCount, 1); ?> (<?php echo $borrowedCount; ?> currently borrowed)
                    </small>
                </div>
                <div class="form-group">
                    <label>Cover Image URL</label>
                    <input type="text" name="cover_url" value="<?php echo htmlspecialchars($book['cover_url']); ?>">
                </div>
                <button type="submit">Update Book</button>
            </form>
        </div>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>