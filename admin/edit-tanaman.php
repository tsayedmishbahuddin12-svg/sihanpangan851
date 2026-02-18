<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$success = '';
$error = '';

// Get tanaman data
$result = $conn->query("SELECT * FROM tanaman WHERE id = $id");
$tanaman = $result->fetch_assoc();

if (!$tanaman) {
    header('Location: tanaman.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pleton_id = $_POST['pleton_id'];
    $nama = $conn->real_escape_string($_POST['nama']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $status = $_POST['status'];
    
    // Handle file upload
    $gambar = $tanaman['gambar'];
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['gambar']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newname = uniqid() . '.' . $ext;
            $upload_path = '../uploads/' . $newname;
            
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                // Delete old image
                if ($gambar && file_exists('../uploads/' . $gambar)) {
                    unlink('../uploads/' . $gambar);
                }
                $gambar = $newname;
            }
        }
    }
    
    $sql = "UPDATE tanaman SET pleton_id = ?, nama = ?, gambar = ?, deskripsi = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssi", $pleton_id, $nama, $gambar, $deskripsi, $status, $id);
    
    if ($stmt->execute()) {
        $success = 'Tanaman berhasil diupdate!';
        $tanaman = $conn->query("SELECT * FROM tanaman WHERE id = $id")->fetch_assoc();
    } else {
        $error = 'Gagal mengupdate tanaman: ' . $conn->error;
    }
}

// Get pleton list (only Pertanian)
$pletons = $conn->query("SELECT p.* FROM pleton p JOIN kategori k ON p.kategori_id = k.id WHERE k.id = 1 ORDER BY p.urutan");

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tanaman - SIHANPANGAN851</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-layout">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <div class="topbar">
                <h1>Edit Tanaman</h1>
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
                        <h3>Form Edit Tanaman</h3>
                        <a href="tanaman.php" class="btn btn-secondary btn-sm">← Kembali</a>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Pleton *</label>
                                <select name="pleton_id" required>
                                    <option value="">Pilih Pleton</option>
                                    <?php while ($pleton = $pletons->fetch_assoc()): ?>
                                        <option value="<?= $pleton['id'] ?>" <?= $tanaman['pleton_id'] == $pleton['id'] ? 'selected' : '' ?>>
                                            <?= $pleton['nama'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Nama Tanaman *</label>
                                <input type="text" name="nama" value="<?= htmlspecialchars($tanaman['nama']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Gambar Thumbnail</label>
                                <?php if ($tanaman['gambar']): ?>
                                    <img src="../uploads/<?= $tanaman['gambar'] ?>" class="image-preview show" id="currentImage" alt="Current">
                                <?php endif; ?>
                                <div class="file-upload" onclick="document.getElementById('fileInput').click()">
                                    <input type="file" id="fileInput" name="gambar" accept="image/*" onchange="previewImage(this)">
                                    <div class="file-upload-label">
                                        <p>📷 Klik untuk ganti gambar</p>
                                        <small>Format: JPG, PNG, GIF (Max 2MB)</small>
                                    </div>
                                </div>
                                <img id="imagePreview" class="image-preview" alt="Preview">
                            </div>
                            
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="deskripsi" rows="4"><?= htmlspecialchars($tanaman['deskripsi']) ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label>Status *</label>
                                <select name="status" required>
                                    <option value="published" <?= $tanaman['status'] == 'published' ? 'selected' : '' ?>>Published</option>
                                    <option value="draft" <?= $tanaman['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Update Tanaman</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        const current = document.getElementById('currentImage');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.add('show');
                if (current) current.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>
</body>
</html>
