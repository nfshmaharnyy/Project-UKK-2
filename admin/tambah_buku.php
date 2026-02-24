<?php
session_start();
include '../config/koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['role']) || ($_SESSION['role'] != "petugas" && $_SESSION['role'] != "admin")) {
    header("location:../auth/login.php?pesan=belum_login");
    exit;
}

// Proses Simpan Data
if (isset($_POST['simpan'])) {
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $penulis  = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $penerbit = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
    $tahun    = mysqli_real_escape_string($koneksi, $_POST['tahun_terbit']);
    $stok     = mysqli_real_escape_string($koneksi, $_POST['stok']);

    $query = mysqli_query($koneksi, "INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, stok) 
                                     VALUES ('$judul', '$penulis', '$penerbit', '$tahun', '$stok')");

    if ($query) {
        echo "<script>alert('✨ Berhasil! Buku baru telah ditambahkan ke koleksi.'); window.location='buku.php';</script>";
    } else {
        echo "<script>alert('❌ Gagal Menambah Data: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Koleksi | Sakura Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { 
            font-family: 'Quicksand', sans-serif; 
            background: radial-gradient(circle at top right, #fffafa 0%, #fed7e2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .sakura-card { 
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: blur(10px);
            border-radius: 30px; 
            border: 1px solid rgba(255, 255, 255, 0.5); 
            box-shadow: 0 25px 50px rgba(214, 158, 172, 0.3); 
            overflow: hidden;
        }
        .card-header-sakura {
            background: linear-gradient(135deg, #fbb6ce 0%, #ed64a6 100%);
            color: white;
            padding: 35px;
            text-align: center;
        }
        .form-label {
            font-weight: 700;
            color: #b83280;
            font-size: 0.85rem;
            margin-left: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-control { 
            border-radius: 15px; 
            border: 2px solid #fed7e2; 
            padding: 12px 18px;
            background-color: white;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #ed64a6;
            box-shadow: 0 0 15px rgba(237, 100, 166, 0.1);
            transform: translateY(-2px);
        }
        .btn-simpan { 
            background: #ed64a6;
            color: white; 
            border-radius: 15px; 
            font-weight: 700; 
            border: none; 
            padding: 14px;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(237, 100, 166, 0.3);
        }
        .btn-simpan:hover { 
            background: #d53f8c; 
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(213, 63, 140, 0.4);
            color: white;
        }
        .btn-back {
            border-radius: 15px;
            font-weight: 600;
            color: #b83280 !important;
            text-decoration: none;
            transition: 0.2s;
        }
        .btn-back:hover { opacity: 0.7; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card sakura-card">
                <div class="card-header-sakura">
                    <i class="bi bi-plus-circle-dotted fs-1 mb-2"></i>
                    <h3 class="fw-bold mb-0">Tambah Buku Baru</h3>
                    <p class="mb-0 opacity-75 small fw-medium">Lengkapi detail untuk menambah koleksi perpustakaan</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="" method="POST">
                        <div class="mb-4">
                            <label class="form-label"><i class="bi bi-book"></i> Judul Lengkap</label>
                            <input type="text" name="judul" class="form-control shadow-sm" 
                                   placeholder="Contoh: Laskar Pelangi" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-7 mb-4">
                                <label class="form-label"><i class="bi bi-person-heart"></i> Nama Penulis</label>
                                <input type="text" name="penulis" class="form-control shadow-sm" 
                                       placeholder="Nama pengarang..." required>
                            </div>
                            <div class="col-md-5 mb-4">
                                <label class="form-label"><i class="bi bi-box-seam"></i> Stok Awal</label>
                                <input type="number" name="stok" class="form-control shadow-sm" 
                                       placeholder="0" required min="0">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-7 mb-4 mb-md-0">
                                <label class="form-label"><i class="bi bi-building"></i> Penerbit</label>
                                <input type="text" name="penerbit" class="form-control shadow-sm" 
                                       placeholder="Nama penerbit..." required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label"><i class="bi bi-calendar-event"></i> Tahun Terbit</label>
                                <input type="number" name="tahun_terbit" class="form-control shadow-sm" 
                                       value="<?= date('Y') ?>" required>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-3 pt-2">
                            <button type="submit" name="simpan" class="btn btn-simpan">
                                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Tambahkan ke Koleksi
                            </button>
                            <a href="buku.php" class="btn btn-back text-center">
                                <i class="bi bi-arrow-left-short"></i> Batal dan Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <p class="text-center mt-4 text-muted small">Sakura Digital Library &copy; 2026</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>