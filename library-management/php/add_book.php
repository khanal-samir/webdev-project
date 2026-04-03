<?php
require_once 'auth.php';
require_once 'db.php';
requireLogin();

$error = '';

$semesters = ['First Semester', 'Second Semester', 'Third Semester', 'Fourth Semester', 'Fifth Semester', 'Sixth Semester', 'Seventh Semester', 'Eighth Semester'];
$quantities = [1, 2, 3, 4, 5];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? 'First Semester');
    $cover_url = trim($_POST['cover_url'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 1);
    
    if (empty($title)) {
        $error = 'Title is required';
    } elseif ($quantity < 1 || $quantity > 5) {
        $error = 'Copy must be between 1 and 5';
    } else {
        $stmt = $pdo->prepare('INSERT INTO books (title, category, cover_url, quantity, available) VALUES (?, ?, ?, ?, ?)');
        if ($stmt->execute([$title, $category, $cover_url, $quantity, $quantity])) {
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Failed to add book';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Book - Prithvi Narayan Campus Library</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <div class="header">
                <h1><img src="https://prnc.tu.edu.np/assets/logo.png" alt="PNC" style="height: 40px; vertical-align: middle; margin-right: 10px;">Add New Book</h1>
                <div class="nav-links">
                    <a href="dashboard.php">← Back to Dashboard</a>
                </div>
            </div>
            
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" placeholder="Enter book title" required>
                </div>
                <div class="form-group">
                    <label>Semester</label>
                    <select name="category">
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?php echo $sem; ?>"><?php echo $sem; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Copy</label>
                    <select name="quantity">
                        <?php foreach ($quantities as $q): ?>
                            <option value="<?php echo $q; ?>" <?php echo $q == 1 ? 'selected' : ''; ?>><?php echo $q; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Cover Image URL</label>
                    <input type="text" name="cover_url" placeholder="https://example.com/cover.jpg">
                </div>
                <button type="submit">Add Book</button>
            </form>
        </div>
    </div>
    <script src="../js/script.js"></script>
</body>
</html>