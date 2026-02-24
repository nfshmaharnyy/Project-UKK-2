<?php 
session_start();
include '../config/koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['status']) || ($_SESSION['role'] != "petugas" && $_SESSION['role'] != "admin")) {
    header("location:../auth/login.php?pesan=belum_login");
    exit;
}

// Ambil statistik sederhana untuk petugas
$count_buku = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM buku"));
// Jika ada tabel peminjaman, bisa diaktifkan:
// $count_pinjam = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE status='dipinjam'"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas | Sakura Library</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background: radial-gradient(circle at top right, #fffafa 0%, #fed7e2 100%);
            min-height: 100vh;
        }
        
        /* Navbar Modern - Selaras dengan Admin */
        .navbar-sakura {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(214, 158, 172, 0.1);
            border-bottom: 2px solid rgba(251, 182, 206, 0.5);
        }

        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,245,247,0.7) 100%);
            border-radius: 35px;
            padding: 40px 30px;
            border: 2px solid white;
            box-shadow: 0 15px 35px rgba(214, 158, 172, 0.1);
            margin-bottom: 30px;
        }

        /* Menu Card */
        .sakura-card {
            background: white;
            border-radius: 30px;
            border: none;
            box-shadow: 0 10px 30px rgba(214, 158, 172, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            overflow: hidden;
        }
        .sakura-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(214, 158, 172, 0.25);
        }

        .icon-circle {
            width: 70px;
            height: 70px;
            background: #fff5f7;
            color: #ed64a6;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 20px;
            margin: 0 auto 20px;
            font-size: 2rem;
            transition: 0.3s;
        }
        .sakura-card:hover .icon-circle {
            background: #ed64a6;
            color: white;
            transform: rotate(-10deg);
        }

        .sakura-text { color: #b83280; font-weight: 700; }
        
        .btn-sakura {
            background: #ed64a6;
            color: white;
            border-radius: 15px;
            font-weight: 700;
            padding: 12px;
            border: none;
            transition: 0.3s;
        }
        .btn-sakura:hover {
            background: #d53f8c;
            color: white;
            box-shadow: 0 8px 20px rgba(237, 100, 166, 0.3);
        }

        .badge-role {
            background: #fed7e2;
            color: #d53f8c;
            padding: 5px 15px;
            border-radius: 10px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-sakura sticky-top">
    <div class="container">
        <a class="navbar-brand sakura-text fs-4" href="index.php">
            🌸 Sakura<span class="fw-light text-secondary">Staff</span>
        </a>
        <div class="ms-auto d-flex align-items-center">
            <div class="text-end me-3 d-none d-md-block">
                <span class="badge-role fw-bold">Petugas</span>
                <p class="mb-0 fw-bold sakura-text" style="font-size: 0.9rem;"><?= $_SESSION['nama']; ?></p>
            </div>
            <a href="../auth/logout.php" class="btn btn-outline-danger rounded-pill px-4 btn-sm fw-bold" 
               onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </div>
    </div>
</nav>

<div class="container mt-5 pb-5">
    <div class="welcome-section text-center">
        <h1 class="sakura-text">Semangat Bekerja, <?= explode(' ', $_SESSION['nama'])[0]; ?>! 🌸</h1>
        <p class="text-muted mb-0">Kelola operasional harian perpustakaan dengan mudah dan cepat.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-5">
            <div class="card sakura-card p-4 text-center">
                <div class="icon-circle shadow-sm">
                    <i class="bi bi-journal-plus"></i>
                </div>
                <h4 class="sakura-text mb-3">Katalog Buku</h4>
                <p class="text-muted small mb-4">Tambah koleksi baru, perbarui data buku, atau kelola kategori pustaka.</p>
                <a href="buku.php" class="btn btn-sakura w-100">
                    Buka Koleksi <i class="bi bi-arrow-right-short"></i>
                </a>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card sakura-card p-4 text-center">
                <div class="icon-circle shadow-sm">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <h4 class="sakura-text mb-3">Laporan Peminjaman</h4>
                <p class="text-muted small mb-4">Pantau sirkulasi buku, cek anggota yang meminjam, dan status pengembalian.</p>
                <a href="laporan.php" class="btn btn-sakura w-100">
                    Lihat Laporan <i class="bi bi-arrow-right-short"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-muted small mt-auto">
    &copy; 2026 🌸 Sakura Digital Library | <span class="sakura-text">Staff Panel</span>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>