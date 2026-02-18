<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

// Get all hewan with their details
$query = "SELECT h.*, p.nama as pleton_nama, 
                 hd.nama_latin, hd.tanggal_update, hd.informasi_kegiatan
          FROM hewan h 
          LEFT JOIN pleton p ON h.pleton_id = p.id
          LEFT JOIN hewan_detail hd ON h.id = hd.hewan_id
          ORDER BY h.nama";
$result = $conn->query($query);

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Detail Hewan - Admin SIHANPANGAN851</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .detail-table th,
        .detail-table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .detail-table th {
            background-color: #2E5E3E;
            color: white;
        }
        .detail-table tr:hover {
            background-color: #f5f5f5;
        }
        .btn {
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 0.9rem;
        }
        .btn-primary {
            background-color: #2E5E3E;
            color: white;
        }
        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        .status-complete {
            background-color: #d4edda;
            color: #155724;
        }
        .status-incomplete {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <h1>Kelola Detail Hewan</h1>
                <p>Kelola informasi detail untuk setiap hewan ternak</p>
            </div>
            
            <div class="content-body">
                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Hewan Ternak</h3>
                        <div class="card-actions">
                            <a href="tambah-detail-hewan.php" class="btn btn-success">+ Tambah Detail Baru</a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <table class="detail-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Hewan</th>
                                    <th>Pleton</th>
                                    <th>Nama Latin</th>
                                    <th>Tanggal Update</th>
                                    <th>Status Detail</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while ($row = $result->fetch_assoc()): 
                                    $has_detail = !empty($row['nama_latin']) || !empty($row['informasi_kegiatan']);
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['nama']) ?></strong>
                                        <?php if ($row['gambar']): ?>
                                            <br><small>📷 Ada gambar</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['pleton_nama']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_latin'] ?? '-') ?></td>
                                    <td><?= $row['tanggal_update'] ? date('d/m/Y', strtotime($row['tanggal_update'])) : '-' ?></td>
                                    <td>
                                        <?php if ($has_detail): ?>
                                            <span class="status-badge status-complete">Lengkap</span>
                                        <?php else: ?>
                                            <span class="status-badge status-incomplete">Belum Lengkap</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="edit-detail-hewan.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit Detail</a>
                                        <a href="kelola-variabel-hewan.php?id=<?= $row['id'] ?>" class="btn btn-primary">Kelola Variabel</a>
                                        <a href="../detail_new.php?id=<?= $row['id'] ?>&type=hewan" class="btn btn-success" target="_blank">Lihat</a>
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