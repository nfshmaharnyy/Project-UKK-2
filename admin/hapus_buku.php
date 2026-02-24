<?php
session_start();
include '../config/koneksi.php';

// 1. Proteksi Akses
if (!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "petugas")) {
    header("location:../auth/login.php?pesan=belum_login");
    exit();
}

// 2. Cek apakah ID ada di URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // 3. Proses Hapus Data
    $query = mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku='$id'");

    if ($query) {
        // Berhasil dihapus
        echo "<script>
                alert('✨ Berhasil! Buku telah dihapus dari koleksi.');
                window.location='buku.php';
              </script>";
    } else {
        // Gagal dihapus (biasanya karena relasi database/FK)
        $error_pesan = mysqli_error($koneksi);
        echo "<script>
                alert('❌ Gagal menghapus buku! Kemungkinan buku ini masih memiliki riwayat peminjaman atau sedang dipinjam.');
                console.log('Error Database: " . $error_pesan . "');
                window.location='buku.php';
              </script>";
    }
} else {
    // Jika mencoba akses file langsung tanpa ID
    header("location:buku.php");
    exit();
}
?>