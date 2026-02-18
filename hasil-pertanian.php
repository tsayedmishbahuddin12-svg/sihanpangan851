<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';
$conn = getConnection();
$kategori_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 1;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Data Hasil - SIHANPANGAN851</title>
    <style>
        body { font-family: Arial; margin: 0; padding: 0; background: #f5f5f5; }
        .header { background: #2E5E3E; color: white; padding: 1.5rem; text-align: center; }
        .header h1 { margin: 0; font-size: 1.8rem; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .back-btn { display: inline-block; background: #2E5E3E; color: white; padding: 0.75rem 2rem; text-decoration: none; border-radius: 5px; margin-bottom: 2rem; }
        .table-wrapper { background: white; padding: 2rem; margin-bottom: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .table-title { color: #2E5E3E; font-size: 1.3rem; font-weight: bold; text-align: center; margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 3px solid #2E5E3E; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #2E5E3E; color: white; padding: 0.75rem 0.5rem; text-align: center; font-size: 0.9rem; border: 1px solid #1a3a28; }
        td { padding: 0.6rem 0.5rem; border: 1px solid #ddd; text-align: center; font-size: 0.85rem; }
        tr:nth-child(even) { background: #f9f9f9; }
        tr:hover { background: #e9ecef; }
        .left { text-align: left !important; }
        .right { text-align: right !important; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DATA HASIL <?= $kategori_id == 1 ? 'PERTANIAN' : 'PETERNAKAN' ?></h1>
        <p>SIHANPANGAN851 - YONIF TP 851/BBC</p>
    </div>
    
    <div class="container">
        <a href="kategori.php?id=<?= $kategori_id ?>" class="back-btn">← Kembali ke Kategori</a>
        
        <?php if ($kategori_id == 1): ?>
            
            <div class="table-wrapper">
                <h3 class="table-title">LAPORAN HASIL PANEN DAN BELANJA TANAMAN</h3>
                <table>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Panen</th>
                        <th>Komoditas</th>
                        <th>Pleton</th>
                        <th>Luas (Ha)</th>
                        <th>Hasil (Kg)</th>
                        <th>Harga (Rp)</th>
                        <th>Jumlah (Rp)</th>
                        <th>Keterangan</th>
                    </tr>
                    <?php
                    $result = $conn->query("SELECT * FROM pertanian_panen_belanja ORDER BY tanggal_panen DESC");
                    $no = 1;
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal_panen'])) ?></td>
                        <td class="left"><?= $row['komoditas'] ?></td>
                        <td><?= $row['pleton'] ?></td>
                        <td><?= number_format($row['luas'], 2) ?></td>
                        <td><?= number_format($row['hasil'], 2) ?></td>
                        <td class="right"><?= number_format($row['harga'], 0) ?></td>
                        <td class="right"><?= number_format($row['jumlah'], 0) ?></td>
                        <td class="left"><?= $row['keterangan'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            
            <div class="table-wrapper">
                <h3 class="table-title">LAPORAN BELANJA DAN HASIL PANEN</h3>
                <table>
                    <tr>
                        <th>No</th>
                        <th>Komoditas</th>
                        <th>Tanggal</th>
                        <th>Luas (Ha)</th>
                        <th>Hasil (Kg)</th>
                        <th>Harga (Rp)</th>
                        <th>Jumlah (Rp)</th>
                    </tr>
                    <?php
                    $result = $conn->query("SELECT * FROM pertanian_belanja_hasil ORDER BY tanggal DESC");
                    $no = 1;
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="left"><?= $row['komoditas'] ?></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= number_format($row['luas'], 2) ?></td>
                        <td><?= number_format($row['hasil'], 2) ?></td>
                        <td class="right"><?= number_format($row['harga'], 0) ?></td>
                        <td class="right"><?= number_format($row['jumlah'], 0) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>
            
        <?php else: ?>
            
            <div class="table-wrapper">
                <h3 class="table-title">TABEL JUMLAH POPULASI TERNAK</h3>
                <table>
                    <tr>
                        <th rowspan="2">No</th>
                        <th colspan="3">UNGGAS</th>
                        <th colspan="3">RUMINANSIA</th>
                        <th colspan="3">PERIKANAN</th>
                    </tr>
                    <tr>
                        <th>Ayam</th>
                        <th>Bebek</th>
                        <th>Total</th>
                        <th>Sapi</th>
                        <th>Kambing</th>
                        <th>Total</th>
                        <th>Lele</th>
                        <th>Gurame</th>
                        <th>Total</th>
                    </tr>
                    <?php
                    $result = $conn->query("SELECT * FROM peternakan_populasi");
                    $data = [];
                    while ($row = $result->fetch_assoc()) {
                        $data[$row['jenis_ternak']] = $row['jantan'] + $row['betina'] + $row['anakan'];
                    }
                    $ayam = isset($data['Ayam Petelur']) ? $data['Ayam Petelur'] : 0;
                    $bebek = isset($data['Bebek Petelur']) ? $data['Bebek Petelur'] : 0;
                    $sapi = isset($data['Sapi']) ? $data['Sapi'] : 0;
                    $kambing = isset($data['Kambing']) ? $data['Kambing'] : 0;
                    $lele = isset($data['Ikan Lele']) ? $data['Ikan Lele'] : 0;
                    $gurame = isset($data['Ikan Gurame']) ? $data['Ikan Gurame'] : 0;
                    ?>
                    <tr>
                        <td>1</td>
                        <td><?= number_format($ayam) ?></td>
                        <td><?= number_format($bebek) ?></td>
                        <td><strong><?= number_format($ayam + $bebek) ?></strong></td>
                        <td><?= number_format($sapi) ?></td>
                        <td><?= number_format($kambing) ?></td>
                        <td><strong><?= number_format($sapi + $kambing) ?></strong></td>
                        <td><?= number_format($lele) ?></td>
                        <td><?= number_format($gurame) ?></td>
                        <td><strong><?= number_format($lele + $gurame) ?></strong></td>
                    </tr>
                </table>
            </div>
            
            <div class="table-wrapper">
                <h3 class="table-title">RINCIAN HASIL PRODUKS