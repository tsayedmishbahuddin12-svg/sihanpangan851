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
    $jenis_ternak = $conn->real_escape_string($_POST['jenis_ternak']);
    $kategori = $conn->real_escape_string($_POST['kategori']);
    $jantan = (int)$_POST['jantan'];
    $betina = (int)$_POST['betina'];
    $anakan = (int)$_POST['anakan'];
    $tahun = (int)$_POST['tahun'];
    
    $sql = "INSERT INTO peternakan_populasi (jenis_ternak, kategori, jantan, betina, anakan, tahun) 
            VALUES ('$jenis_ternak', '$kategori', $jantan, $betina, $anakan, $tahun)";
    
    if ($conn->query($sql)) {
        header('Location: peternakan-populasi.php?success=added');
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
    <title>Tambah Populasi - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Tambah Data Populasi</h1>
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
                        <a href="peternakan-populasi.php" class="btn btn-secondary">← Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="form-group">
                                <label>Kategori *</label>
                                <select name="kategori" required class="form-control">
                                    <option value="">Pilih Kategori</option>
                                    <option value="Unggas">Unggas</option>
                                    <option value="Ruminansia">Ruminansia</option>
                                    <option value="Perikanan">Perikanan</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Jenis Ternak *</label>
                                <input type="text" name="jenis_ternak" required class="form-control" 
                                       placeholder="Contoh: Ayam Petelur, Sapi, Ikan Lele">
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                                <div class="form-group">
                                    <label>Jantan</label>
                                    <input type="number" name="jantan" value="0" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Betina</label>
                                    <input type="number" name="betina" value="0" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label>Anakan</label>
                                    <input type="number" name="anakan" value="0" class="form-control">
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
