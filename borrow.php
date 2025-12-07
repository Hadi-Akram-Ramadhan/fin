<?php
require_once 'config.php';
require_once 'functions.php';

$borrowings = readData(BORROWINGS_FILE);
$activeBorrowings = [];

foreach ($borrowings as $line) {
    $borrow = parseBorrowing($line);
    if (!$borrow || $borrow['status'] !== BORROW_STATUS_ONGOING) continue;

$memberLine = findByID(MEMBERS_FILE, $borrow['member_id']);
    $member = $memberLine ? parseMember($memberLine) : null;
    
    $bookLine = findByID(BOOKS_FILE, $borrow['book_id']);
    $book = $bookLine ? parseBook($bookLine) : null;

$today = new DateTime();
    $dueDate = new DateTime($borrow['tanggal_jatuh_tempo']);
    $isOverdue = $today > $dueDate;
    $daysOverdue = $isOverdue ? $today->diff($dueDate)->days : 0;
    
    $activeBorrowings[] = [
        'borrow' => $borrow,
        'member' => $member,
        'book' => $book,
        'overdue' => $isOverdue,
        'days_overdue' => $daysOverdue
    ];
}

$successMessage = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Buku - Perpustakaan</title>
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
            <h1>📤 Sistem Peminjaman</h1>
            <p>Kelola peminjaman buku perpustakaan</p>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>

        <div class="card mb-3">
            <div class="card-body">
                <a href="borrow_add.php" class="btn btn-primary">+ Proses Peminjaman Baru</a>
            </div>
        </div>

        <?php if (empty($activeBorrowings)): ?>
            <div class="card">
                <div class="empty-state">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">📤</div>
                    <h3>Tidak Ada Peminjaman Aktif</h3>
                    <p>Belum ada buku yang sedang dipinjam</p>
                    <a href="borrow_add.php" class="btn btn-primary mt-3">Proses Peminjaman Pertama</a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activeBorrowings as $item): 
                            $borrow = $item['borrow'];
                            $member = $item['member'];
                            $book = $item['book'];
                        ?>
                            <tr <?= $item['overdue'] ? 'style="background: rgba(239, 68, 68, 0.05);"' : '' ?>>
                                <td><strong><?= htmlspecialchars($borrow['id']) ?></strong></td>
                                <td>
                                    <?= $member ? htmlspecialchars($member['nama']) : 'Member dihapus' ?>
                                    <br>
                                    <small style="color: var(--gray);">
                                        <?= $member ? htmlspecialchars($member['id']) : '' ?>
                                    </small>
                                </td>
                                <td><?= $book ? htmlspecialchars($book['judul']) : 'Buku dihapus' ?></td>
                                <td><?= formatDate($borrow['tanggal_pinjam']) ?></td>
                                <td><?= formatDate($borrow['tanggal_jatuh_tempo']) ?></td>
                                <td>
                                    <?php if ($item['overdue']): ?>
                                        <span class="badge badge-danger">
                                            Terlambat <?= $item['days_overdue'] ?> hari
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="return.php?borrow_id=<?= urlencode($borrow['id']) ?>" 
                                       class="btn btn-sm btn-success">Kembalikan</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="card mt-3">
                <div class="card-body text-center">
                    <strong>Total Peminjaman Aktif: <?= count($activeBorrowings) ?></strong>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
