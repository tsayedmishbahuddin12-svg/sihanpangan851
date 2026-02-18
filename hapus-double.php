<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Data Double - SIHANPANGAN851</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
        h1 { color: #2E5E3E; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .success { background: #d4edda; border: 1px solid #28a745; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .error { background: #f8d7da; border: 1px solid #dc3545; padding: 15px; border-radius: 5px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #2E5E3E; color: white; }
        .btn { padding: 12px 30px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn:hover { background: #c82333; }
        .btn-secondary { background: #6c757d; margin-left: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗑️ Hapus Data Double</h1>
        
        <?php
        require_once 'config/database.php';
        $conn = getConnection();
        
        // Handle delete action
        if (isset($_POST['hapus_double'])) {
            echo "<div class='success'><strong>Proses Penghapusan Dimulai...</strong></div>";
            
            // 1. Hapus duplikat di tabel tanaman
            $result1 = $conn->query("DELETE t1 FROM tanaman t1 INNER JOIN tanaman t2 WHERE t1.id > t2.id AND t1.nama = t2.nama AND t1.pleton_id = t2.pleton_id");
            echo "<p>✓ Hapus duplikat di tabel tanaman: " . $conn->affected_rows . " baris dihapus</p>";
            
            // 2. Hapus duplikat di tabel tanaman_variabel
            $result2 = $conn->query("DELETE tv1 FROM tanaman_variabel tv1 INNER JOIN tanaman_variabel tv2 WHERE tv1.id > tv2.id AND tv1.tanaman_id = tv2.tanaman_id AND tv1.no = tv2.no");
            echo "<p>✓ Hapus duplikat di tabel tanaman_variabel: " . $conn->affected_rows . " baris dihapus</p>";
            
            // 3. Hapus duplikat di tabel tanaman_detail
            $result3 = $conn->query("DELETE td1 FROM tanaman_detail td1 INNER JOIN tanaman_detail td2 WHERE td1.id > td2.id AND td1.tanaman_id = td2.tanaman_id");
            echo "<p>✓ Hapus duplikat di tabel tanaman_detail: " . $conn->affected_rows . " baris dihapus</p>";
            
            echo "<div class='success'><strong>✓ Selesai! Data double berhasil dihapus.</strong></div>";
            echo "<p><a href='hapus-double.php'>Refresh halaman</a> untuk cek ulang.</p>";
        }
        
        // Cek duplikat di tabel tanaman
        echo "<h2>1. Duplikat di Tabel Tanaman</h2>";
        $result = $conn->query("SELECT nama, pleton_id, COUNT(*) as jumlah FROM tanaman GROUP BY nama, pleton_id HAVING COUNT(*) > 1");
        
        if ($result && $result->num_rows > 0) {
            echo "<div class='warning'><strong>⚠️ Ditemukan " . $result->num_rows . " item yang duplikat!</strong></div>";
            echo "<table>";
            echo "<tr><th>Nama</th><th>Pleton ID</th><th>Jumlah Duplikat</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                echo "<td>" . $row['pleton_id'] . "</td>";
                echo "<td><strong>" . $row['jumlah'] . "x</strong></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='success'>✓ Tidak ada duplikat di tabel tanaman</div>";
        }
        
        // Cek duplikat di tabel tanaman_variabel
        echo "<h2>2. Duplikat di Tabel Tanaman Variabel</h2>";
        $result = $conn->query("SELECT tanaman_id, no, COUNT(*) as jumlah FROM tanaman_variabel GROUP BY tanaman_id, no HAVING COUNT(*) > 1");
        
        if ($result && $result->num_rows > 0) {
            echo "<div class='warning'><strong>⚠️ Ditemukan " . $result->num_rows . " variabel yang duplikat!</strong></div>";
            echo "<table>";
            echo "<tr><th>Tanaman ID</th><th>No</th><th>Jumlah Duplikat</th></tr>";
            $count = 0;
            while ($row = $result->fetch_assoc()) {
                if ($count < 20) { // Tampilkan max 20 baris
                    echo "<tr>";
                    echo "<td>" . $row['tanaman_id'] . "</td>";
                    echo "<td>" . $row['no'] . "</td>";
                    echo "<td><strong>" . $row['jumlah'] . "x</strong></td>";
                    echo "</tr>";
                }
                $count++;
            }
            if ($count > 20) {
                echo "<tr><td colspan='3' style='text-align: center;'><em>... dan " . ($count - 20) . " lainnya</em></td></tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='success'>✓ Tidak ada duplikat di tabel tanaman_variabel</div>";
        }
        
        // Cek duplikat di tabel tanaman_detail
        echo "<h2>3. Duplikat di Tabel Tanaman Detail</h2>";
        $result = $conn->query("SELECT tanaman_id, COUNT(*) as jumlah FROM tanaman_detail GROUP BY tanaman_id HAVING COUNT(*) > 1");
        
        if ($result && $result->num_rows > 0) {
            echo "<div class='warning'><strong>⚠️ Ditemukan " . $result->num_rows . " detail yang duplikat!</strong></div>";
            echo "<table>";
            echo "<tr><th>Tanaman ID</th><th>Jumlah Duplikat</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['tanaman_id'] . "</td>";
                echo "<td><strong>" . $row['jumlah'] . "x</strong></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='success'>✓ Tidak ada duplikat di tabel tanaman_detail</div>";
        }
        
        // Tombol hapus
        $has_duplicates = false;
        $check1 = $conn->query("SELECT COUNT(*) as total FROM (SELECT nama, pleton_id FROM tanaman GROUP BY nama, pleton_id HAVING COUNT(*) > 1) as t")->fetch_assoc();
        $check2 = $conn->query("SELECT COUNT(*) as total FROM (SELECT tanaman_id, no FROM tanaman_variabel GROUP BY tanaman_id, no HAVING COUNT(*) > 1) as t")->fetch_assoc();
        $check3 = $conn->query("SELECT COUNT(*) as total FROM (SELECT tanaman_id FROM tanaman_detail GROUP BY tanaman_id HAVING COUNT(*) > 1) as t")->fetch_assoc();
        
        if ($check1['total'] > 0 || $check2['total'] > 0 || $check3['total'] > 0) {
            echo "<div class='warning'>";
            echo "<h3>⚠️ Ada Data Double yang Perlu Dihapus!</h3>";
            echo "<p>Klik tombol di bawah untuk menghapus semua data double. Data yang akan disimpan adalah data dengan ID terkecil.</p>";
            echo "<form method='POST' onsubmit='return confirm(\"Yakin ingin menghapus semua data double? Proses ini tidak bisa dibatalkan!\")'>";
            echo "<button type='submit' name='hapus_double' class='btn'>🗑️ Hapus Semua Data Double</button>";
            echo "<a href='check-tables.php' class='btn btn-secondary'>📊 Cek Database</a>";
            echo "</form>";
            echo "</div>";
        } else {
            echo "<div class='success'>";
            echo "<h3>✓ Tidak Ada Data Double</h3>";
            echo "<p>Database Anda sudah bersih dari data duplikat!</p>";
            echo "<a href='check-tables.php' class='btn btn-secondary'>📊 Cek Database</a>";
            echo "</div>";
        }
        
        closeConnection($conn);
        ?>
    </div>
</body>
</html>
