<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM data_hasil WHERE id = $id");
    header('Location: data-hasil.php?success=deleted');
    exit;
}

// Get filter
$kategori_filter = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;

// Get data hasil
$query = "SELECT dh.*, k.nama as kategori_nama FROM data_hasil dh 
          JOIN kategori k ON dh.kategori_id = k.id";
if ($kategori_filter > 0) {
    $query .= " WHERE dh.kategori_id = $kategori_filter";
}
$query .= " ORDER BY k.nama, dh.komoditas";
$data_hasil = $conn->query($query);

// Get kategori list
$kategori_list = $conn->query("SELECT * FROM kategori ORDER BY urutan");

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Hasil - Admin SIHANPANGAN851</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Data Hasil Pertanian & Peternakan</h1>
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
                        <h3>Daftar Data Hasil</h3>
                        <div style="display: flex; gap: 1rem; align-items: center;">
                            <form method="GET" style="display: flex; gap: 0.5rem;">
                                <select name="kategori" class="form-control" style="width: auto;">
                                    <option value="0">Semua Kategori</option>
                                    <?php 
                                    $kategori_list->data_seek(0);
                                    while ($kat = $kategori_list->fetch_assoc()): 
                                    ?>
                                        <option value="<?= $kat['id'] ?>" <?= $kategori_filter == $kat['id'] ? 'selected' : '' ?>>
                                            <?= $kat['nama'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                                <a href="data-hasil.php" class="btn btn-reset btn-sm">Reset</a>
                            </form>
                            <a href="tambah-data-hasil.php" class="btn btn-primary">+ Tambah Data</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kategori</th>
                                        <th>Komoditas</th>
                                        <th>Bulan</th>
                                        <th>Luas Tanam</th>
                                        <th>Luas Panen</th>
                                        <th>Produksi</th>
                                        <th>Produktivitas</th>
                                        <th>Tahun</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while ($row = $data_hasil->fetch_assoc()): 
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= $row['kategori_nama'] ?></td>
                                            <td><?= htmlspecialchars($row['komoditas']) ?></td>
                                            <td><?= $row['bulan'] ?></td>
                                            <td><?= number_format($row['luas_tanam'], 2) ?> <?= $row['satuan_luas'] ?></td>
                                            <td><?= number_format($row['luas_panen'], 2) ?> <?= $row['satuan_luas'] ?></td>
                                            <td><?= number_format($row['produksi'], 2) ?> <?= $row['satuan_produksi'] ?></td>
                                            <td><?= number_format($row['produktivitas'], 2) ?> <?= $row['satuan_produktivitas'] ?></td>
                                            <td><?= $row['tahun'] ?></td>
                                            <td>
                                                <a href="edit-data-hasil.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                                <a href="data-hasil.php?delete=<?= $row['id'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                    <?php if ($data_hasil->num_rows == 0): ?>
                                        <tr>
                                            <td colspan="10" style="text-align: center; padding: 2rem;">
                                                Belum ada data hasil
                                            </td>
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
