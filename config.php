<?php
/**
 * Configuration File - Sistem Manajemen Perpustakaan
 * Berisi konfigurasi path, konstanta, dan pengaturan error reporting
 */

// Error Reporting untuk debugging (Komponen Wajib #5)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base path
define('BASE_PATH', __DIR__);
define('DATA_PATH', BASE_PATH . '/data');

// Data file paths
define('BOOKS_FILE', DATA_PATH . '/books.txt');
define('MEMBERS_FILE', DATA_PATH . '/members.txt');
define('BORROWINGS_FILE', DATA_PATH . '/borrowings.txt');
define('RETURNS_FILE', DATA_PATH . '/returns.txt');

// Application constants
define('MAX_BORROW_LIMIT', 3); // Maksimal buku yang bisa dipinjam per anggota
define('LATE_FEE_PER_DAY', 2000); // Denda per hari keterlambatan (Rp)
define('DEFAULT_BORROW_DAYS', 7); // Durasi peminjaman default (hari)

// Categories
define('BOOK_CATEGORIES', [
    'Fiksi',
    'Non-Fiksi',
    'Sains',
    'Teknologi',
    'Sejarah',
    'Biografi',
    'Pendidikan',
    'Religi',
    'Komik',
    'Majalah'
]);

// Status constants
define('STATUS_AVAILABLE', 'Tersedia');
define('STATUS_BORROWED', 'Dipinjam');
define('STATUS_ACTIVE', 'Aktif');
define('STATUS_INACTIVE', 'Nonaktif');
define('BORROW_STATUS_ONGOING', 'Ongoing');
define('BORROW_STATUS_RETURNED', 'Returned');

// Create data directory if not exists
if (!file_exists(DATA_PATH)) {
    mkdir(DATA_PATH, 0777, true);
}

// Initialize data files if not exists
$files = [BOOKS_FILE, MEMBERS_FILE, BORROWINGS_FILE, RETURNS_FILE];
foreach ($files as $file) {
    if (!file_exists($file)) {
        touch($file);
    }
}
