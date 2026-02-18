<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get tanaman data
$tanaman = $conn->query("SELECT * FROM tanaman WHERE id = $id")->fetch_assoc();
if (!$tanaman) {
    header('Location: detail-tanaman.php');
    exit;
}

// Handle form submission
if ($_POST) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $no = $_POST['no'];
            $variabel = $_POST['variabel'];
            $keterangan = $_POST['keterangan'];
            
            $stmt = $conn->prepare("INSERT INTO tanaman_variabel (tanaman_id, no, variabel, keterangan) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $id, $no, $variabel, $keterangan);
            
            if ($stmt->execute()) {
                $success = "Variabel berhasil ditambahkan";
            } else {
                $error = "Gagal menambahkan variabel: " . $conn->error;
            }
        } elseif ($_POST['action'] == 'update') {
            $var_id = $_POST['var_id'];
            $no = $_POST['no'];
            $variabel = $_POST['variabel'];
            $keterangan = $_POST['keterangan'];
            
            $stmt = $conn->prepare("UPDATE tanaman_variabel SET no = ?, variabel = ?, keterangan = ? WHERE id = ?");
            $stmt->bind_param("issi", $no, $variabel, $keterangan, $var_id);
            
            if ($stmt->execute()) {
                $success = "Variabel berhasil diperbarui";
            } else {
                $error = "Gagal memperbarui variabel: " . $conn->error;
            }
        } elseif ($_POST['action'] == 'delete') {
            $var_id = $_POST['var_id'];
            
            $stmt = $conn->prepare("DELETE FROM tanaman_variabel WHERE id = ?");
            $stmt->bind_param("i", $var_id);
            
            if ($stmt->execute()) {
                $success = "Variabel berhasil dihapus";
            } else {
                $error = "Gagal menghapus variabel: " . $conn->error;
            }
        }
    }
}

// Get existing variabel
$variabel_result = $conn->query("SELECT * FROM tanaman_variabel WHERE tanaman_id = $id ORDER BY no");

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Variabel Tanaman - Admin SIHANPANGAN851</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .variabel-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        .variabel-table th,
        .variabel-table td {
            padding: 0.75rem;
            text-align: left;
            border: 1px solid #ddd;
        }
        .variabel-table th {
            background-color: #2E5E3E;
            color: white;
            text-align: center;
        }
        .variabel-table td {
            text-align: center;
        }
        .variabel-table tr:hover {
            background-color: #f5f5f5;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #2E5E3E;
        }
        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
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
        .btn-primary { background-color: #2E5E3E; color: white; }
        .btn-warning { background-color: #ffc107; color: #212529; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-secondary { background-color: #6c757d; color: white; }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .add-form {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        .form-row {
            display: grid;
            grid-template-columns: 80px 1fr 1fr auto;
            gap: 1rem;
            align-items: end;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <h1>Kelola Variabel Tanaman</h1>
                <p>Kelola tabel keterangan untuk: <strong><?= htmlspecialchars($tanaman['nama']) ?></strong></p>
            </div>
            
            <div class="content-body">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Tambah Variabel Baru</h3>
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" class="add-form">
                            <input type="hidden" name="action" value="add">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="no">No</label>
                                    <input type="number" id="no" name="no" class="form-control" min="1" required>
                                </div>
                                <div class="form-group">
                                    <label for="variabel">Variabel</label>
                                    <input type="text" id="variabel" name="variabel" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <input type="text" id="keterangan" name="keterangan" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Tambah</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Daftar Variabel</h3>
                        <div class="card-actions">
                            <a href="detail-tanaman.php" class="btn btn-secondary">← Kembali</a>
                            <a href="../detail_new.php?id=<?= $id ?>&type=tanaman" class="btn btn-primary" target="_blank">Lihat Preview</a>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <table class="variabel-table">
                            <thead>
                                <tr>
                                    <th>NO</th>
                                    <th>VARIABEL</th>
                                    <th>KETERANGAN</th>
                                    <th>AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($variabel_result->num_rows > 0): ?>
                                    <?php while ($var = $variabel_result->fetch_assoc()): ?>
                                    <tr>
                                        <form method="POST" style="display: contents;">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="var_id" value="<?= $var['id'] ?>">
                                            <td>
                                                <input type="number" name="no" value="<?= $var['no'] ?>" class="form-control" min="1" style="width: 60px;">
                                            </td>
                                            <td>
                                                <input type="text" name="variabel" value="<?= htmlspecialchars($var['variabel']) ?>" class="form-control">
                                            </td>
                                            <td>
                                                <input type="text" name="keterangan" value="<?= htmlspecialchars($var['keterangan']) ?>" class="form-control">
                                            </td>
                                            <td>
                                                <button type="submit" class="btn btn-warning">Update</button>
                                        </form>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus variabel ini?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="var_id" value="<?= $var['id'] ?>">
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </form>
                                            </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center; padding: 2rem; color: #666;">
                                            Belum ada variabel. Tambahkan variabel pertama menggunakan form di atas.
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
</body>
</html>