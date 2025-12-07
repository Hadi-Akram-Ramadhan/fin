<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', __DIR__);
define('DATA_PATH', BASE_PATH . '/data');

define('BOOKS_FILE', DATA_PATH . '/books.txt');
define('MEMBERS_FILE', DATA_PATH . '/members.txt');
define('BORROWINGS_FILE', DATA_PATH . '/borrowings.txt');
define('RETURNS_FILE', DATA_PATH . '/returns.txt');

define('MAX_BORROW_LIMIT', 3); 
define('LATE_FEE_PER_DAY', 2000); 
define('DEFAULT_BORROW_DAYS', 7); 

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

define('STATUS_AVAILABLE', 'Tersedia');
define('STATUS_BORROWED', 'Dipinjam');
define('STATUS_ACTIVE', 'Aktif');
define('STATUS_INACTIVE', 'Nonaktif');
define('BORROW_STATUS_ONGOING', 'Ongoing');
define('BORROW_STATUS_RETURNED', 'Returned');

if (!file_exists(DATA_PATH)) {
    mkdir(DATA_PATH, 0777, true);
}

$files = [BOOKS_FILE, MEMBERS_FILE, BORROWINGS_FILE, RETURNS_FILE];
foreach ($files as $file) {
    if (!file_exists($file)) {
        touch($file);
    }
}
