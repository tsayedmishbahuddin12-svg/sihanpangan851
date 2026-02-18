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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; }
        .navbar { background: #2E5E3E; color: white; padding: 1rem 2rem; }
        .navbar h1 { font-size: 1.5rem; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        .btn-back { display: inline-block; background: #2E5E3E; color: white; padding: 0.75rem 2rem; text-decoration: none; border-radius: 5px; margin-bottom: 2rem; }
        .table-box { background: white; padding: 2rem; margin-bottom: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .table-title { color: #2E5E3E; font-size: 1.2rem; font-weight: bold; text-align: center; margin-bottom: 1.5rem; padding-bottom: 0.5rem; border-bottom: 2px solid #2E5E3E; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #2E5E3E; color: white; padding: 0.75rem; text-align: center; font-size: 0.9rem; }
        td { padding: 0.6rem; border: 1px solid #ddd; text-align: center; font-size: 0.85rem; }
        tr:nth-child(even) { background: #f9f9f9; }
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>SIHANPANGAN851 - Data Hasil <?= $kategori_id == 1 ? 'Pertanian' : 'Peternakan' ?></h1>
    </div>
    
    <div class="container">
        <a href="kategori.php?id=<?= $kategori_id ?>" class="btn-back">← Kembali</a>
        
        <?php if ($kategori_id == 1): ?>
            <?php
            $panen = $conn->query("SELECT * FROM pertanian_panen_belanja ORDER BY tanggal_panen DESC");
            ?>
            <div class="table-box">
                <h3 class="table-title">LAPORAN HASIL PANEN DAN BELANJA TANAMAN</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Komoditas</th>
                            <th>Pleton</th>
                            <th>Luas (Ha)</th>
                            <th>Hasil (Kg)</th>
                            <th>Harga (Rp)</th>
                            <th>Jumlah (Rp)</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = $panen->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_panen'])) ?></td>
                            <td class="text-left"><?= $row['komoditas'] ?></td>
                            <td><?= $row['pleton'] ?></td>
                            <td><?= number_format($row['luas'], 2) ?></td>
                            <td><?= number_format($row['hasil'], 2) ?></td>
                            <td class="text-right"><?= number_format($row['harga'], 0) ?></td>
                            <td class="text-right"><?= number_format($row['jumlah'], 0) ?></td>
                            <td class="text-left"><?= $row['keterangan'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <?php
            $belanja = $conn->query("SELECT * FROM pertanian_belanja_hasil ORDER BY tanggal DESC");
            ?>
            <div class="table-box">
                <h3 class="table-title">LAPORAN BELANJA DAN HASIL PANEN</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Komoditas</th>
                            <th>Tanggal</th>
                            <th>Luas (Ha)</th>
                            <th>Hasil (Kg)</th>
                            <th>Harga (Rp)</th>
                            <th>Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = $belanja->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="text-left"><?= $row['komoditas'] ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= number_format($row['luas'], 2) ?></td>
                            <td><?= number_format($row['hasil'], 2) ?></td>
                            <td class="text-right"><?= number_format($row['harga'], 0) ?></td>
                            <td class="text-right"><?= number_format($row['jumlah'], 0) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
        <?php else: ?>
            <?php
            $populasi = $conn->query("SELECT * FROM peternakan_populasi ORDER BY kategori, jenis_ternak");
            $data_pop = [];
            while ($row = $populasi->fetch_assoc()) {
                $data_pop[$row['jenis_ternak']] = $row['jantan'] + $row['betina'] + $row['anakan'];
            }
            ?>
            <div class="table-box">
                <h3 class="table-title">TABEL JUMLAH POPULASI TERNAK</h3>
                <table>
                    <thead>
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
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><?= isset($data_pop['Ayam Petelur']) ? number_format($data_pop['Ayam Petelur']) : 0 ?></td>
                            <td><?= isset($data_pop['Bebek Petelur']) ? number_format($data_pop['Bebek Petelur']) : 0 ?></td>
                            <td><strong><?= number_format((isset($data_pop['Ayam Petelur']) ? $data_pop['Ayam Petelur'] : 0) + (isset($data_pop['Bebek Petelur']) ? $data_pop['Bebek Petelur'] : 0)) ?></strong></td>
                            <td><?= isset($data_pop['Sapi']) ? number_format($data_pop['Sapi']) : 0 ?></td>
                            <td><?= isset($data_pop['Kambing']) ? number_format($data_pop['Kambing']) : 0 ?></td>
                            <td><strong><?= number_format((isset($data_pop['Sapi']) ? $data_pop['Sapi'] : 0) + (isset($data_pop['Kambing']) ? $data_pop['Kambing'] : 0)) ?></strong></td>
                            <td><?= isset($data_pop['Ikan Lele']) ? number_format($data_pop['Ikan Lele']) : 0 ?></td>
                            <td><?= isset($data_pop['Ikan Gurame']) ? number_format($data_pop['Ikan Gurame']) : 0 ?></td>
                            <td><strong><?= number_format((isset($data_pop['Ikan Lele']) ? $data_pop['Ikan Lele'] : 0) + (isset($data_pop['Ikan Gurame']) ? $data_pop['Ikan Gurame'] : 0)) ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <?php
            $telur = $conn->query("SELECT * FROM peternakan_rincian_telur ORDER BY jenis_unggas, no_urut");
            ?>
            <div class="table-box">
                <h3 class="table-title">RINCIAN HASIL PRODUKSI TELUR</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Unggas</th>
                            <th>No</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>
                            <th>Telur (Butir)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $telur->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['jenis_unggas'] ?></td>
                            <td><?= $row['no_urut'] ?></td>
                            <td class="text-left"><?= $row['keterangan'] ?></td>
                            <td><?= $row['tanggal'] ? date('d/m/Y', strtotime($row['tanggal'])) : '-' ?></td>
                            <td><?= number_format($row['telur_butir']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <?php
            $pemasukan_unggas = $conn->query("SELECT * FROM peternakan_pemasukan_unggas ORDER BY tanggal DESC");
            ?>
            <div class="table-box">
                <h3 class="table-title">TABEL PEMASUKAN TON UNGGAS</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>
                            <th>Telur (Butir)</th>
                            <th>Harga (Rp)</th>
                            <th>Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = $pemasukan_unggas->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="text-left"><?= $row['keterangan'] ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= number_format($row['telur_butir']) ?></td>
                            <td class="text-right"><?= number_format($row['harga']) ?></td>
                            <td class="text-right"><?= number_format($row['total']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <?php
            $pengeluaran_unggas = $conn->query("SELECT * FROM peternakan_pengeluaran_unggas ORDER BY tanggal DESC");
            ?>
            <div class="table-box">
                <h3 class="table-title">TABEL PENGELUARAN TON UNGGAS</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>
                            <th>Kuantiti</th>
                            <th>Harga (Rp)</th>
                            <th>Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = $pengeluaran_unggas->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="text-left"><?= $row['keterangan'] ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= $row['kuantiti'] ?></td>
                            <td class="text-right"><?= number_format($row['harga']) ?></td>
                            <td class="text-right"><?= number_format($row['total']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <?php
            $pemasukan_ikan = $conn->query("SELECT * FROM peternakan_pemasukan_perikanan ORDER BY tanggal DESC");
            ?>
            <div class="table-box">
                <h3 class="table-title">TABEL PEMASUKAN TON PERIKANAN</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>
                            <th>Kuantiti (KG)</th>
                            <th>Harga (Rp)</th>
                            <th>Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = $pemasukan_ikan->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="text-left"><?= $row['keterangan'] ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= number_format($row['kuantiti_kg'], 2) ?></td>
                            <td class="text-right"><?= number_format($row['harga']) ?></td>
                            <td class="text-right"><?= number_format($row['total']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <?php
            $pengeluaran_ikan = $conn->query("SELECT * FROM peternakan_pengeluaran_perikanan ORDER BY tanggal DESC");
            ?>
            <div class="table-box">
                <h3 class="table-title">TABEL PENGELUARAN TON PERIKANAN</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Keterangan</th>
                            <th>Tanggal</th>
                            <th>Kuantiti</th>
                            <th>Harga (Rp)</th>
                            <th>Total (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = $pengeluaran_ikan->fetch_assoc()): 
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td class="text-left"><?= $row['keterangan'] ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= $row['kuantiti'] ?></td>
                            <td class="text-right"><?= number_format($row['harga']) ?></td>
                            <td class="text-right"><?= number_format($row['total']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
        <?php endif; ?>
        
        <?php closeConnection($conn); ?>
    </div>
</body>
</html>
