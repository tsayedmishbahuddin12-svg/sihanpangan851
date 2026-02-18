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
    $tanggal_panen = $conn->real_escape_string($_POST['tanggal_panen']);
    $komoditas = $conn->real_escape_string($_POST['komoditas']);
    $pleton = $conn->real_escape_string($_POST['pleton']);
    $luas = (float)$_POST['luas'];
    $hasil = (float)$_POST['hasil'];
    $harga = (float)$_POST['harga'];
    $jumlah = (float)$_POST['jumlah'];
    $keterangan = $conn->real_escape_string($_POST['keterangan']);
    $tahun = (int)$_POST['tahun'];
    
    $sql = "INSERT INTO pertanian_panen_belanja (tanggal_panen, komoditas, pleton, luas, hasil, harga, jumlah, keterangan, tahun) 
            VALUES ('$tanggal_panen', '$komoditas', '$pleton', $luas, $hasil, $harga, $jumlah, '$keterangan', $tahun)";
    
    if ($conn->query($sql)) {
        header('Location: pertanian-panen.php?success=added');
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
    <title>Tambah Data Panen - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Tambah Data Panen & Belanja</h1>
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
                        <a href="pertanian-panen.php" class="btn btn-secondary">← Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label>Tanggal Panen *</label>
                                <input type="date" name="tanggal_panen" required class="form-control">
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Komoditas *</label>
                                    <input type="text" name="komoditas" required class="form-control" 
                                           placeholder="Contoh: Padi, Jagung, Cabai">
                                </div>
                                
                                <div class="form-group">
                                    <label>Pleton</label>
                                    <input type="text" name="pleton" class="form-control" 
                                           placeholder="Contoh: Pleton 1">
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Luas (Ha)</label>
                                    <input type="number" step="0.01" name="luas" value="0" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Hasil (Kg)</label>
                                    <input type="number" step="0.01" name="hasil" value="0" class="form-control">
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Harga (Rp)</label>
                                    <input type="number" step="0.01" name="harga" value="0" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Jumlah (Rp)</label>
                                    <input type="number" step="0.01" name="jumlah" value="0" class="form-control">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Tahun *</label>
                                <input type="number" name="tahun" value="<?= date('Y') ?>" required class="form-control">
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
