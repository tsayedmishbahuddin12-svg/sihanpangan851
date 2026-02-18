<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Check Database Tables</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        h1 { color: #2E5E3E; }
        h2 { color: #555; margin-top: 30px; border-bottom: 2px solid #2E5E3E; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #2E5E3E; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Check Database Tables - SIHANPANGAN851</h1>
        
        <?php
        require_once 'config/database.php';
        $conn = getConnection();
        
        echo "<div class='info'>";
        echo "<strong>Database:</strong> " . DB_NAME . "<br>";
        echo "<strong>Host:</strong> " . DB_HOST . "<br>";
        echo "<strong>Status:</strong> <span class='success'>Connected ✓</span>";
        echo "</div>";
        
        // Check tanaman table
        echo "<h2>1. Tabel TANAMAN</h2>";
        $result = $conn->query("SHOW COLUMNS FROM tanaman");
        if ($result) {
            echo "<table>";
            echo "<tr><th>Column Name</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            $has_nama_latin = false;
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
                echo "</tr>";
                if ($row['Field'] == 'nama_latin') $has_nama_latin = true;
            }
            echo "</table>";
            
            if ($has_nama_latin) {
                echo "<p class='success'>✓ Kolom nama_latin ADA</p>";
            } else {
                echo "<p class='error'>✗ Kolom nama_latin TIDAK ADA - Perlu import database_detail_fix.sql</p>";
            }
            
            // Count data
            $count = $conn->query("SELECT COUNT(*) as total FROM tanaman")->fetch_assoc();
            echo "<p><strong>Total data:</strong> " . $count['total'] . " rows</p>";
            
            // Show sample data
            echo "<h3>Sample Data (5 rows):</h3>";
            $sample = $conn->query("SELECT id, nama, pleton_id, nama_latin FROM tanaman LIMIT 5");
            echo "<table>";
            echo "<tr><th>ID</th><th>Nama</th><th>Pleton ID</th><th>Nama Latin</th></tr>";
            while ($row = $sample->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['nama'] . "</td>";
                echo "<td>" . $row['pleton_id'] . "</td>";
                echo "<td>" . ($row['nama_latin'] ?? '<span class="warning">NULL</span>') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p class='error'>✗ Tabel tanaman TIDAK ADA</p>";
        }
        
        // Check tanaman_detail table
        echo "<h2>2. Tabel TANAMAN_DETAIL</h2>";
        $result = $conn->query("SHOW TABLES LIKE 'tanaman_detail'");
        if ($result->num_rows > 0) {
            echo "<p class='success'>✓ Tabel tanaman_detail ADA</p>";
            
            $result = $conn->query("SHOW COLUMNS FROM tanaman_detail");
            echo "<table>";
            echo "<tr><th>Column Name</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            $count = $conn->query("SELECT COUNT(*) as total FROM tanaman_detail")->fetch_assoc();
            echo "<p><strong>Total data:</strong> " . $count['total'] . " rows</p>";
            
            if ($count['total'] > 0) {
                echo "<h3>Sample Data:</h3>";
                $sample = $conn->query("SELECT * FROM tanaman_detail LIMIT 5");
                echo "<table>";
                echo "<tr><th>ID</th><th>Tanaman ID</th><th>Nama Latin</th><th>Tanggal Update</th><th>Informasi Kegiatan</th></tr>";
                while ($row = $sample->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['tanaman_id'] . "</td>";
                    echo "<td>" . $row['nama_latin'] . "</td>";
                    echo "<td>" . $row['tanggal_update'] . "</td>";
                    echo "<td>" . substr($row['informasi_kegiatan'], 0, 50) . "...</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='warning'>⚠ Tabel kosong - Perlu import fix_data_peternakan.sql</p>";
            }
        } else {
            echo "<p class='error'>✗ Tabel tanaman_detail TIDAK ADA - Perlu import database_detail_fix.sql</p>";
        }
        
        // Check tanaman_variabel table
        echo "<h2>3. Tabel TANAMAN_VARIABEL</h2>";
        $result = $conn->query("SHOW TABLES LIKE 'tanaman_variabel'");
        if ($result->num_rows > 0) {
            echo "<p class='success'>✓ Tabel tanaman_variabel ADA</p>";
            
            $result = $conn->query("SHOW COLUMNS FROM tanaman_variabel");
            echo "<table>";
            echo "<tr><th>Column Name</th><th>Type</th><th>Null</th><th>Key</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            $count = $conn->query("SELECT COUNT(*) as total FROM tanaman_variabel")->fetch_assoc();
            echo "<p><strong>Total data:</strong> " . $count['total'] . " rows</p>";
            
            if ($count['total'] > 0) {
                echo "<h3>Sample Data:</h3>";
                $sample = $conn->query("SELECT * FROM tanaman_variabel LIMIT 10");
                echo "<table>";
                echo "<tr><th>ID</th><th>Tanaman ID</th><th>No</th><th>Variabel</th><th>Keterangan</th></tr>";
                while ($row = $sample->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['tanaman_id'] . "</td>";
                    echo "<td>" . $row['no'] . "</td>";
                    echo "<td>" . $row['variabel'] . "</td>";
                    echo "<td>" . $row['keterangan'] . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='warning'>⚠ Tabel kosong - Perlu import fix_data_peternakan.sql</p>";
            }
        } else {
            echo "<p class='error'>✗ Tabel tanaman_variabel TIDAK ADA - Perlu import database_detail_fix.sql</p>";
        }
        
        // Check specific ID (85)
        echo "<h2>4. Check ID 85 (dari URL yang error)</h2>";
        $result = $conn->query("SELECT * FROM tanaman WHERE id = 85");
        if ($result && $result->num_rows > 0) {
            $item = $result->fetch_assoc();
            echo "<p class='success'>✓ Item dengan ID 85 DITEMUKAN</p>";
            echo "<table>";
            echo "<tr><th>Field</th><th>Value</th></tr>";
            foreach ($item as $key => $value) {
                echo "<tr><td><strong>$key</strong></td><td>" . ($value ?? '<span class="warning">NULL</span>') . "</td></tr>";
            }
            echo "</table>";
            
            // Check if detail exists
            $detail = $conn->query("SELECT * FROM tanaman_detail WHERE tanaman_id = 85");
            if ($detail && $detail->num_rows > 0) {
                echo "<p class='success'>✓ Detail untuk ID 85 ADA</p>";
            } else {
                echo "<p class='warning'>⚠ Detail untuk ID 85 TIDAK ADA (akan dibuat otomatis saat save)</p>";
            }
            
            // Check if variabel exists
            $variabel = $conn->query("SELECT * FROM tanaman_variabel WHERE tanaman_id = 85");
            if ($variabel && $variabel->num_rows > 0) {
                echo "<p class='success'>✓ Variabel untuk ID 85 ADA (" . $variabel->num_rows . " rows)</p>";
            } else {
                echo "<p class='warning'>⚠ Variabel untuk ID 85 TIDAK ADA (akan dibuat otomatis saat save)</p>";
            }
        } else {
            echo "<p class='error'>✗ Item dengan ID 85 TIDAK DITEMUKAN</p>";
        }
        
        // Summary
        echo "<h2>📋 KESIMPULAN</h2>";
        echo "<div class='info'>";
        
        $issues = [];
        
        // Check all requirements
        $has_tanaman = $conn->query("SHOW TABLES LIKE 'tanaman'")->num_rows > 0;
        $has_detail = $conn->query("SHOW TABLES LIKE 'tanaman_detail'")->num_rows > 0;
        $has_variabel = $conn->query("SHOW TABLES LIKE 'tanaman_variabel'")->num_rows > 0;
        
        if (!$has_tanaman) $issues[] = "Tabel tanaman tidak ada";
        if (!$has_detail) $issues[] = "Tabel tanaman_detail tidak ada - Import database_detail_fix.sql";
        if (!$has_variabel) $issues[] = "Tabel tanaman_variabel tidak ada - Import database_detail_fix.sql";
        
        if ($has_tanaman) {
            $result = $conn->query("SHOW COLUMNS FROM tanaman LIKE 'nama_latin'");
            if ($result->num_rows == 0) {
                $issues[] = "Kolom nama_latin tidak ada di tabel tanaman - Import database_detail_fix.sql";
            }
        }
        
        if (count($issues) == 0) {
            echo "<p class='success'><strong>✓ SEMUA TABEL SUDAH SIAP!</strong></p>";
            echo "<p>Anda bisa menggunakan halaman edit-detail.php sekarang.</p>";
            echo "<p><a href='admin/edit-detail.php?id=85' style='background: #2E5E3E; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;'>Buka Edit Detail (ID 85)</a></p>";
        } else {
            echo "<p class='error'><strong>✗ ADA MASALAH YANG PERLU DIPERBAIKI:</strong></p>";
            echo "<ul>";
            foreach ($issues as $issue) {
                echo "<li class='error'>$issue</li>";
            }
            echo "</ul>";
            echo "<p><strong>LANGKAH PERBAIKAN:</strong></p>";
            echo "<ol>";
            echo "<li>Buka phpMyAdmin</li>";
            echo "<li>Pilih database: sihanpangan851</li>";
            echo "<li>Import file: <strong>database_detail_fix.sql</strong></li>";
            echo "<li>Import file: <strong>fix_data_peternakan.sql</strong></li>";
            echo "<li>Refresh halaman ini untuk cek ulang</li>";
            echo "</ol>";
        }
        
        echo "</div>";
        
        closeConnection($conn);
        ?>
        
        <div style="margin-top: 30px; padding: 20px; background: #f0f0f0; border-radius: 5px;">
            <h3>🔗 Link Berguna:</h3>
            <ul>
                <li><a href="check-tables.php">Refresh halaman ini</a></li>
                <li><a href="test-database.php">Test Database (versi lama)</a></li>
                <li><a href="admin/debug-edit-detail.php?id=85">Debug Edit Detail (ID 85)</a></li>
                <li><a href="admin/login.php">Login Admin</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
