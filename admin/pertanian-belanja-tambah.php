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
    $komoditas = $conn->real_escape_string($_POST['komoditas']);
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $luas = (float)$_POST['luas'];
    $hasil = (float)$_POST['hasil'];
    $harga = (float)$_POST['harga'];
    $jumlah = (float)$_POST['jumlah'];
    $tahun = (int)$_POST['tahun'];
    
    $sql = "INSERT INTO pertanian_belanja_hasil (komoditas, tanggal, luas, hasil, harga, jumlah, tahun) 
            VALUES ('$komoditas', '$tanggal', $luas, $hasil, $harga, $jumlah, $tahun)";
    
    if ($conn->query($sql)) {
        header('Location: pertanian-belanja.php?success=added');
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
    <title>Tambah Data Belanja - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Tambah Data Belanja & Hasil</h1>
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
                        <a href="pertanian-belanja.php" class="btn btn-secondary">← Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Komoditas *</label>
                                    <input type="text" name="komoditas" required class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Tanggal *</label>
                                    <input type="date" name="tanggal" required class="form-control">
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
