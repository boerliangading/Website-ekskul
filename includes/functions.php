<?php
// Functions helper untuk sistem ekstrakurikuler

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_nim($nim) {
    // Validasi NIM (contoh: minimal 8 karakter, hanya angka)
    return preg_match('/^[0-9]{8,}$/', $nim);
}

function format_date($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

function format_datetime($datetime, $format = 'd/m/Y H:i') {
    return date($format, strtotime($datetime));
}

function get_total_count($pdo, $table) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM $table");
        $stmt->execute();
        return $stmt->fetchColumn();
    } catch(PDOException $e) {
        return 0;
    }
}

function get_mahasiswa_data($pdo, $nim = null) {
    try {
        if ($nim) {
            $stmt = $pdo->prepare("SELECT * FROM mahasiswa WHERE nim = ?");
            $stmt->execute([$nim]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $pdo->query("SELECT * FROM mahasiswa ORDER BY nama");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch(PDOException $e) {
        return false;
    }
}

function get_ekstrakurikuler_data($pdo, $id = null) {
    try {
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM ekstrakurikuler WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $pdo->query("SELECT * FROM ekstrakurikuler ORDER BY nama_ekskul");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch(PDOException $e) {
        return false;
    }
}

function get_pendaftaran_data($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT p.id, p.tanggal_daftar, m.nim, m.nama as nama_mahasiswa, 
                   e.nama_ekskul, e.pembina
            FROM pendaftaran p
            JOIN mahasiswa m ON p.nim_mahasiswa = m.nim
            JOIN ekstrakurikuler e ON p.id_ekskul = e.id
            ORDER BY p.tanggal_daftar DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return false;
    }
}

function get_mahasiswa_with_ekskul($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT m.*, 
                   COUNT(p.id) as total_ekskul,
                   GROUP_CONCAT(e.nama_ekskul SEPARATOR ', ') as ekskul_diikuti
            FROM mahasiswa m
            LEFT JOIN pendaftaran p ON m.nim = p.nim_mahasiswa
            LEFT JOIN ekstrakurikuler e ON p.id_ekskul = e.id
            GROUP BY m.nim
            ORDER BY m.nama
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        return false;
    }
}

function add_mahasiswa($pdo, $data) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO mahasiswa (nim, nama, email, jurusan, semester) 
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['nim'],
            $data['nama'],
            $data['email'],
            $data['jurusan'],
            $data['semester']
        ]);
    } catch(PDOException $e) {
        return false;
    }
}

function add_ekstrakurikuler($pdo, $data) {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO ekstrakurikuler (nama_ekskul, deskripsi, pembina, hari, waktu, tempat) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['nama_ekskul'],
            $data['deskripsi'],
            $data['pembina'],
            $data['hari'],
            $data['waktu'],
            $data['tempat']
        ]);
    } catch(PDOException $e) {
        return false;
    }
}

function add_pendaftaran($pdo, $nim_mahasiswa, $id_ekskul) {
    try {
        // Cek apakah sudah terdaftar
        $check = $pdo->prepare("
            SELECT id FROM pendaftaran 
            WHERE nim_mahasiswa = ? AND id_ekskul = ?
        ");
        $check->execute([$nim_mahasiswa, $id_ekskul]);
        
        if ($check->fetch()) {
            return ['success' => false, 'message' => 'Mahasiswa sudah terdaftar di ekstrakurikuler ini!'];
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO pendaftaran (nim_mahasiswa, id_ekskul, tanggal_daftar) 
            VALUES (?, ?, NOW())
        ");
        $result = $stmt->execute([$nim_mahasiswa, $id_ekskul]);
        
        return ['success' => $result, 'message' => $result ? 'Pendaftaran berhasil!' : 'Pendaftaran gagal!'];
    } catch(PDOException $e) {
        return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
    }
}

function get_jurusan_options() {
    return [
        'Pendidikan Matematika',
        'Pendidikan Bahasa Indonesia',
        'Pendidikan Bahasa Inggris',
        'Pendidikan Ekonomi',
        'Teknik Informatika',
        'Sistem Informasi',
        'Pendidikan Guru Sekolah Dasar',
        'Pendidikan Jasmani',
        'Pendidikan Seni Rupa'
    ];
}

function get_hari_options() {
    return ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
}

function show_alert($message, $type = 'success') {
    $icon = $type === 'success' ? 'check-circle' : 'exclamation-triangle';
    echo "<div class='alert alert-{$type}'>
            <i class='fas fa-{$icon}'></i> {$message}
          </div>";
}

function redirect($url, $delay = 0) {
    if ($delay > 0) {
        header("refresh:{$delay}; url={$url}");
    } else {
        header("Location: {$url}");
    }
    exit();
}

function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function get_current_page() {
    return $_GET['page'] ?? 'dashboard';
}

function create_slug($string) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    return $slug;
}
?>