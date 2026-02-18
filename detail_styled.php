<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';
$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'tanaman';

$query = "SELECT t.*, p.nama as pleton_nama, k.nama as kategori_nama, k.id as kategori_id,
                 td.nama_latin as detail_nama_latin, td.tanggal_update, td.gambar_detail, td.informasi_kegiatan
          FROM tanaman t 
          LEFT JOIN pleton p ON t.pleton_id = p.id 
          LEFT JOIN kategori k ON p.kategori_id = k.id
          LEFT JOIN tanaman_detail td ON t.id = td.tanaman_id
          WHERE t.id = $id";

$result = $conn->query($query);
$item = $result ? $result->fetch_assoc() : null;

$variabel_query = "SELECT * FROM tanaman_variabel WHERE tanaman_id = $id ORDER BY no";
$variabel_result = $conn->query($variabel_query);

if (!$item) {
    echo "<!DOCTYPE html><html><head><title>Item Not Found</title></head><body>";
    echo "<h1>Item tidak ditemukan</h1>";
    echo "<p>ID: $id tidak ditemukan di database.</p>";
    echo "<p><a href='index.php'>Kembali ke Beranda</a></p>";
    echo "</body></html>";
    exit;
}

$is_hewan = $item['kategori_id'] == 2;
closeConnection($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($item['nama']) ?> - SIHANPANGAN851</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #1a4d2e 0%, #2d6a4f 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 30px;
            align-items: start;
        }
        
        /* LEFT SECTION - FOTO */
        .left-section {
            position: relative;
        }
        
        .update-badge {
            background: white;
            border: 3px solid #2d6a4f;
            border-radius: 30px;
            padding: 12px 25px;
            text-align: center;
            font-weight: bold;
            color: #1a4d2e;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        
        .photo-frame {
            background: #d4ff00;
            border-radius: 40px;
            padding: 30px;
            position: relative;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }
        
        .photo-container {
            position: relative;
            border-radius: 30px;
            overflow: hidden;
            background: white;
        }
        
        .photo-container img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            display: block;
        }
        
        .logo-badge {
            position: absolute;
            bottom: 20px;
            left: 20px;
            width: 80px;
            height: 80px;
            background: #2d6a4f;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            border: 4px solid #d4ff00;
        }
        
        .logo-badge.peternakan::before {
            content: '🐄';
            font-size: 40px;
        }
        
        .logo-badge.pertanian::before {
            content: '🌾';
            font-size: 40px;
        }
        
        .photo-label {
            background: #2d6a4f;
            color: white;
            text-align: center;
            padding: 15px;
            font-weight: bold;
            font-size: 1.1rem;
            margin-top: -5px;
            border-radius: 0 0 30px 30px;
        }
        
        /* RIGHT SECTION - INFO */
        .right-section {
            background: rgba(255,255,255,0.05);
            padding: 30px;
            border-radius: 20px;
        }
        
        .title-section {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .item-title {
            color: #d4ff00;
            font-size: 4rem;
            font-weight: 900;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.3);
            margin-bottom: 10px;
            letter-spacing: 3px;
        }
        
        .item-latin {
            color: white;
            font-size: 1.8rem;
            font-style: italic;
            margin-bottom: 10px;
        }
        
        /* TABLE */
        .table-section {
            margin: 30px 0;
        }
        
        .table-title {
            color: white;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 3px solid #2d6a4f;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .info-table th {
            background: #1a4d2e;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            border: 2px solid #2d6a4f;
        }
        
        .info-table td {
            background: rgba(45, 106, 79, 0.3);
            color: white;
            padding: 15px;
            text-align: center;
            border: 2px solid #2d6a4f;
        }
        
        .info-table tr:hover td {
            background: rgba(45, 106, 79, 0.5);
        }
        
        /* INFO BOX */
        .info-section {
            margin-top: 30px;
        }
        
        .info-title {
            color: white;
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 15px;
        }
        
        .info-box {
            background: #d4ff00;
            color: #1a4d2e;
            padding: 25px 30px;
            border-radius: 40px;
            font-size: 1.1rem;
            line-height: 1.6;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        
        .back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            background: white;
            color: #1a4d2e;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background: #d4ff00;
            transform: translateY(-2px);
        }
        
        @media (max-width: 1200px) {
            .container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .item-title {
                font-size: 2.5rem;
            }
            
            .photo-container img {
                height: 300px;
            }
            
            .left-section {
                max-width: 500px;
                margin: 0 auto;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .container {
                gap: 15px;
            }
            
            .back-btn {
                top: 10px;
                left: 10px;
                padding: 10px 20px;
                font-size: 0.9rem;
            }
            
            .update-badge {
                padding: 10px 20px;
                font-size: 0.9rem;
                margin-bottom: 15px;
            }
            
            .photo-frame {
                padding: 20px;
                border-radius: 30px;
            }
            
            .photo-container img {
                height: 250px;
            }
            
            .logo-badge {
                width: 60px;
                height: 60px;
                bottom: 15px;
                left: 15px;
            }
            
            .logo-badge.peternakan::before,
            .logo-badge.pertanian::before {
                font-size: 30px;
            }
            
            .photo-label {
                padding: 12px;
                font-size: 0.95rem;
            }
            
            .right-section {
                padding: 20px;
            }
            
            .item-title {
                font-size: 2rem;
                letter-spacing: 1px;
            }
            
            .item-latin {
                font-size: 1.2rem;
            }
            
            .table-title,
            .info-title {
                font-size: 1.1rem;
            }
            
            .info-table th,
            .info-table td {
                padding: 10px 8px;
                font-size: 0.9rem;
            }
            
            .info-table th:first-child,
            .info-table td:first-child {
                width: 50px;
            }
            
            .info-box {
                padding: 20px;
                font-size: 1rem;
                border-radius: 30px;
            }
        }
        
        @media (max-width: 480px) {
            .item-title {
                font-size: 1.5rem;
            }
            
            .item-latin {
                font-size: 1rem;
            }
            
            .photo-container img {
                height: 200px;
            }
            
            .info-table {
                font-size: 0.85rem;
            }
            
            .info-table th,
            .info-table td {
                padding: 8px 5px;
            }
        }
    </style>
</head>
<body>
    <a href="pleton.php?id=<?= $item['pleton_id'] ?? 1 ?>" class="back-btn">← Kembali</a>
    
    <div class="container">
        <!-- LEFT SECTION -->
        <div class="left-section">
            <div class="update-badge">
                <?php if (!empty($item['tanggal_update'])): ?>
                    <?= date('d/m/Y', strtotime($item['tanggal_update'])) ?>
                <?php endif; ?>
            </div>
            
            <div class="photo-frame">
                <div class="photo-container">
                    <?php if ($item['gambar']): ?>
                        <img src="uploads/<?= $item['gambar'] ?>" alt="<?= htmlspecialchars($item['nama']) ?>">
                    <?php else: ?>
                        <img src="assets/img/placeholder.jpg" alt="No image">
                    <?php endif; ?>
                    <div class="logo-badge <?= $is_hewan ? 'peternakan' : 'pertanian' ?>"></div>
                </div>
            </div>
            <div class="photo-label">GAMBAR <?= strtoupper($is_hewan ? 'HEWAN TERNAK' : 'TANAMAN') ?></div>
        </div>
        
        <!-- RIGHT SECTION -->
        <div class="right-section">
            <div class="title-section">
                <h1 class="item-title"><?= strtoupper(htmlspecialchars($item['nama'])) ?></h1>
                <div class="item-latin"><?= htmlspecialchars($item['nama_latin'] ?? $item['detail_nama_latin'] ?? 'Nama latin belum diisi') ?></div>
            </div>
            
            <!-- TABLE -->
            <div class="table-section">
                <div class="table-title">TABEL KETERANGAN</div>
                <table class="info-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">NO</th>
                            <th>VARIABEL</th>
                            <th>KETERANGAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($variabel_result && $variabel_result->num_rows > 0):
                            while ($var = $variabel_result->fetch_assoc()): 
                        ?>
                            <tr>
                                <td><?= $var['no'] ?></td>
                                <td><?= htmlspecialchars($var['variabel']) ?></td>
                                <td><?= htmlspecialchars($var['keterangan'] ?: '(kosongkan)') ?></td>
                            </tr>
                        <?php 
                            endwhile;
                        else:
                            $default_vars = $is_hewan ? 
                                [['Jenis', ''], ['Populasi', ''], ['Umur ternak', ''], ['Umur panen', '']] :
                                [['Varietas', ''], ['Populasi', ''], ['Luas lahan', ''], ['Umur tanaman', ''], ['Umur panen', '']];
                            
                            foreach ($default_vars as $i => $var):
                        ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= $var[0] ?></td>
                                <td>(kosongkan)</td>
                            </tr>
                        <?php 
                            endforeach;
                        endif; 
                        ?>
                    </tbody>
                </table>
            </div>
            
            <!-- INFO BOX -->
            <div class="info-section">
                <div class="info-title">INFORMASI KEGIATAN</div>
                <div class="info-box">
                    <?= !empty($item['informasi_kegiatan']) ? 
                        htmlspecialchars($item['informasi_kegiatan']) : 
                        '(INFORMASI BEBAS YANG BISA DIINPUT TEKS SENDIRI OLEH PEMEGANG AKUNG)' ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
