<?php
session_start();
include '../config/koneksi.php';

// Proteksi Role
if (!isset($_SESSION['role']) || $_SESSION['role'] != "peminjam") {
    header("location:../auth/login.php");
    exit();
}

$id_buku = mysqli_real_escape_string($koneksi, $_GET['id']);
$query = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = '$id_buku'");
$buku = mysqli_fetch_array($query);

// Validasi jika buku tidak ditemukan atau stok habis
if (!$buku || $buku['stok'] < 1) {
    echo "<script>alert('Maaf, buku tidak tersedia saat ini!'); window.location='index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pinjam | Sakura Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { 
            font-family: 'Quicksand', sans-serif; 
            background: linear-gradient(135deg, #fff5f5 0%, #fed7e2 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .confirm-card { 
            background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);
            border-radius: 30px; box-shadow: 0 15px 35px rgba(214, 158, 172, 0.3); 
            border: 1px solid rgba(255, 255, 255, 0.5); width: 100%; max-width: 500px; overflow: hidden;
        }
        .card-header-sakura {
            background: linear-gradient(to right, #ed64a6, #d53f8c);
            color: white; padding: 25px; text-align: center;
        }
        .book-info-box {
            background: white; border: 2px dashed #fbb6ce; border-radius: 20px; padding: 20px; margin-bottom: 25px;
        }
        .form-label { color: #b83280; font-weight: 700; font-size: 0.85rem; margin-left: 5px; }
        .form-control { border-radius: 15px; border: 2px solid #fed7e2; padding: 12px 15px; background: #fff; }
        .btn-sakura { 
            background: linear-gradient(to right, #ed64a6, #d53f8c); border: none; 
            border-radius: 15px; color: white; font-weight: 700; padding: 14px; 
            transition: 0.3s; box-shadow: 0 4px 15px rgba(213, 63, 140, 0.3);
        }
        .btn-sakura:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(213, 63, 140, 0.4); color: white; }
        .text-sakura { color: #d53f8c; }
    </style>
</head>
<body>

<div class="confirm-card">
    <div class="card-header-sakura">
        <i class="bi bi-journal-bookmark-fill fs-1"></i>
        <h3 class="mb-0 fw-bold mt-2">Konfirmasi Pinjam</h3>
        <p class="small mb-0 opacity-75">Pastikan data peminjaman sudah benar</p>
    </div>

    <div class="p-4 p-md-5">
        <div class="book-info-box text-center">
            <span class="badge bg-light text-sakura border border-sakura mb-2">Informasi Buku</span>
            <h4 class="fw-bold mb-1 text-dark"><?= $buku['judul']; ?></h4>
            <p class="text-muted small mb-0"><i class="bi bi-box-seam me-1"></i> Stok tersedia: <?= $buku['stok']; ?> Buku</p>
        </div>

        <form action="proses_pinjam.php" method="POST">
            <input type="hidden" name="id_buku" value="<?= $buku['id_buku']; ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="bi bi-calendar-event"></i> Tgl Pinjam</label>
                    <input type="text" class="form-control bg-light" value="<?= date('d M Y'); ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="bi bi-calendar-check"></i> Tgl Kembali</label>
                    <input type="text" class="form-control bg-light" value="<?= date('d M Y', strtotime('+7 days')); ?>" readonly>
                </div>
            </div>

            <div class="alert alert-warning border-0 rounded-4 small mb-4">
                <i class="bi bi-info-circle-fill me-2"></i> Durasi pinjam adalah <strong>7 hari</strong>. Harap mengembalikan tepat waktu untuk menghindari denda.
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-sakura">
                    Konfirmasi Pinjam <i class="bi bi-check-circle-fill ms-1"></i>
                </button>
                <a href="index.php" class="btn btn-link text-decoration-none text-muted small mt-1">Batal dan Kembali</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>