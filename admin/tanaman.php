<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM tanaman WHERE id = $id");
    header('Location: tanaman.php');
    exit;
}

// Get tanaman (only Pertanian - kategori_id = 1)
$tanaman = $conn->query("
    SELECT t.*, p.nama as pleton_nama, k.nama as kategori_nama 
    FROM tanaman t 
    JOIN pleton p ON t.pleton_id = p.id 
    JOIN kategori k ON p.kategori_id = k.id 
    WHERE k.id = 1
    ORDER BY t.created_at DESC
");

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tanaman - SIHANPANGAN851</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Kelola Tanaman Pertanian</h1>
                <div class="topbar-right">
                    <div class="user-info">
                        <span>👤</span>
                        <span><?= $_SESSION['admin_name'] ?></span>
                    </div>
                    <a href="logout.php" class="btn-logout">Keluar</a>
                </div>
            </div>
            
            <div class="content-area">
                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Tanaman Pertanian</h3>
                        <a href="tambah-tanaman.php" class="btn btn-primary btn-sm">+ Tambah Tanaman</a>
                    </div>
                    <div class="card-body">
                        <div class="thumbnail-grid">
                            <?php while ($row = $tanaman->fetch_assoc()): ?>
                                <div class="thumbnail-item">
                                    <?php if ($row['gambar']): ?>
                                        <img src="../uploads/<?= $row['gambar'] ?>" alt="<?= htmlspecialchars($row['nama']) ?>">
                                    <?php else: ?>
                                        <img src="../assets/img/placeholder.jpg" alt="No image">
                                    <?php endif; ?>
                                    <div class="overlay">
                                        <strong><?= htmlspecialchars($row['nama']) ?></strong><br>
                                        <small><?= $row['pleton_nama'] ?></small><br>
                                        <small>Views: <?= $row['views'] ?></small>
                                        <div style="margin-top: 0.5rem;">
                                            <a href="input-detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">Detail</a>
                                            <a href="edit-tanaman.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">Edit</a>
                                            <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')" class="btn btn-sm btn-danger" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">Hapus</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
