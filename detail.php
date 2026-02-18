<?php
require_once 'config/database.php';
$conn = getConnection();

$tanaman_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get filter parameters
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Track QR scan
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$conn->query("INSERT INTO qr_scans (tanaman_id, ip_address, user_agent) VALUES ($tanaman_id, '$ip', '$user_agent')");

// Update views
$conn->query("UPDATE tanaman SET views = views + 1 WHERE id = $tanaman_id");

// Get tanaman detail
$tanaman = $conn->query("
    SELECT t.*, p.nama as pleton_nama, k.nama as kategori_nama 
    FROM tanaman t 
    JOIN pleton p ON t.pleton_id = p.id 
    JOIN kategori k ON p.kategori_id = k.id 
    WHERE t.id = $tanaman_id
")->fetch_assoc();

// Get budidaya data with filter
$where_clause = "WHERE tanaman_id = $tanaman_id";
if ($filter_bulan && $filter_tahun) {
    $where_clause .= " AND MONTH(tanggal) = '$filter_bulan' AND YEAR(tanggal) = '$filter_tahun'";
} elseif ($filter_tahun) {
    $where_clause .= " AND YEAR(tanggal) = '$filter_tahun'";
}

$budidaya = $conn->query("SELECT * FROM budidaya_data $where_clause ORDER BY tanggal DESC");

// Get available years for filter
$years = $conn->query("SELECT DISTINCT YEAR(tanggal) as tahun FROM budidaya_data WHERE tanaman_id = $tanaman_id AND tanggal IS NOT NULL ORDER BY tahun DESC");

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tanaman['nama']) ?> - SIHANPANGAN851</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
    .detail-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 2rem;
    }
    .detail-header {
        text-align: center;
        margin-bottom: 2rem;
    }
    .detail-header img {
        width: 100%;
        max-width: 400px;
        height: 300px;
        object-fit: cover;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .detail-header h1 {
        color: var(--primary);
        margin: 1rem 0;
    }
    .detail-info {
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }
    .table-wrapper {
        overflow-x: auto;
        background: white;
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #e9ecef;
    }
    th {
        background: var(--primary);
        color: white;
    }
    
    .filter-container {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 1rem;
    }
    
    .filter-form {
        display: flex;
        gap: 0.75rem;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .filter-form select {
        padding: 0.75rem;
        border: 2px solid var(--primary);
        border-radius: 8px;
        font-size: 1rem;
        background: white;
        color: var(--dark);
        min-width: 150px;
    }
    
    .filter-form select:focus {
        outline: none;
        border-color: var(--primary-dark);
        box-shadow: 0 0 0 3px rgba(45, 122, 62, 0.1);
    }
    
    .btn-filter {
        padding: 0.75rem 1.5rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .btn-filter:hover {
        background: var(--primary-dark);
    }
    
    .btn-reset {
        padding: 0.5rem 1rem;
        background: #e9ecef;
        color: #495057;
        border: 1px solid #ced4da;
        border-radius: 8px;
        font-size: 0.9rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s;
    }
    
    .btn-reset:hover {
        background: #dee2e6;
        color: #212529;
    }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="assets/img/logo1.png" alt="Logo 1" class="logo-icon">
                <img src="assets/img/logo2.png" alt="Logo 2" class="logo-icon">
                <span class="logo-text">SIHANPANGAN851</span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="kategori.php?id=1">Pertanian</a></li>
                <li><a href="kategori.php?id=2">Peternakan</a></li>
            </ul>
        </div>
    </nav>

    <div class="detail-container">
        <div class="detail-header">
            <?php if ($tanaman['gambar']): ?>
                <img src="uploads/<?= $tanaman['gambar'] ?>" alt="<?= htmlspecialchars($tanaman['nama']) ?>">
            <?php endif; ?>
            <h1><?= htmlspecialchars($tanaman['nama']) ?></h1>
            <p><?= $tanaman['kategori_nama'] ?> - <?= $tanaman['pleton_nama'] ?></p>
        </div>
        
        <?php if ($tanaman['deskripsi']): ?>
        <div class="detail-info">
            <h3 style="color: var(--primary); margin-bottom: 1rem;">Deskripsi</h3>
            <p><?= nl2br(htmlspecialchars($tanaman['deskripsi'])) ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Filter Data Budidaya -->
        <div class="filter-container">
            <form method="GET" class="filter-form">
                <input type="hidden" name="id" value="<?= $tanaman_id ?>">
                <select name="bulan">
                    <option value="">Semua Bulan</option>
                    <option value="1" <?= $filter_bulan == '1' ? 'selected' : '' ?>>Januari</option>
                    <option value="2" <?= $filter_bulan == '2' ? 'selected' : '' ?>>Februari</option>
                    <option value="3" <?= $filter_bulan == '3' ? 'selected' : '' ?>>Maret</option>
                    <option value="4" <?= $filter_bulan == '4' ? 'selected' : '' ?>>April</option>
                    <option value="5" <?= $filter_bulan == '5' ? 'selected' : '' ?>>Mei</option>
                    <option value="6" <?= $filter_bulan == '6' ? 'selected' : '' ?>>Juni</option>
                    <option value="7" <?= $filter_bulan == '7' ? 'selected' : '' ?>>Juli</option>
                    <option value="8" <?= $filter_bulan == '8' ? 'selected' : '' ?>>Agustus</option>
                    <option value="9" <?= $filter_bulan == '9' ? 'selected' : '' ?>>September</option>
                    <option value="10" <?= $filter_bulan == '10' ? 'selected' : '' ?>>Oktober</option>
                    <option value="11" <?= $filter_bulan == '11' ? 'selected' : '' ?>>November</option>
                    <option value="12" <?= $filter_bulan == '12' ? 'selected' : '' ?>>Desember</option>
                </select>
                <select name="tahun">
                    <option value="">Semua Tahun</option>
                    <?php while ($year = $years->fetch_assoc()): ?>
                        <option value="<?= $year['tahun'] ?>" <?= $filter_tahun == $year['tahun'] ? 'selected' : '' ?>>
                            <?= $year['tahun'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn-filter">Filter</button>
                <a href="detail.php?id=<?= $tanaman_id ?>" class="btn-reset">Reset</a>
            </form>
        </div>
        
        <div class="table-wrapper">
            <h3 style="color: var(--primary); margin-bottom: 1rem;">Data Budidaya</h3>
            <?php if ($budidaya->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kegiatan</th>
                        <th>Luas</th>
                        <th>Populasi</th>
                        <th>Hasil</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = $budidaya->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['kegiatan']) ?></td>
                        <td><?= htmlspecialchars($row['luas']) ?></td>
                        <td><?= htmlspecialchars($row['populasi']) ?></td>
                        <td><?= htmlspecialchars($row['hasil']) ?></td>
                        <td><?= $row['tanggal'] ? date('d/m/Y', strtotime($row['tanggal'])) : '-' ?></td>
                        <td><?= htmlspecialchars($row['keterangan']) ?: '-' ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #666;">Belum ada data budidaya</p>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <a href="pleton.php?id=<?= $tanaman['pleton_id'] ?>" class="btn-primary">← Kembali</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 SIHANPANGAN851. All rights reserved.</p>
    </footer>
</body>
</html>
