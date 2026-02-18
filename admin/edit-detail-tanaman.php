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

// Get existing detail
$detail = $conn->query("SELECT * FROM tanaman_detail WHERE tanaman_id = $id")->fetch_assoc();

// Handle form submission
if ($_POST) {
    $nama_latin = $_POST['nama_latin'];
    $tanggal_update = $_POST['tanggal_update'];
    $informasi_kegiatan = $_POST['informasi_kegiatan'];
    
    if ($detail) {
        // Update existing detail
        $stmt = $conn->prepare("UPDATE tanaman_detail SET nama_latin = ?, tanggal_update = ?, informasi_kegiatan = ? WHERE tanaman_id = ?");
        $stmt->bind_param("sssi", $nama_latin, $tanggal_update, $informasi_kegiatan, $id);
    } else {
        // Insert new detail
        $stmt = $conn->prepare("INSERT INTO tanaman_detail (tanaman_id, nama_latin, tanggal_update, informasi_kegiatan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $id, $nama_latin, $tanggal_update, $informasi_kegiatan);
    }
    
    if ($stmt->execute()) {
        header('Location: detail-tanaman.php?success=1');
        exit;
    } else {
        $error = "Gagal menyimpan data: " . $conn->error;
    }
}

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Detail Tanaman - Admin SIHANPANGAN851</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
            color: #2E5E3E;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        .form-control:focus {
            outline: none;
            border-color: #2E5E3E;
            box-shadow: 0 0 0 2px rgba(46, 94, 62, 0.2);
        }
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 1rem;
            margin-right: 0.5rem;
        }
        .btn-primary {
            background-color: #2E5E3E;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .preview-section {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <h1>Edit Detail Tanaman</h1>
                <p>Edit informasi detail untuk: <strong><?= htmlspecialchars($tanaman['nama']) ?></strong></p>
            </div>
            
            <div class="content-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Informasi Detail Tanaman</h3>
                    </div>
                    
                    <div class="card-body">
                        <div class="preview-section">
                            <h4>Preview Tanaman</h4>
                            <p><strong>Nama:</strong> <?= htmlspecialchars($tanaman['nama']) ?></p>
                            <p><strong>Deskripsi:</strong> <?= htmlspecialchars($tanaman['deskripsi'] ?? '-') ?></p>
                            <?php if ($tanaman['gambar']): ?>
                                <p><strong>Gambar:</strong> <img src="../uploads/<?= $tanaman['gambar'] ?>" alt="Preview" style="max-width: 200px; height: auto; border-radius: 4px;"></p>
                            <?php endif; ?>
                        </div>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label for="nama_latin">Nama Latin</label>
                                <input type="text" id="nama_latin" name="nama_latin" class="form-control" 
                                       value="<?= htmlspecialchars($detail['nama_latin'] ?? '') ?>" 
                                       placeholder="Contoh: Zea mays.">
                                <small class="form-text">Nama ilmiah tanaman dalam bahasa Latin</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="tanggal_update">Tanggal Update</label>
                                <input type="date" id="tanggal_update" name="tanggal_update" class="form-control" 
                                       value="<?= $detail['tanggal_update'] ?? date('Y-m-d') ?>">
                                <small class="form-text">Tanggal terakhir informasi diperbarui</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="informasi_kegiatan">Informasi Kegiatan</label>
                                <textarea id="informasi_kegiatan" name="informasi_kegiatan" class="form-control" 
                                          placeholder="Informasi bebas yang bisa diinput teks sendiri oleh pemegang akun"><?= htmlspecialchars($detail['informasi_kegiatan'] ?? '') ?></textarea>
                                <small class="form-text">Informasi tambahan tentang kegiatan atau catatan khusus</small>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Simpan Detail</button>
                                <a href="detail-tanaman.php" class="btn btn-secondary">Kembali</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>