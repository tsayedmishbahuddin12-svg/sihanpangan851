<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jenis_unggas = $conn->real_escape_string($_POST['jenis_unggas']);
    $no_urut = (int)$_POST['no_urut'];
    $keterangan = $conn->real_escape_string($_POST['keterangan']);
    $tanggal = $_POST['tanggal'] ? $conn->real_escape_string($_POST['tanggal']) : NULL;
    $telur_butir = (int)$_POST['telur_butir'];
    $tahun = (int)$_POST['tahun'];
    
    $tanggal_sql = $tanggal ? "'$tanggal'" : "NULL";
    
    $sql = "INSERT INTO peternakan_rincian_telur (jenis_unggas, no_urut, keterangan, tanggal, telur_butir, tahun) 
            VALUES ('$jenis_unggas', $no_urut, '$keterangan', $tanggal_sql, $telur_butir, $tahun)";
    
    if ($conn->query($sql)) {
        header('Location: peternakan-telur.php?success=added');
        exit;
    } else {
        $error = 'Gagal menambahkan data: ' . $conn->error;
    }
}

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Rincian Telur - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Tambah Rincian Telur</h1>
                <div class="topbar-right">
                    <span class="user-info">👤 <?= $_SESSION['admin_name'] ?></span>
                    <a href="logout.php" class="btn-logout">Logout</a>
                </div>
            </div>
            
            <div class="content-area">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Form Tambah Data</h3>
                        <a href="peternakan-telur.php" class="btn btn-secondary">← Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Jenis Unggas *</label>
                                    <select name="jenis_unggas" required class="form-control">
                                        <option value="">Pilih Jenis</option>
                                        <option value="Ayam">Ayam</option>
                                        <option value="Bebek">Bebek</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label>No Urut *</label>
                                    <input type="number" name="no_urut" value="1" required class="form-control">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Keterangan *</label>
                                <textarea name="keterangan" required class="form-control" rows="2" 
                                          placeholder="Contoh: TELUR TIDAK LAYAK JUAL (BERI BELI AJAR)"></textarea>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Telur (Butir) *</label>
                                    <input type="number" name="telur_butir" value="0" required class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Tahun *</label>
                                    <input type="number" name="tahun" value="<?= date('Y') ?>" required class="form-control">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">Simpan Data</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
