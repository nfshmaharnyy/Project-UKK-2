<?php
session_start();
include '../config/koneksi.php';

// 1. Proteksi halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] != "peminjam") {
    header("location:../auth/login.php");
    exit();
}

// 2. Pastikan ID ada di URL
if (isset($_GET['id'])) {
    $id_peminjaman = mysqli_real_escape_string($koneksi, $_GET['id']);
    $id_user = $_SESSION['userid']; 
    $tgl_sekarang = date('Y-m-d');

    // 3. CEK STATUS TERLEBIH DAHULU (PENTING!)
    // Pastikan buku yang dikembalikan statusnya masih 'Dipinjam'
    $cek_status = mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE id_peminjaman = '$id_peminjaman' AND id_user = '$id_user'");
    $data_peminjaman = mysqli_fetch_assoc($cek_status);

    if ($data_peminjaman && $data_peminjaman['status_peminjaman'] == 'Dipinjam') {
        
        // 4. Update status peminjaman menjadi 'Dikembalikan'
        $query_update = mysqli_query($koneksi, "UPDATE peminjaman SET 
            status_peminjaman = 'Dikembalikan', 
            tanggal_pengembalian = '$tgl_sekarang' 
            WHERE id_peminjaman = '$id_peminjaman' AND id_user = '$id_user'");

        if ($query_update) {
            // 5. AMBIL ID BUKU untuk menambah stok
            $id_buku = $data_peminjaman['id_buku'];

            // 6. OTOMATIS TAMBAH STOK (+1)
            $update_stok = mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id_buku = '$id_buku'");

            if ($update_stok) {
                echo "<script>alert('Buku berhasil dikembalikan! Stok buku telah tersedia kembali.'); window.location='pinjaman.php';</script>";
            } else {
                echo "<script>alert('Status berubah, tapi gagal memperbarui stok.'); window.location='pinjaman.php';</script>";
            }
        }
    } else {
        // Jika status sudah 'Dikembalikan', jangan tambah stok lagi
        echo "<script>alert('Buku ini sudah dikembalikan sebelumnya.'); window.location='pinjaman.php';</script>";
    }
} else {
    header("location:pinjaman.php");
}
?>