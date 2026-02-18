<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';
$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get hewan data
$hewan = $conn->query("SELECT * FROM hewan WHERE id = $id")->fetch_assoc();
if (!$hewan) {
    header('Location: detail-hewan.php');
    exit;
}

// Get existing detail
$detail = $conn->query("SELECT * FROM hewan_detail WHERE hewan_id = $id")->fetch_assoc();

// Handle form submission
if ($_POST) {
    $nama_latin = $_POST['nama_latin'];
    $tanggal_update = $_POST['tanggal_update'];
    $informasi_kegiatan = $_POST['informasi_kegiatan'];
    
    if ($detail) {
        // Update existing detail
        $stmt = $conn->prepare("UPDATE hewan_detail SET nama_latin = ?, tanggal_update = ?, informasi_kegiatan = ? WHERE hewan_id = ?");
        $stmt->bind_par