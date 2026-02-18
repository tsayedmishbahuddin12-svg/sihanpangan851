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
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $jenis = $conn->real_escape_string($_POST['jenis']);
    $telur_butir = (int)$_POST['telur_butir'];
    $tahun = (int)$_POST['tahun'];
    
    $sql = "INSERT INTO peternakan_produksi_unggas (tanggal, jenis, telur_butir, tahun) 
            VALUES ('$tanggal', '$jenis', $telur_butir, $tahun)";
    
    if ($conn->query($sql)) {
        header('Location: peternakan-produksi.php?success=added');
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
    <title>Tambah Produksi - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Tambah Data Produksi</h1>
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
                        <a href="peternakan-produksi.php" class="btn btn-secondary">← Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Tanggal *</label>
                                    <input type="date" name="tanggal" required class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Jenis *</label>
                                    <select name="jenis" required class="form-control">
                                        <option value="">Pilih Jenis</option>
                                        <option value="Ayam">Ayam</option>
                                        <option value="Bebek">Bebek</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
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
