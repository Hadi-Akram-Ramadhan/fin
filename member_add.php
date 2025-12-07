<?php
require_once 'config.php';
require_once 'functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $nama = sanitizeInput($_POST['nama'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $telepon = sanitizeInput($_POST['telepon'] ?? '');
    $alamat = sanitizeInput($_POST['alamat'] ?? '');
    $jenis = isset($_POST['jenis']) ? $_POST['jenis'] : [];
    
    // Validations
    if (!validateRequired($nama, 3)) {
        $errors[] = "Nama lengkap minimal 3 karakter";
    }
    
    // Email validation menggunakan filter_var() (Komponen Wajib #2)
    if (!validateEmail($email)) {
        $errors[] = "Format email tidak valid";
    }
    
    // Phone validation
    if (!validatePhone($telepon)) {
        $errors[] = "Format nomor telepon tidak valid (harus dimulai 08 atau +62)";
    }
    
    if (!validateRequired($alamat, 10)) {
        $errors[] = "Alamat minimal 10 karakter";
    }
    
    // Checkbox validation (Komponen Wajib #1 - Checkbox)
    if (empty($jenis)) {
        $errors[] = "Pilih minimal satu jenis keanggotaan";
    }
    
    // If no errors, save data
    if (empty($errors)) {
        // Capitalize nama (Komponen Wajib #3)
        $nama = capitalizeName($nama);
        
        // Generate member ID
        $memberID = getNextID(MEMBERS_FILE, 'MBR', 3);
        
        // Process jenis keanggotaan (checkbox bisa multiple)
        $jenisStr = implode(',', $jenis);
        
        // Create data line
        $data = implode('|', [
            $memberID,
            $nama,
            $email,
            $telepon,
            $alamat,
            $jenisStr,
            STATUS_ACTIVE,
            date('Y-m-d')
        ]);
        
        if (appendData(MEMBERS_FILE, $data)) {
            header('Location: members.php?success=' . urlencode('Anggota berhasil didaftarkan! ID: ' . $memberID));
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
    <title>Registrasi Anggota - Perpustakaan</title>
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
            <h1>👥 Registrasi Anggota Baru</h1>
            <p>Daftarkan anggota baru perpustakaan</p>
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
                <h3 class="card-title">Form Registrasi</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="grid grid-2">
                        <!-- Input: Nama Lengkap -->
                        <div class="form-group">
                            <label class="form-label required">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" 
                                   placeholder="Masukkan nama lengkap"
                                   value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>"
                                   required>
                        </div>

                        <!-- Input: Email (dengan validasi filter_var) -->
                        <div class="form-group">
                            <label class="form-label required">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   placeholder="contoh@email.com"
                                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                                   required>
                            <small style="color: var(--gray); font-size: 0.875rem;">
                                Divalidasi dengan filter_var()
                            </small>
                        </div>

                        <!-- Input: No. Telepon -->
                        <div class="form-group">
                            <label class="form-label required">No. Telepon</label>
                            <input type="tel" name="telepon" class="form-control" 
                                   placeholder="08XXXXXXXXXX atau +62XXXXXXXXXX"
                                   value="<?= isset($_POST['telepon']) ? htmlspecialchars($_POST['telepon']) : '' ?>"
                                   required>
                            <small style="color: var(--gray); font-size: 0.875rem;">
                                Format: 08XXXXXXXXXX atau +62XXXXXXXXXX
                            </small>
                        </div>

                        <!-- Checkbox: Jenis Keanggotaan (Komponen Wajib #1) -->
                        <div class="form-group">
                            <label class="form-label required">Jenis Keanggotaan</label>
                            <div>
                                <div class="form-check">
                                    <input type="checkbox" name="jenis[]" id="jenis_reguler" 
                                           value="Reguler"
                                           <?= (isset($_POST['jenis']) && in_array('Reguler', $_POST['jenis'])) ? 'checked' : '' ?>>
                                    <label for="jenis_reguler">Reguler</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="jenis[]" id="jenis_premium" 
                                           value="Premium"
                                           <?= (isset($_POST['jenis']) && in_array('Premium', $_POST['jenis'])) ? 'checked' : '' ?>>
                                    <label for="jenis_premium">Premium</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="jenis[]" id="jenis_pelajar" 
                                           value="Pelajar"
                                           <?= (isset($_POST['jenis']) && in_array('Pelajar', $_POST['jenis'])) ? 'checked' : '' ?>>
                                    <label for="jenis_pelajar">Pelajar/Mahasiswa</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Textarea: Alamat -->
                    <div class="form-group">
                        <label class="form-label required">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control" 
                                  placeholder="Masukkan alamat lengkap"
                                  required><?= isset($_POST['alamat']) ? htmlspecialchars($_POST['alamat']) : '' ?></textarea>
                    </div>

                    <div class="card-footer" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 2px solid var(--gray-lighter);">
                        <button type="submit" class="btn btn-success">💾 Daftarkan Anggota</button>
                        <a href="members.php" class="btn btn-outline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
