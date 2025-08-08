<?php
// Get mahasiswa data with ekstrakurikuler info
$mahasiswa_data = get_mahasiswa_with_ekskul($pdo);

// Filter dan pencarian
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$jurusan_filter = isset($_GET['jurusan']) ? sanitize_input($_GET['jurusan']) : '';

if ($search || $jurusan_filter) {
    $sql = "SELECT m.*, 
                   COUNT(p.id) as total_ekskul,
                   GROUP_CONCAT(e.nama_ekskul SEPARATOR ', ') as ekskul_diikuti
            FROM mahasiswa m
            LEFT JOIN pendaftaran p ON m.nim = p.nim_mahasiswa
            LEFT JOIN ekstrakurikuler e ON p.id_ekskul = e.id
            WHERE 1=1";
    
    $params = [];
    
    if ($search) {
        $sql .= " AND (m.nim LIKE ? OR m.nama LIKE ? OR m.email LIKE ?)";
        $search_param = "%{$search}%";
        $params = array_merge($params, [$search_param, $search_param, $search_param]);
    }
    
    if ($jurusan_filter) {
        $sql .= " AND m.jurusan = ?";
        $params[] = $jurusan_filter;
    }
    
    $sql .= " GROUP BY m.nim ORDER BY m.nama";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $mahasiswa_data = $stmt->fetchAll();
}

// Get jurusan untuk filter
$jurusan_list = $pdo->query("SELECT DISTINCT jurusan FROM mahasiswa ORDER BY jurusan")->fetchAll();
?>

<div class="header">
    <h2>Data Mahasiswa</h2>
    <p>Daftar semua mahasiswa yang terdaftar dalam sistem ekstrakurikuler</p>
</div>

<!-- Filter dan Pencarian -->
<div class="form-container" style="margin-bottom: 2rem;">
    <form method="GET" style="display: grid; grid-template-columns: 1fr 200px auto; gap: 1rem; align-items: end;">
        <input type="hidden" name="page" value="data-mahasiswa">
        
        <div class="form-group" style="margin-bottom: 0;">
            <label for="search">Cari Mahasiswa</label>
            <input type="text" id="search" name="search" class="form-control" 
                   placeholder="Cari berdasarkan NIM, nama, atau email..." 
                   value="<?= htmlspecialchars($search) ?>">
        </div>
        
        <div class="form-group" style="margin-bottom: 0;">
            <label for="jurusan">Filter Jurusan</label>
            <select id="jurusan" name="jurusan" class="form-control">
                <option value="">Semua Jurusan</option>
                <?php foreach ($jurusan_list as $jurusan): ?>
                    <option value="<?= $jurusan['jurusan'] ?>" 
                            <?= $jurusan_filter == $jurusan['jurusan'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($jurusan['jurusan']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div style="display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Cari
            </button>
            <a href="?page=data-mahasiswa" class="btn" style="background: #6c757d; color: white;">
                <i class="fas fa-refresh"></i> Reset
            </a>
        </div>
    </form>
</div>

<!-- Statistik Singkat -->
<div class="dashboard-cards" style="margin-bottom: 2rem;">
    <div class="card card-1">
        <div class="card-icon">
            <i class="fas fa-users"></i>
        </div>
        <h3><?= count($mahasiswa_data) ?></h3>
        <p>Total Mahasiswa <?= $search || $jurusan_filter ? '(Filtered)' : '' ?></p>
    </div>
    
    <div class="card card-2">
        <div class="card-icon">
            <i class="fas fa-user-check"></i>
        </div>
        <h3><?= count(array_filter($mahasiswa_data, function($mhs) { return $mhs['total_ekskul'] > 0; })) ?></h3>
        <p>Mahasiswa Aktif Ekskul</p>
    </div>
    
    <div class="card card-3">
        <div class="card-icon">
            <i class="fas fa-percentage"></i>
        </div>
        <h3><?= count($mahasiswa_data) > 0 ? round((count(array_filter($mahasiswa_data, function($mhs) { return $mhs['total_ekskul'] > 0; })) / count($mahasiswa_data)) * 100) : 0 ?>%</h3>
        <p>Tingkat Partisipasi</p>
    </div>
</div>

<!-- Tabel Data Mahasiswa -->
<div class="table-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3><i class="fas fa-table"></i> Data Mahasiswa</h3>
        <div>
            <a href="?page=input-data" class="btn btn-primary" style="font-size: 0.9rem;">
                <i class="fas fa-plus"></i> Tambah Mahasiswa
            </a>
            <button onclick="exportToCSV()" class="btn" style="background: #28a745; color: white; font-size: 0.9rem;">
                <i class="fas fa-download"></i> Export CSV
            </button>
        </div>
    </div>
    
    <?php if ($mahasiswa_data && count($mahasiswa_data) > 0): ?>
        <div style="overflow-x: auto;">
            <table class="table" id="mahasiswaTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Jurusan</th>
                        <th>Semester</th>
                        <th>Total Ekskul</th>
                        <th>Ekstrakurikuler yang Diikuti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mahasiswa_data as $index => $mhs): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><strong><?= $mhs['nim'] ?></strong></td>
                        <td><?= htmlspecialchars($mhs['nama']) ?></td>
                        <td>
                            <a href="mailto:<?= $mhs['email'] ?>" style="color: #667eea;">
                                <?= htmlspecialchars($mhs['email']) ?>
                            </a>
                        </td>
                        <td>
                            <span style="background: #e3f2fd; color: #1976d2; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                                <?= htmlspecialchars($mhs['jurusan']) ?>
                            </span>
                        </td>
                        <td>
                            <span style="background: #f3e5f5; color: #7b1fa2; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                                Semester <?= $mhs['semester'] ?>
                            </span>
                        </td>
                        <td>
                            <span style="background: <?= $mhs['total_ekskul'] > 0 ? '#4CAF50' : '#9e9e9e' ?>; color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                                <?= $mhs['total_ekskul'] ?> ekskul
                            </span>
                        </td>
                        <td>
                            <?php if ($mhs['ekskul_diikuti']): ?>
                                <div style="max-width: 200px;">
                                    <?php
                                    $ekskul_list = explode(', ', $mhs['ekskul_diikuti']);
                                    foreach ($ekskul_list as $ekskul):
                                    ?>
                                        <span style="display: inline-block; background: #fff3e0; color: #ef6c00; padding: 0.2rem 0.4rem; border-radius: 8px; font-size: 0.75rem; margin: 0.1rem;">
                                            <?= htmlspecialchars($ekskul) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <span style="color: #999; font-style: italic;">Belum mengikuti ekstrakurikuler</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <button onclick="viewDetail('<?= $mhs['nim'] ?>')" 
                                        class="btn" style="background: #17a2b8; color: white; padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editMahasiswa('<?= $mhs['nim'] ?>')" 
                                        class="btn" style="background: #ffc107; color: #212529; padding: 0.25rem 0.5rem; font-size: 0.8rem;">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination info -->
        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #eee; text-align: center; color: #666;">
            Menampilkan <?= count($mahasiswa_data) ?> mahasiswa
            <?php if ($search): ?>
                dengan pencarian "<?= htmlspecialchars($search) ?>"
            <?php endif; ?>
            <?php if ($jurusan_filter): ?>
                dari jurusan <?= htmlspecialchars($jurusan_filter) ?>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="alert" style="background: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6; text-align: center; padding: 3rem;">
            <i class="fas fa-users" style="font-size: 3rem; opacity: 0.3; margin-bottom: 1rem;"></i>
            <h4>Tidak ada data mahasiswa</h4>
            <p>
                <?php if ($search || $jurusan_filter): ?>
                    Tidak ditemukan mahasiswa dengan kriteria pencarian yang Anda masukkan.
                    <br><a href="?page=data-mahasiswa">Tampilkan semua data</a>
                <?php else: ?>
                    Belum ada mahasiswa yang terdaftar dalam sistem.
                    <br><a href="?page=input-data" class="btn btn-primary" style="margin-top: 1rem;">
                        <i class="fas fa-plus"></i> Tambah Mahasiswa Pertama
                    </a>
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Detail Mahasiswa -->
<div id="detailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;" onclick="closeModal()">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 15px; max-width: 500px; width: 90%;" onclick="event.stopPropagation()">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3><i class="fas fa-user"></i> Detail Mahasiswa</h3>
            <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div id="detailContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
function viewDetail(nim) {
    // Simulate loading detail data
    const detailModal = document.getElementById('detailModal');
    const detailContent = document.getElementById('detailContent');
    
    // Find mahasiswa data
    const table = document.getElementById('mahasiswaTable');
    const rows = table.querySelectorAll('tbody tr');
    let mahasiswaData = null;
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells[1].textContent.trim() === nim) {
            mahasiswaData = {
                nim: cells[1].textContent.trim(),
                nama: cells[2].textContent.trim(),
                email: cells[3].querySelector('a').textContent.trim(),
                jurusan: cells[4].textContent.trim(),
                semester: cells[5].textContent.trim(),
                totalEkskul: cells[6].textContent.trim(),
                ekskulDiikuti: cells[7].textContent.trim()
            };
        }
    });
    
    if (mahasiswaData) {
        detailContent.innerHTML = `
            <div style="display: grid; gap: 1rem;">
                <div><strong>NIM:</strong> ${mahasiswaData.nim}</div>
                <div><strong>Nama:</strong> ${mahasiswaData.nama}</div>
                <div><strong>Email:</strong> ${mahasiswaData.email}</div>
                <div><strong>Jurusan:</strong> ${mahasiswaData.jurusan}</div>
                <div><strong>Semester:</strong> ${mahasiswaData.semester}</div>
                <div><strong>Total Ekstrakurikuler:</strong> ${mahasiswaData.totalEkskul}</div>
                <div><strong>Ekstrakurikuler:</strong><br>${mahasiswaData.ekskulDiikuti}</div>
            </div>
        `;
        detailModal.style.display = 'block';
    }
}

function editMahasiswa(nim) {
    alert(`Fitur edit mahasiswa dengan NIM ${nim} akan segera tersedia!`);
}

function closeModal() {
    document.getElementById('detailModal').style.display = 'none';
}

function exportToCSV() {
    const table = document.getElementById('mahasiswaTable');
    let csv = [];
    
    // Get headers
    const headers = [];
    table.querySelectorAll('thead th').forEach(th => {
        if (th.textContent.trim() !== 'Aksi') {
            headers.push(th.textContent.trim());
        }
    });
    csv.push(headers.join(','));
    
    // Get data rows
    table.querySelectorAll('tbody tr').forEach(row => {
        const rowData = [];
        const cells = row.querySelectorAll('td');
        for (let i = 0; i < cells.length - 1; i++) { // Skip last column (Aksi)
            let cellText = cells[i].textContent.trim().replace(/,/g, ';');
            rowData.push(`"${cellText}"`);
        }
        csv.push(rowData.join(','));
    });
    
    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `data_mahasiswa_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Auto refresh setiap 5 menit
setTimeout(() => {
    location.reload();
}, 300000);
</script>