<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

// Get statistics
$stats = [
    'total_tanaman' => $conn->query("SELECT COUNT(*) as count FROM tanaman")->fetch_assoc()['count'],
    'published' => $conn->query("SELECT COUNT(*) as count FROM tanaman WHERE status='published'")->fetch_assoc()['count'],
    'draft' => $conn->query("SELECT COUNT(*) as count FROM tanaman WHERE status='draft'")->fetch_assoc()['count'],
    'total_views' => $conn->query("SELECT SUM(views) as total FROM tanaman")->fetch_assoc()['total'] ?? 0,
    'qr_scans' => $conn->query("SELECT COUNT(*) as count FROM qr_scans")->fetch_assoc()['count']
];

// Get popular tanaman
$popular = $conn->query("SELECT * FROM tanaman ORDER BY views DESC LIMIT 5");

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIHANPANGAN851</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Dashboard</h1>
                <div class="topbar-right">
                    <div class="user-info">
                        <span>👤</span>
                        <span>Selamat datang, <?= $_SESSION['admin_name'] ?>!</span>
                    </div>
                    <a href="logout.php" class="btn-logout">Keluar</a>
                </div>
            </div>
            
            <div class="content-area">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="icon">🌱</div>
                        <div class="number"><?= $stats['total_tanaman'] ?></div>
                        <div class="label">Total Tanaman</div>
                    </div>
                    <div class="stat-card">
                        <div class="icon">✅</div>
                        <div class="number"><?= $stats['published'] ?></div>
                        <div class="label">Dipublikasikan</div>
                    </div>
                    <div class="stat-card">
                        <div class="icon">📝</div>
                        <div class="number"><?= $stats['draft'] ?></div>
                        <div class="label">Draft</div>
                    </div>
                    <div class="stat-card">
                        <div class="icon">👁️</div>
                        <div class="number"><?= $stats['total_views'] ?></div>
                        <div class="label">Total Views</div>
                    </div>
                    <div class="stat-card">
                        <div class="icon">📱</div>
                        <div class="number"><?= $stats['qr_scans'] ?></div>
                        <div class="label">QR Scans</div>
                    </div>
                </div>
                
                <!-- Popular Tanaman -->
                <div class="card">
                    <div class="card-header">
                        <h3>Tanaman Terpopuler</h3>
                    </div>
                    <div class="card-body">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Views</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $popular->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['nama']) ?></td>
                                    <td><?= $row['views'] ?> views</td>
                                    <td>
                                        <span class="badge badge-<?= $row['status'] === 'published' ? 'success' : 'secondary' ?>">
                                            <?= $row['status'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
