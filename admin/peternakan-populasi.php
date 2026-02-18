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
    $conn->query("DELETE FROM peternakan_populasi WHERE id = $id");
    header('Location: peternakan-populasi.php?success=deleted');
    exit;
}

$data = $conn->query("SELECT * FROM peternakan_populasi ORDER BY kategori, jenis_ternak");
closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Populasi Ternak - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Tabel Jumlah Populasi Ternak</h1>
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
                        <h3>Daftar Populasi Ternak</h3>
                        <a href="peternakan-populasi-tambah.php" class="btn btn-primary">+ Tambah Data</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kategori</th>
                                        <th>Jenis Ternak</th>
                                        <th>Jantan</th>
                                        <th>Betina</th>
                                        <th>Anakan</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while ($row = $data->fetch_assoc()): 
                                        $total = $row['jantan'] + $row['betina'] + $row['anakan'];
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($row['kategori']) ?></td>
                                            <td><?= htmlspecialchars($row['jenis_ternak']) ?></td>
                                            <td><?= number_format($row['jantan'], 0) ?></td>
                                            <td><?= number_format($row['betina'], 0) ?></td>
                                            <td><?= number_format($row['anakan'], 0) ?></td>
                                            <td><strong><?= number_format($total, 0) ?></strong></td>
                                            <td>
                                                <a href="peternakan-populasi-edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                                <a href="peternakan-populasi.php?delete=<?= $row['id'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                    <?php if ($data->num_rows == 0): ?>
                                        <tr>
                                            <td colspan="8" style="text-align: center; padding: 2rem;">Belum ada data</td>
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
