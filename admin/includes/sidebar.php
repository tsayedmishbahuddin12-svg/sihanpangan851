<div class="sidebar">
    <div class="sidebar-header">
        <span class="logo-icon">🌾</span>
        <h2>Admin Panel</h2>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                <span class="icon">📊</span>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="tanaman.php" class="<?= basename($_SERVER['PHP_SELF']) == 'tanaman.php' ? 'active' : '' ?>">
                <span class="icon">🌱</span>
                <span>Kelola Tanaman</span>
            </a>
        </li>
        <li>
            <a href="tambah-tanaman.php" class="<?= basename($_SERVER['PHP_SELF']) == 'tambah-tanaman.php' ? 'active' : '' ?>">
                <span class="icon">➕</span>
                <span>Tambah Tanaman</span>
            </a>
        </li>
        <li>
            <a href="hewan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'hewan.php' ? 'active' : '' ?>">
                <span class="icon">🐄</span>
                <span>Kelola Hewan</span>
            </a>
        </li>
        <li>
            <a href="tambah-hewan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'tambah-hewan.php' ? 'active' : '' ?>">
                <span class="icon">➕</span>
                <span>Tambah Hewan</span>
            </a>
        </li>
        <li>
            <a href="statistik.php" class="<?= basename($_SERVER['PHP_SELF']) == 'statistik.php' ? 'active' : '' ?>">
                <span class="icon">📈</span>
                <span>Statistik</span>
            </a>
        </li>

        <li>
            <a href="data-hasil-menu.php" class="<?= strpos($_SERVER['PHP_SELF'], 'pertanian-') !== false || strpos($_SERVER['PHP_SELF'], 'peternakan-') !== false ? 'active' : '' ?>">
                <span class="icon">📋</span>
                <span>Data Hasil</span>
            </a>
        </li>
        <li>
            <a href="../index.php" target="_blank">
                <span class="icon">🌐</span>
                <span>Lihat Website</span>
            </a>
        </li>
    </ul>
</div>
