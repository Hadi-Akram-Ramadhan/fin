<?php
require_once 'config.php';
require_once 'functions.php';

$borrowID = isset($_GET['borrow_id']) ? sanitizeInput($_GET['borrow_id']) : '';
$errors = [];
$borrowData = null;
$memberData = null;
$bookData = null;
$lateFeeInfo = null;

// If borrow_id provided, load data
if ($borrowID) {
    $borrowLine = findByID(BORROWINGS_FILE, $borrowID);
    if ($borrowLine) {
        $borrowData = parseBorrowing($borrowLine);
        
        if ($borrowData['status'] === BORROW_STATUS_RETURNED) {
            $errors[] = "Peminjaman ini sudah dikembalikan";
        } else {
            // Get member and book info
            $memberLine = findByID(MEMBERS_FILE, $borrowData['member_id']);
            $memberData = $memberLine ? parseMember($memberLine) : null;
            
            $bookLine = findByID(BOOKS_FILE, $borrowData['book_id']);
            $bookData = $bookLine ? parseBook($bookLine) : null;
            
            // Calculate late fee
            $returnDate = date('Y-m-d');
            $lateFeeInfo = calculateLateFee($borrowData['tanggal_jatuh_tempo'], $returnDate);
        }
    } else {
        $errors[] = "ID Peminjaman tidak ditemukan";
    }
}

// Process return
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_return'])) {
    $returnBorrowID = sanitizeInput($_POST['borrow_id'] ?? '');
    $returnDate = sanitizeInput($_POST['tanggal_kembali'] ?? '');
    
    if (empty($returnBorrowID) || empty($returnDate)) {
        $errors[] = "Data tidak lengkap";
    } else {
        $borrowLine = findByID(BORROWINGS_FILE, $returnBorrowID);
        if (!$borrowLine) {
            $errors[] = "Peminjaman tidak ditemukan";
        } else {
            $borrow = parseBorrowing($borrowLine);
            
            // Calculate late fee
            $feeInfo = calculateLateFee($borrow['tanggal_jatuh_tempo'], $returnDate);
            
            // Generate return ID
            $returnID = getNextID(RETURNS_FILE, 'RT', 2);
            
            // Save return record
            $returnData = implode('|', [
                $returnID,
                $returnBorrowID,
                $returnDate,
                $feeInfo['days'],
                $feeInfo['fee']
            ]);
            
            if (appendData(RETURNS_FILE, $returnData)) {
                // Update borrowing status
                $borrowingData = implode('|', [
                    $borrow['id'],
                    $borrow['member_id'],
                    $borrow['book_id'],
                    $borrow['tanggal_pinjam'],
                    $borrow['tanggal_jatuh_tempo'],
                    BORROW_STATUS_RETURNED
                ]);
                
                updateByID(BORROWINGS_FILE, $returnBorrowID, $borrowingData);
                
                // Update book status to available
                $bookLine = findByID(BOOKS_FILE, $borrow['book_id']);
                if ($bookLine) {
                    $book = parseBook($bookLine);
                    $bookData = implode('|', [
                        $book['id'],
                        $book['judul'],
                        $book['pengarang'],
                        $book['kategori'],
                        $book['isbn'],
                        $book['tahun'],
                        STATUS_AVAILABLE
                    ]);
                    
                    updateByID(BOOKS_FILE, $borrow['book_id'], $bookData);
                }
                
                $message = 'Pengembalian berhasil! ID Return: ' . $returnID;
                if ($feeInfo['fee'] > 0) {
                    $message .= ' | Denda: ' . formatRupiah($feeInfo['fee']);
                }
                
                header('Location: return.php?success=' . urlencode($message));
                exit;
            } else {
                $errors[] = "Gagal menyimpan data pengembalian";
            }
        }
    }
}

$successMessage = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku - Perpustakaan</title>
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
                <li><a href="borrow.php">Peminjaman</a></li>
                <li><a href="return.php" class="active">Pengembalian</a></li>
            </ul>
        </div>
    </nav>

    <div class="container animate-fade-in">
        <div class="page-header">
            <h1>📥 Sistem Pengembalian</h1>
            <p>Proses pengembalian buku perpustakaan</p>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>

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

        <!-- Search Form -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Cari Peminjaman</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div style="display: flex; gap: 1rem;">
                        <div style="flex: 1;">
                            <label class="form-label">ID Peminjaman</label>
                            <input type="text" name="borrow_id" class="form-control" 
                                   placeholder="Masukkan ID Peminjaman (contoh: BR001)"
                                   value="<?= htmlspecialchars($borrowID) ?>">
                        </div>
                        <div style="align-self: flex-end;">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Return Form -->
        <?php if ($borrowData && empty($errors)): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Peminjaman</h3>
                </div>
                <div class="card-body">
                    <div class="grid grid-2 mb-3">
                        <div>
                            <strong>ID Peminjaman:</strong>
                            <p><?= htmlspecialchars($borrowData['id']) ?></p>
                        </div>
                        <div>
                            <strong>Anggota:</strong>
                            <p>
                                <?= $memberData ? htmlspecialchars($memberData['nama']) : 'N/A' ?><br>
                                <small style="color: var(--gray);">
                                    <?= $memberData ? htmlspecialchars($memberData['id']) : '' ?>
                                </small>
                            </p>
                        </div>
                        <div>
                            <strong>Buku:</strong>
                            <p>
                                <?= $bookData ? htmlspecialchars($bookData['judul']) : 'N/A' ?><br>
                                <small style="color: var(--gray);">
                                    <?= $bookData ? htmlspecialchars($bookData['pengarang']) : '' ?>
                                </small>
                            </p>
                        </div>
                        <div>
                            <strong>Tanggal Pinjam:</strong>
                            <p><?= formatDate($borrowData['tanggal_pinjam']) ?></p>
                        </div>
                        <div>
                            <strong>Tanggal Jatuh Tempo:</strong>
                            <p><?= formatDate($borrowData['tanggal_jatuh_tempo']) ?></p>
                        </div>
                    </div>

                    <hr style="border: none; border-top: 2px solid var(--gray-lighter); margin: 1.5rem 0;">

                    <form method="POST" action="">
                        <input type="hidden" name="borrow_id" value="<?= htmlspecialchars($borrowData['id']) ?>">
                        
                        <div class="form-group">
                            <label class="form-label required">Tanggal Pengembalian</label>
                            <input type="date" name="tanggal_kembali" class="form-control" 
                                   value="<?= date('Y-m-d') ?>"
                                   required>
                        </div>

                        <?php if ($lateFeeInfo): ?>
                            <div class="alert <?= $lateFeeInfo['fee'] > 0 ? 'alert-warning' : 'alert-info' ?>">
                                <?php if ($lateFeeInfo['fee'] > 0): ?>
                                    <strong>⚠️ Keterlambatan Terdeteksi!</strong>
                                    <p style="margin: 0.5rem 0 0 0;">
                                        Terlambat: <strong><?= $lateFeeInfo['days'] ?> hari</strong><br>
                                        Total Denda: <strong><?= formatRupiah($lateFeeInfo['fee']) ?></strong>
                                        (<?= formatRupiah(LATE_FEE_PER_DAY) ?>/hari)
                                    </p>
                                <?php else: ?>
                                    <strong>✓ Pengembalian Tepat Waktu</strong>
                                    <p style="margin: 0.5rem 0 0 0;">Tidak ada denda</p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="card-footer" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid var(--gray-lighter);">
                            <button type="submit" name="process_return" value="1" class="btn btn-success">
                                📥 Proses Pengembalian
                            </button>
                            <a href="return.php" class="btn btn-outline">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Quick Link -->
        <div class="card mt-3">
            <div class="card-body text-center">
                <a href="borrow.php" class="btn btn-outline">Lihat Daftar Peminjaman Aktif</a>
            </div>
        </div>
    </div>
</body>
</html>
