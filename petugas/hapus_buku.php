<?php
session_start();
include '../config/koneksi.php';

// 1. Proteksi: Izinkan Admin dan Petugas
if (!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "petugas")) {
    header("location:../auth/login.php?pesan=belum_login");
    exit();
}

// 2. Cek apakah ID ada di URL dan tidak kosong
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    // Menggunakan mysqli_real_escape_string untuk keamanan tambahan
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // 3. Proses hapus data buku
    $query = mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku='$id'");

    if ($query) {
        // Mengarahkan kembali ke halaman buku petugas dengan notifikasi sukses
        echo "<script>
                alert('✨ Berhasil! Buku telah dihapus dari koleksi.'); 
                window.location='buku.php';
              </script>";
    } else {
        // Jika gagal, tampilkan pesan yang lebih spesifik
        $error_pesan = mysqli_error($koneksi);
        echo "<script>
                alert('❌ Gagal menghapus buku! Data mungkin masih terikat dengan transaksi peminjaman (riwayat tidak boleh hilang).'); 
                console.log('Error Database: " . $error_pesan . "');
                window.location='buku.php';
              </script>";
    }
} else {
    // Jika tidak ada ID, langsung balik ke daftar buku
    header("location:buku.php");
    exit();
}
?>