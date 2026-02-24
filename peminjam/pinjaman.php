<?php
session_start();
include '../config/koneksi.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] != "peminjam") {
    header("location:../auth/login.php");
    exit();
}

$id_user = $_SESSION['userid'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjaman Saya | Sakura Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background: linear-gradient(135deg, #fff5f5 0%, #fed7e2 100%);
            background-attachment: fixed;
            min-height: 100vh;
        }

        .navbar-sakura {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid #fbb6ce;
        }
        .navbar-brand, .nav-link {
            color: #b83280 !important;
            font-weight: 700;
        }

        .hero-info {
            background: white;
            border-radius: 20px;
            border-left: 6px solid #ed64a6;
            box-shadow: 0 10px 20px rgba(214, 158, 172, 0.1);
        }

        .card-sakura {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 15px 35px rgba(214, 158, 172, 0.2);
            overflow: hidden;
        }

        .table thead {
            background: linear-gradient(to right, #ed64a6, #d53f8c);
            color: white;
        }

        .text-sakura { color: #ed64a6; }
        .empty-state { padding: 50px; text-align: center; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-sakura mb-4 shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🌸 Sakura Library</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">Daftar Buku</a>
                <a class="nav-link active" href="pinjaman.php">Pinjaman Saya</a>
                <a class="nav-link text-danger fw-bold" href="../auth/logout.php" onclick="return confirm('Yakin ingin logout?')">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="hero-info d-flex justify-content-between align-items-center mb-5 p-4">
        <div>
            <h3 class="mb-1" style="color: #b83280; font-weight: 700;">Rak Buku Pinjaman 📚</h3>
            <p class="text-muted mb-0">Daftar buku yang kamu pinjam beserta status dendanya.</p>
        </div>
        <div class="text-end d-none d-md-block">
            <span class="badge bg-light text-sakura border border-sakura p-2 px-3 rounded-pill">
                <i class="bi bi-person-heart me-1"></i> <?= htmlspecialchars($_SESSION['nama']); ?>
            </span>
        </div>
    </div>

    <div class="card card-sakura">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="px-4 py-3 border-0 text-center">No</th>
                        <th class="py-3 border-0">Informasi Buku</th>
                        <th class="py-3 border-0">Periode Pinjam</th>
                        <th class="py-3 border-0 text-center">Status</th>
                        <th class="py-3 border-0 text-center">Denda</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $sql = "SELECT peminjaman.*, buku.judul, buku.penulis 
                            FROM peminjaman 
                            INNER JOIN buku ON peminjaman.id_buku = buku.id_buku 
                            WHERE peminjaman.id_user = ? 
                            ORDER BY peminjaman.id_peminjaman DESC";

                    $stmt = mysqli_prepare($koneksi, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $id_user); 
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) == 0) : ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="bi bi-journal-x display-1 text-sakura opacity-25"></i>
                                <h5 class="mt-3 text-muted">Belum ada riwayat pinjaman.</h5>
                                <a href="index.php" class="btn btn-outline-secondary mt-2" style="border-radius: 12px;">Cari Buku Sekarang</a>
                            </td>
                        </tr>
                    <?php else : 
                        while($d = mysqli_fetch_assoc($result)) : 
                            // Logika Hitung Denda Otomatis
                            $tgl_kembali = new DateTime($d['tanggal_pengembalian']);
                            $tgl_sekarang = new DateTime(); 
                            $denda = 0;
                            $hari_terlambat = 0;

                            if ($d['status_peminjaman'] == 'Dipinjam' && $tgl_sekarang > $tgl_kembali) {
                                $selisih = $tgl_sekarang->diff($tgl_kembali);
                                $hari_terlambat = $selisih->days;
                                $denda = $hari_terlambat * 1000; // Tarif Rp 1.000/hari
                            }
                        ?>
                        <tr>
                            <td class="px-4 fw-bold text-muted text-center"><?= $no++; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle p-2 me-3 d-none d-md-flex align-items-center justify-content-center" style="background: #fff5f7; width: 40px; height: 40px;">
                                        <i class="bi bi-book text-pink" style="color: #ed64a6;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($d['judul']); ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($d['penulis']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small text-muted">Pinjam: <span class="text-dark fw-bold"><?= date('d/m/Y', strtotime($d['tanggal_peminjaman'])); ?></span></div>
                                <div class="small text-muted">Batas: <span class="text-dark fw-bold"><?= date('d/m/Y', strtotime($d['tanggal_pengembalian'])); ?></span></div>
                            </td>
                            <td class="text-center">
                                <?php if($d['status_peminjaman'] == 'Dipinjam') : ?>
                                    <span class="badge px-3 py-2 rounded-pill" style="background-color: #fffaf0; color: #975a16; border: 1px solid #fbd38d;">Dipinjam</span>
                                <?php else : ?>
                                    <span class="badge px-3 py-2 rounded-pill" style="background-color: #f0fff4; color: #276749; border: 1px solid #9ae6b4;">Selesai</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($denda > 0) : ?>
                                    <span class="fw-bold text-danger">Rp <?= number_format($denda, 0, ',', '.'); ?></span>
                                    <div class="text-muted" style="font-size: 0.7rem;">Terlambat <?= $hari_terlambat; ?> hari</div>
                                <?php else : ?>
                                    <span class="text-success fw-bold">Rp 0</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>