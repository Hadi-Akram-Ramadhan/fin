<?php
require_once 'config.php';
require_once 'functions.php';

// Get all members
$members = readData(MEMBERS_FILE);

// Search
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Filter members
$filteredMembers = [];
foreach ($members as $line) {
    $member = parseMember($line);
    if (!$member) continue;
    
    if ($search !== '') {
        $searchLower = strtolower($search);
        if (strpos(strtolower($member['nama']), $searchLower) === false && 
            strpos(strtolower($member['email']), $searchLower) === false &&
            strpos(strtolower($member['id']), $searchLower) === false) {
            continue;
        }
    }
    
    $filteredMembers[] = $member;
}

$successMessage = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - Perpustakaan</title>
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
            <h1>👥 Manajemen Anggota</h1>
            <p>Kelola data anggota perpustakaan</p>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>

        <div class="card mb-3">
            <div class="card-body">
                <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <a href="member_add.php" class="btn btn-primary">+ Registrasi Anggota Baru</a>
                    
                    <form method="GET" style="display: flex; gap: 1rem; flex: 1;">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Cari nama, email, atau ID anggota..." 
                               value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-primary">Cari</button>
                        <?php if ($search): ?>
                            <a href="members.php" class="btn btn-outline">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <?php if (empty($filteredMembers)): ?>
            <div class="card">
                <div class="empty-state">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">👥</div>
                    <h3>Belum Ada Anggota</h3>
                    <p>Mulai daftarkan anggota perpustakaan</p>
                    <a href="member_add.php" class="btn btn-primary mt-3">Registrasi Anggota Pertama</a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th>Tgl Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($filteredMembers as $member): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($member['id']) ?></strong></td>
                                <td><?= htmlspecialchars($member['nama']) ?></td>
                                <td><?= htmlspecialchars($member['email']) ?></td>
                                <td><?= htmlspecialchars($member['telepon']) ?></td>
                                <td><span class="badge badge-info"><?= htmlspecialchars($member['jenis']) ?></span></td>
                                <td>
                                    <?php if ($member['status'] === STATUS_ACTIVE): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= formatDate($member['tanggal_daftar']) ?></td>
                                <td>
                                    <a href="member_history.php?id=<?= urlencode($member['id']) ?>" 
                                       class="btn btn-sm btn-secondary">Riwayat</a>
                                    <a href="member_edit.php?id=<?= urlencode($member['id']) ?>" 
                                       class="btn btn-sm btn-primary">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card mt-3">
                <div class="card-body text-center">
                    <strong>Total: <?= count($filteredMembers) ?> anggota</strong>
                    <?php if ($search): ?>
                        (dari <?= count($members) ?> total anggota)
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
