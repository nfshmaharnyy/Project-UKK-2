<?php
session_start();
include '../config/koneksi.php';

// Proteksi: Pastikan hanya petugas/admin yang bisa akses
if(!isset($_SESSION['role']) || ($_SESSION['role'] != "petugas" && $_SESSION['role'] != "admin")){
    header("location:../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_peminjaman = $_GET['id'];
    $tanggal_sekarang = date('Y-m-d');

    // 1. Cari dulu ID Buku yang terkait dengan peminjaman ini
    $cari_buku = mysqli_query($koneksi, "SELECT id_buku FROM peminjaman WHERE id_peminjaman = '$id_peminjaman'");
    $data_peminjaman = mysqli_fetch_assoc($cari_buku);
    $id_buku = $data_peminjaman['id_buku'];

    // 2. Jalankan dua perintah sekaligus (Update Status & Update Stok)
    
    // Update status di tabel peminjaman
    $sql_update_peminjaman = "UPDATE peminjaman SET 
            status_peminjaman = 'Dikembalikan', 
            tanggal_pengembalian = '$tanggal_sekarang' 
            WHERE id_peminjaman = '$id_peminjaman'";
    
    // Tambah stok +1 di tabel buku
    $sql_update_stok = "UPDATE buku SET stok = stok + 1 WHERE id_buku = '$id_buku'";

    // Eksekusi keduanya
    if (mysqli_query($koneksi, $sql_update_peminjaman) && mysqli_query($koneksi, $sql_update_stok)) {
        echo "<script>
                alert('Buku berhasil dikembalikan dan stok telah diperbarui!');
                window.location='laporan.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
} else {
    header("location:laporan.php");
}
?>