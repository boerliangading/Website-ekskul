<?php
// Konfigurasi timezone
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi session
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Set ke 1 jika menggunakan HTTPS
session_start();

// Konstanta aplikasi
define('APP_NAME', 'Sistem Ekstrakurikuler UNINDRA');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/ekskul-unindra/');

// Path konstanta
define('ASSETS_PATH', 'assets/');
define('UPLOADS_PATH', 'uploads/');
define('BACKUP_PATH', 'backup/');

// Konfigurasi upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Error reporting (set ke 0 untuk production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>