<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    die("Silakan login terlebih dahulu. <a href='login.php'>Login</a>");
}

require_once '../config/database.php';
$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id == 0) die("ID tidak valid");

// Get item
$item = $conn->query("SELECT t.*, k.id as kategori_id FROM tanaman t LEFT JOIN pleton p ON t.pleton_id = p.id LEFT JOIN kategori k ON p.kategori_id = k.id WHERE t.id = $id")->fetch_assoc();
if (!$item) die("Item tidak ditemukan");

$is_hewan = $item['kategori_id'] == 2;

// Get detail
$detail_result = $conn->query("SELECT * FROM tanaman_detail WHERE tanaman_id = $id");
$detail = $detail_result ? $detail_result->fetch_assoc() : null;

// Get variabel
$variabel = [];
$var_result = $conn->query("SELECT * FROM tanaman_variabel WHERE tanaman_id = $id ORDER BY no");
if ($var_result) {
    while ($row = $var_result->fetch_assoc()) {
        $variabel[$row['no']] = $row;
    }
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_latin = $conn->real_escape_string($_POST['nama_latin']);
    $info = $conn->real_escape_string($_POST['informasi_kegiatan']);
    
    $conn->query("UPDATE tanaman SET nama_latin = '$nama_latin' WHERE id = $id");
    
    if ($detail) {
        $conn->query("UPDATE tanaman_detail SET nama_latin = '$nama_latin', informasi_kegiatan = '$info', tanggal_update = CURDATE() WHERE tanaman_id = $id");
    } else {
        $conn->query("INSERT INTO tanaman_detail (tanaman_id, nama_latin, informasi_kegiatan, tanggal_update) VALUES ($id, '$nama_latin', '$info', CURDATE())");
    }
    
    for ($i = 1; $i <= 5; $i++) {
        $var = $conn->real_escape_string($_POST["var_$i"]);
        $ket = $conn->real_escape_string($_POST["ket_$i"]);
        
        if (isset($variabel[$i])) {
            $conn->query("UPDATE tanaman_variabel SET variabel = '$var', keterangan = '$ket' WHERE tanaman_id = $id AND no = $i");
        } else {
            $conn->query("INSERT INTO tanaman_variabel (tanaman_id, no, variabel, keterangan) VALUES ($id, $i, '$var', '$ket')");
        }
    }
    
    echo "<script>alert('Data berhasil disimpan!'); window.location.href='input-detail.php?id=$id';</script>";
}

$def = $is_hewan ? ['Jenis', 'Populasi', 'Umur ternak', 'Umur panen', 'Lainnya'] : ['Varietas', 'Populasi', 'Luas lahan', 'Umur tanaman', 'Umur panen'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Input Detail - <?= htmlspecialchars($item['nama']) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial; background: #f5f5f5; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #2E5E3E; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; color: #333; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        input[type="text"]:focus, textarea:focus { outline: none; border-color: #2E5E3E; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; }
        th { background: #2E5E3E; color: white; text-align: center; }
        td input { width: 100%; border: none; padding: 5px; }
        .btn { padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-primary { background: #2E5E3E; color: white; }
        .btn-primary:hover { background: #1a3a24; }
        .btn-secondary { background: #6c757d; color: white; margin-left: 10px; }
        .btn-info { background: #17a2b8; color: white; margin-left: 10px; }
        .btn-group { margin-top: 30px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Input Detail: <?= htmlspecialchars($item['nama']) ?></h1>
        <div class="subtitle">ID: <?= $id ?> | Kategori: <?= $is_hewan ? 'Hewan' : 'Tanaman' ?></div>
        
        <form method="POST">
            <div class="form-group">
                <label>Nama Latin *</label>
                <input type="text" name="nama_latin" value="<?= htmlspecialchars($item['nama_latin'] ?? $detail['nama_latin'] ?? '') ?>" placeholder="Contoh: Bos taurus" required>
            </div>
            
            <label>Tabel Keterangan (5 Baris)</label>
            <table>
                <tr>
                    <th style="width: 50px;">NO</th>
                    <th style="width: 40%;">VARIABEL</th>
                    <th>KETERANGAN</th>
                </tr>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <tr>
                    <td style="text-align: center;"><?= $i ?></td>
                    <td><input type="text" name="var_<?= $i ?>" value="<?= htmlspecialchars($variabel[$i]['variabel'] ?? $def[$i-1]) ?>"></td>
                    <td><input type="text" name="ket_<?= $i ?>" value="<?= htmlspecialchars($variabel[$i]['keterangan'] ?? '') ?>" placeholder="Isi keterangan..."></td>
                </tr>
                <?php endfor; ?>
            </table>
            
            <div class="form-group">
                <label>Informasi Kegiatan</label>
                <textarea name="informasi_kegiatan" rows="5" placeholder="Masukkan informasi kegiatan..."><?= htmlspecialchars($detail['informasi_kegiatan'] ?? '') ?></textarea>
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">💾 Simpan</button>
                <a href="<?= $is_hewan ? 'hewan.php' : 'tanaman.php' ?>" class="btn btn-secondary">← Kembali</a>
                <a href="../detail_new.php?id=<?= $id ?>&type=<?= $is_hewan ? 'hewan' : 'tanaman' ?>" target="_blank" class="btn btn-info">👁️ Lihat</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php closeConnection($conn); ?>
