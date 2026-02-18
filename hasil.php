<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'config/database.php';
$conn = getConnection();
$kat = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 1;
?><!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>Data Hasil</title>
<style>
body{font-family:Arial;margin:0;background:#f5f5f5}
.hdr{background:#2E5E3E;color:#fff;padding:1.5rem;text-align:center}
.hdr h1{margin:0;font-size:1.8rem}
.wrap{max-width:1400px;margin:2rem auto;padding:0 1rem}
.btn{display:inline-block;background:#2E5E3E;color:#fff;padding:0.75rem 2rem;text-decoration:none;border-radius:5px;margin-bottom:2rem}
.box{background:#fff;padding:2rem;margin-bottom:2rem;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,0.1);overflow-x:auto}
.ttl{color:#2E5E3E;font-size:1.1rem;font-weight:bold;text-align:center;margin-bottom:1.5rem;padding-bottom:0.5rem;border-bottom:3px solid #2E5E3E;text-transform:uppercase}
table{width:100%;border-collapse:collapse;font-size:0.8rem}
th{background:#2E5E3E;color:#fff;padding:0.6rem 0.4rem;text-align:center;font-size:0.75rem;border:1px solid #1a3a28;font-weight:600}
td{padding:0.5rem 0.4rem;border:1px solid #000;text-align:center;font-size:0.75rem}
tr:nth-child(even){background:#f9f9f9}
.l{text-align:left!important}
.r{text-align:right!important}
.total-row{background:#e0e0e0!important;font-weight:bold}
@media (max-width: 768px){
body{font-size:14px}
.hdr h1{font-size:1.3rem}
.wrap{padding:0 0.5rem;margin:1rem auto}
.btn{padding:0.6rem 1.5rem;font-size:0.9rem}
.box{padding:1rem;margin-bottom:1.5rem}
.ttl{font-size:0.95rem;margin-bottom:1rem}
table{font-size:0.7rem;min-width:600px}
th{padding:0.5rem 0.3rem;font-size:0.7rem}
td{padding:0.4rem 0.3rem;font-size:0.7rem}
}
@media (max-width: 480px){
.hdr h1{font-size:1.1rem}
.hdr p{font-size:0.85rem}
.btn{padding:0.5rem 1rem;font-size:0.85rem}
.box{padding:0.75rem}
.ttl{font-size:0.85rem}
table{font-size:0.65rem;min-width:500px}
th,td{padding:0.3rem 0.2rem;font-size:0.65rem}
}
</style>
</head><body>
<div class="hdr"><h1>DATA HASIL <?=$kat==1?'PERTANIAN':'PETERNAKAN'?></h1><p>SIHANPANGAN851 - YONIF TP 851/BBC</p></div>
<div class="wrap">
<a href="kategori.php?id=<?=$kat?>" class="btn">← Kembali</a>
<?php if($kat==1):?>

<div class="box">
<h3 class="ttl">Laporan Hasil Penjualan Tanaman Kompi Pertanian Yonif TP 851/BBC</h3>
<table>
<tr>
<th>NO</th>
<th>TANGGAL PANEN</th>
<th>KOMODITAS</th>
<th>HASIL PANEN</th>
<th>SATUAN</th>
<th>HARGA/SATUAN</th>
<th>TOTAL</th>
<th>KETERANGAN</th>
</tr>
<?php 
$r=$conn->query("SELECT * FROM pertanian_hasil_penjualan ORDER BY tanggal_panen DESC");
$n=1;
$total_all = 0;
while($d=$r->fetch_assoc()):
$total_all += $d['total'];
?>
<tr>
<td><?=$n++?></td>
<td><?=date('d/m/Y',strtotime($d['tanggal_panen']))?></td>
<td class="l"><?=$d['komoditas']?></td>
<td><?=number_format($d['hasil_panen'],2)?></td>
<td><?=$d['satuan']?></td>
<td class="r">Rp <?=number_format($d['harga_satuan'],0,',','.')?></td>
<td class="r">Rp <?=number_format($d['total'],0,',','.')?></td>
<td class="l"><?=$d['keterangan']?></td>
</tr>
<?php endwhile;?>
<tr class="total-row">
<td colspan="6" class="r"><strong>TOTAL</strong></td>
<td class="r"><strong>Rp <?=number_format($total_all,0,',','.')?></strong></td>
<td></td>
</tr>
</table>
</div>

<div class="box">
<h3 class="ttl">Laporan Belanja Dari Hasil Panen</h3>
<table>
<tr>
<th>NO</th>
<th>BARANG</th>
<th>TANGGAL</th>
<th>UNIT</th>
<th>SATUAN</th>
<th>HARGA</th>
<th>JUMLAH</th>
</tr>
<?php 
$r=$conn->query("SELECT * FROM pertanian_belanja_panen ORDER BY tanggal DESC");
$n=1;
$total = 0;
while($d=$r->fetch_assoc()):
$total += $d['jumlah'];
?>
<tr>
<td><?=$n++?></td>
<td class="l"><?=$d['barang']?></td>
<td><?=date('d/m/Y',strtotime($d['tanggal']))?></td>
<td><?=number_format($d['unit'],2)?></td>
<td><?=$d['satuan']?></td>
<td class="r">Rp <?=number_format($d['harga'],0,',','.')?></td>
<td class="r">Rp <?=number_format($d['jumlah'],0,',','.')?></td>
</tr>
<?php endwhile;?>
<tr class="total-row">
<td colspan="5"></td>
<td class="r"><strong>TOTAL</strong></td>
<td class="r"><strong>Rp <?=number_format($total,0,',','.')?></strong></td>
</tr>
<tr class="total-row">
<td colspan="2"><strong>PENDAPATAN</strong></td>
<td colspan="3" class="r"><strong>Rp <?=number_format($total_all,0,',','.')?></strong></td>
<td><strong>PENGELUARAN</strong></td>
<td class="r"><strong>Rp <?=number_format($total,0,',','.')?></strong></td>
</tr>
<tr class="total-row">
<td colspan="5"></td>
<td><strong>SALDO</strong></td>
<td class="r"><strong>Rp <?=number_format($total_all - $total,0,',','.')?></strong></td>
</tr>
</table>
</div>

<?php else:?>

<div class="box">
<h3 class="ttl">Tabel Jumlah Populasi Ternak</h3>
<table>
<tr>
<th rowspan="3">NO</th>
<th colspan="6">UNGGAS</th>
<th colspan="6">RUMINANSIA</th>
<th colspan="6">PERIKANAN</th>
</tr>
<tr>
<th colspan="3">AYAM PETELUR</th>
<th colspan="3">BEBEK PETELUR</th>
<th colspan="3">SAPI</th>
<th colspan="3">KAMBING</th>
<th colspan="3">IKAN LELE</th>
<th colspan="3">IKAN GURAME</th>
</tr>
<tr>
<th>JANTAN</th><th>BETINA</th><th>ANAKAN</th>
<th>JANTAN</th><th>BETINA</th><th>ANAKAN</th>
<th>JANTAN</th><th>BETINA</th><th>ANAKAN</th>
<th>JANTAN</th><th>BETINA</th><th>ANAKAN</th>
<th>JANTAN</th><th>BETINA</th><th>ANAKAN</th>
<th>JANTAN</th><th>BETINA</th><th>ANAKAN</th>
</tr>
<?php 
$r=$conn->query("SELECT * FROM peternakan_populasi LIMIT 1");
if($d=$r->fetch_assoc()):
?>
<tr>
<td><?=$d['no']?></td>
<td><?=$d['ayam_petelur_jantan']?></td>
<td><?=$d['ayam_petelur_betina']?></td>
<td><?=$d['ayam_petelur_anakan']?></td>
<td><?=$d['bebek_petelur_jantan']?></td>
<td><?=$d['bebek_petelur_betina']?></td>
<td><?=$d['bebek_petelur_anakan']?></td>
<td><?=$d['sapi_jantan']?></td>
<td><?=$d['sapi_betina']?></td>
<td><?=$d['sapi_anakan']?></td>
<td><?=$d['kambing_jantan']?></td>
<td><?=$d['kambing_betina']?></td>
<td><?=$d['kambing_anakan']?></td>
<td><?=$d['ikan_lele_jantan']?></td>
<td><?=$d['ikan_lele_betina']?></td>
<td><?=$d['ikan_lele_anakan']?></td>
<td><?=$d['ikan_gurame_jantan']?></td>
<td><?=$d['ikan_gurame_betina']?></td>
<td><?=$d['ikan_gurame_anakan']?></td>
</tr>
<tr class="total-row">
<td>TOTAL POPULASI</td>
<td colspan="3"><?=($d['ayam_petelur_jantan']+$d['ayam_petelur_betina']+$d['ayam_petelur_anakan'])?> EKOR</td>
<td colspan="3"><?=($d['bebek_petelur_jantan']+$d['bebek_petelur_betina']+$d['bebek_petelur_anakan'])?> EKOR</td>
<td colspan="3"><?=($d['sapi_jantan']+$d['sapi_betina']+$d['sapi_anakan'])?> EKOR</td>
<td colspan="3"><?=($d['kambing_jantan']+$d['kambing_betina']+$d['kambing_anakan'])?> EKOR</td>
<td colspan="3"><?=number_format($d['ikan_lele_jantan']+$d['ikan_lele_betina']+$d['ikan_lele_anakan'])?> EKOR</td>
<td colspan="3"><?=($d['ikan_gurame_jantan']+$d['ikan_gurame_betina']+$d['ikan_gurame_anakan'])?> EKOR</td>
</tr>
<?php endif;?>
</table>
</div>

<div class="box">
<h3 class="ttl">Rincian Hasil Produksi Telur</h3>
<table>
<tr>
<th>UNGGAS</th>
<th>NO.</th>
<th>KETERANGAN</th>
<th>TANGGAL</th>
<th>TELUR (BUTIR)</th>
</tr>
<?php 
$r=$conn->query("SELECT * FROM peternakan_rincian_telur ORDER BY unggas, no");
while($d=$r->fetch_assoc()):
?>
<tr>
<td><?=$d['unggas']?></td>
<td><?=$d['no']?></td>
<td class="l"><?=$d['keterangan']?></td>
<td><?=$d['tanggal']?date('d/m/Y',strtotime($d['tanggal'])):'-'?></td>
<td><?=number_format($d['telur_butir'])?></td>
</tr>
<?php endwhile;?>
</table>
</div>

<div class="box">
<h3 class="ttl">Tabel Pemasukan Ton Unggas</h3>
<table>
<tr>
<th>NO.</th>
<th>KETERANGAN</th>
<th>TANGGAL</th>
<th>TELUR<br>(BUTIR)</th>
<th>HARGA<br>/BUTIR</th>
<th>TOTAL</th>
</tr>
<?php 
$r=$conn->query("SELECT * FROM peternakan_pemasukan_unggas ORDER BY no");
$total=0;
while($d=$r->fetch_assoc()):
$total+=$d['total'];
?>
<tr>
<td><?=$d['no']?></td>
<td class="l"><?=$d['keterangan']?></td>
<td><?=date('d/m/Y',strtotime($d['tanggal']))?></td>
<td><?=number_format($d['telur_butir'])?></td>
<td class="r">Rp<?=number_format($d['harga_butir'],0,',','.')?></td>
<td class="r">Rp <?=number_format($d['total'],0,',','.')?></td>
</tr>
<?php endwhile;?>
<tr class="total-row">
<td colspan="5" class="r"><strong>TOTAL HASIL PENJUALAN</strong></td>
<td class="r"><strong>Rp <?=number_format($total,0,',','.')?></strong></td>
</tr>
</table>
</div>

<div class="box">
<h3 class="ttl">Tabel Pengeluaran Ton Unggas</h3>
<table>
<tr>
<th>NO.</th>
<th>KETERANGAN</th>
<th>TANGGAL</th>
<th>KUANTITI</th>
<th>HARGA</th>
<th>TOTAL</th>
</tr>
<?php 
$r=$conn->query("SELECT * FROM peternakan_pengeluaran_unggas ORDER BY no");
$total_keluar=0;
while($d=$r->fetch_assoc()):
$total_keluar+=$d['total'];
?>
<tr>
<td><?=$d['no']?></td>
<td class="l"><?=$d['keterangan']?></td>
<td><?=date('d/m/Y',strtotime($d['tanggal']))?></td>
<td><?=$d['kuantiti']?></td>
<td class="r">Rp <?=number_format($d['harga'],0,',','.')?></td>
<td class="r">Rp <?=number_format($d['total'],0,',','.')?></td>
</tr>
<?php endwhile;?>
<tr class="total-row">
<td colspan="5" class="r"><strong>TOTAL HASIL PENGELUARAN</strong></td>
<td class="r"><strong>Rp <?=number_format($total_keluar,0,',','.')?></strong></td>
</tr>
<tr class="total-row">
<td colspan="5" class="r"><strong>SISA SALDO</strong></td>
<td class="r"><strong>Rp <?=number_format($total-$total_keluar,0,',','.')?></strong></td>
</tr>
</table>
</div>

<div class="box">
<h3 class="ttl">Tabel Pemasukan Ton Perikanan</h3>
<table>
<tr>
<th>NO.</th>
<th>KETERANGAN</th>
<th>TANGGAL</th>
<th>KUANTITI (KG)</th>
<th>HARGA /KG</th>
<th>TOTAL</th>
</tr>
<?php 
$r=$conn->query("SELECT * FROM peternakan_pemasukan_perikanan ORDER BY no");
$total_masuk=0;
while($d=$r->fetch_assoc()):
$total_masuk+=$d['total'];
?>
<tr>
<td><?=$d['no']?></td>
<td class="l"><?=$d['keterangan']?></td>
<td><?=date('d/m/Y',strtotime($d['tanggal']))?></td>
<td><?=number_format($d['kuantiti_kg'],0)?></td>
<td class="r">Rp <?=number_format($d['harga_kg'],0,',','.')?></td>
<td class="r">Rp <?=number_format($d['total'],0,',','.')?></td>
</tr>
<?php endwhile;?>
<tr class="total-row">
<td colspan="5" class="r"><strong>TOTAL HASIL PANEN</strong></td>
<td class="r"><strong>Rp <?=number_format($total_masuk,0,',','.')?></strong></td>
</tr>
</table>
</div>

<div class="box">
<h3 class="ttl">Tabel Pengeluaran Ton Perikanan</h3>
<table>
<tr>
<th>NO.</th>
<th>KETERANGAN</th>
<th>TANGGAL</th>
<th>KUANTITI</th>
<th>HARGA</th>
<th>TOTAL</th>
</tr>
<?php 
$r=$conn->query("SELECT * FROM peternakan_pengeluaran_perikanan ORDER BY no");
$total_keluar_ikan=0;
while($d=$r->fetch_assoc()):
$total_keluar_ikan+=$d['total'];
?>
<tr>
<td><?=$d['no']?></td>
<td class="l"><?=$d['keterangan']?></td>
<td><?=date('d/m/Y',strtotime($d['tanggal']))?></td>
<td><?=$d['kuantiti']?></td>
<td class="r">Rp <?=number_format($d['harga'],0,',','.')?></td>
<td class="r">Rp <?=number_format($d['total'],0,',','.')?></td>
</tr>
<?php endwhile;?>
<tr class="total-row">
<td colspan="5" class="r"><strong>TOTAL HASIL PENGELUARAN</strong></td>
<td class="r"><strong>Rp <?=number_format($total_keluar_ikan,0,',','.')?></strong></td>
</tr>
<tr class="total-row">
<td colspan="5" class="r"><strong>SISA SALDO</strong></td>
<td class="r"><strong>Rp <?=number_format($total_masuk-$total_keluar_ikan,0,',','.')?></strong></td>
</tr>
</table>
</div>

<?php endif;closeConnection($conn);?>
</div></body></html>
