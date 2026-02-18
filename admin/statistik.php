<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

// Get statistics by kategori
$stats_pertanian = $conn->query("
    SELECT COUNT(*) as total 
    FROM tanaman t 
    JOIN pleton p ON t.pleton_id = p.id 
    WHERE p.kategori_id = 1
")->fetch_assoc()['total'];

$stats_peternakan = $conn->query("
    SELECT COUNT(*) as total 
    FROM tanaman t 
    JOIN pleton p ON t.pleton_id = p.id 
    WHERE p.kategori_id = 2
")->fetch_assoc()['total'];

// Get recent scans
$recent_scans = $conn->query("
    SELECT qs.*, t.nama as tanaman_nama 
    FROM qr_scans qs 
    LEFT JOIN tanaman t ON qs.tanaman_id = t.id 
    ORDER BY qs.scanned_at DESC 
    LIMIT 10
");

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistik - SIHANPANGAN851</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Statistik</h1>
                <div class="topbar-right">
                    <div class="user-info">
                        <span>👤</span>
                        <span><?= $_SESSION['admin_name'] ?></span>
                    </div>
                    <a href="logout.php" class="btn-logout">Keluar</a>
                </div>
            </div>
            
            <div class="content-area">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="icon">🌾</div>
                        <div class="number"><?= $stats_pertanian ?></div>
                        <div class="label">Tanaman Pertanian</div>
                    </div>
                    <div class="stat-card">
                        <div class="icon">🐄</div>
                        <div class="number"><?= $stats_peternakan ?></div>
                        <div class="label">Hewan Peternakan</div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Aktivitas QR Scan Terbaru</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tanaman</th>
                                        <th>IP Address</th>
                                        <th>Waktu Scan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recent_scans->num_rows > 0): ?>
                                        <?php while ($scan = $recent_scans->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= $scan['tanaman_nama'] ?? 'Unknown' ?></td>
                                            <td><?= htmlspecialchars($scan['ip_address']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($scan['scanned_at'])) ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" style="text-align: center;">Belum ada aktivitas scan</td>
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
