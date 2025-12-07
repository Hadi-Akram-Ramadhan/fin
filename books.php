<?php
require_once 'config.php';
require_once 'functions.php';

$books = readData(BOOKS_FILE);

$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';

$filteredBooks = [];
foreach ($books as $line) {
    $book = parseBook($line);
    if (!$book) continue;

if ($search !== '') {
        $searchLower = strtolower($search);
        if (strpos(strtolower($book['judul']), $searchLower) === false && 
            strpos(strtolower($book['pengarang']), $searchLower) === false) {
            continue;
        }
    }

if ($category !== '' && $book['kategori'] !== $category) {
        continue;
    }
    
    $filteredBooks[] = $book;
}

$successMessage = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku - Perpustakaan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-content">
            <a href="index.php" class="navbar-brand">
                <span class="logo">📚 Perpustakaan</span>
            </a>
            <ul class="navbar-nav">
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="books.php" class="active">Buku</a></li>
                <li><a href="members.php">Anggota</a></li>
                <li><a href="borrow.php">Peminjaman</a></li>
                <li><a href="return.php">Pengembalian</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container animate-fade-in">
        <!-- Page Header -->
        <div class="page-header">
            <h1>📖 Manajemen Buku</h1>
            <p>Kelola koleksi buku perpustakaan</p>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>

        <!-- Actions Bar -->
        <div class="card mb-3">
            <div class="card-body">
                <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <a href="book_add.php" class="btn btn-primary">+ Tambah Buku Baru</a>
                    
                    <form method="GET" style="display: flex; gap: 1rem; flex: 1; min-width: 300px;">
                        <div style="flex: 1;">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari judul atau pengarang..." 
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                        
                        <select name="category" class="form-select" style="max-width: 200px;">
                            <option value="">Semua Kategori</option>
                            <?php foreach (BOOK_CATEGORIES as $cat): ?>
                                <option value="<?= $cat ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                    <?= $cat ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <button type="submit" class="btn btn-primary">Cari</button>
                        
                        <?php if ($search || $category): ?>
                            <a href="books.php" class="btn btn-outline">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Books Table -->
        <?php if (empty($filteredBooks)): ?>
            <div class="card">
                <div class="empty-state">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📚</div>
                    <h3>Belum Ada Buku</h3>
                    <p>Mulai tambahkan buku ke perpustakaan Anda</p>
                    <a href="book_add.php" class="btn btn-primary mt-3">Tambah Buku Pertama</a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul Buku</th>
                            <th>Pengarang</th>
                            <th>Kategori</th>
                            <th>ISBN</th>
                            <th>Tahun</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filteredBooks as $book): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($book['id']) ?></strong></td>
                                <td><?= htmlspecialchars($book['judul']) ?></td>
                                <td><?= htmlspecialchars($book['pengarang']) ?></td>
                                <td><span class="badge badge-info"><?= htmlspecialchars($book['kategori']) ?></span></td>
                                <td><?= htmlspecialchars($book['isbn']) ?></td>
                                <td><?= htmlspecialchars($book['tahun']) ?></td>
                                <td>
                                    <?php if ($book['status'] === STATUS_AVAILABLE): ?>
                                        <span class="badge badge-success">Tersedia</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">Dipinjam</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="book_edit.php?id=<?= urlencode($book['id']) ?>" 
                                       class="btn btn-sm btn-primary">Edit</a>
                                    <a href="book_delete.php?id=<?= urlencode($book['id']) ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Yakin ingin menghapus buku ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card mt-3">
                <div class="card-body text-center">
                    <strong>Total: <?= count($filteredBooks) ?> buku</strong>
                    <?php if ($search || $category): ?>
                        (dari <?= count($books) ?> total buku)
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
