<?php
require_once 'config.php';
require_once 'functions.php';

$memberID = isset($_GET['id']) ? sanitizeInput($_GET['id']) : '';

if (empty($memberID)) {
    header('Location: members.php');
    exit;
}

$memberLine = findByID(MEMBERS_FILE, $memberID);
if (!$memberLine) {
    header('Location: members.php?success=' . urlencode('Anggota tidak ditemukan'));
    exit;
}

$member = parseMember($memberLine);

$allBorrowings = readData(BORROWINGS_FILE);
$memberBorrowings = [];

foreach ($allBorrowings as $line) {
    $borrow = parseBorrowing($line);
    if ($borrow && $borrow['member_id'] === $memberID) {
        
        $bookLine = findByID(BOOKS_FILE, $borrow['book_id']);
        $book = $bookLine ? parseBook($bookLine) : null;

$returnInfo = null;
        if ($borrow['status'] === BORROW_STATUS_RETURNED) {
            $returns = readData(RETURNS_FILE);
            foreach ($returns as $retLine) {
                $ret = parseReturn($retLine);
                if ($ret && $ret['borrow_id'] === $borrow['id']) {
                    $returnInfo = $ret;
                    break;
                }
            }
        }
        
        $memberBorrowings[] = [
            'borrow' => $borrow,
            'book' => $book,
            'return' => $returnInfo
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Perpustakaan</title>
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
                <li><a href="members.php" class="active">Anggota</a></li>
                <li><a href="borrow.php">Peminjaman</a></li>
                <li><a href="return.php">Pengembalian</a></li>
            </ul>
        </div>
    </nav>

    <div class="container animate-fade-in">
        <div class="page-header">
            <h1>📚 Riwayat Peminjaman</h1>
            <p>Anggota: <?= htmlspecialchars($member['nama']) ?> (<?= htmlspecialchars($member['id']) ?>)</p>
        </div>

        <!-- Member Info -->
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Informasi Anggota</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-4">
                    <div>
                        <strong>Email:</strong>
                        <p><?= htmlspecialchars($member['email']) ?></p>
                    </div>
                    <div>
                        <strong>Telepon:</strong>
                        <p><?= htmlspecialchars($member['telepon']) ?></p>
                    </div>
                    <div>
                        <strong>Jenis:</strong>
                        <p><span class="badge badge-info"><?= htmlspecialchars($member['jenis']) ?></span></p>
                    </div>
                    <div>
                        <strong>Status:</strong>
                        <p>
                            <?php if ($member['status'] === STATUS_ACTIVE): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Borrowing History -->
        <?php if (empty($memberBorrowings)): ?>
            <div class="card">
                <div class="empty-state">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📚</div>
                    <h3>Belum Ada Riwayat Peminjaman</h3>
                    <p>Anggota ini belum pernah meminjam buku</p>
                </div>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Pinjam</th>
                            <th>Judul Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Tgl Kembali</th>
                            <th>Denda</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($memberBorrowings as $item): 
                            $borrow = $item['borrow'];
                            $book = $item['book'];
                            $return = $item['return'];
                        ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($borrow['id']) ?></strong></td>
                                <td><?= $book ? htmlspecialchars($book['judul']) : 'Buku dihapus' ?></td>
                                <td><?= formatDate($borrow['tanggal_pinjam']) ?></td>
                                <td><?= formatDate($borrow['tanggal_jatuh_tempo']) ?></td>
                                <td>
                                    <?php if ($return): ?>
                                        <?= formatDate($return['tanggal_kembali']) ?>
                                    <?php else: ?>
                                        <em style="color: var(--gray);">Belum dikembalikan</em>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($return && $return['denda'] > 0): ?>
                                        <span class="badge badge-danger"><?= formatRupiah($return['denda']) ?></span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($borrow['status'] === BORROW_STATUS_ONGOING): ?>
                                        <span class="badge badge-warning">Dipinjam</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Dikembalikan</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card mt-3">
                <div class="card-body">
                    <strong>Total Peminjaman: <?= count($memberBorrowings) ?> transaksi</strong>
                </div>
            </div>
        <?php endif; ?>

        <div class="card mt-3">
            <div class="card-footer">
                <a href="members.php" class="btn btn-primary">Kembali ke Daftar Anggota</a>
                <a href="member_edit.php?id=<?= urlencode($memberID) ?>" class="btn btn-outline">Edit Data Anggota</a>
            </div>
        </div>
    </div>
</body>
</html>
