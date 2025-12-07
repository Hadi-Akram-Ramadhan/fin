<?php
require_once 'config.php';
require_once 'functions.php';

$bookID = isset($_GET['id']) ? sanitizeInput($_GET['id']) : '';

if (empty($bookID)) {
    header('Location: books.php');
    exit;
}

$bookLine = findByID(BOOKS_FILE, $bookID);
if (!$bookLine) {
    header('Location: books.php?success=' . urlencode('Buku tidak ditemukan'));
    exit;
}

$book = parseBook($bookLine);

$canDelete = true;
$borrowings = readData(BORROWINGS_FILE);
foreach ($borrowings as $line) {
    $borrow = parseBorrowing($line);
    if ($borrow && $borrow['book_id'] === $bookID && $borrow['status'] === BORROW_STATUS_ONGOING) {
        $canDelete = false;
        break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    if (!$canDelete) {
        header('Location: books.php?success=' . urlencode('Tidak dapat menghapus buku yang sedang dipinjam!'));
        exit;
    }
    
    if (deleteByID(BOOKS_FILE, $bookID)) {
        header('Location: books.php?success=' . urlencode('Buku berhasil dihapus!'));
        exit;
    } else {
        header('Location: books.php?success=' . urlencode('Gagal menghapus buku!'));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Buku - Perpustakaan</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
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

    <div class="container animate-fade-in">
        <div class="page-header" style="background: linear-gradient(135deg, 
            <h1>🗑️ Hapus Buku</h1>
            <p>Konfirmasi penghapusan buku</p>
        </div>

        <?php if (!$canDelete): ?>
            <div class="alert alert-error">
                <strong>⚠️ Tidak dapat menghapus buku!</strong>
                <p>Buku ini sedang dipinjam dan tidak dapat dihapus.</p>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Buku</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-2">
                    <div>
                        <strong>ID Buku:</strong>
                        <p><?= htmlspecialchars($book['id']) ?></p>
                    </div>
                    <div>
                        <strong>Status:</strong>
                        <p>
                            <?php if ($book['status'] === STATUS_AVAILABLE): ?>
                                <span class="badge badge-success">Tersedia</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Dipinjam</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div>
                        <strong>Judul:</strong>
                        <p><?= htmlspecialchars($book['judul']) ?></p>
                    </div>
                    <div>
                        <strong>Pengarang:</strong>
                        <p><?= htmlspecialchars($book['pengarang']) ?></p>
                    </div>
                    <div>
                        <strong>Kategori:</strong>
                        <p><span class="badge badge-info"><?= htmlspecialchars($book['kategori']) ?></span></p>
                    </div>
                    <div>
                        <strong>ISBN:</strong>
                        <p><?= htmlspecialchars($book['isbn']) ?></p>
                    </div>
                    <div>
                        <strong>Tahun Terbit:</strong>
                        <p><?= htmlspecialchars($book['tahun']) ?></p>
                    </div>
                </div>

                <?php if ($canDelete): ?>
                    <div class="alert alert-warning mt-3">
                        <strong>⚠️ Perhatian!</strong>
                        <p>Apakah Anda yakin ingin menghapus buku ini? Tindakan ini tidak dapat dibatalkan.</p>
                    </div>

                    <form method="POST" action="">
                        <div class="card-footer" style="border-top: 2px solid var(--gray-lighter); padding-top: 1.5rem;">
                            <button type="submit" name="confirm" value="1" class="btn btn-danger">
                                🗑️ Ya, Hapus Buku
                            </button>
                            <a href="books.php" class="btn btn-outline">Batal</a>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="card-footer" style="border-top: 2px solid var(--gray-lighter); padding-top: 1.5rem;">
                        <a href="books.php" class="btn btn-primary">Kembali ke Daftar Buku</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
