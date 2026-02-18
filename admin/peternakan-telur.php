<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM peternakan_rincian_telur WHERE id = $id");
    header('Location: peternakan-telur.php?success=deleted');
    exit;
}

$data = $conn->query("SELECT * FROM peternakan_rincian_telur ORDER BY jenis_unggas, no_urut");
closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Telur - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Rincian Hasil Produksi Telur</h1>
                <div class="topbar-right">
                    <span class="user-info">👤 <?= $_SESSION['admin_name'] ?></span>
                    <a href="logout.php" class="btn-logout">Logout</a>
                </div>
            </div>
            
            <div class="content-area">
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?php
                        if ($_GET['success'] == 'added') echo 'Data berhasil ditambahkan!';
                        if ($_GET['success'] == 'updated') echo 'Data berhasil diupdate!';
                        if ($_GET['success'] == 'deleted') echo 'Data berhasil dihapus!';
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Rincian Telur</h3>
                        <a href="peternakan-telur-tambah.php" class="btn btn-primary">+ Tambah Data</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Jenis Unggas</th>
                                        <th>No</th>
                                        <th>Keterangan</th>
                                        <th>Tanggal</th>
                                        <th>Telur (Butir)</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $data->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['jenis_unggas']) ?></td>
                                            <td><?= $row['no_urut'] ?></td>
                                            <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                            <td><?= $row['tanggal'] ? date('d/m/Y', strtotime($row['tanggal'])) : '-' ?></td>
                                            <td><?= number_format($row['telur_butir'], 0) ?></td>
                                            <td>
                                                <a href="peternakan-telur-edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                                <a href="peternakan-telur.php?delete=<?= $row['id'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                    <?php if ($data->num_rows == 0): ?>
                                        <tr>
                                            <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada data</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
