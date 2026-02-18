<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Data Hasil - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
        margin-top: 2rem;
    }
    
    .menu-card {
        background: white;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-left: 4px solid var(--primary);
    }
    
    .menu-card h3 {
        color: var(--primary);
        margin-bottom: 1rem;
    }
    
    .menu-card ul {
        list-style: none;
        padding: 0;
    }
    
    .menu-card li {
        margin: 0.5rem 0;
    }
    
    .menu-card a {
        color: var(--dark);
        text-decoration: none;
        display: block;
        padding: 0.5rem;
        border-radius: 5px;
        transition: all 0.3s;
    }
    
    .menu-card a:hover {
        background: var(--light);
        color: var(--primary);
        padding-left: 1rem;
    }
    </style>
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Menu Data Hasil Pertanian & Peternakan</h1>
                <div class="topbar-right">
                    <span class="user-info">👤 <?= $_SESSION['admin_name'] ?></span>
                    <a href="logout.php" class="btn-logout">Logout</a>
                </div>
            </div>
            
            <div class="content-area">
                <div class="menu-grid">
                    <!-- PERTANIAN -->
                    <div class="menu-card">
                        <h3>🌾 PERTANIAN</h3>
                        <ul>
                            <li><a href="pertanian-panen.php">→ Laporan Panen & Belanja Tanaman</a></li>
                            <li><a href="pertanian-belanja.php">→ Laporan Belanja & Hasil Panen</a></li>
                        </ul>
                    </div>
                    
                    <!-- PETERNAKAN -->
                    <div class="menu-card">
                        <h3>🐄 PETERNAKAN - Populasi & Produksi</h3>
                        <ul>
                            <li><a href="peternakan-populasi.php">→ Jumlah Populasi Ternak</a></li>
                            <li><a href="peternakan-produksi.php">→ Hasil Produksi Unggas</a></li>
                            <li><a href="peternakan-telur.php">→ Rincian Produksi Telur</a></li>
                            <li><a href="peternakan-sapi.php">→ Berat Badan Sapi</a></li>
                        </ul>
                    </div>
                    
                    <!-- PETERNAKAN - UNGGAS -->
                    <div class="menu-card">
                        <h3>🐔 PETERNAKAN - Unggas</h3>
                        <ul>
                            <li><a href="peternakan-pemasukan-unggas.php">→ Pemasukan Ton Unggas</a></li>
                            <li><a href="peternakan-pengeluaran-unggas.php">→ Pengeluaran Ton Unggas</a></li>
                        </ul>
                    </div>
                    
                    <!-- PETERNAKAN - PERIKANAN -->
                    <div class="menu-card">
                        <h3>🐟 PETERNAKAN - Perikanan</h3>
                        <ul>
                            <li><a href="peternakan-pemasukan-ikan.php">→ Pemasukan Ton Perikanan</a></li>
                            <li><a href="peternakan-pengeluaran-ikan.php">→ Pengeluaran Ton Perikanan</a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
