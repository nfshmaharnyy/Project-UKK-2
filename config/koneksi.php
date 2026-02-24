<?php
// Pengaturan Database
$host     = "localhost";
$username = "root";
$password = ""; // Kosongkan jika menggunakan XAMPP standar
$database = "perpustakaan"; // Pastikan ini sama dengan nama database yang kamu buat di MySQL

// Melakukan koneksi
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>