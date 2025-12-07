<?php
require_once 'config.php';
require_once 'functions.php';

$bookID = isset($_GET['id']) ? sanitizeInput($_GET['id']) : '';
$errors = [];

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = sanitizeInput($_POST['judul'] ?? '');
    $pengarang = sanitizeInput($_POST['pengarang'] ?? '');
    $kategori = sanitizeInput($_POST['kategori'] ?? '');
    $isbn = sanitizeInput($_POST['isbn'] ?? '');
    $tahun = sanitizeInput($_POST['tahun'] ?? '');
    $status = sanitizeInput($_POST['status'] ?? STATUS_AVAILABLE);

if (!validateRequired($judul, 3)) {
        $errors[] = "Judul buku minimal 3 karakter";
    }
    
    if (!validateRequired($pengarang, 3)) {
        $errors[] = "Nama pengarang minimal 3 karakter";
    }
    
    if (empty($kategori)) {
        $errors[] = "Kategori harus dipilih";
    }
    
    if (!validateISBN($isbn)) {
        $errors[] = "Format ISBN tidak valid";
    }
    
    if (empty($tahun) || !is_numeric($tahun) || $tahun < 1000 || $tahun > date('Y')) {
        $errors[] = "Tahun terbit tidak valid";
    }

if (empty($errors)) {
        $pengarang = capitalizeName($pengarang);
        
        $data = implode('|', [
            $bookID,
            $judul,
            $pengarang,
            $kategori,
            $isbn,
            $tahun,
            $status
        ]);
        
        if (updateByID(BOOKS_FILE, $bookID, $data)) {
            header('Location: books.php?success=' . urlencode('Buku berhasil diupdate!'));
            exit;
        } else {
            $errors[] = "Gagal mengupdate data";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Perpustakaan</title>
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
        <div class="page-header">
            <h1>📖 Edit Buku</h1>
            <p>Update informasi buku: <?= htmlspecialchars($book['judul']) ?></p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <strong>Terdapat kesalahan:</strong>
                <ul style="margin: 0.5rem 0 0 1.5rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Edit Buku (ID: <?= htmlspecialchars($book['id']) ?>)</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label required">Judul Buku</label>
                            <input type="text" name="judul" class="form-control" 
                                   value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : htmlspecialchars($book['judul']) ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Pengarang</label>
                            <input type="text" name="pengarang" class="form-control" 
                                   value="<?= isset($_POST['pengarang']) ? htmlspecialchars($_POST['pengarang']) : htmlspecialchars($book['pengarang']) ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach (BOOK_CATEGORIES as $cat): ?>
                                    <option value="<?= $cat ?>" 
                                            <?php 
                                            $selected = isset($_POST['kategori']) ? $_POST['kategori'] : $book['kategori'];
                                            echo ($selected === $cat) ? 'selected' : '';
                                            ?>>
                                        <?= $cat ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">ISBN</label>
                            <input type="text" name="isbn" class="form-control" 
                                   value="<?= isset($_POST['isbn']) ? htmlspecialchars($_POST['isbn']) : htmlspecialchars($book['isbn']) ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Tahun Terbit</label>
                            <input type="number" name="tahun" class="form-control" 
                                   min="1000" max="<?= date('Y') ?>"
                                   value="<?= isset($_POST['tahun']) ? htmlspecialchars($_POST['tahun']) : htmlspecialchars($book['tahun']) ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Status Buku</label>
                            <div>
                                <div class="form-check">
                                    <input type="radio" name="status" id="status_tersedia" 
                                           value="<?= STATUS_AVAILABLE ?>"
                                           <?php 
                                           $status = isset($_POST['status']) ? $_POST['status'] : $book['status'];
                                           echo ($status === STATUS_AVAILABLE) ? 'checked' : '';
                                           ?>>
                                    <label for="status_tersedia">Tersedia</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="status" id="status_dipinjam" 
                                           value="<?= STATUS_BORROWED ?>"
                                           <?php echo ($status === STATUS_BORROWED) ? 'checked' : ''; ?>>
                                    <label for="status_dipinjam">Dipinjam</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid var(--gray-lighter);">
                        <button type="submit" class="btn btn-success">💾 Update Buku</button>
                        <a href="books.php" class="btn btn-outline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
