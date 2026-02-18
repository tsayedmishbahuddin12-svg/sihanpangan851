<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIHANPANGAN851 - Sistem Informasi Ketahanan Pangan</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
                <li><a href="index.php" class="active">Beranda</a></li>
                <li><a href="kategori.php?id=1">Pertanian</a></li>
                <li><a href="kategori.php?id=2">Peternakan</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-logos">
                <img src="assets/img/logo1.png" alt="Logo 1" class="hero-logo">
                <div class="hero-text">
                    <h1>Sihanpangan 851</h1>
                    <p class="subtitle">Sistem Informasi ketahanan pangan</p>
                    <p>YONIF TP 851/BBC<br>BRIGIF TP 89/GG</p>
                </div>
                <img src="assets/img/logo2.png" alt="Logo 2" class="hero-logo">
            </div>
        </div>
    </section>

    <!-- Kategori Section -->
    <section class="kategori-section" id="kategori">
        <div class="container">
            <h2 class="section-title">Pilih Kategori</h2>
            <div class="kategori-grid">
                <?php
                require_once 'config/database.php';
                $conn = getConnection();
                $result = $conn->query("SELECT * FROM kategori ORDER BY urutan");
                
                while ($row = $result->fetch_assoc()) {
                    echo '<a href="kategori.php?id=' . $row['id'] . '" class="kategori-card">';
                    echo '<div class="kategori-icon">' . $row['icon'] . '</div>';
                    echo '<h3>' . $row['nama'] . '</h3>';
                    echo '</a>';
                }
                
                closeConnection($conn);
                ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 SIHANPANGAN851. All rights reserved.</p>
    </footer>
</body>
</html>
