<?php 
session_start();
include '../config/koneksi.php';

// Periksa apakah status login ada DAN role-nya adalah 'admin'
if (!isset($_SESSION['status']) || $_SESSION['role'] != "admin") {
    header("location:../auth/login.php?pesan=belum_login");
    exit;
}

// Mengambil data ringkasan untuk statistik
$count_buku = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM buku"));
$count_user = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM user"));
// Jika kamu punya tabel peminjaman, aktifkan ini:
// $count_pinjam = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM peminjaman WHERE status='dipinjam'"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Digital Library Sakura</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background: radial-gradient(circle at top right, #fffafa 0%, #fed7e2 100%);
            min-height: 100vh;
        }
        
        /* Navbar Modern */
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
            padding: 50px 30px;
            border: 2px solid white;
            box-shadow: 0 15px 35px rgba(214, 158, 172, 0.1);
            margin-bottom: 30px;
        }

        /* Stats Card */
        .stats-pill {
            background: white;
            border-radius: 20px;
            padding: 15px 25px;
            display: inline-flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.03);
            border-bottom: 4px solid #ed64a6;
            transition: 0.3s;
        }
        .stats-pill:hover { transform: translateY(-5px); }

        /* Menu Card */
        .admin-card {
            background: white;
            border-radius: 30px;
            border: none;
            box-shadow: 0 10px 30px rgba(214, 158, 172, 0.1);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            overflow: hidden;
        }
        .admin-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 40px rgba(214, 158, 172, 0.25);
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: #fff5f7;
            color: #ed64a6;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 25px;
            margin: 0 auto 20px;
            font-size: 2.2rem;
            transition: 0.3s;
        }
        .admin-card:hover .icon-circle {
            background: #ed64a6;
            color: white;
            transform: rotate(-10deg);
        }

        /* Buttons */
        .btn-sakura {
            background: #ed64a6;
            color: white;
            border-radius: 15px;
            font-weight: 700;
            padding: 10px 25px;
            border: none;
            transition: 0.3s;
        }
        .btn-sakura:hover {
            background: #d53f8c;
            color: white;
            box-shadow: 0 8px 20px rgba(237, 100, 166, 0.3);
        }
        
        .btn-outline-sakura {
            border: 2px solid #ed64a6;
            color: #ed64a6;
            border-radius: 15px;
            font-weight: 700;
            transition: 0.3s;
        }
        .btn-outline-sakura:hover { background: #ed64a6; color: white; }

        .sakura-text { color: #b83280; font-weight: 700; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-sakura sticky-top">
    <div class="container">
        <a class="navbar-brand sakura-text fs-4" href="index.php">
            🌸 Sakura<span class="fw-light text-secondary">Admin</span>
        </a>
        <div class="ms-auto d-flex align-items-center">
            <div class="text-end me-3 d-none d-md-block">
                <span class="text-muted small">Logged in as:</span>
                <p class="mb-0 fw-bold sakura-text" style="margin-top: -5px;"><?= $_SESSION['nama']; ?></p>
            </div>
            <a href="../auth/logout.php" class="btn btn-outline-danger rounded-pill px-4 btn-sm fw-bold" 
               onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </div>
    </div>
</nav>

<div class="container mt-5 pb-5">
    <div class="welcome-section text-center position-relative overflow-hidden">
        <h1 class="sakura-text display-5">Selamat Datang, <?= explode(' ', $_SESSION['nama'])[0]; ?>! 🌸</h1>
        <p class="text-muted fs-5 mb-4">Akses kontrol penuh untuk manajemen perpustakaan ada di tangan Anda.</p>
        
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <div class="stats-pill">
                <i class="bi bi-book fs-3 text-pink"></i>
                <div class="text-start">
                    <h5 class="mb-0 fw-bold"><?= $count_buku; ?></h5>
                    <span class="small text-muted">Total Buku</span>
                </div>
            </div>
            <div class="stats-pill">
                <i class="bi bi-people fs-3 text-pink"></i>
                <div class="text-start">
                    <h5 class="mb-0 fw-bold"><?= $count_user; ?></h5>
                    <span class="small text-muted">Pengguna</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="card admin-card p-4 text-center">
                <div class="icon-circle shadow-sm">
                    <i class="bi bi-journal-album"></i>
                </div>
                <h4 class="sakura-text mb-3">Katalog Buku</h4>
                <p class="text-muted small px-3 mb-4">Manajemen stok, judul, dan informasi detail semua koleksi perpustakaan.</p>
                <div class="mt-auto">
                    <a href="buku.php" class="btn btn-sakura w-100 py-3">Buka Koleksi</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card admin-card p-4 text-center border-top border-4 border-info">
                <div class="icon-circle shadow-sm">
                    <i class="bi bi-person-gear"></i>
                </div>
                <h4 class="sakura-text mb-3">Manajemen User</h4>
                <p class="text-muted small px-3 mb-4">Daftarkan petugas baru atau kelola akses semua anggota sistem.</p>
                <div class="mt-auto d-grid gap-2">
                    <a href="../auth/register.php" class="btn btn-outline-sakura py-2"><i class="bi bi-person-plus"></i> Daftar Petugas</a>
                    <a href="user.php" class="btn btn-sakura py-2">Lihat Semua User</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card admin-card p-4 text-center">
                <div class="icon-circle shadow-sm">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                </div>
                <h4 class="sakura-text mb-3">Laporan Aktivitas</h4>
                <p class="text-muted small px-3 mb-4">Lihat statistik peminjaman dan ekspor data laporan dalam format PDF/Cetak.</p>
                <div class="mt-auto">
                    <a href="laporan.php" class="btn btn-sakura w-100 py-3">Generate Laporan</a>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-muted small">
    &copy; 2026 🌸 Sakura Digital Library | <span class="sakura-text">Administrator Panel</span>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>