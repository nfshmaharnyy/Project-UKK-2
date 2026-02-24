<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "peminjam") {
    header("location:../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("location:index.php");
    exit;
}

$id_buku = mysqli_real_escape_string($koneksi, $_GET['id']);
$id_user = $_SESSION['userid']; 

$query_buku = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = '$id_buku'");
$buku = mysqli_fetch_array($query_buku);

if (!$buku) {
    echo "<script>alert('Buku tidak ditemukan!'); window.location='index.php';</script>";
    exit;
} elseif ($buku['stok'] <= 0) {
    echo "<script>alert('Maaf, stok buku ini sedang habis!'); window.location='index.php';</script>";
    exit;
}

if (isset($_POST['submit_pinjam'])) {
    $tgl_pinjam = date('Y-m-d');
    $tgl_kembali = $_POST['tgl_kembali'];
    $status = "Dipinjam";

    // Re-check stok sebelum eksekusi
    $cek_ulang = mysqli_query($koneksi, "SELECT stok FROM buku WHERE id_buku = '$id_buku'");
    $data_ulang = mysqli_fetch_assoc($cek_ulang);

    if ($data_ulang['stok'] > 0) {
        $insert = mysqli_query($koneksi, "INSERT INTO peminjaman (id_user, id_buku, tanggal_peminjaman, tanggal_pengembalian, status_peminjaman) 
                                          VALUES ('$id_user', '$id_buku', '$tgl_pinjam', '$tgl_kembali', '$status')");
        if ($insert) {
            mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");
            echo "<script>alert('🌸 Berhasil meminjam buku! Stok telah diperbarui.'); window.location='pinjaman.php';</script>";
        }
    } else {
        echo "<script>alert('Maaf, stok buku tiba-tiba habis!'); window.location='index.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pinjaman | Sakura Library</title>
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
        .book-detail-box { background: white; border-radius: 20px; padding: 20px; border: 1.5px solid #fed7e2; }
        .form-control { border-radius: 12px; border: 2px solid #fed7e2; padding: 10px; }
        .btn-sakura { 
            background: linear-gradient(to right, #ed64a6, #d53f8c); border: none; 
            border-radius: 15px; color: white; font-weight: 700; padding: 12px; transition: 0.3s;
        }
        .btn-sakura:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(213, 63, 140, 0.4); color: white; }
    </style>
</head>
<body>

<div class="confirm-card">
    <div class="card-header-sakura">
        <i class="bi bi-bookmark-heart-fill fs-2"></i>
        <h4 class="fw-bold mb-0 mt-2">Konfirmasi Peminjaman 🌸</h4>
    </div>

    <div class="p-4 p-md-5">
        <div class="book-detail-box mb-4">
            <h5 class="fw-bold text-dark mb-1"><?= $buku['judul'] ?></h5>
            <p class="text-muted small mb-2">Penulis: <?= $buku['penulis'] ?></p>
            <hr class="opacity-25">
            <div class="d-flex justify-content-between small">
                <span>Sisa Stok:</span>
                <span class="fw-bold text-success"><?= $buku['stok'] ?> Buku</span>
            </div>
            <div class="d-flex justify-content-between small">
                <span>Tanggal Pinjam:</span>
                <span class="fw-bold"><?= date('d M Y') ?></span>
            </div>
        </div>

        <form method="POST">
            <div class="mb-4">
                <label class="form-label small fw-bold text-sakura">Pilih Tanggal Kembali:</label>
                <input type="date" name="tgl_kembali" class="form-control" required 
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>" 
                       max="<?= date('Y-m-d', strtotime('+14 days')) ?>">
                <div class="form-text" style="font-size: 0.75rem;">
                    <i class="bi bi-info-circle me-1"></i> Maksimal durasi pinjam adalah 14 hari.
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="submit_pinjam" class="btn btn-sakura">
                    Konfirmasi Pinjam <i class="bi bi-check2-circle ms-1"></i>
                </button>
                <a href="index.php" class="btn btn-link text-decoration-none text-muted small">Batal</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>