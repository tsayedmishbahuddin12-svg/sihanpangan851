<?php
require_once 'config/database.php';
$conn = getConnection();

$pleton_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get pleton info
$pleton = $conn->query("SELECT p.*, k.nama as kategori_nama, k.id as kategori_id FROM pleton p JOIN kategori k ON p.kategori_id = k.id WHERE p.id = $pleton_id")->fetch_assoc();

// Get items (tanaman/hewan) based on pleton
$items = $conn->query("SELECT * FROM tanaman WHERE pleton_id = $pleton_id AND status = 'published' ORDER BY nama");

// Determine item type based on kategori
if ($pleton['kategori_id'] == 1) {
    $item_type = 'tanaman';
} else {
    $item_type = 'hewan';
}

closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pleton['nama'] ?> - SIHANPANGAN851</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
    .item-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1.5rem;
        padding: 2rem;
    }
    .item-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-decoration: none;
        color: inherit;
        transition: transform 0.3s;
    }
    .item-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    .item-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }
    .item-card .name {
        padding: 1rem;
        text-align: center;
        font-weight: 500;
    }
    
    @media (max-width: 768px) {
        .item-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
            padding: 1rem;
        }
        
        .item-card img {
            height: 120px;
        }
        
        .item-card .name {
            padding: 0.75rem;
            font-size: 0.9rem;
        }
    }
    
    @media (max-width: 480px) {
        .item-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        
        .item-card img {
            height: 100px;
        }
        
        .item-card .name {
            padding: 0.5rem;
            font-size: 0.85rem;
        }
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

    <!-- Hero Section -->
    <section class="hero" style="padding: 2rem;">
        <div class="hero-content">
            <h1><?= htmlspecialchars($pleton['nama']) ?></h1>
            <p><?= htmlspecialchars($pleton['kategori_nama']) ?></p>
            <a href="kategori.php?id=<?= $pleton['kategori_id'] ?>" class="btn-primary" style="margin-top: 1rem;">← Kembali</a>
        </div>
    </section>

    <!-- Items Grid -->
    <div class="item-grid">
        <?php while ($row = $items->fetch_assoc()): ?>
            <a href="detail_styled.php?id=<?= $row['id'] ?>&type=<?= $item_type ?>" class="item-card">
                <?php if ($row['gambar']): ?>
                    <img src="uploads/<?= $row['gambar'] ?>" alt="<?= htmlspecialchars($row['nama']) ?>">
                <?php else: ?>
                    <img src="assets/img/placeholder.jpg" alt="No image">
                <?php endif; ?>
                <div class="name"><?= htmlspecialchars($row['nama']) ?></div>
            </a>
        <?php endwhile; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 SIHANPANGAN851. All rights reserved.</p>
    </footer>
</body>
</html>
