<?php
require_once 'config/database.php';
$conn = getConnection();

$kategori_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$kategori = $conn->query("SELECT * FROM kategori WHERE id = $kategori_id")->fetch_assoc();
$pletons = $conn->query("SELECT * FROM pleton WHERE kategori_id = $kategori_id ORDER BY urutan");

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $kategori['nama'] ?> - SIHANPANGAN851</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-logo">
                <img src="assets/img/logo1.png" alt="Logo 1" class="logo-icon">
                <img src="assets/img/logo2.png" alt="Logo 2" class="logo-icon">
                <span class="logo-text">SIHANPANGAN851</span>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="kategori.php?id=1" class="<?= $kategori_id == 1 ? 'active' : '' ?>">Pertanian</a></li>
                <li><a href="kategori.php?id=2" class="<?= $kategori_id == 2 ? 'active' : '' ?>">Peternakan</a></li>
            </ul>
        </div>
    </nav>

    <section class="hero" style="padding: 3rem 2rem;">
        <div class="hero-content">
            <h1>Kompi <?= strtolower($kategori['nama']) ?></h1>
            <p class="subtitle">yonif tp 851/bbc</p>
        </div>
    </section>

    <section class="kategori-section">
        <div class="container">
            <div class="kategori-grid">
                <?php 
                $pleton_backgrounds = [];
                if ($kategori_id == 1) {
                    $pleton_backgrounds = [
                        1 => 'assets/img/padi palawija.png',
                        2 => 'assets/img/buah sayur.png',
                        3 => 'assets/img/industri.png'
                    ];
                } elseif ($kategori_id == 2) {
                    $pleton_backgrounds = [
                        4 => 'assets/img/unggas.png',
                        5 => 'assets/img/ruminansia.png',
                        6 => 'assets/img/perikanan.png'
                    ];
                }
                
                while ($pleton = $pletons->fetch_assoc()): 
                    $bg_image = isset($pleton_backgrounds[$pleton['id']]) ? $pleton_backgrounds[$pleton['id']] : '';
                ?>
                    <a href="pleton.php?id=<?= $pleton['id'] ?>" class="kategori-card <?= $bg_image ? 'pleton-card-bg' : '' ?>" <?= $bg_image ? 'style="background-image: url(\'' . $bg_image . '\');"' : '' ?>>
                        <div class="pleton-card-overlay">
                            <h3><?= htmlspecialchars($pleton['nama']) ?></h3>
                            <?php if ($pleton['deskripsi']): ?>
                                <p><?= htmlspecialchars($pleton['deskripsi']) ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>
            
            <div style="text-align: center; margin-top: 3rem;">
                <div style="max-width: 600px; margin: 0 auto; padding: 2rem; background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); border: 2px solid #2E5E3E;">
                    <a href="hasil.php?kategori=<?= $kategori_id ?>" style="display: inline-block; background: #2E5E3E; color: white; padding: 1.2rem 3.5rem; border-radius: 50px; text-decoration: none; font-weight: 600; font-size: 1.2rem; box-shadow: 0 4px 15px rgba(46, 94, 62, 0.3); transition: all 0.3s ease;">
                        Data hasil <?= strtolower($kategori['nama']) ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2026 SIHANPANGAN851. All rights reserved.</p>
    </footer>
</body>
</html>
