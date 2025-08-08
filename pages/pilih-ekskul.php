<?php
$success_message = '';
$error_message = '';

// Handle form submission
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'daftar_ekskul') {
    $nim_mahasiswa = sanitize_input($_POST['nim_mahasiswa']);
    $id_ekskul = (int)$_POST['id_ekskul'];
    
    $result = add_pendaftaran($pdo, $nim_mahasiswa, $id_ekskul);
    if ($result['success']) {
        $success_message = $result['message'];
    } else {
        $error_message = $result['message'];
    }
}

// Get data for dropdowns
$mahasiswa_list = get_mahasiswa_data($pdo);
$ekskul_list = get_ekstrakurikuler_data($pdo);
$pendaftaran_list = get_pendaftaran_data($pdo);
?>

<div class="header">
    <h2>Pilih Ekstrakurikuler</h2>
    <p>Daftarkan mahasiswa ke ekstrakurikuler yang tersedia</p>
</div>

<?php if ($success_message): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?= $success_message ?>
    </div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;">
        <i class="fas fa-exclamation-triangle"></i> <?= $error_message ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
    <!-- Form Pendaftaran -->
    <div class="form-container">
        <h3><i class="fas fa-hand-paper"></i> Daftar Ekstrakurikuler</h3>
        <form method="POST">
            <input type="hidden" name="action" value="daftar_ekskul">
            
            <div class="form-group">
                <label for="nim_mahasiswa">Pilih Mahasiswa <span style="color: red;">*</span></label>
                <select id="nim_mahasiswa" name="nim_mahasiswa" class="form-control" required>
                    <option value="">-- Pilih Mahasiswa --</option>
                    <?php if ($mahasiswa_list): ?>
                        <?php foreach ($mahasiswa_list as $mhs): ?>
                            <option value="<?= $mhs['nim'] ?>">
                                <?= $mhs['nim'] ?> - <?= htmlspecialchars($mhs['nama']) ?> (<?= htmlspecialchars($mhs['jurusan']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="id_ekskul">Pilih Ekstrakurikuler <span style="color: red;">*</span></label>
                <select id="id_ekskul" name="id_ekskul" class="form-control" required>
                    <option value="">-- Pilih Ekstrakurikuler --</option>
                    <?php if ($ekskul_list): ?>
                        <?php foreach ($ekskul_list as $ekskul): ?>
                            <option value="<?= $ekskul['id'] ?>">
                                <?= htmlspecialchars($ekskul['nama_ekskul']) ?> - <?= $ekskul['hari'] ?> <?= substr($ekskul['waktu'], 0, 5) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-hand-paper"></i> Daftar Ekstrakurikuler
            </button>
        </form>
    </div>
    
    <!-- Info Ekstrakurikuler -->
    <div class="form-container">
        <h3><i class="fas fa-info-circle"></i> Informasi</h3>
        <div class="alert" style="background: #d1ecf1; color: #0c5460; border: 1px solid #b8daff;">
            <h4><i class="fas fa-lightbulb"></i> Tips Memilih Ekstrakurikuler:</h4>
            <ul style="margin: 1rem 0; padding-left: 1.5rem;">
                <li>Pilih sesuai minat dan bakat Anda</li>
                <li>Perhatikan jadwal agar tidak bentrok dengan kuliah</li>
                <li>Maksimal mengikuti 3 ekstrakurikuler</li>
                <li>Konsultasi dengan pembina jika ragu</li>
            </ul>
        </div>
        
        <div style="margin-top: 1rem;">
            <h4>Statistik Pendaftaran</h4>
            <?php
            $stats = $pdo->query("
                SELECT COUNT(*) as total_pendaftar,
                       COUNT(DISTINCT nim_mahasiswa) as unique_mahasiswa,
                       COUNT(DISTINCT id_ekskul) as ekskul_diminati
                FROM pendaftaran
            ")->fetch();
            ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                <div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                    <h3 style="color: #4CAF50;"><?= $stats['total_pendaftar'] ?></h3>
                    <p>Total Pendaftaran</p>
                </div>
                <div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                    <h3 style="color: #2196F3;"><?= $stats['unique_mahasiswa'] ?></h3>
                    <p>Mahasiswa Aktif</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Ekstrakurikuler Tersedia -->
<div class="table-container">
    <h3><i class="fas fa-list"></i> Ekstrakurikuler Tersedia</h3>
    <?php if ($ekskul_list && count($ekskul_list) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Ekstrakurikuler</th>
                    <th>Deskripsi</th>
                    <th>Pembina</th>
                    <th>Jadwal</th>
                    <th>Tempat</th>
                    <th>Pendaftar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ekskul_list as $ekskul): ?>
                    <?php
                    // Hitung jumlah pendaftar untuk setiap ekstrakurikuler
                    $pendaftar_count = $pdo->prepare("SELECT COUNT(*) FROM pendaftaran WHERE id_ekskul = ?");
                    $pendaftar_count->execute([$ekskul['id']]);
                    $jumlah_pendaftar = $pendaftar_count->fetchColumn();
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($ekskul['nama_ekskul']) ?></strong></td>
                        <td><?= htmlspecialchars($ekskul['deskripsi']) ?></td>
                        <td><?= htmlspecialchars($ekskul['pembina']) ?></td>
                        <td>
                            <span style="background: #e3f2fd; color: #1976d2; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                                <?= $ekskul['hari'] ?> - <?= substr($ekskul['waktu'], 0, 5) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($ekskul['tempat']) ?></td>
                        <td>
                            <span style="background: #4CAF50; color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                                <?= $jumlah_pendaftar ?> orang
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert" style="background: #fff3cd; color: #856404; border: 1px solid #ffeaa7;">
            <i class="fas fa-exclamation-triangle"></i> Belum ada ekstrakurikuler yang tersedia.
        </div>
    <?php endif; ?>
</div>

<!-- Daftar Pendaftaran Terbaru -->
<div class="table-container" style="margin-top: 2rem;">
    <h3><i class="fas fa-clock"></i> Pendaftaran Terbaru</h3>
    <?php if ($pendaftaran_list && count($pendaftaran_list) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal Daftar</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Ekstrakurikuler</th>
                    <th>Pembina</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($pendaftaran_list, 0, 10) as $daftar): ?>
                <tr>
                    <td><?= format_datetime($daftar['tanggal_daftar']) ?></td>
                    <td><?= $daftar['nim'] ?></td>
                    <td><?= htmlspecialchars($daftar['nama_mahasiswa']) ?></td>
                    <td><?= htmlspecialchars($daftar['nama_ekskul']) ?></td>
                    <td><?= htmlspecialchars($daftar['pembina']) ?></td>
                    <td>
                        <span style="background: #4CAF50; color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                            <i class="fas fa-check"></i> Terdaftar
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (count($pendaftaran_list) > 10): ?>
            <div style="text-align: center; margin-top: 1rem;">
                <p style="color: #666;">Menampilkan 10 dari <?= count($pendaftaran_list) ?> pendaftaran terbaru</p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert" style="background: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6;">
            <i class="fas fa-info-circle"></i> Belum ada pendaftaran ekstrakurikuler.
        </div>
    <?php endif; ?>
</div>