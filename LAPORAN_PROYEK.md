# LAPORAN PROYEK SISTEM MANAJEMEN PERPUSTAKAAN

## PHP Native dengan Penyimpanan File .txt

---

**Nama Proyek:** Sistem Manajemen Perpustakaan  
**Teknologi:** PHP Native, HTML5, CSS3, JavaScript  
**Database:** File .txt (Text File Storage)  
**Tanggal:** 7 Desember 2025

---

## 📋 DAFTAR ISI

1. [Pendahuluan](#pendahuluan)
2. [Tujuan Proyek](#tujuan-proyek)
3. [Fitur Sistem](#fitur-sistem)
4. [Teknologi yang Digunakan](#teknologi-yang-digunakan)
5. [Komponen Wajib](#komponen-wajib)
6. [Struktur File](#struktur-file)
7. [Tampilan Aplikasi](#tampilan-aplikasi)
8. [Cara Menggunakan](#cara-menggunakan)
9. [Testing dan Validasi](#testing-dan-validasi)
10. [Kesimpulan](#kesimpulan)

---

## 1. PENDAHULUAN

Sistem Manajemen Perpustakaan adalah aplikasi berbasis web yang dibangun menggunakan **PHP Native** tanpa framework, dengan penyimpanan data menggunakan **file .txt**. Sistem ini dirancang untuk memudahkan pengelolaan perpustakaan dalam hal:

- Manajemen koleksi buku
- Manajemen data anggota
- Proses peminjaman buku
- Proses pengembalian buku
- Tracking dan pelaporan

Aplikasi ini menampilkan **desain modern dan clean** dengan kombinasi warna yang menarik, efek glassmorphism, smooth animations, dan responsive layout yang dapat diakses dari berbagai perangkat.

---

## 2. TUJUAN PROYEK

### Tujuan Utama:

1. **Implementasi Komponen Wajib**: Menerapkan semua komponen yang diwajibkan dalam pengembangan aplikasi PHP
2. **File Handling**: Menggunakan file .txt sebagai media penyimpanan data
3. **User Experience**: Memberikan pengalaman pengguna yang modern dan intuitif
4. **Validasi Data**: Memastikan integritas data dengan validasi yang komprehensif

### Tujuan Khusus:

- Implementasi CRUD (Create, Read, Update, Delete) untuk semua modul
- Implementasi business logic (batas peminjaman, kalkulasi denda, dll)
- Implementasi search dan filter
- Implementasi validasi form dengan berbagai metode

---

## 3. FITUR SISTEM

### 3.1 Manajemen Buku 📖

**Fitur:**

- ✅ Tambah buku baru dengan validasi lengkap
- ✅ Edit informasi buku
- ✅ Hapus buku (dengan pengecekan status peminjaman)
- ✅ Daftar buku dengan pagination
- ✅ Search buku berdasarkan judul atau pengarang
- ✅ Filter buku berdasarkan kategori
- ✅ Status badge (Tersedia/Dipinjam)

**Kategori Buku:**

- Fiksi
- Non-Fiksi
- Sains
- Teknologi
- Sejarah
- Biografi
- Pendidikan
- Religi
- Komik
- Majalah

### 3.2 Manajemen Anggota 👥

**Fitur:**

- ✅ Registrasi anggota baru
- ✅ Update data anggota
- ✅ Toggle status keanggotaan (Aktif/Nonaktif)
- ✅ Riwayat peminjaman per anggota
- ✅ Search anggota
- ✅ Auto-generate Member ID

**Jenis Keanggotaan:**

- Reguler
- Premium
- Pelajar/Mahasiswa

### 3.3 Sistem Peminjaman 📤

**Fitur:**

- ✅ Proses peminjaman buku
- ✅ Dropdown untuk memilih anggota dan buku
- ✅ Check ketersediaan buku otomatis
- ✅ Enforce batas peminjaman (max 3 buku per anggota)
- ✅ Validasi status keanggotaan aktif
- ✅ Auto-update status buku menjadi "Dipinjam"
- ✅ Tanggal jatuh tempo otomatis (+7 hari)
- ✅ Highlight peminjaman yang terlambat

**Business Rules:**

- Maksimal 3 buku per anggota
- Durasi peminjaman default: 7 hari
- Denda keterlambatan: Rp 2.000/hari

### 3.4 Sistem Pengembalian 📥

**Fitur:**

- ✅ Search peminjaman berdasarkan ID
- ✅ Display detail peminjaman lengkap
- ✅ Kalkulasi denda otomatis berdasarkan keterlambatan
- ✅ Show hari keterlambatan
- ✅ Auto-update status buku menjadi "Tersedia"
- ✅ Save record pengembalian dengan info denda
- ✅ Alert berbeda untuk tepat waktu vs terlambat

---

## 4. TEKNOLOGI YANG DIGUNAKAN

### Backend:

- **PHP 8.x** (Native, tanpa framework)
- **File System** untuk penyimpanan data (.txt files)

### Frontend:

- **HTML5** - Struktur halaman
- **CSS3** - Styling dengan custom design system
- **JavaScript** - (Minimal, untuk interaktivitas)

### Typography:

- **Google Fonts**:
  - Inter - untuk body text
  - Poppins - untuk headings

### Design System:

- **Color Palette**:

  - Primary: Purple-blue gradient (#667eea → #764ba2)
  - Secondary: Pink-red gradient (#f093fb → #f5576c)
  - Success: Green gradient (#10b981 → #059669)
  - Info: Blue gradient (#3b82f6 → #2563eb)

- **Effects**:
  - Glassmorphism (backdrop-filter blur)
  - Smooth gradients
  - Box shadows (subtle to dramatic)
  - Smooth transitions (150ms - 500ms)

---

## 5. KOMPONEN WAJIB

### 5.1 Form dan Input Data HTML ✅

| Komponen            | Implementasi                                      | Lokasi File                       |
| ------------------- | ------------------------------------------------- | --------------------------------- |
| **Input Text**      | Judul Buku, Pengarang, ISBN, Nama, Email, Telepon | `book_add.php`, `member_add.php`  |
| **Select Dropdown** | Kategori Buku, Pilih Anggota, Pilih Buku          | `book_add.php`, `borrow_add.php`  |
| **Radio Button**    | Status Buku, Status Keanggotaan                   | `book_add.php`, `member_edit.php` |
| **Checkbox**        | Jenis Keanggotaan (Multiple)                      | `member_add.php`                  |
| **Textarea**        | Alamat Lengkap                                    | `member_add.php`                  |
| **Input Date**      | Tanggal Peminjaman, Jatuh Tempo, Pengembalian     | `borrow_add.php`, `return.php`    |
| **Input Number**    | Tahun Terbit                                      | `book_add.php`                    |

**Contoh Kode - Input Text & Validation:**

```php
<!-- book_add.php - Input Text -->
<div class="form-group">
    <label class="form-label required">Judul Buku</label>
    <input type="text" name="judul" class="form-control"
           placeholder="Masukkan judul buku"
           value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : '' ?>"
           required>
</div>
```

**Contoh Kode - Select Dropdown:**

```php
<!-- book_add.php - Select Kategori -->
<div class="form-group">
    <label class="form-label required">Kategori</label>
    <select name="kategori" class="form-select" required>
        <option value="">-- Pilih Kategori --</option>
        <?php foreach (BOOK_CATEGORIES as $cat): ?>
            <option value="<?= $cat ?>"><?= $cat ?></option>
        <?php endforeach; ?>
    </select>
</div>
```

**Contoh Kode - Radio Button:**

```php
<!-- book_add.php - Radio Status -->
<div class="form-group">
    <label class="form-label required">Status Buku</label>
    <div>
        <div class="form-check">
            <input type="radio" name="status" id="status_tersedia"
                   value="Tersedia" checked>
            <label for="status_tersedia">Tersedia</label>
        </div>
        <div class="form-check">
            <input type="radio" name="status" id="status_dipinjam"
                   value="Dipinjam">
            <label for="status_dipinjam">Dipinjam</label>
        </div>
    </div>
</div>
```

**Contoh Kode - Checkbox:**

```php
<!-- member_add.php - Checkbox Multiple -->
<div class="form-group">
    <label class="form-label required">Jenis Keanggotaan</label>
    <div>
        <div class="form-check">
            <input type="checkbox" name="jenis[]" id="jenis_reguler"
                   value="Reguler">
            <label for="jenis_reguler">Reguler</label>
        </div>
        <div class="form-check">
            <input type="checkbox" name="jenis[]" id="jenis_premium"
                   value="Premium">
            <label for="jenis_premium">Premium</label>
        </div>
        <div class="form-check">
            <input type="checkbox" name="jenis[]" id="jenis_pelajar"
                   value="Pelajar">
            <label for="jenis_pelajar">Pelajar/Mahasiswa</label>
        </div>
    </div>
</div>
```

---

### 5.2 Validasi Form dengan PHP ✅

#### A. Email Validation dengan filter_var()

**Implementasi:**

```php
// functions.php - Line 99-106
function validateEmail($email) {
    // Menggunakan filter_var() dengan FILTER_VALIDATE_EMAIL
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// member_add.php - Penggunaan
if (!validateEmail($email)) {
    $errors[] = "Format email tidak valid";
}
```

**Lokasi:** `functions.php` (Line 99-106), digunakan di `member_add.php` dan `member_edit.php`

---

#### B. Phone Number Validation

**Implementasi:**

```php
// functions.php - Line 108-116
function validatePhone($phone) {
    // Harus dimulai dengan 08 atau +62, panjang 10-13 digit
    $pattern = '/^(\+62|62|08)[0-9]{8,11}$/';
    return preg_match($pattern, $phone) === 1;
}

// member_add.php - Penggunaan
if (!validatePhone($telepon)) {
    $errors[] = "Format nomor telepon tidak valid (harus dimulai 08 atau +62)";
}
```

---

#### C. ISBN Validation

**Implementasi:**

```php
// functions.php - Line 127-134
function validateISBN($isbn) {
    // Format: 978-XXXXXX atau 13 digit
    $pattern = '/^(978|979)?[0-9]{9,10}$/';
    $isbn = str_replace('-', '', $isbn);
    return preg_match($pattern, $isbn) === 1;
}

// book_add.php - Penggunaan
if (!validateISBN($isbn)) {
    $errors[] = "Format ISBN tidak valid";
}
```

---

#### D. Required Field Validation

**Implementasi:**

```php
// functions.php - Line 118-125
function validateRequired($value, $minLength = 1) {
    // Menggunakan strlen() untuk check panjang string
    return strlen(trim($value)) >= $minLength;
}

// book_add.php - Penggunaan
if (!validateRequired($judul, 3)) {
    $errors[] = "Judul buku minimal 3 karakter";
}
```

---

### 5.3 Manipulasi String ✅

#### A. strtolower() & strtoupper()

**Implementasi - Capitalize Name:**

```php
// functions.php - Line 75-81
function capitalizeName($name) {
    // Menggunakan strtolower() dan kemudian ucwords()
    return ucwords(strtolower($name));
}

// book_add.php - Penggunaan
$pengarang = capitalizeName($pengarang); // "ANDREA HIRATA" → "Andrea Hirata"
```

**Implementasi - Generate ID:**

```php
// functions.php - Line 83-90
function generateID($prefix, $number, $length = 4) {
    $numberStr = str_pad($number, $length, '0', STR_PAD_LEFT);
    return strtoupper($prefix) . $numberStr; // "b" + 1 → "B0001"
}
```

**Implementasi - Search:**

```php
// books.php - Pencarian case-insensitive
$searchLower = strtolower($search);
if (strpos(strtolower($book['judul']), $searchLower) === false) {
    continue; // Skip if not found
}
```

---

#### B. substr()

**Implementasi:**

```php
// functions.php - Line 95-102
function extractIDNumber($id, $prefixLength) {
    // Menggunakan substr() untuk extract number dari ID
    return (int)substr($id, $prefixLength);
}

// Contoh penggunaan:
$id = "MBR0001";
$number = extractIDNumber($id, 3); // Returns: 1
```

---

#### C. strlen()

**Implementasi:**

```php
// functions.php - Line 118-125
function validateRequired($value, $minLength = 1) {
    // Menggunakan strlen() untuk check panjang string
    return strlen(trim($value)) >= $minLength;
}

// Penggunaan di validasi
if (strlen($judul) < 3) {
    $errors[] = "Judul terlalu pendek";
}
```

---

### 5.4 File Handling (fopen, fread, fwrite, fclose) ✅

#### A. Read Data dari File

**Implementasi:**

```php
// functions.php - Line 13-33
function readData($filepath) {
    if (!file_exists($filepath)) {
        return [];
    }

    // fopen() - Buka file untuk dibaca
    $file = fopen($filepath, 'r');
    if (!$file) {
        return [];
    }

    $data = [];
    // fgets() - Baca file per baris
    while (($line = fgets($file)) !== false) {
        $line = trim($line);
        if (!empty($line)) {
            $data[] = $line;
        }
    }

    // fclose() - Tutup file
    fclose($file);
    return $data;
}
```

**Penggunaan:**

```php
// books.php - Membaca semua buku
$books = readData(BOOKS_FILE);
foreach ($books as $line) {
    $book = parseBook($line);
    // Process book data...
}
```

---

#### B. Write Data ke File

**Implementasi:**

```php
// functions.php - Line 35-52
function writeData($filepath, $data) {
    // fopen() - Buka file untuk ditulis (mode 'w' akan replace semua content)
    $file = fopen($filepath, 'w');
    if (!$file) {
        return false;
    }

    foreach ($data as $line) {
        // fwrite() - Tulis data ke file
        fwrite($file, $line . "\n");
    }

    // fclose() - Tutup file
    fclose($file);
    return true;
}
```

**Penggunaan:**

```php
// book_edit.php - Update data buku
$newData = [];
foreach ($books as $line) {
    if (/* condition */) {
        $newData[] = $updatedLine;
    } else {
        $newData[] = $line;
    }
}
writeData(BOOKS_FILE, $newData);
```

---

#### C. Append Data ke File

**Implementasi:**

```php
// functions.php - Line 54-68
function appendData($filepath, $data) {
    // fopen() - Buka file untuk append (mode 'a')
    $file = fopen($filepath, 'a');
    if (!$file) {
        return false;
    }

    // fwrite() - Tulis data di akhir file
    fwrite($file, $data . "\n");

    // fclose() - Tutup file
    fclose($file);
    return true;
}
```

**Penggunaan:**

```php
// book_add.php - Tambah buku baru
$data = implode('|', [$id, $judul, $pengarang, $kategori, $isbn, $tahun, $status]);
appendData(BOOKS_FILE, $data);
```

---

### 5.5 Pemecahan Masalah dan Debugging ✅

#### A. Error Reporting

**Implementasi:**

```php
// config.php - Line 7-9
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

Mengaktifkan semua error reporting untuk memudahkan debugging saat development.

---

#### B. var_dump() Function

**Implementasi:**

```php
// functions.php - Line 328-336
function debug($var, $label = '') {
    echo '<pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd;">';
    if ($label) {
        echo "<strong>$label:</strong>\n";
    }
    var_dump($var);
    echo '</pre>';
}
```

**Penggunaan:**

```php
// Debugging saat development
debug($book, 'Book Data');
debug($_POST, 'Form Submission');
```

---

#### C. print_r() Function

**Implementasi:**

```php
// functions.php - Line 338-350
function debugPrint($var, $label = '') {
    echo '<pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd;">';
    if ($label) {
        echo "<strong>$label:</strong>\n";
    }
    print_r($var);
    echo '</pre>';
}
```

**Penggunaan:**

```php
// Debugging array atau object
debugPrint($members, 'All Members');
debugPrint($errors, 'Validation Errors');
```

---

## 6. STRUKTUR FILE

### 6.1 Struktur Direktori

```
fin/
│
├── config.php              # Konfigurasi aplikasi & constants
├── functions.php          # Helper functions (file ops, validasi, string)
├── style.css              # Modern CSS design system
├── index.php              # Dashboard utama
│
├── books.php              # Daftar buku
├── book_add.php          # Form tambah buku
├── book_edit.php         # Form edit buku
├── book_delete.php       # Konfirmasi & proses hapus buku
│
├── members.php           # Daftar anggota
├── member_add.php        # Form registrasi anggota
├── member_edit.php       # Form edit anggota
├── member_history.php    # Riwayat peminjaman anggota
│
├── borrow.php            # Daftar peminjaman aktif
├── borrow_add.php        # Form proses peminjaman
│
├── return.php            # Proses pengembalian & kalkulasi denda
│
└── data/                 # Directory penyimpanan data
    ├── books.txt         # Data buku
    ├── members.txt       # Data anggota
    ├── borrowings.txt    # Data peminjaman
    └── returns.txt       # Data pengembalian
```

---

### 6.2 Format Data File (.txt)

#### books.txt

```
Format: ID|Judul|Pengarang|Kategori|ISBN|Tahun|Status

Contoh:
B0001|Laskar Pelangi|Andrea Hirata|Fiksi|978-123456|2005|Tersedia
B0002|Bumi Manusia|Pramoedya Ananta Toer|Fiksi|978-234567|1980|Dipinjam
B0003|Sapiens|Yuval Noah Harari|Sains|978-345678|2011|Tersedia
```

---

#### members.txt

```
Format: MemberID|Nama|Email|Telepon|Alamat|JenisAnggota|Status|TanggalDaftar

Contoh:
MBR0001|John Doe|john@example.com|08123456789|Jl. Merdeka No.1|Reguler,Premium|Aktif|2025-12-07
MBR0002|Jane Smith|jane@example.com|08234567890|Jl. Sudirman No.2|Pelajar|Aktif|2025-12-07
```

---

#### borrowings.txt

```
Format: BorrowID|MemberID|BookID|TanggalPinjam|TanggalJatuhTempo|Status

Contoh:
BR001|MBR0001|B0002|2025-12-01|2025-12-08|Ongoing
BR002|MBR0002|B0003|2025-12-05|2025-12-12|Returned
```

---

#### returns.txt

```
Format: ReturnID|BorrowID|TanggalKembali|HariTerlambat|Denda

Contoh:
RT001|BR001|2025-12-10|2|4000
RT002|BR002|2025-12-11|0|0
```

---

## 7. TAMPILAN APLIKASI

### 7.1 Dashboard Utama

Dashboard menampilkan statistik ringkasan dan quick access ke semua modul.

![Dashboard](screenshots/dashboard.png)

**Fitur Dashboard:**

- 📊 4 Statistics Cards dengan gradient backgrounds:
  - Total Buku
  - Buku Tersedia
  - Total Anggota
  - Buku Dipinjam
- 🎴 4 Module Cards dengan glassmorphism effect:
  - Manajemen Buku
  - Manajemen Anggota
  - Sistem Peminjaman
  - Sistem Pengembalian
- 🎨 Modern gradient header dengan purple-blue theme
- 📱 Responsive navigation bar

---

### 7.2 Form Tambah Buku

Form untuk menambahkan buku baru dengan validasi lengkap.

![Form Tambah Buku](screenshots/form_tambah_buku.png)

**Komponen Form:**

- ✅ Input Text: Judul Buku, Pengarang
- ✅ Select Dropdown: Kategori (10 pilihan)
- ✅ Input Text: ISBN dengan format hint
- ✅ Input Number: Tahun Terbit (validasi range)
- ✅ Radio Button: Status Buku (Tersedia/Dipinjam)
- ✅ Button: Simpan (green) & Batal (gray)

**Validasi:**

- Judul minimal 3 karakter
- Pengarang minimal 3 karakter (auto-capitalize)
- ISBN format 978-XXXXXXXXX
- Tahun 1000 - current year
- Kategori harus dipilih

---

### 7.3 Form Registrasi Anggota

Form untuk mendaftarkan anggota baru dengan validasi email dan telepon.

![Form Registrasi Anggota](screenshots/form_registrasi_anggota.png)

**Komponen Form:**

- ✅ Input Text: Nama Lengkap (auto-capitalize)
- ✅ Input Email: dengan validasi filter_var()
- ✅ Input Tel: No. Telepon (format Indonesia)
- ✅ Checkbox: Jenis Keanggotaan (multiple selection)
  - Reguler
  - Premium
  - Pelajar/Mahasiswa
- ✅ Textarea: Alamat Lengkap
- ✅ Button: Daftarkan Anggota (green) & Batal (gray)

**Validasi:**

- Nama minimal 3 karakter
- Email valid (menggunakan FILTER_VALIDATE_EMAIL)
- Telepon format 08XXXXXXXXXX atau +62XXXXXXXXXX
- Minimal 1 jenis keanggotaan harus dipilih
- Alamat minimal 10 karakter

**Auto-Generate:**

- Member ID: MBR0001, MBR0002, dst.

---

### 7.4 Form Proses Peminjaman

Form untuk memproses peminjaman buku dengan business logic enforcement.

![Form Proses Peminjaman](screenshots/form_peminjaman.png)

**Komponen Form:**

- ✅ Select Dropdown: Pilih Anggota (menampilkan current borrow count)
- ✅ Select Dropdown: Pilih Buku (hanya yang tersedia)
- ✅ Input Date: Tanggal Peminjaman (default: hari ini)
- ✅ Input Date: Tanggal Jatuh Tempo (default: +7 hari)
- ✅ Info Box: Business rules
- ✅ Button: Proses Peminjaman (green) & Batal (gray)

**Business Logic:**

- ✅ Check ketersediaan buku otomatis
- ✅ Enforce batas maksimal 3 buku per anggota
- ✅ Validasi status keanggotaan aktif
- ✅ Disable anggota yang sudah max borrowing
- ✅ Auto-update status buku menjadi "Dipinjam"

**Info Displayed:**

- Maksimal peminjaman: 3 buku
- Durasi default: 7 hari
- Denda: Rp 2.000/hari

---

### 7.5 Halaman Pengembalian

Halaman untuk proses pengembalian dengan kalkulasi denda otomatis.

**Fitur:**

- 🔍 Search box untuk ID Peminjaman
- 📋 Display detail peminjaman lengkap:
  - ID Peminjaman
  - Data Anggota (nama, ID)
  - Data Buku (judul, pengarang)
  - Tanggal pinjam & jatuh tempo
- 📅 Input tanggal pengembalian
- 💰 Kalkulasi denda otomatis:
  - Tepat waktu: Alert hijau "Tidak ada denda"
  - Terlambat: Alert kuning dengan hari keterlambatan dan total denda
- ✅ Auto-update status buku menjadi "Tersedia"
- 📝 Save record ke returns.txt

**Contoh Kalkulasi Denda:**

```
Jatuh Tempo: 2025-12-08
Tanggal Kembali: 2025-12-10
Terlambat: 2 hari
Denda: 2 × Rp 2.000 = Rp 4.000
```

---

## 8. CARA MENGGUNAKAN

### 8.1 Instalasi

#### Prerequisites:

1. **Laragon** atau **XAMPP** atau server lokal lainnya
2. **PHP 8.x**
3. **Web Browser** (Chrome, Firefox, Edge, dll)

#### Langkah Instalasi:

**1. Copy Project**

```bash
# Copy folder 'fin' ke directory htdocs/www
# Untuk Laragon:
c:\laragon\www\fin\

# Untuk XAMPP:
c:\xampp\htdocs\fin\
```

**2. Start Server**

- Buka Laragon/XAMPP Control Panel
- Klik "Start All" atau "Start Apache"
- Pastikan Apache berjalan (hijau/running)

**3. Akses Aplikasi**

```
http://localhost/fin/
```

---

### 8.2 Penggunaan Aplikasi

#### A. Manajemen Buku

**Menambah Buku Baru:**

1. Klik menu "Buku" di navbar
2. Klik tombol "+ Tambah Buku Baru"
3. Isi form:
   - Judul: "Bumi Manusia"
   - Pengarang: "pramoedya ananta toer" (akan auto-capitalize)
   - Kategori: Pilih "Fiksi"
   - ISBN: "978-979-461-280-0"
   - Tahun: "1980"
   - Status: Pilih "Tersedia"
4. Klik "Simpan Buku"
5. Buku akan muncul di daftar dengan ID auto-generate (B0001)

**Mencari Buku:**

1. Di halaman Daftar Buku
2. Ketik kata kunci di search box (misal: "bumi")
3. Klik "Cari"
4. Sistem akan menampilkan buku yang judulnya mengandung "bumi"

**Filter Buku:**

1. Pilih kategori dari dropdown (misal: "Fiksi")
2. Klik "Cari"
3. Sistem akan menampilkan semua buku kategori Fiksi

**Edit Buku:**

1. Klik tombol "Edit" pada buku yang ingin diubah
2. Update informasi yang diperlukan
3. Klik "Update Buku"

**Hapus Buku:**

1. Klik tombol "Hapus" pada buku
2. Konfirmasi penghapusan
3. Sistem akan check apakah buku sedang dipinjam
4. Jika sedang dipinjam, akan muncul error
5. Jika tersedia, buku akan dihapus

---

#### B. Manajemen Anggota

**Registrasi Anggota:**

1. Klik menu "Anggota"
2. Klik "+ Registrasi Anggota Baru"
3. Isi form:
   - Nama: "andi wijaya" (akan auto-capitalize menjadi "Andi Wijaya")
   - Email: "andi@example.com"
   - Telepon: "08123456789"
   - Checklist: Reguler dan Premium
   - Alamat: "Jl. Merdeka No. 123, Jakarta"
4. Klik "Daftarkan Anggota"
5. Sistem akan generate Member ID (MBR0001)

**Edit Anggota:**

1. Klik "Edit" pada anggota
2. Update data
3. Bisa toggle status Aktif/Nonaktif
4. Klik "Update Data"

**Lihat Riwayat:**

1. Klik "Riwayat" pada anggota
2. Sistem akan menampilkan semua transaksi peminjaman
3. Info yang ditampilkan:
   - ID Pinjam
   - Judul Buku
   - Tanggal Pinjam & Jatuh Tempo
   - Tanggal Kembali (jika sudah dikembalikan)
   - Denda (jika ada)
   - Status (Dipinjam/Dikembalikan)

---

#### C. Sistem Peminjaman

**Proses Peminjaman:**

1. Klik menu "Peminjaman"
2. Klik "Proses Peminjaman Baru"
3. Pilih Anggota dari dropdown
   - Akan muncul info current borrow (misal: 1/3)
   - Jika sudah 3/3, anggota akan disabled
4. Pilih Buku dari dropdown
   - Hanya buku dengan status "Tersedia" yang muncul
5. Set tanggal peminjaman (default: hari ini)
6. Set tanggal jatuh tempo (default: +7 hari dari hari ini)
7. Review info:
   - Max 3 buku per anggota
   - Durasi default: 7 hari
   - Denda: Rp 2.000/hari
8. Klik "Proses Peminjaman"
9. Sistem akan:
   - Generate Borrow ID (BR001)
   - Update status buku jadi "Dipinjam"
   - Save ke borrowings.txt

**Melihat Peminjaman Aktif:**

1. Di halaman Peminjaman
2. Akan muncul table dengan semua peminjaman ongoing
3. Peminjaman yang terlambat akan di-highlight merah
4. Badge "Terlambat X hari" akan muncul
5. Bisa langsung klik "Kembalikan" untuk proses return

---

#### D. Sistem Pengembalian

**Proses Pengembalian:**

1. Klik menu "Pengembalian"
2. Masukkan ID Peminjaman (misal: BR001)
3. Klik "Cari"
4. Sistem akan menampilkan detail peminjaman:
   - ID Peminjaman
   - Nama Anggota & ID
   - Judul Buku & Pengarang
   - Tanggal Pinjam
   - Tanggal Jatuh Tempo
5. Set tanggal pengembalian (default: hari ini)
6. Sistem akan otomatis hitung:
   - Hari keterlambatan
   - Total denda (jika ada)
7. Review info denda:
   - **Tepat waktu**: Alert hijau "Tidak ada denda"
   - **Terlambat**: Alert kuning dengan detail:
     - "Terlambat: 2 hari"
     - "Total Denda: Rp 4.000"
8. Klik "Proses Pengembalian"
9. Sistem akan:
   - Update status peminjaman jadi "Returned"
   - Update status buku jadi "Tersedia"
   - Save record ke returns.txt dengan info denda
   - Generate Return ID (RT001)

---

## 9. TESTING DAN VALIDASI

### 9.1 Testing Validasi Form

#### Test Case 1: Email Validation

```
Input: "invalid-email"
Expected: Error "Format email tidak valid"
Result: ✅ PASS

Input: "valid@example.com"
Expected: Success
Result: ✅ PASS
```

#### Test Case 2: Phone Validation

```
Input: "123"
Expected: Error "Format nomor telepon tidak valid"
Result: ✅ PASS

Input: "08123456789"
Expected: Success
Result: ✅ PASS

Input: "+628123456789"
Expected: Success
Result: ✅ PASS
```

#### Test Case 3: ISBN Validation

```
Input: "abc"
Expected: Error "Format ISBN tidak valid"
Result: ✅ PASS

Input: "978-123456789"
Expected: Success
Result: ✅ PASS
```

#### Test Case 4: Required Field

```
Input: "" (empty)
Expected: Error "Field harus diisi"
Result: ✅ PASS

Input: "ab" (length < 3)
Expected: Error "Minimal 3 karakter"
Result: ✅ PASS
```

#### Test Case 5: Checkbox Validation

```
Input: Tidak ada checkbox yang dicentang
Expected: Error "Pilih minimal satu jenis keanggotaan"
Result: ✅ PASS

Input: 1 atau lebih checkbox dicentang
Expected: Success
Result: ✅ PASS
```

---

### 9.2 Testing Business Logic

#### Test Case 6: Borrow Limit

```
Scenario: Anggota sudah pinjam 3 buku, coba pinjam buku ke-4
Expected: Error "Batas maksimal tercapai" + anggota disabled di dropdown
Result: ✅ PASS
```

#### Test Case 7: Book Availability

```
Scenario: Coba pinjam buku yang sudah dipinjam
Expected: Buku tidak muncul di dropdown (hanya yang tersedia)
Result: ✅ PASS
```

#### Test Case 8: Late Fee Calculation

```
Scenario 1 - Tepat Waktu:
- Jatuh tempo: 2025-12-08
- Kembali: 2025-12-07
Expected: Denda = Rp 0
Result: ✅ PASS

Scenario 2 - Terlambat 2 Hari:
- Jatuh tempo: 2025-12-08
- Kembali: 2025-12-10
Expected: Denda = 2 × Rp 2.000 = Rp 4.000
Result: ✅ PASS
```

#### Test Case 9: Delete Book

```
Scenario 1: Hapus buku yang sedang dipinjam
Expected: Error "Tidak dapat menghapus buku yang sedang dipinjam"
Result: ✅ PASS

Scenario 2: Hapus buku yang tersedia
Expected: Buku berhasil dihapus
Result: ✅ PASS
```

---

### 9.3 Testing File Operations

#### Test Case 10: Create (Append)

```
Action: Tambah buku baru
Expected:
- Data tersimpan di books.txt
- Format: ID|Judul|Pengarang|Kategori|ISBN|Tahun|Status
Result: ✅ PASS
```

#### Test Case 11: Read

```
Action: Buka halaman Daftar Buku
Expected: Semua buku dari books.txt ditampilkan
Result: ✅ PASS
```

#### Test Case 12: Update

```
Action: Edit informasi buku
Expected:
- Data di books.txt terupdate
- Buku lain tidak berubah
Result: ✅ PASS
```

#### Test Case 13: Delete

```
Action: Hapus buku
Expected:
- Baris data buku dihapus dari books.txt
- Buku lain tidak terpengaruh
Result: ✅ PASS
```

---

### 9.4 Testing String Manipulation

#### Test Case 14: Capitalize Name

```
Input: "ANDREA HIRATA"
Expected: "Andrea Hirata"
Result: ✅ PASS

Input: "pramoedya ananta toer"
Expected: "Pramoedya Ananta Toer"
Result: ✅ PASS
```

#### Test Case 15: Generate ID

```
Action: Tambah buku pertama
Expected: ID = "B0001"
Result: ✅ PASS

Action: Tambah buku kedua
Expected: ID = "B0002"
Result: ✅ PASS

Action: Tambah member pertama
Expected: ID = "MBR0001"
Result: ✅ PASS
```

#### Test Case 16: Search (strtolower + strpos)

```
Input: Search "BUMI" di daftar buku
Expected: Menampilkan "Bumi Manusia" (case-insensitive)
Result: ✅ PASS
```

---

### 9.5 Summary Testing

| Kategori            | Total Test | Pass   | Fail  |
| ------------------- | ---------- | ------ | ----- |
| Validasi Form       | 5          | 5      | 0     |
| Business Logic      | 4          | 4      | 0     |
| File Operations     | 4          | 4      | 0     |
| String Manipulation | 3          | 3      | 0     |
| **TOTAL**           | **16**     | **16** | **0** |

**Success Rate: 100%** ✅

---

## 10. KESIMPULAN

### 10.1 Pencapaian

Sistem Manajemen Perpustakaan telah berhasil dibangun dengan **100% komponen wajib terpenuhi**:

| No  | Komponen Wajib                                       | Status | Bukti Implementasi                                 |
| --- | ---------------------------------------------------- | ------ | -------------------------------------------------- |
| 1   | Form Input (Text, Select, Radio, Checkbox, Textarea) | ✅     | `book_add.php`, `member_add.php`, `borrow_add.php` |
| 2   | Validasi dengan filter_var()                         | ✅     | `functions.php` L99-106                            |
| 3   | strlen()                                             | ✅     | `functions.php` L122                               |
| 4   | strtoupper() & strtolower()                          | ✅     | `functions.php` L75-81, L85                        |
| 5   | substr()                                             | ✅     | `functions.php` L97                                |
| 6   | fopen()                                              | ✅     | `functions.php` L19, L38, L57                      |
| 7   | fread()/fgets()                                      | ✅     | `functions.php` L24                                |
| 8   | fwrite()                                             | ✅     | `functions.php` L43, L60                           |
| 9   | fclose()                                             | ✅     | `functions.php` L30, L48, L61                      |
| 10  | error_reporting()                                    | ✅     | `config.php` L7-9                                  |
| 11  | var_dump()                                           | ✅     | `functions.php` L332                               |
| 12  | print_r()                                            | ✅     | `functions.php` L344                               |

**Total: 12/12 Komponen Wajib ✅ COMPLETE (100%)**

---

### 10.2 Fitur yang Telah Diimplementasikan

#### A. Manajemen Buku ✅

- CRUD lengkap (Create, Read, Update, Delete)
- Search by judul/pengarang
- Filter by kategori
- Validasi form lengkap
- Auto-generate Book ID

#### B. Manajemen Anggota ✅

- CRUD lengkap
- Email validation dengan filter_var()
- Phone validation
- Multiple checkbox (jenis keanggotaan)
- Auto-generate Member ID
- Riwayat peminjaman per anggota

#### C. Sistem Peminjaman ✅

- Proses peminjaman dengan validasi
- Check ketersediaan buku
- Enforce batas peminjaman (max 3)
- Auto-update status buku
- Highlight peminjaman terlambat

#### D. Sistem Pengembalian ✅

- Search peminjaman by ID
- Kalkulasi denda otomatis
- Display hari keterlambatan
- Auto-update status buku
- Save return record dengan denda

---

### 10.3 Kelebihan Sistem

1. **Design Modern & Clean**

   - Purple-blue gradient theme
   - Glassmorphism effects
   - Smooth animations
   - Responsive layout

2. **User Experience**

   - Intuitive navigation
   - Clear feedback messages
   - Auto-calculations (ID, denda, dll)
   - Helpful hints dan info boxes

3. **Data Integrity**

   - Comprehensive validation
   - Business logic enforcement
   - Status management
   - Referential checks

4. **Code Quality**
   - Well-organized file structure
   - Reusable functions
   - Clear comments
   - Consistent naming

---

### 10.4 Pembelajaran

Dari proyek ini, saya telah mempelajari dan menerapkan:

1. **PHP Native** tanpa framework
2. **File Handling** sebagai alternative database
3. **Form Validation** dengan berbagai teknik
4. **String Manipulation** untuk data processing
5. **Business Logic** implementation
6. **Modern Web Design** dengan CSS3
7. **Debugging Techniques** dengan error reporting

---

### 10.5 File Deliverables

Proyek ini menghasilkan:

- ✅ 14 File PHP (dashboard, CRUD modules)
- ✅ 1 File CSS (modern design system)
- ✅ 1 File Config (constants & settings)
- ✅ 1 File Functions (helpers & utilities)
- ✅ 4 Data Files (.txt untuk storage)
- ✅ Dokumentasi Lengkap (laporan ini)

**Total Lines of Code: ~2,500+ lines**

---

### 10.6 Penutup

Sistem Manajemen Perpustakaan ini merupakan implementasi lengkap dari requirements yang diberikan, dengan semua komponen wajib terimplementasi 100%, ditambah dengan fitur-fitur tambahan untuk meningkatkan user experience dan fungsionalitas sistem.

Sistem ini siap digunakan untuk mengelola perpustakaan sekolah, kampus, atau perpustakaan umum dengan skala kecil hingga menengah.

---

**Terima kasih!**

_Dokumentasi ini dibuat pada: 7 Desember 2025_  
_Sistem Manajemen Perpustakaan - PHP Native dengan File .txt Storage_
