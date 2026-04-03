<?php
require_once 'auth.php';
require_once 'db.php';
requireLogin();

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';

$query = 'SELECT * FROM books WHERE 1=1';
$params = [];

if ($search) {
    $query .= ' AND title LIKE ?';
    $params[] = "%$search%";
}

if ($category) {
    $query .= ' AND category = ?';
    $params[] = $category;
}

if ($status === 'available') {
    $query .= ' AND available > 0';
} elseif ($status === 'borrowed') {
    $query .= ' AND available < quantity';
}

$query .= ' ORDER BY created_at DESC';

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll();

$totalBooks = $pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
$totalAvailable = $pdo->query('SELECT SUM(available) FROM books')->fetchColumn() ?: 0;
$totalCopies = $pdo->query('SELECT SUM(quantity) FROM books')->fetchColumn() ?: 0;
$totalBorrowed = $totalCopies - $totalAvailable;

$semesters = ['First Semester', 'Second Semester', 'Third Semester', 'Fourth Semester', 'Fifth Semester', 'Sixth Semester', 'Seventh Semester', 'Eighth Semester'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Prithvi Narayan Campus Library</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <div class="header">
                <h1><img src="https://prnc.tu.edu.np/assets/logo.png" alt="PNC" style="height: 40px; vertical-align: middle; margin-right: 10px;">Prithvi Narayan Campus Library</h1>
                <div class="user-info">
                    Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> | 
                    <a href="available.php">Available Books</a> | 
                    <a href="logout.php">Logout</a>
                </div>
            </div>
            
            <div class="stats">
                <div class="stat-box stat-total">
                    <h3>Total Books</h3>
                    <span><?php echo $totalBooks; ?></span>
                </div>
                <div class="stat-box stat-available">
                    <h3>Available</h3>
                    <span><?php echo $totalAvailable; ?></span>
                </div>
                <div class="stat-box stat-borrowed">
                    <h3>Borrowed</h3>
                    <span><?php echo $totalBorrowed; ?></span>
                </div>
            </div>
            
            <div class="actions">
                <a href="add_book.php" class="btn btn-success">+ Add New Book</a>
                <a href="available.php" class="btn">View Available Books</a>
            </div>
            
            <div class="search-box">
                <form method="GET">
                    <input type="text" name="search" placeholder="Search books..." value="<?php echo htmlspecialchars($search); ?>">
                    <select name="category">
                        <option value="">All Semesters</option>
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?php echo $sem; ?>" <?php echo $category === $sem ? 'selected' : ''; ?>><?php echo $sem; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="status">
                        <option value="">All Status</option>
                        <option value="available" <?php echo $status === 'available' ? 'selected' : ''; ?>>Available</option>
                        <option value="borrowed" <?php echo $status === 'borrowed' ? 'selected' : ''; ?>>Borrowed</option>
                    </select>
                    <button type="submit">Search</button>
                    <a href="dashboard.php">Clear</a>
                </form>
            </div>
            
            <?php if (empty($books)): ?>
                <div class="empty-state">
                    <p>No books found. Add your first book!</p>
                    <a href="add_book.php" class="btn">Add New Book</a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Semester</th>
                            <th>Copy</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <?php $bookBorrowed = $book['quantity'] - $book['available']; ?>
                            <tr>
                                <td>
                                    <?php if ($book['cover_url']): ?>
                                        <img src="<?php echo htmlspecialchars($book['cover_url']); ?>" alt="Cover" class="cover-img" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="no-cover" style="display:none;">Error</div>
                                    <?php else: ?>
                                        <div class="no-cover">No Image</div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['category']); ?></td>
                                <td>
                                    <strong><?php echo $book['available']; ?></strong> / <?php echo $book['quantity']; ?>
                                    <?php if ($bookBorrowed > 0): ?>
                                        <br><small style="color: #e74c3c;"><?php echo $bookBorrowed; ?> borrowed</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($book['available'] > 0): ?>
                                        <span class="status-available"><?php echo $book['available']; ?> available</span>
                                    <?php else: ?>
                                        <span class="status-borrowed">All borrowed</span>
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <?php if ($book['available'] > 0): ?>
                                        <a href="borrow.php?id=<?php echo $book['id']; ?>" class="btn-small btn-success">Borrow</a>
                                    <?php endif; ?>
                                    <?php if ($bookBorrowed > 0): ?>
                                        <a href="borrowed.php?id=<?php echo $book['id']; ?>" class="btn-small">View</a>
                                    <?php endif; ?>
                                    <a href="edit_book.php?id=<?php echo $book['id']; ?>" class="btn-small">Edit</a>
                                    <a href="delete.php?id=<?php echo $book['id']; ?>" class="btn-small btn-danger" onclick="return confirm('Delete this book?')">Delete</a>
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