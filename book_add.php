<?php
require_once 'config.php';
require_once 'functions.php';

$errors = [];
$success = false;

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

$newID = getNextID(BOOKS_FILE, 'B', 1);

$data = implode('|', [
            $newID,
            $judul,
            $pengarang,
            $kategori,
            $isbn,
            $tahun,
            $status
        ]);

if (appendData(BOOKS_FILE, $data)) {
            header('Location: books.php?success=' . urlencode('Buku berhasil ditambahkan!'));
            exit;
        } else {
            $errors[] = "Gagal menyimpan data ke file";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - Perpustakaan</title>
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
            <h1>📖 Tambah Buku Baru</h1>
            <p>Tambahkan buku baru ke koleksi perpustakaan</p>
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
                <h3 class="card-title">Form Data Buku</h3>
            </div>
            <div class="card-body">
                <!-- Komponen Wajib 
                <form method="POST" action="">
                    <div class="grid grid-2">
                        <!-- Input Text: Judul Buku -->
                        <div class="form-group">
                            <label class="form-label required">Judul Buku</label>
                            <input type="text" name="judul" class="form-control" 
                                   placeholder="Masukkan judul buku"
                                   value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : '' ?>"
                                   required>
                        </div>

                        <!-- Input Text: Pengarang -->
                        <div class="form-group">
                            <label class="form-label required">Pengarang</label>
                            <input type="text" name="pengarang" class="form-control" 
                                   placeholder="Masukkan nama pengarang"
                                   value="<?= isset($_POST['pengarang']) ? htmlspecialchars($_POST['pengarang']) : '' ?>"
                                   required>
                        </div>

                        <!-- Select: Kategori -->
                        <div class="form-group">
                            <label class="form-label required">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach (BOOK_CATEGORIES as $cat): ?>
                                    <option value="<?= $cat ?>" 
                                            <?= (isset($_POST['kategori']) && $_POST['kategori'] === $cat) ? 'selected' : '' ?>>
                                        <?= $cat ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Input Text: ISBN -->
                        <div class="form-group">
                            <label class="form-label required">ISBN</label>
                            <input type="text" name="isbn" class="form-control" 
                                   placeholder="978-XXXXXXXXX"
                                   value="<?= isset($_POST['isbn']) ? htmlspecialchars($_POST['isbn']) : '' ?>"
                                   required>
                            <small style="color: var(--gray); font-size: 0.875rem;">
                                Format: 978-XXXXXXXXX atau 13 digit angka
                            </small>
                        </div>

                        <!-- Input Number: Tahun Terbit -->
                        <div class="form-group">
                            <label class="form-label required">Tahun Terbit</label>
                            <input type="number" name="tahun" class="form-control" 
                                   placeholder="<?= date('Y') ?>"
                                   min="1000" max="<?= date('Y') ?>"
                                   value="<?= isset($_POST['tahun']) ? htmlspecialchars($_POST['tahun']) : '' ?>"
                                   required>
                        </div>

                        <!-- Radio: Status -->
                        <div class="form-group">
                            <label class="form-label required">Status Buku</label>
                            <div>
                                <div class="form-check">
                                    <input type="radio" name="status" id="status_tersedia" 
                                           value="<?= STATUS_AVAILABLE ?>"
                                           <?= (!isset($_POST['status']) || $_POST['status'] === STATUS_AVAILABLE) ? 'checked' : '' ?>>
                                    <label for="status_tersedia">Tersedia</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="status" id="status_dipinjam" 
                                           value="<?= STATUS_BORROWED ?>"
                                           <?= (isset($_POST['status']) && $_POST['status'] === STATUS_BORROWED) ? 'checked' : '' ?>>
                                    <label for="status_dipinjam">Dipinjam</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid var(--gray-lighter);">
                        <button type="submit" class="btn btn-success">💾 Simpan Buku</button>
                        <a href="books.php" class="btn btn-outline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
