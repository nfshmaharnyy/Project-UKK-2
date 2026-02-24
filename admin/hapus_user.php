<?php
session_start();
include '../config/koneksi.php';

// 1. Proteksi Halaman: Pastikan hanya admin yang bisa eksekusi
if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    header("location:../auth/login.php");
    exit();
}

// 2. Validasi Parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location:user.php");
    exit();
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// 3. Keamanan Tingkat Lanjut: Admin dilarang hapus diri sendiri
if ($id == $_SESSION['userid']) {
    header("location:user.php?pesan=gagal_hapus_sendiri");
    exit();
}

// 4. Proses Hapus dengan Prepared Statement (Lebih Aman)
$stmt = $koneksi->prepare("DELETE FROM user WHERE id_user = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Berhasil dihapus
    header("location:user.php?pesan=hapus_berhasil");
} else {
    // Gagal karena relasi database atau error lainnya
    header("location:user.php?pesan=hapus_gagal&error=" . urlencode($stmt->error));
}

$stmt->close();
$koneksi->close();
?>