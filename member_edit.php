<?php
require_once 'config.php';
require_once 'functions.php';

$memberID = isset($_GET['id']) ? sanitizeInput($_GET['id']) : '';
$errors = [];

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitizeInput($_POST['nama'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $telepon = sanitizeInput($_POST['telepon'] ?? '');
    $alamat = sanitizeInput($_POST['alamat'] ?? '');
    $jenis = isset($_POST['jenis']) ? $_POST['jenis'] : [];
    $status = sanitizeInput($_POST['status'] ?? STATUS_ACTIVE);
    
    // Validations
    if (!validateRequired($nama, 3)) {
        $errors[] = "Nama lengkap minimal 3 karakter";
    }
    
    if (!validateEmail($email)) {
        $errors[] = "Format email tidak valid";
    }
    
    if (!validatePhone($telepon)) {
        $errors[] = "Format nomor telepon tidak valid";
    }
    
    if (!validateRequired($alamat, 10)) {
        $errors[] = "Alamat minimal 10 karakter";
    }
    
    if (empty($jenis)) {
        $errors[] = "Pilih minimal satu jenis keanggotaan";
    }
    
    if (empty($errors)) {
        $nama = capitalizeName($nama);
        $jenisStr = implode(',', $jenis);
        
        $data = implode('|', [
            $memberID,
            $nama,
            $email,
            $telepon,
            $alamat,
            $jenisStr,
            $status,
            $member['tanggal_daftar']
        ]);
        
        if (updateByID(MEMBERS_FILE, $memberID, $data)) {
            header('Location: members.php?success=' . urlencode('Data anggota berhasil diupdate!'));
            exit;
        } else {
            $errors[] = "Gagal mengupdate data";
        }
    }
}

// Parse existing jenis for checkbox
$existingJenis = explode(',', $member['jenis']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Anggota - Perpustakaan</title>
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
            <h1>👥 Edit Data Anggota</h1>
            <p>Update informasi anggota: <?= htmlspecialchars($member['nama']) ?></p>
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
                <h3 class="card-title">Form Edit Anggota (ID: <?= htmlspecialchars($member['id']) ?>)</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label class="form-label required">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" 
                                   value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : htmlspecialchars($member['nama']) ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($member['email']) ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">No. Telepon</label>
                            <input type="tel" name="telepon" class="form-control" 
                                   value="<?= isset($_POST['telepon']) ? htmlspecialchars($_POST['telepon']) : htmlspecialchars($member['telepon']) ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Jenis Keanggotaan</label>
                            <div>
                                <?php 
                                $jenisList = ['Reguler', 'Premium', 'Pelajar'];
                                $selectedJenis = isset($_POST['jenis']) ? $_POST['jenis'] : $existingJenis;
                                foreach ($jenisList as $j): 
                                ?>
                                    <div class="form-check">
                                        <input type="checkbox" name="jenis[]" 
                                               id="jenis_<?= strtolower($j) ?>" 
                                               value="<?= $j ?>"
                                               <?= in_array($j, $selectedJenis) ? 'checked' : '' ?>>
                                        <label for="jenis_<?= strtolower($j) ?>"><?= $j ?></label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Status Keanggotaan</label>
                            <div>
                                <div class="form-check">
                                    <input type="radio" name="status" id="status_aktif" 
                                           value="<?= STATUS_ACTIVE ?>"
                                           <?php 
                                           $status = isset($_POST['status']) ? $_POST['status'] : $member['status'];
                                           echo ($status === STATUS_ACTIVE) ? 'checked' : '';
                                           ?>>
                                    <label for="status_aktif">Aktif</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="status" id="status_nonaktif" 
                                           value="<?= STATUS_INACTIVE ?>"
                                           <?= ($status === STATUS_INACTIVE) ? 'checked' : '' ?>>
                                    <label for="status_nonaktif">Nonaktif</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label required">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" required><?= isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : htmlspecialchars($member['alamat']) ?></textarea>
                    </div>

                    <div class="card-footer" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid var(--gray-lighter);">
                        <button type="submit" class="btn btn-success">💾 Update Data</button>
                        <a href="members.php" class="btn btn-outline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
