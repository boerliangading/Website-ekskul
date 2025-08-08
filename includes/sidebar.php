<?php
$current_page = get_current_page();
?>

<aside class="sidebar">
    <div class="logo">
        <img src="<?= ASSETS_PATH ?>images/logo-unindra.png" alt="UNINDRA Logo" style="width: 50px; height: 50px; margin-bottom: 10px; display: none;">
        <h1><i class="fas fa-graduation-cap"></i> UNINDRA</h1>
        <p>Sistem Ekstrakurikuler</p>
    </div>
    
    <nav>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="index.php?page=dashboard" class="nav-link <?= $current_page == 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?page=input-data" class="nav-link <?= $current_page == 'input-data' ? 'active' : '' ?>">
                    <i class="fas fa-plus-circle"></i>
                    Input Data
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?page=pilih-ekskul" class="nav-link <?= $current_page == 'pilih-ekskul' ? 'active' : '' ?>">
                    <i class="fas fa-hand-paper"></i>
                    Pilih Ekstrakurikuler
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?page=data-mahasiswa" class="nav-link <?= $current_page == 'data-mahasiswa' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i>
                    Data Mahasiswa
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?page=data-ekskul" class="nav-link <?= $current_page == 'data-ekskul' ? 'active' : '' ?>">
                    <i class="fas fa-list"></i>
                    Data Ekstrakurikuler
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php?page=laporan" class="nav-link <?= $current_page == 'laporan' ? 'active' : '' ?>">
                    <i class="fas fa-chart-bar"></i>
                    Laporan
                </a>
            </li>
        </ul>
    </nav>
    
    <div style="position: absolute; bottom: 20px; left: 20px; right: 20px; text-align: center; color: #888; font-size: 0.8rem;">
        <p>&copy; <?= date('Y') ?> UNINDRA</p>
        <p>Version <?= APP_VERSION ?></p>
    </div>
</aside>