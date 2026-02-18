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
    $keterangan = $conn->real_escape_string($_POST['keterangan']);
    $tanggal = $conn->real_escape_string($_POST['tanggal']);
    $kuantiti = $conn->real_escape_string($_POST['kuantiti']);
    $harga = (float)$_POST['harga'];
    $total = (float)$_POST['total'];
    $tahun = (int)$_POST['tahun'];
    
    $sql = "INSERT INTO peternakan_pengeluaran_unggas (keterangan, tanggal, kuantiti, harga, total, tahun) 
            VALUES ('$keterangan', '$tanggal', '$kuantiti', $harga, $total, $tahun)";
    
    if ($conn->query($sql)) {
        header('Location: peternakan-pengeluaran-unggas.php?success=added');
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
    <title>Tambah Pengeluaran Unggas - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Tambah Pengeluaran Unggas</h1>
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
                        <a href="peternakan-pengeluaran-unggas.php" class="btn btn-secondary">← Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label>Keterangan *</label>
                                <textarea name="keterangan" required class="form-control" rows="2" 
                                          placeholder="Contoh: PEMBELIAN VITAMIN (EGG STIMULANT)"></textarea>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Tanggal *</label>
                                    <input type="date" name="tanggal" required class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Kuantiti *</label>
                                    <input type="text" name="kuantiti" required class="form-control" 
                                           placeholder="Contoh: 1 KOTAK, 2 KG">
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Harga (Rp)</label>
                                    <input type="number" step="0.01" name="harga" value="0" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Total (Rp)</label>
                                    <input type="number" step="0.01" name="total" value="0" class="form-control">
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
