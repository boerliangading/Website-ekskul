<?php
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_student':
                $data = [
                    'nim' => sanitize_input($_POST['nim']),
                    'nama' => sanitize_input($_POST['nama']),
                    'email' => sanitize_input($_POST['email']),
                    'jurusan' => sanitize_input($_POST['jurusan']),
                    'semester' => (int)$_POST['semester']
                ];
                
                // Validasi
                if (!validate_nim($data['nim'])) {
                    $error_message = "Format NIM tidak valid! Minimal 8 digit angka.";
                } elseif (!validate_email($data['email'])) {
                    $error_message = "Format email tidak valid!";
                } else {
                    if (add_mahasiswa($pdo, $data)) {
                        $success_message = "Data mahasiswa berhasil ditambahkan!";
                    } else {
                        $error_message = "Gagal menambahkan data mahasiswa. NIM mungkin sudah ada.";
                    }
                }
                break;
            
            case 'add_ekskul':
                $data = [
                    'nama_ekskul' => sanitize_input($_POST['nama_ekskul']),
                    'deskripsi' => sanitize_input($_POST['deskripsi']),
                    'pembina' => sanitize_input($_POST['pembina']),
                    'hari' => sanitize_input($_POST['hari']),
                    'waktu' => sanitize_input($_POST['waktu']),
                    'tempat' => sanitize_input($_POST['tempat'])
                ];
                
                if (add_ekstrakurikuler($pdo, $data)) {
                    $success_message = "Ekstrakurikuler berhasil ditambahkan!";
                } else {
                    $error_message = "Gagal menambahkan ekstrakurikuler.";
                }
                break;
        }
    }
}

$jurusan_options = get_jurusan_options();
$hari_options = get_hari_options();
?>

<div class="header">
    <h2>Input Data</h2>
    <p>Tambahkan data mahasiswa dan ekstrakurikuler baru</p>
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

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Form Input Mahasiswa -->
    <div class="form-container">
        <h3><i class="fas fa-user-plus"></i> Tambah Mahasiswa</h3>
        <form method="POST" id="formMahasiswa">
            <input type="hidden" name="action" value="add_student">
            
            <div class="form-group">
                <label for="nim">NIM <span style="color: red;">*</span></label>
                <input type="text" id="nim" name="nim" class="form-control" required 
                       placeholder="Contoh: 20230001" pattern="[0-9]{8,}" 
                       title="NIM minimal 8 digit angka">
            </div>
            
            <div class="form-group">
                <label for="nama">Nama Lengkap <span style="color: red;">*</span></label>
                <input type="text" id="nama" name="nama" class="form-control" required
                       placeholder="Masukkan nama lengkap">
            </div>
            
            <div class="form-group">
                <label for="email">Email <span style="color: red;">*</span></label>
                <input type="email" id="email" name="email" class="form-control" required
                       placeholder="contoh@email.com">
            </div>
            
            <div class="form-group">
                <label for="jurusan">Jurusan <span style="color: red;">*</span></label>
                <select id="jurusan" name="jurusan" class="form-control" required>
                    <option value="">Pilih Jurusan</option>
                    <?php foreach ($jurusan_options as $jurusan): ?>
                        <option value="<?= $jurusan ?>"><?= $jurusan ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="semester">Semester <span style="color: red;">*</span></label>
                <select id="semester" name="semester" class="form-control" required>
                    <option value="">Pilih Semester</option>
                    <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>">Semester <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Mahasiswa
            </button>
        </form>
    </div>

    <!-- Form Input Ekstrakurikuler -->
    <div class="form-container">
        <h3><i class="fas fa-plus-circle"></i> Tambah Ekstrakurikuler</h3>
        <form method="POST" id="formEkskul">
            <input type="hidden" name="action" value="add_ekskul">
            
            <div class="form-group">
                <label for="nama_ekskul">Nama Ekstrakurikuler <span style="color: red;">*</span></label>
                <input type="text" id="nama_ekskul" name="nama_ekskul" class="form-control" required
                       placeholder="Contoh: Sepak Bola, Paduan Suara, dll">
            </div>
            
            <div class="form-group">
                <label for="deskripsi">Deskripsi <span style="color: red;">*</span></label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3" required
                          placeholder="Deskripsi singkat tentang ekstrakurikuler"></textarea>
            </div>
            
            <div class="form-group">
                <label for="pembina">Pembina <span style="color: red;">*</span></label>
                <input type="text" id="pembina" name="pembina" class="form-control" required
                       placeholder="Nama pembina/penanggung jawab">
            </div>
            
            <div class="form-group">
                <label for="hari">Hari <span style="color: red;">*</span></label>
                <select id="hari" name="hari" class="form-control" required>
                    <option value="">Pilih Hari</option>
                    <?php foreach ($hari_options as $hari): ?>
                        <option value="<?= $hari ?>"><?= $hari ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="waktu">Waktu <span style="color: red;">*</span></label>
                <input type="time" id="waktu" name="waktu" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="tempat">Tempat <span style="color: red;">*</span></label>
                <input type="text" id="tempat" name="tempat" class="form-control" required
                       placeholder="Lokasi kegiatan">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Ekstrakurikuler
            </button>
        </form>
    </div>
</div>

<!-- Quick Stats -->
<div style="margin-top: 2rem;">
    <div class="table-container">
        <h3><i class="fas fa-chart-line"></i> Statistik Terkini</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="card" style="margin: 0;">
                <h4>Mahasiswa Hari Ini</h4>
                <?php
                $today_students = $pdo->query("SELECT COUNT(*) FROM mahasiswa WHERE DATE(created_at) = CURDATE()")->fetchColumn();
                ?>
                <h2 style="color: #4CAF50;"><?= $today_students ?></h2>
                <p>Pendaftaran hari ini</p>
            </div>
            
            <div class="card" style="margin: 0;">
                <h4>Ekstrakurikuler Aktif</h4>
                <?php
                $active_ekskul = $pdo->query("SELECT COUNT(*) FROM ekstrakurikuler")->fetchColumn();
                ?>
                <h2 style="color: #2196F3;"><?= $active_ekskul ?></h2>
                <p>Total ekstrakurikuler</p>
            </div>
            
            <div class="card" style="margin: 0;">
                <h4>Pendaftaran Bulan Ini</h4>
                <?php
                $monthly_registrations = $pdo->query("SELECT COUNT(*) FROM pendaftaran WHERE MONTH(tanggal_daftar) = MONTH(CURDATE())")->fetchColumn();
                ?>
                <h2 style="color: #FF9800;"><?= $monthly_registrations ?></h2>
                <p>Bulan <?= date('F') ?></p>
            </div>
        </div>
    </div>
</div>