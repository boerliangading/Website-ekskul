<?php
// Get statistics data
$total_mahasiswa = get_total_count($pdo, 'mahasiswa');
$total_ekskul = get_total_count($pdo, 'ekstrakurikuler');
$total_pendaftar = get_total_count($pdo, 'pendaftaran');

// Get recent ekstrakurikuler
$recent_ekskul = get_ekstrakurikuler_data($pdo);
$recent_ekskul = array_slice($recent_ekskul, 0, 5); // Ambil 5 terakhir
?>

<div class="header">
    <h2>Dashboard Ekstrakurikuler</h2>
    <p>Universitas Indraprasta PGRI - Sistem Manajemen Ekstrakurikuler</p>
</div>

<div class="dashboard-cards">
    <div class="card card-1">
        <div class="card-icon">
            <i class="fas fa-user-graduate"></i>
        </div>
        <h3><?= number_format($total_mahasiswa) ?></h3>
        <p>Total Mahasiswa</p>
    </div>
    
    <div class="card card-2">
        <div class="card-icon">
            <i class="fas fa-futbol"></i>
        </div>
        <h3><?= number_format($total_ekskul) ?></h3>
        <p>Total Ekstrakurikuler</p>
    </div>
    
    <div class="card card-3">
        <div class="card-icon">
            <i class="fas fa-clipboard-list"></i>
        </div>
        <h3><?= number_format($total_pendaftar) ?></h3>
        <p>Total Pendaftar</p>
    </div>
</div>

<div class="table-container">
    <h3><i class="fas fa-star"></i> Ekstrakurikuler Terbaru</h3>
    <?php if ($recent_ekskul && count($recent_ekskul) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Ekstrakurikuler</th>
                    <th>Pembina</th>
                    <th>Hari</th>
                    <th>Waktu</th>
                    <th>Tempat</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_ekskul as $ekskul): ?>
                <tr>
                    <td><?= htmlspecialchars($ekskul['nama_ekskul']) ?></td>
                    <td><?= htmlspecialchars($ekskul['pembina']) ?></td>
                    <td><?= htmlspecialchars($ekskul['hari']) ?></td>
                    <td><?= htmlspecialchars($ekskul['waktu']) ?></td>
                    <td><?= htmlspecialchars($ekskul['tempat']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Belum ada data ekstrakurikuler.
        </div>
    <?php endif; ?>
</div>

<!-- Quick Stats -->
<div class="dashboard-cards" style="margin-top: 2rem;">
    <div class="table-container">
        <h3><i class="fas fa-chart-pie"></i> Statistik Singkat</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
            <?php
            // Get statistics per jurusan
            $stats_jurusan = $pdo->query("
                SELECT jurusan, COUNT(*) as total 
                FROM mahasiswa 
                GROUP BY jurusan 
                ORDER BY total DESC 
                LIMIT 5
            ")->fetchAll();
            
            if ($stats_jurusan):
            ?>
            <div>
                <h4>Mahasiswa per Jurusan</h4>
                <?php foreach ($stats_jurusan as $stat): ?>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #eee;">
                        <span><?= htmlspecialchars($stat['jurusan']) ?></span>
                        <strong><?= $stat['total'] ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php
            // Get ekstrakurikuler paling diminati
            $popular_ekskul = $pdo->query("
                SELECT e.nama_ekskul, COUNT(p.id) as total_pendaftar
                FROM ekstrakurikuler e
                LEFT JOIN pendaftaran p ON e.id = p.id_ekskul
                GROUP BY e.id
                ORDER BY total_pendaftar DESC
                LIMIT 5
            ")->fetchAll();
            
            if ($popular_ekskul):
            ?>
            <div>
                <h4>Ekstrakurikuler Terpopuler</h4>
                <?php foreach ($popular_ekskul as $ekskul): ?>
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #eee;">
                        <span><?= htmlspecialchars($ekskul['nama_ekskul']) ?></span>
                        <strong><?= $ekskul['total_pendaftar'] ?> orang</strong>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>