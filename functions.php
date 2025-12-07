<?php

require_once 'config.php';

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

function appendData($filepath, $data) {
    $file = fopen($filepath, 'a');
    if (!$file) {
        return false;
    }
    
    fwrite($file, $data . "\n");
    fclose($file);
    return true;
}

function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input);
    return $input;
}

function capitalizeName($name) {
    
    return ucwords(strtolower($name));
}

function generateID($prefix, $number, $length = 4) {
    
    $numberStr = str_pad($number, $length, '0', STR_PAD_LEFT);
    return strtoupper($prefix) . $numberStr;
}

function extractIDNumber($id, $prefixLength) {
    
    return (int)substr($id, $prefixLength);
}

function validateEmail($email) {
    
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    
    $pattern = '/^(\+62|62|08)[0-9]{8,11}$/';
    return preg_match($pattern, $phone) === 1;
}

function validateRequired($value, $minLength = 1) {
    
    return strlen(trim($value)) >= $minLength;
}

function validateISBN($isbn) {
    
    $pattern = '/^(978|979)?[0-9]{9,10}$/';
    $isbn = str_replace('-', '', $isbn);
    return preg_match($pattern, $isbn) === 1;
}

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

function calculateLateFee($dueDate, $returnDate) {
    $due = new DateTime($dueDate);
    $return = new DateTime($returnDate);
    
    $diff = $return->diff($due);
    $days = $diff->days;
    
    if ($return > $due) {
        $fee = $days * LATE_FEE_PER_DAY;
        return ['days' => $days, 'fee' => $fee];
    }
    
    return ['days' => 0, 'fee' => 0];
}

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

function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

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

function debug($var, $label = '') {
    echo '<pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd;">';
    if ($label) {
        echo "<strong>$label:</strong>\n";
    }
    var_dump($var);
    echo '</pre>';
}

function debugPrint($var, $label = '') {
    echo '<pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ddd;">';
    if ($label) {
        echo "<strong>$label:</strong>\n";
    }
    print_r($var);
    echo '</pre>';
}
