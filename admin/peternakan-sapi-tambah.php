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
    $no_sapi = (int)$_POST['no_sapi'];
    $lingkar_dada = (float)$_POST['lingkar_dada'];
    $konversi_berat = (float)$_POST['konversi_berat'];
    $tahun = (int)$_POST['tahun'];
    
    $sql = "INSERT INTO peternakan_berat_sapi (tanggal, no_sapi, lingkar_dada, konversi_berat, tahun) 
            VALUES ('$tanggal', $no_sapi, $lingkar_dada, $konversi_berat, $tahun)";
    
    if ($conn->query($sql)) {
        header('Location: peternakan-sapi.php?success=added');
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
    <title>Tambah Berat Sapi - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Tambah Data Berat Sapi</h1>
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
                        <a href="peternakan-sapi.php" class="btn btn-secondary">← Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Tanggal *</label>
                                    <input type="date" name="tanggal" required class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>No Sapi *</label>
                                    <input type="number" name="no_sapi" required class="form-control">
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Lingkar Dada (CM)</label>
                                    <input type="number" step="0.01" name="lingkar_dada" value="0" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Konversi Berat (KG)</label>
                                    <input type="number" step="0.01" name="konversi_berat" value="0" class="form-control">
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
