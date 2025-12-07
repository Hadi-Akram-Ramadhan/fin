<?php
/**
 * Helper Functions - Sistem Manajemen Perpustakaan
 * Berisi fungsi-fungsi untuk file operations, validasi, dan manipulasi string
 */

require_once 'config.php';

// ==================== FILE OPERATIONS (Komponen Wajib #4) ====================

/**
 * Read all data from file
 * @param string $filepath Path to file
 * @return array Array of data lines
 */
function readData($filepath) {
    if (!file_exists($filepath)) {
        return [];
    }
    
    $file = fopen($filepath, 'r');
    if (!$file) {
        return [];
    }
    
    $data = [];
    while (($line = fgets($file)) !== false) {
        $line = trim($line);
        if (!empty($line)) {
            $data[] = $line;
        }
    }
    
    fclose($file);
    return $data;
}

/**
 * Write data to file
 * @param string $filepath Path to file
 * @param array $data Array of data to write
 * @return bool Success status
 */
function writeData($filepath, $data) {
    $file = fopen($filepath, 'w');
    if (!$file) {
        return false;
    }
    
    foreach ($data as $line) {
        fwrite($file, $line . "\n");
    }
    
    fclose($file);
    return true;
}

/**
 * Append data to file
 * @param string $filepath Path to file
 * @param string $data Data to append
 * @return bool Success status
 */
function appendData($filepath, $data) {
    $file = fopen($filepath, 'a');
    if (!$file) {
        return false;
    }
    
    fwrite($file, $data . "\n");
    fclose($file);
    return true;
}

// ==================== STRING MANIPULATION (Komponen Wajib #3) ====================

/**
 * Sanitize input string
 * @param string $input Input string
 * @return string Sanitized string
 */
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

/**
 * Capitalize name properly
 * @param string $name Name to capitalize
 * @return string Capitalized name
 */
function capitalizeName($name) {
    // Menggunakan strtolower() dan kemudian ucwords()
    return ucwords(strtolower($name));
}

/**
 * Generate ID with prefix
 * @param string $prefix ID prefix
 * @param int $number Number part
 * @param int $length Total length of number part
 * @return string Generated ID
 */
function generateID($prefix, $number, $length = 4) {
    // Menggunakan strlen() untuk validasi panjang
    $numberStr = str_pad($number, $length, '0', STR_PAD_LEFT);
    return strtoupper($prefix) . $numberStr;
}

/**
 * Extract ID number from full ID
 * @param string $id Full ID
 * @param int $prefixLength Length of prefix
 * @return int Number part
 */
function extractIDNumber($id, $prefixLength) {
    // Menggunakan substr() untuk extract number
    return (int)substr($id, $prefixLength);
}

// ==================== VALIDATION (Komponen Wajib #2) ====================

/**
 * Validate email address
 * @param string $email Email to validate
 * @return bool Valid status
 */
function validateEmail($email) {
    // Menggunakan filter_var() dengan FILTER_VALIDATE_EMAIL
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (Indonesian format)
 * @param string $phone Phone number
 * @return bool Valid status
 */
function validatePhone($phone) {
    // Harus dimulai dengan 08 atau +62, panjang 10-13 digit
    $pattern = '/^(\+62|62|08)[0-9]{8,11}$/';
    return preg_match($pattern, $phone) === 1;
}

/**
 * Validate required field
 * @param string $value Value to validate
 * @param int $minLength Minimum length
 * @return bool Valid status
 */
function validateRequired($value, $minLength = 1) {
    // Menggunakan strlen() untuk check panjang string
    return strlen(trim($value)) >= $minLength;
}

/**
 * Validate ISBN format
 * @param string $isbn ISBN to validate
 * @return bool Valid status
 */
function validateISBN($isbn) {
    // Format: 978-XXXXXX atau 13 digit
    $pattern = '/^(978|979)?[0-9]{9,10}$/';
    $isbn = str_replace('-', '', $isbn);
    return preg_match($pattern, $isbn) === 1;
}

// ==================== DATA PARSING ====================

/**
 * Parse book data line
 * @param string $line Data line
 * @return array|null Parsed data array
 */
function parseBook($line) {
    $parts = explode('|', $line);
    if (count($parts) < 7) return null;
    
    return [
        'id' => $parts[0],
        'judul' => $parts[1],
        'pengarang' => $parts[2],
        'kategori' => $parts[3],
        'isbn' => $parts[4],
        'tahun' => $parts[5],
        'status' => $parts[6]
    ];
}

/**
 * Parse member data line
 * @param string $line Data line
 * @return array|null Parsed data array
 */
function parseMember($line) {
    $parts = explode('|', $line);
    if (count($parts) < 8) return null;
    
    return [
        'id' => $parts[0],
        'nama' => $parts[1],
        'email' => $parts[2],
        'telepon' => $parts[3],
        'alamat' => $parts[4],
        'jenis' => $parts[5],
        'status' => $parts[6],
        'tanggal_daftar' => $parts[7]
    ];
}

/**
 * Parse borrowing data line
 * @param string $line Data line
 * @return array|null Parsed data array
 */
function parseBorrowing($line) {
    $parts = explode('|', $line);
    if (count($parts) < 6) return null;
    
    return [
        'id' => $parts[0],
        'member_id' => $parts[1],
        'book_id' => $parts[2],
        'tanggal_pinjam' => $parts[3],
        'tanggal_jatuh_tempo' => $parts[4],
        'status' => $parts[5]
    ];
}

/**
 * Parse return data line
 * @param string $line Data line
 * @return array|null Parsed data array
 */
function parseReturn($line) {
    $parts = explode('|', $line);
    if (count($parts) < 5) return null;
    
    return [
        'id' => $parts[0],
        'borrow_id' => $parts[1],
        'tanggal_kembali' => $parts[2],
        'hari_terlambat' => $parts[3],
        'denda' => $parts[4]
    ];
}

// ==================== UTILITY FUNCTIONS ====================

/**
 * Get next available ID
 * @param string $filepath File path
 * @param string $prefix ID prefix
 * @param int $prefixLength Length of prefix
 * @return string Next ID
 */
function getNextID($filepath, $prefix, $prefixLength) {
    $data = readData($filepath);
    
    if (empty($data)) {
        return generateID($prefix, 1);
    }
    
    $maxNumber = 0;
    foreach ($data as $line) {
        $parts = explode('|', $line);
        $id = $parts[0];
        $number = extractIDNumber($id, $prefixLength);
        if ($number > $maxNumber) {
            $maxNumber = $number;
        }
    }
    
    return generateID($prefix, $maxNumber + 1);
}

/**
 * Find data by ID
 * @param string $filepath File path
 * @param string $id ID to find
 * @return string|null Data line
 */
function findByID($filepath, $id) {
    $data = readData($filepath);
    
    foreach ($data as $line) {
        $parts = explode('|', $line);
        if ($parts[0] === $id) {
            return $line;
        }
    }
    
    return null;
}

/**
 * Update data by ID
 * @param string $filepath File path
 * @param string $id ID to update
 * @param string $newData New data line
 * @return bool Success status
 */
function updateByID($filepath, $id, $newData) {
    $data = readData($filepath);
    $updated = false;
    
    foreach ($data as $key => $line) {
        $parts = explode('|', $line);
        if ($parts[0] === $id) {
            $data[$key] = $newData;
            $updated = true;
            break;
        }
    }
    
    if ($updated) {
        return writeData($filepath, $data);
    }
    
    return false;
}

/**
 * Delete data by ID
 * @param string $filepath File path
 * @param string $id ID to delete
 * @return bool Success status
 */
function deleteByID($filepath, $id) {
    $data = readData($filepath);
    $newData = [];
    $deleted = false;
    
    foreach ($data as $line) {
        $parts = explode('|', $line);
        if ($parts[0] !== $id) {
            $newData[] = $line;
        } else {
            $deleted = true;
        }
    }
    
    if ($deleted) {
        return writeData($filepath, $newData);
    }
    
    return false;
}

/**
 * Calculate late fee
 * @param string $dueDate Due date (Y-m-d)
 * @param string $returnDate Return date (Y-m-d)
 * @return array ['days' => int, 'fee' => int]
 */
function calculateLateFee($dueDate, $returnDate) {
    $due = new DateTime($dueDate);
    $return = new DateTime($returnDate);
    
    $diff = $return->diff($due);
    $days = $diff->days;
    
    // If returned late
    if ($return > $due) {
        $fee = $days * LATE_FEE_PER_DAY;
        return ['days' => $days, 'fee' => $fee];
    }
    
    return ['days' => 0, 'fee' => 0];
}

/**
 * Count active borrowings by member
 * @param string $memberID Member ID
 * @return int Count
 */
function countActiveBorrowings($memberID) {
    $data = readData(BORROWINGS_FILE);
    $count = 0;
    
    foreach ($data as $line) {
        $borrow = parseBorrowing($line);
        if ($borrow && $borrow['member_id'] === $memberID && $borrow['status'] === BORROW_STATUS_ONGOING) {
            $count++;
        }
    }
    
    return $count;
}

/**
 * Format currency (Rupiah)
 * @param int $amount Amount
 * @return string Formatted currency
 */
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format date to Indonesian format
 * @param string $date Date (Y-m-d)
 * @return string Formatted date
 */
function formatDate($date) {
    $months = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    
    $parts = explode('-', $date);
    if (count($parts) === 3) {
        return $parts[2] . ' ' . $months[$parts[1]] . ' ' . $parts[0];
    }
    
    return $date;
}

// ==================== DEBUGGING HELPERS (Komponen Wajib #5) ====================

/**
 * Debug print with var_dump
 * @param mixed $var Variable to dump
 * @param string $label Label for the dump
 */
function debug($var, $label = '') {
    echo '<pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd;">';
    if ($label) {
        echo "<strong>$label:</strong>\n";
    }
    var_dump($var);
    echo '</pre>';
}

/**
 * Debug print with print_r
 * @param mixed $var Variable to print
 * @param string $label Label for the print
 */
function debugPrint($var, $label = '') {
    echo '<pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd;">';
    if ($label) {
        echo "<strong>$label:</strong>\n";
    }
    print_r($var);
    echo '</pre>';
}
