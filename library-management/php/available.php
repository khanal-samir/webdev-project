<?php
require_once 'auth.php';
require_once 'db.php';
requireLogin();

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

$query = 'SELECT * FROM books WHERE available > 0';
$params = [];

if ($search) {
    $query .= ' AND title LIKE ?';
    $params[] = "%$search%";
}

if ($category) {
    $query .= ' AND category = ?';
    $params[] = $category;
}

$query .= ' ORDER BY created_at DESC';

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll();

$totalAvailable = $pdo->query('SELECT SUM(available) FROM books WHERE available > 0')->fetchColumn() ?: 0;

$semesters = ['First Semester', 'Second Semester', 'Third Semester', 'Fourth Semester', 'Fifth Semester', 'Sixth Semester', 'Seventh Semester', 'Eighth Semester'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Books - Prithvi Narayan Campus Library</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="main-content">
            <div class="header">
                <h1><img src="https://prnc.tu.edu.np/assets/logo.png" alt="PNC" style="height: 40px; vertical-align: middle; margin-right: 10px;">Available Books</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span> | 
                    <a href="dashboard.php">Dashboard</a> | 
                    <a href="logout.php">Logout</a>
                </div>
            </div>
            
            <div class="stats">
                <div class="stat-box stat-available">
                    <h3>Available Copy</h3>
                    <span><?php echo $totalAvailable; ?></span>
                </div>
            </div>
            
            <div class="search-box">
                <form method="GET">
                    <input type="text" name="search" placeholder="Search available books..." value="<?php echo htmlspecialchars($search); ?>">
                    <select name="category">
                        <option value="">All Semesters</option>
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?php echo $sem; ?>" <?php echo $category === $sem ? 'selected' : ''; ?>><?php echo $sem; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Search</button>
                    <a href="available.php">Clear</a>
                </form>
            </div>
            
            <?php if (empty($books)): ?>
                <div class="empty-state">
                    <p>No available books found</p>
                    <a href="dashboard.php" class="btn">Go to Dashboard</a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Cover</th>
                            <th>Title</th>
                            <th>Semester</th>
                            <th>Copy</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
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
                                <td><strong><?php echo $book['available']; ?></strong> / <?php echo $book['quantity']; ?></td>
                                <td>
                                    <a href="borrow.php?id=<?php echo $book['id']; ?>" class="btn-small btn-success">Borrow</a>
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