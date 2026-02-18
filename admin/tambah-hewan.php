<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pleton_id = $_POST['pleton_id'];
    $nama = $conn->real_escape_string($_POST['nama']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $status = $_POST['status'];
    
    // Handle file upload
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newname = uniqid() . '.' . $ext;
            $upload_path = '../uploads/' . $newname;
            
            if (!file_exists('../uploads')) {
                mkdir('../uploads', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                $gambar = $newname;
            }
        }
    }
    
    $sql = "INSERT INTO tanaman (pleton_id, nama, gambar, deskripsi, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $pleton_id, $nama, $gambar, $deskripsi, $status);
    
    if ($stmt->execute()) {
        $success = 'Hewan berhasil ditambahkan!';
    } else {
        $error = 'Gagal menambahkan hewan: ' . $conn->error;
    }
}

// Get pleton list (only Peternakan - kategori_id = 2)
$pletons = $conn->query("SELECT p.*, k.nama as kategori_nama FROM pleton p JOIN kategori k ON p.kategori_id = k.id WHERE k.id = 2 ORDER BY p.urutan");

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Hewan - SIHANPANGAN851</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Tambah Hewan Peternakan</h1>
                <div class="topbar-right">
                    <div class="user-info">
                        <span>👤</span>
                        <span><?= $_SESSION['admin_name'] ?></span>
                    </div>
                    <a href="logout.php" class="btn-logout">Keluar</a>
                </div>
            </div>
            
            <div class="content-area">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Form Tambah Hewan</h3>
                        <a href="hewan.php" class="btn btn-secondary btn-sm">← Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Pleton *</label>
                                <select name="pleton_id" required>
                                    <option value="">Pilih Pleton</option>
                                    <?php while ($pleton = $pletons->fetch_assoc()): ?>
                                        <option value="<?= $pleton['id'] ?>">
                                            <?= $pleton['nama'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Nama Hewan *</label>
                                <input type="text" name="nama" required placeholder="Contoh: Ayam Broiler">
                            </div>
                            
                            <div class="form-group">
                                <label>Gambar Thumbnail *</label>
                                <div class="file-upload" onclick="document.getElementById('fileInput').click()">
                                    <input type="file" id="fileInput" name="gambar" accept="image/*" onchange="previewImage(this)" required>
                                    <div class="file-upload-label">
                                        <p>📷 Klik untuk upload gambar</p>
                                        <small>Format: JPG, PNG, GIF (Max 2MB)</small>
                                    </div>
                                </div>
                                <img id="imagePreview" class="image-preview" alt="Preview">
                            </div>
                            
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" rows="4" placeholder="Deskripsi singkat tentang hewan..."></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" required>
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Simpan Hewan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.add('show');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>
