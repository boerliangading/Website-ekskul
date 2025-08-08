<?php
// Load configuration
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Initialize database connection
$db = new Database();
$pdo = $db->connect();

// Get current page
$current_page = get_current_page();
$page_title = ucfirst(str_replace('-', ' ', $current_page)) . ' - ' . APP_NAME;

// Include header
include 'includes/header.php';
?>

<div class="container">
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <?php
        // Route to appropriate page
        switch($current_page) {
            case 'dashboard':
                include 'pages/dashboard.php';
                break;
                
            case 'input-data':
                include 'pages/input-data.php';
                break;
                
            case 'pilih-ekskul':
                include 'pages/pilih-ekskul.php';
                break;
                
            case 'data-mahasiswa':
                include 'pages/data-mahasiswa.php';
                break;
                
            case 'data-ekskul':
                include 'pages/data-ekskul.php';
                break;
                
            case 'laporan':
                include 'pages/laporan.php';
                break;
                
            default:
                include 'pages/404.php';
                break;
        }
        ?>
    </main>
</div>

<?php include 'includes/footer.php'; ?>

<?php
?>