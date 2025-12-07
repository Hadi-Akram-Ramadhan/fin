<?php
require_once 'config.php';
require_once 'functions.php';

$books = readData(BOOKS_FILE);
$members = readData(MEMBERS_FILE);
$borrowings = readData(BORROWINGS_FILE);

$totalBooks = count($books);
$totalMembers = count($members);

$availableBooks = 0;
$borrowedBooks = 0;
foreach ($books as $line) {
    $book = parseBook($line);
    if ($book) {
        if ($book['status'] === STATUS_AVAILABLE) {
            $availableBooks++;
        } else {
            $borrowedBooks++;
        }
    }
}

$activeBorrowings = 0;
foreach ($borrowings as $line) {
    $borrow = parseBorrowing($line);
    if ($borrow && $borrow['status'] === BORROW_STATUS_ONGOING) {
        $activeBorrowings++;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Perpustakaan</title>
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
                <li><a href="index.php" class="active">Dashboard</a></li>
                <li><a href="books.php">Buku</a></li>
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
            <h1>Dashboard Perpustakaan</h1>
            <p>Sistem Manajemen Perpustakaan - Kelola buku, anggota, peminjaman, dan pengembalian</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-4 mb-4">
            <div class="stats-card" style="background: var(--gradient-primary);">
                <div class="stats-number"><?= $totalBooks ?></div>
                <div class="stats-label">Total Buku</div>
            </div>
            
            <div class="stats-card" style="background: var(--gradient-success);">
                <div class="stats-number"><?= $availableBooks ?></div>
                <div class="stats-label">Buku Tersedia</div>
            </div>
            
            <div class="stats-card" style="background: var(--gradient-secondary);">
                <div class="stats-number"><?= $totalMembers ?></div>
                <div class="stats-label">Total Anggota</div>
            </div>
            
            <div class="stats-card" style="background: var(--gradient-info);">
                <div class="stats-number"><?= $activeBorrowings ?></div>
                <div class="stats-label">Dipinjam</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-2">
            <!-- Manajemen Buku -->
            <div class="card card-glass">
                <div class="card-header">
                    <h3 class="card-title">📖 Manajemen Buku</h3>
                </div>
                <div class="card-body">
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem;">✓ Tambah data buku baru</li>
                        <li style="margin-bottom: 0.5rem;">✓ Edit informasi buku</li>
                        <li style="margin-bottom: 0.5rem;">✓ Hapus data buku</li>
                        <li style="margin-bottom: 0.5rem;">✓ Pencarian buku</li>
                        <li style="margin-bottom: 0.5rem;">✓ Filter berdasarkan kategori</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="books.php" class="btn btn-primary">Kelola Buku</a>
                </div>
            </div>

            <!-- Manajemen Anggota -->
            <div class="card card-glass">
                <div class="card-header">
                    <h3 class="card-title">👥 Manajemen Anggota</h3>
                </div>
                <div class="card-body">
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem;">✓ Registrasi anggota baru</li>
                        <li style="margin-bottom: 0.5rem;">✓ Update data anggota</li>
                        <li style="margin-bottom: 0.5rem;">✓ Status keanggotaan</li>
                        <li style="margin-bottom: 0.5rem;">✓ Riwayat peminjaman</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="members.php" class="btn btn-primary">Kelola Anggota</a>
                </div>
            </div>

            <!-- Sistem Peminjaman -->
            <div class="card card-glass">
                <div class="card-header">
                    <h3 class="card-title">📤 Sistem Peminjaman</h3>
                </div>
                <div class="card-body">
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem;">✓ Proses peminjaman buku</li>
                        <li style="margin-bottom: 0.5rem;">✓ Pengecekan ketersediaan</li>
                        <li style="margin-bottom: 0.5rem;">✓ Batas peminjaman</li>
                        <li style="margin-bottom: 0.5rem;">✓ Notifikasi jatuh tempo</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="borrow.php" class="btn btn-success">Kelola Peminjaman</a>
                </div>
            </div>

            <!-- Sistem Pengembalian -->
            <div class="card card-glass">
                <div class="card-header">
                    <h3 class="card-title">📥 Sistem Pengembalian</h3>
                </div>
                <div class="card-body">
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem;">✓ Proses pengembalian</li>
                        <li style="margin-bottom: 0.5rem;">✓ Perhitungan denda</li>
                        <li style="margin-bottom: 0.5rem;">✓ Update status buku</li>
                        <li style="margin-bottom: 0.5rem;">✓ Riwayat pengembalian</li>
                    </ul>
                </div>
                <div class="card-footer">
                    <a href="return.php" class="btn btn-success">Kelola Pengembalian</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
