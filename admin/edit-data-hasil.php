<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';

// Get data
$data = $conn->query("SELECT * FROM data_hasil WHERE id = $id")->fetch_assoc();
if (!$data) {
    header('Location: data-hasil.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kategori_id = (int)$_POST['kategori_id'];
    $komoditas = $conn->real_escape_string($_POST['komoditas']);
    $bulan = $conn->real_escape_string($_POST['bulan']);
    $luas_tanam = (float)$_POST['luas_tanam'];
    $luas_panen = (float)$_POST['luas_panen'];
    $produksi = (float)$_POST['produksi'];
    $produktivitas = (float)$_POST['produktivitas'];
    $satuan_luas = $conn->real_escape_string($_POST['satuan_luas']);
    $satuan_produksi = $conn->real_escape_string($_POST['satuan_produksi']);
    $satuan_produktivitas = $conn->real_escape_string($_POST['satuan_produktivitas']);
    $tahun = (int)$_POST['tahun'];
    
    $sql = "UPDATE data_hasil SET 
            kategori_id = $kategori_id,
            komoditas = '$komoditas',
            bulan = '$bulan',
            luas_tanam = $luas_tanam,
            luas_panen = $luas_panen,
            produksi = $produksi,
            produktivitas = $produktivitas,
            satuan_luas = '$satuan_luas',
            satuan_produksi = '$satuan_produksi',
            satuan_produktivitas = '$satuan_produktivitas',
            tahun = $tahun
            WHERE id = $id";
    
    if ($conn->query($sql)) {
        header('Location: data-hasil.php?success=updated');
        exit;
    } else {
        $error = 'Gagal mengupdate data: ' . $conn->error;
    }
}

// Get kategori list
$kategori_list = $conn->query("SELECT * FROM kategori ORDER BY urutan");

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Hasil - Admin SIHANPANGAN851</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Edit Data Hasil</h1>
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
                        <h3>Form Edit Data Hasil</h3>
                        <a href="data-hasil.php" class="btn btn-secondary">← Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label>Kategori *</label>
                                <select name="kategori_id" required class="form-control">
                                    <option value="">Pilih Kategori</option>
                                    <?php while ($kat = $kategori_list->fetch_assoc()): ?>
                                        <option value="<?= $kat['id'] ?>" <?= $data['kategori_id'] == $kat['id'] ? 'selected' : '' ?>>
                                            <?= $kat['nama'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Komoditas *</label>
                                <input type="text" name="komoditas" value="<?= htmlspecialchars($data['komoditas']) ?>" 
                                       required class="form-control">
                            </div>
                            
                            <div class="form-group">
                                <label>Bulan *</label>
                                <select name="bulan" required class="form-control">
                                    <option value="">Pilih Bulan</option>
                                    <option value="Januari" <?= $data['bulan'] == 'Januari' ? 'selected' : '' ?>>Januari</option>
                                    <option value="Februari" <?= $data['bulan'] == 'Februari' ? 'selected' : '' ?>>Februari</option>
                                    <option value="Maret" <?= $data['bulan'] == 'Maret' ? 'selected' : '' ?>>Maret</option>
                                    <option value="April" <?= $data['bulan'] == 'April' ? 'selected' : '' ?>>April</option>
                                    <option value="Mei" <?= $data['bulan'] == 'Mei' ? 'selected' : '' ?>>Mei</option>
                                    <option value="Juni" <?= $data['bulan'] == 'Juni' ? 'selected' : '' ?>>Juni</option>
                                    <option value="Juli" <?= $data['bulan'] == 'Juli' ? 'selected' : '' ?>>Juli</option>
                                    <option value="Agustus" <?= $data['bulan'] == 'Agustus' ? 'selected' : '' ?>>Agustus</option>
                                    <option value="September" <?= $data['bulan'] == 'September' ? 'selected' : '' ?>>September</option>
                                    <option value="Oktober" <?= $data['bulan'] == 'Oktober' ? 'selected' : '' ?>>Oktober</option>
                                    <option value="November" <?= $data['bulan'] == 'November' ? 'selected' : '' ?>>November</option>
                                    <option value="Desember" <?= $data['bulan'] == 'Desember' ? 'selected' : '' ?>>Desember</option>
                                </select>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Luas Tanam</label>
                                    <input type="number" step="0.01" name="luas_tanam" 
                                           value="<?= $data['luas_tanam'] ?>" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Satuan Luas</label>
                                    <input type="text" name="satuan_luas" value="<?= $data['satuan_luas'] ?>" class="form-control">
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Luas Panen</label>
                                    <input type="number" step="0.01" name="luas_panen" 
                                           value="<?= $data['luas_panen'] ?>" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Produksi</label>
                                    <input type="number" step="0.01" name="produksi" 
                                           value="<?= $data['produksi'] ?>" class="form-control">
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Satuan Produksi</label>
                                    <input type="text" name="satuan_produksi" 
                                           value="<?= $data['satuan_produksi'] ?>" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Produktivitas</label>
                                    <input type="number" step="0.01" name="produktivitas" 
                                           value="<?= $data['produktivitas'] ?>" class="form-control">
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Satuan Produktivitas</label>
                                    <input type="text" name="satuan_produktivitas" 
                                           value="<?= $data['satuan_produktivitas'] ?>" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Tahun *</label>
                                    <input type="number" name="tahun" value="<?= $data['tahun'] ?>" 
                                           required class="form-control">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-block">Update Data</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
