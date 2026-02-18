<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

$tanaman_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get filter parameters
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Get tanaman info
$tanaman = $conn->query("SELECT t.*, p.nama as pleton_nama FROM tanaman t JOIN pleton p ON t.pleton_id = p.id WHERE t.id = $tanaman_id")->fetch_assoc();

if (!$tanaman) {
    header('Location: tanaman.php');
    exit;
}

$success = '';
$error = '';

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $kegiatan = $conn->real_escape_string($_POST['kegiatan']);
            $luas = $conn->real_escape_string($_POST['luas']);
            $populasi = $conn->real_escape_string($_POST['populasi']);
            $hasil = $conn->real_escape_string($_POST['hasil']);
            $tanggal = $_POST['tanggal'];
            $keterangan = $conn->real_escape_string($_POST['keterangan']);
            
            $sql = "INSERT INTO budidaya_data (tanaman_id, kegiatan, luas, populasi, hasil, tanggal, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issssss", $tanaman_id, $kegiatan, $luas, $populasi, $hasil, $tanggal, $keterangan);
            
            if ($stmt->execute()) {
                $success = 'Data budidaya berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambahkan data: ' . $conn->error;
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $conn->query("DELETE FROM budidaya_data WHERE id = $id");
            $success = 'Data berhasil dihapus!';
        }
    }
}

// Get budidaya data with filter
$where_clause = "WHERE tanaman_id = $tanaman_id";
if ($filter_bulan && $filter_tahun) {
    $where_clause .= " AND MONTH(tanggal) = '$filter_bulan' AND YEAR(tanggal) = '$filter_tahun'";
} elseif ($filter_tahun) {
    $where_clause .= " AND YEAR(tanggal) = '$filter_tahun'";
}

$budidaya = $conn->query("SELECT * FROM budidaya_data $where_clause ORDER BY tanggal DESC, created_at DESC");

// Get available years for filter
$years = $conn->query("SELECT DISTINCT YEAR(tanggal) as tahun FROM budidaya_data WHERE tanaman_id = $tanaman_id AND tanggal IS NOT NULL ORDER BY tahun DESC");

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Budidaya - <?= htmlspecialchars($tanaman['nama']) ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Data Budidaya: <?= htmlspecialchars($tanaman['nama']) ?></h1>
                <div class="topbar-right">
                    <div class="user-info">
                        <span>👤</span>
                        <span><?= $_SESSION['admin_name'] ?></span>
                    </div>
                    <a href="logout.php" class="btn-logout">Keluar</a>
                </div>
            </div>
            
            <div class="content-area">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <!-- Form Tambah Data -->
                <div class="card">
                    <div class="card-header">
                        <h3>Tambah Data Budidaya</h3>
                        <a href="tanaman.php" class="btn btn-secondary btn-sm">← Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="add">
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                                <div class="form-group">
                                    <label>Kegiatan *</label>
                                    <input type="text" name="kegiatan" required placeholder="Contoh: Penanaman">
                                </div>
                                
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="date" name="tanggal">
                                </div>
                                
                                <div class="form-group">
                                    <label>Luas</label>
                                    <input type="text" name="luas" placeholder="Contoh: 2 Ha">
                                </div>
                                
                                <div class="form-group">
                                    <label>Populasi</label>
                                    <input type="text" name="populasi" placeholder="Contoh: 1000 batang">
                                </div>
                                
                                <div class="form-group">
                                    <label>Hasil</label>
                                    <input type="text" name="hasil" placeholder="Contoh: 5 Ton">
                                </div>
                                
                                <div class="form-group">
                                    <label>Keterangan</label>
                                    <input type="text" name="keterangan" placeholder="Keterangan tambahan">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Tambah Data</button>
                        </form>
                    </div>
                </div>
                
                <!-- Tabel Data Budidaya -->
                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Data Budidaya</h3>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <form method="GET" style="display: flex; gap: 0.5rem;">
                                <input type="hidden" name="id" value="<?= $tanaman_id ?>">
                                <select name="bulan" style="padding: 0.5rem; border: 1px solid #ced4da; border-radius: 5px;">
                                    <option value="">Semua Bulan</option>
                                    <option value="1" <?= $filter_bulan == '1' ? 'selected' : '' ?>>Januari</option>
                                    <option value="2" <?= $filter_bulan == '2' ? 'selected' : '' ?>>Februari</option>
                                    <option value="3" <?= $filter_bulan == '3' ? 'selected' : '' ?>>Maret</option>
                                    <option value="4" <?= $filter_bulan == '4' ? 'selected' : '' ?>>April</option>
                                    <option value="5" <?= $filter_bulan == '5' ? 'selected' : '' ?>>Mei</option>
                                    <option value="6" <?= $filter_bulan == '6' ? 'selected' : '' ?>>Juni</option>
                                    <option value="7" <?= $filter_bulan == '7' ? 'selected' : '' ?>>Juli</option>
                                    <option value="8" <?= $filter_bulan == '8' ? 'selected' : '' ?>>Agustus</option>
                                    <option value="9" <?= $filter_bulan == '9' ? 'selected' : '' ?>>September</option>
                                    <option value="10" <?= $filter_bulan == '10' ? 'selected' : '' ?>>Oktober</option>
                                    <option value="11" <?= $filter_bulan == '11' ? 'selected' : '' ?>>November</option>
                                    <option value="12" <?= $filter_bulan == '12' ? 'selected' : '' ?>>Desember</option>
                                </select>
                                <select name="tahun" style="padding: 0.5rem; border: 1px solid #ced4da; border-radius: 5px;">
                                    <option value="">Semua Tahun</option>
                                    <?php while ($year = $years->fetch_assoc()): ?>
                                        <option value="<?= $year['tahun'] ?>" <?= $filter_tahun == $year['tahun'] ? 'selected' : '' ?>>
                                            <?= $year['tahun'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                <a href="budidaya-tanaman.php?id=<?= $tanaman_id ?>" class="btn btn-reset btn-sm">Reset</a>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Kegiatan</th>
                                        <th>Luas</th>
                                        <th>Populasi</th>
                                        <th>Hasil</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($budidaya->num_rows > 0): ?>
                                        <?php $no = 1; while ($row = $budidaya->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['tanggal'] ? date('d/m/Y', strtotime($row['tanggal'])) : '-' ?></td>
                                            <td><?= htmlspecialchars($row['kegiatan']) ?></td>
                                            <td><?= htmlspecialchars($row['luas']) ?></td>
                                            <td><?= htmlspecialchars($row['populasi']) ?></td>
                                            <td><?= htmlspecialchars($row['hasil']) ?></td>
                                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                            <td>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin hapus data ini?')">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" style="text-align: center; color: #666;">Belum ada data budidaya</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
