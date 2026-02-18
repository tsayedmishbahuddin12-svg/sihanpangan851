<?php
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

try {
    $conn = getConnection();
    
    $tanaman_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($tanaman_id == 0) {
        die("Error: ID tidak valid");
    }
    
    // Get tanaman/hewan info
    $item_query = "SELECT t.*, p.nama as pleton_nama, k.nama as kategori_nama, k.id as kategori_id 
                   FROM tanaman t 
                   LEFT JOIN pleton p ON t.pleton_id = p.id 
                   LEFT JOIN kategori k ON p.kategori_id = k.id 
                   WHERE t.id = $tanaman_id";
    
    $item_result = $conn->query($item_query);
    if (!$item_result) {
        die("Error query item: " . $conn->error);
    }
    
    $item = $item_result->fetch_assoc();
    
    if (!$item) {
        die("Error: Item dengan ID $tanaman_id tidak ditemukan");
    }
    
    // Determine if this is tanaman or hewan
    $is_hewan = $item['kategori_id'] == 2;
    $type = $is_hewan ? 'hewan' : 'tanaman';
    
    // Get existing detail data
    $detail_result = $conn->query("SELECT * FROM tanaman_detail WHERE tanaman_id = $tanaman_id");
    $detail = $detail_result ? $detail_result->fetch_assoc() : null;
    
    // Get existing variabel data
    $variabel_result = $conn->query("SELECT * FROM tanaman_variabel WHERE tanaman_id = $tanaman_id ORDER BY no");
    $variabel_data = [];
    if ($variabel_result) {
        while ($row = $variabel_result->fetch_assoc()) {
            $variabel_data[$row['no']] = $row;
        }
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nama_latin = $conn->real_escape_string($_POST['nama_latin']);
        $informasi_kegiatan = $conn->real_escape_string($_POST['informasi_kegiatan']);
        
        // Update nama latin in tanaman table
        if (!$conn->query("UPDATE tanaman SET nama_latin = '$nama_latin' WHERE id = $tanaman_id")) {
            throw new Exception("Error updating tanaman: " . $conn->error);
        }
        
        // Insert or update detail
        if ($detail) {
            if (!$conn->query("UPDATE tanaman_detail SET nama_latin = '$nama_latin', informasi_kegiatan = '$informasi_kegiatan', tanggal_update = CURDATE() WHERE tanaman_id = $tanaman_id")) {
                throw new Exception("Error updating detail: " . $conn->error);
            }
        } else {
            if (!$conn->query("INSERT INTO tanaman_detail (tanaman_id, nama_latin, informasi_kegiatan, tanggal_update) VALUES ($tanaman_id, '$nama_latin', '$informasi_kegiatan', CURDATE())")) {
                throw new Exception("Error inserting detail: " . $conn->error);
            }
        }
        
        // Update variabel data
        for ($i = 1; $i <= 5; $i++) {
            if (isset($_POST["variabel_$i"]) && isset($_POST["keterangan_$i"])) {
                $variabel = $conn->real_escape_string($_POST["variabel_$i"]);
                $keterangan = $conn->real_escape_string($_POST["keterangan_$i"]);
                
                if (isset($variabel_data[$i])) {
                    // Update existing
                    if (!$conn->query("UPDATE tanaman_variabel SET variabel = '$variabel', keterangan = '$keterangan' WHERE tanaman_id = $tanaman_id AND no = $i")) {
                        throw new Exception("Error updating variabel $i: " . $conn->error);
                    }
                } else {
                    // Insert new
                    if (!$conn->query("INSERT INTO tanaman_variabel (tanaman_id, no, variabel, keterangan) VALUES ($tanaman_id, $i, '$variabel', '$keterangan')")) {
                        throw new Exception("Error inserting variabel $i: " . $conn->error);
                    }
                }
            }
        }
        
        $success = 'Data detail berhasil disimpan!';
        
        // Refresh data
        $detail_result = $conn->query("SELECT * FROM tanaman_detail WHERE tanaman_id = $tanaman_id");
        $detail = $detail_result ? $detail_result->fetch_assoc() : null;
        
        $variabel_result = $conn->query("SELECT * FROM tanaman_variabel WHERE tanaman_id = $tanaman_id ORDER BY no");
        $variabel_data = [];
        if ($variabel_result) {
            while ($row = $variabel_result->fetch_assoc()) {
                $variabel_data[$row['no']] = $row;
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

closeConnection($conn);

// Default variabel names
$default_variabel = $is_hewan ? 
    ['Jenis', 'Populasi', 'Umur ternak', 'Umur panen', 'Lainnya'] :
    ['Varietas', 'Populasi', 'Luas lahan', 'Umur tanaman', 'Umur panen'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Detail - <?= htmlspecialchars($item['nama']) ?></title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Edit Detail: <?= htmlspecialchars($item['nama']) ?></h1>
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
                
                <!-- Form Edit Detail -->
                <div class="card">
                    <div class="card-header">
                        <h3>Edit Detail <?= $is_hewan ? 'Hewan' : 'Tanaman' ?></h3>
                        <a href="<?= $is_hewan ? 'hewan.php' : 'tanaman.php' ?>" class="btn btn-secondary btn-sm">← Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <!-- Informasi Dasar -->
                            <div class="form-section">
                                <h4>Informasi Dasar</h4>
                                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                                    <div class="form-group">
                                        <label>Nama <?= $is_hewan ? 'Hewan' : 'Tanaman' ?></label>
                                        <input type="text" value="<?= htmlspecialchars($item['nama']) ?>" readonly style="background: #f8f9fa;">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>Nama Latin *</label>
                                        <input type="text" name="nama_latin" value="<?= htmlspecialchars($item['nama_latin'] ?? $detail['nama_latin'] ?? '') ?>" placeholder="Contoh: Bos taurus" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tabel Keterangan -->
                            <div class="form-section">
                                <h4>Tabel Keterangan</h4>
                                <div class="table-responsive">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                            <tr style="background: #2E5E3E; color: white;">
                                                <th style="padding: 0.75rem; border: 1px solid #ddd; width: 60px;">NO</th>
                                                <th style="padding: 0.75rem; border: 1px solid #ddd;">VARIABEL</th>
                                                <th style="padding: 0.75rem; border: 1px solid #ddd;">KETERANGAN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <tr>
                                                <td style="padding: 0.5rem; border: 1px solid #ddd; text-align: center;"><?= $i ?></td>
                                                <td style="padding: 0.5rem; border: 1px solid #ddd;">
                                                    <input type="text" name="variabel_<?= $i ?>" 
                                                           value="<?= htmlspecialchars($variabel_data[$i]['variabel'] ?? $default_variabel[$i-1]) ?>" 
                                                           style="width: 100%; border: none; padding: 0.25rem;">
                                                </td>
                                                <td style="padding: 0.5rem; border: 1px solid #ddd;">
                                                    <input type="text" name="keterangan_<?= $i ?>" 
                                                           value="<?= htmlspecialchars($variabel_data[$i]['keterangan'] ?? '') ?>" 
                                                           placeholder="Isi keterangan..."
                                                           style="width: 100%; border: none; padding: 0.25rem;">
                                                </td>
                                            </tr>
                                            <?php endfor; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <!-- Informasi Kegiatan -->
                            <div class="form-section">
                                <h4>Informasi Kegiatan</h4>
                                <div class="form-group">
                                    <label>Informasi Kegiatan</label>
                                    <textarea name="informasi_kegiatan" rows="4" placeholder="Masukkan informasi kegiatan..."><?= htmlspecialchars($detail['informasi_kegiatan'] ?? '') ?></textarea>
                                </div>
                            </div>
                            
                            <div style="margin-top: 2rem;">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                <a href="<?= $is_hewan ? 'hewan.php' : 'tanaman.php' ?>" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Preview -->
                <div class="card">
                    <div class="card-header">
                        <h3>Preview Halaman Detail</h3>
                    </div>
                    <div class="card-body">
                        <p>Untuk melihat hasil perubahan, kunjungi:</p>
                        <a href="../detail_new.php?id=<?= $tanaman_id ?>&type=<?= $type ?>" target="_blank" class="btn btn-info btn-sm">
                            Lihat Halaman Detail →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .form-section h4 {
        color: #2E5E3E;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }
    
    .form-section:last-child {
        border-bottom: none;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .alert {
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 5px;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    </style>
</body>
</html>