<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

$error = '';
$success = '';

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
    
    $sql = "INSERT INTO data_hasil (kategori_id, komoditas, bulan, luas_tanam, luas_panen, produksi, produktivitas, 
            satuan_luas, satuan_produksi, satuan_produktivitas, tahun) 
            VALUES ($kategori_id, '$komoditas', '$bulan', $luas_tanam, $luas_panen, $produksi, $produktivitas, 
            '$satuan_luas', '$satuan_produksi', '$satuan_produktivitas', $tahun)";
    
    if ($conn->query($sql)) {
        header('Location: data-hasil.php?success=added');
        exit;
    } else {
        $error = 'Gagal menambahkan data: ' . $conn->error;
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
    <title>Tambah Data Hasil - Admin SIHANPANGAN851</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Tambah Data Hasil</h1>
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
                        <h3>Form Tambah Data Hasil</h3>
                        <a href="data-hasil.php" class="btn btn-secondary">← Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Kategori *</label>
                                <select name="kategori_id" required class="form-control">
                                    <option value="">Pilih Kategori</option>
                                    <?php while ($kat = $kategori_list->fetch_assoc()): ?>
                                        <option value="<?= $kat['id'] ?>"><?= $kat['nama'] ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Komoditas *</label>
                                <input type="text" name="komoditas" required class="form-control" 
                                       placeholder="Contoh: Padi, Jagung, Sapi, dll">
                            </div>
                            
                            <div class="form-group">
                                <label>Bulan *</label>
                                <select name="bulan" required class="form-control">
                                    <option value="">Pilih Bulan</option>
                                    <option value="Januari">Januari</option>
                                    <option value="Februari">Februari</option>
                                    <option value="Maret">Maret</option>
                                    <option value="April">April</option>
                                    <option value="Mei">Mei</option>
                                    <option value="Juni">Juni</option>
                                    <option value="Juli">Juli</option>
                                    <option value="Agustus">Agustus</option>
                                    <option value="September">September</option>
                                    <option value="Oktober">Oktober</option>
                                    <option value="November">November</option>
                                    <option value="Desember">Desember</option>
                                </select>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Luas Tanam</label>
                                    <input type="number" step="0.01" name="luas_tanam" value="0" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Satuan Luas</label>
                                    <input type="text" name="satuan_luas" value="Ha" class="form-control" 
                                           placeholder="Ha, M2, Ekor, dll">
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Luas Panen</label>
                                    <input type="number" step="0.01" name="luas_panen" value="0" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Produksi</label>
                                    <input type="number" step="0.01" name="produksi" value="0" class="form-control">
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Satuan Produksi</label>
                                    <input type="text" name="satuan_produksi" value="Ton" class="form-control" 
                                           placeholder="Ton, Kg, Liter, Butir, dll">
                                </div>
                                
                                <div class="form-group">
                                    <label>Produktivitas</label>
                                    <input type="number" step="0.01" name="produktivitas" value="0" class="form-control">
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Satuan Produktivitas</label>
                                    <input type="text" name="satuan_produktivitas" value="Kw/Ha" class="form-control" 
                                           placeholder="Kw/Ha, Kg/Ekor, dll">
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
