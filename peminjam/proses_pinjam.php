<?php
session_start();
include '../config/koneksi.php';

// 1. Proteksi tambahan: Pastikan user benar-benar sudah login
if (!isset($_SESSION['userid'])) {
    header("location:../auth/login.php");
    exit();
}

if(isset($_POST['id_buku'])){
    // Gunakan mysqli_real_escape_string untuk keamanan dari SQL Injection
    $id_user = $_SESSION['userid'];
    $id_buku = mysqli_real_escape_string($koneksi, $_POST['id_buku']);
    
    // 2. Cek apakah user SUDAH meminjam buku ini dan belum dikembalikan
    // Ini penting agar stok tidak berkurang sia-sia untuk buku yang sama
    $cek_pinjam = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_user='$id_user' AND id_buku='$id_buku' AND status_peminjaman='Dipinjam'");
    
    if(mysqli_num_rows($cek_pinjam) > 0){
        echo "<script>alert('Anda sedang meminjam buku ini. Selesaikan peminjaman sebelumnya terlebih dahulu!'); window.location='pinjaman.php';</script>";
        exit();
    }

    // 3. Cek stok buku
    $cek_stok = mysqli_query($koneksi, "SELECT stok FROM buku WHERE id_buku = '$id_buku'");
    $data_buku = mysqli_fetch_assoc($cek_stok);

    if($data_buku && $data_buku['stok'] > 0) {
        // 4. Siapkan data peminjaman
        $tgl_pinjam = date('Y-m-d');
        $tgl_kembali = date('Y-m-d', strtotime('+7 days'));
        
        // Jalankan Query Pinjam
        $query_pinjam = mysqli_query($koneksi, "INSERT INTO peminjaman (id_user, id_buku, tanggal_peminjaman, tanggal_pengembalian, status_peminjaman) 
                         VALUES ('$id_user', '$id_buku', '$tgl_pinjam', '$tgl_kembali', 'Dipinjam')");

        if($query_pinjam){
            // 5. EKSEKUSI PENGURANGAN STOK
            mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");
            
            echo "<script>alert('Berhasil meminjam! Stok buku telah diperbarui.'); window.location='pinjaman.php';</script>";
        } else {
            echo "<script>alert('Gagal memproses peminjaman.'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Maaf, stok buku baru saja habis!'); window.location='index.php';</script>";
    }
} else {
    header("location:index.php");
}
?>