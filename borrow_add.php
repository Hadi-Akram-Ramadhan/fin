<?php
require_once 'config.php';
require_once 'functions.php';

$errors = [];

$members = readData(MEMBERS_FILE);
$activeMembers = [];
foreach ($members as $line) {
    $member = parseMember($line);
    if ($member && $member['status'] === STATUS_ACTIVE) {
        $activeMembers[] = $member;
    }
}

$books = readData(BOOKS_FILE);
$availableBooks = [];
foreach ($books as $line) {
    $book = parseBook($line);
    if ($book && $book['status'] === STATUS_AVAILABLE) {
        $availableBooks[] = $book;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberID = sanitizeInput($_POST['member_id'] ?? '');
    $bookID = sanitizeInput($_POST['book_id'] ?? '');
    $tanggalPinjam = sanitizeInput($_POST['tanggal_pinjam'] ?? '');
    $tanggalJatuhTempo = sanitizeInput($_POST['tanggal_jatuh_tempo'] ?? '');

if (empty($memberID)) {
        $errors[] = "Pilih anggota";
    }
    
    if (empty($bookID)) {
        $errors[] = "Pilih buku";
    }
    
    if (empty($tanggalPinjam)) {
        $errors[] = "Tanggal peminjaman harus diisi";
    }
    
    if (empty($tanggalJatuhTempo)) {
        $errors[] = "Tanggal jatuh tempo harus diisi";
    }

if (empty($errors)) {
        
        $memberLine = findByID(MEMBERS_FILE, $memberID);
        if (!$memberLine) {
            $errors[] = "Anggota tidak ditemukan";
        } else {
            $member = parseMember($memberLine);
            if ($member['status'] !== STATUS_ACTIVE) {
                $errors[] = "Anggota tidak aktif";
            }
        }

$bookLine = findByID(BOOKS_FILE, $bookID);
        if (!$bookLine) {
            $errors[] = "Buku tidak ditemukan";
        } else {
            $book = parseBook($bookLine);
            if ($book['status'] !== STATUS_AVAILABLE) {
                $errors[] = "Buku tidak tersedia (sedang dipinjam)";
            }
        }

$activeBorrowCount = countActiveBorrowings($memberID);
        if ($activeBorrowCount >= MAX_BORROW_LIMIT) {
            $errors[] = "Anggota sudah mencapai batas maksimal peminjaman (" . MAX_BORROW_LIMIT . " buku)";
        }

$pinjam = new DateTime($tanggalPinjam);
        $jatuhTempo = new DateTime($tanggalJatuhTempo);
        if ($jatuhTempo <= $pinjam) {
            $errors[] = "Tanggal jatuh tempo harus setelah tanggal peminjaman";
        }
    }

if (empty($errors)) {
        
        $borrowID = getNextID(BORROWINGS_FILE, 'BR', 2);

$data = implode('|', [
            $borrowID,
            $memberID,
            $bookID,
            $tanggalPinjam,
            $tanggalJatuhTempo,
            BORROW_STATUS_ONGOING
        ]);

if (appendData(BORROWINGS_FILE, $data)) {
            
            $bookData = implode('|', [
                $bookID,
                $book['judul'],
                $book['pengarang'],
                $book['kategori'],
                $book['isbn'],
                $book['tahun'],
                STATUS_BORROWED
            ]);
            
            updateByID(BOOKS_FILE, $bookID, $bookData);
            
            header('Location: borrow.php?success=' . urlencode('Peminjaman berhasil! ID: ' . $borrowID));
            exit;
        } else {
            $errors[] = "Gagal menyimpan data peminjaman";
        }
    }
}

$defaultPinjam = date('Y-m-d');
$defaultJatuhTempo = date('Y-m-d', strtotime('+' . DEFAULT_BORROW_DAYS . ' days'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Peminjaman - Perpustakaan</title>
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
                <li><a href="books.php">Buku</a></li>
                <li><a href="members.php">Anggota</a></li>
                <li><a href="borrow.php" class="active">Peminjaman</a></li>
                <li><a href="return.php">Pengembalian</a></li>
            </ul>
        </div>
    </nav>

    <div class="container animate-fade-in">
        <div class="page-header">
            <h1>📤 Proses Peminjaman Baru</h1>
            <p>Proses peminjaman buku untuk anggota</p>
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

        <?php if (empty($activeMembers)): ?>
            <div class="alert alert-warning">
                <strong>⚠️ Tidak ada anggota aktif!</strong>
                <p>Silakan <a href="member_add.php">daftarkan anggota</a> terlebih dahulu.</p>
            </div>
        <?php elseif (empty($availableBooks)): ?>
            <div class="alert alert-warning">
                <strong>⚠️ Tidak ada buku tersedia!</strong>
                <p>Semua buku sedang dipinjam atau belum ada data buku. Silakan <a href="book_add.php">tambahkan buku</a>.</p>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Form Peminjaman</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="grid grid-2">
                            <!-- Select: Anggota -->
                            <div class="form-group">
                                <label class="form-label required">Pilih Anggota</label>
                                <select name="member_id" class="form-select" required>
                                    <option value="">-- Pilih Anggota --</option>
                                    <?php foreach ($activeMembers as $m): 
                                        $borrowCount = countActiveBorrowings($m['id']);
                                        $disabled = ($borrowCount >= MAX_BORROW_LIMIT) ? 'disabled' : '';
                                    ?>
                                        <option value="<?= $m['id'] ?>" <?= $disabled ?>
                                                <?= (isset($_POST['member_id']) && $_POST['member_id'] === $m['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($m['id']) ?> - <?= htmlspecialchars($m['nama']) ?>
                                            <?= $disabled ? ' (Batas pinjam tercapai)' : " ($borrowCount/" . MAX_BORROW_LIMIT . ")" ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Select: Buku -->
                            <div class="form-group">
                                <label class="form-label required">Pilih Buku</label>
                                <select name="book_id" class="form-select" required>
                                    <option value="">-- Pilih Buku --</option>
                                    <?php foreach ($availableBooks as $b): ?>
                                        <option value="<?= $b['id'] ?>"
                                                <?= (isset($_POST['book_id']) && $_POST['book_id'] === $b['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($b['id']) ?> - <?= htmlspecialchars($b['judul']) ?> 
                                            (<?= htmlspecialchars($b['pengarang']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Input Date: Tanggal Peminjaman -->
                            <div class="form-group">
                                <label class="form-label required">Tanggal Peminjaman</label>
                                <input type="date" name="tanggal_pinjam" class="form-control" 
                                       value="<?= isset($_POST['tanggal_pinjam']) ? $_POST['tanggal_pinjam'] : $defaultPinjam ?>"
                                       required>
                            </div>

                            <!-- Input Date: Tanggal Jatuh Tempo -->
                            <div class="form-group">
                                <label class="form-label required">Tanggal Jatuh Tempo</label>
                                <input type="date" name="tanggal_jatuh_tempo" class="form-control" 
                                       value="<?= isset($_POST['tanggal_jatuh_tempo']) ? $_POST['tanggal_jatuh_tempo'] : $defaultJatuhTempo ?>"
                                       required>
                                <small style="color: var(--gray); font-size: 0.875rem;">
                                    Default: <?= DEFAULT_BORROW_DAYS ?> hari dari tanggal pinjam
                                </small>
                            </div>
                        </div>

                        <div class="alert alert-info mt-3">
                            <strong>ℹ️ Informasi:</strong>
                            <ul style="margin: 0.5rem 0 0 1.5rem;">
                                <li>Maksimal peminjaman per anggota: <?= MAX_BORROW_LIMIT ?> buku</li>
                                <li>Durasi peminjaman default: <?= DEFAULT_BORROW_DAYS ?> hari</li>
                                <li>Denda keterlambatan: <?= formatRupiah(LATE_FEE_PER_DAY) ?>/hari</li>
                            </ul>
                        </div>

                        <div class="card-footer" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid var(--gray-lighter);">
                            <button type="submit" class="btn btn-success">📤 Proses Peminjaman</button>
                            <a href="borrow.php" class="btn btn-outline">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
