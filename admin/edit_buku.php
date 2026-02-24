<?php 
session_start();
include '../config/koneksi.php';

// Proteksi Akses
if (!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "petugas")) {
    header("location:../auth/login.php?pesan=belum_login");
    exit();
}

// Ambil ID dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location:buku.php");
    exit();
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Ambil data buku
$query_data = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku='$id'");
$d = mysqli_fetch_array($query_data);

if (!$d) {
    echo "<script>alert('Data buku tidak ditemukan!'); window.location='buku.php';</script>";
    exit();
}

// Proses Update
if (isset($_POST['update'])) {
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $penulis  = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $penerbit = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
    $tahun    = mysqli_real_escape_string($koneksi, $_POST['tahun_terbit']);
    $stok     = mysqli_real_escape_string($koneksi, $_POST['stok']);

    $update = mysqli_query($koneksi, "UPDATE buku SET 
                judul='$judul', 
                penulis='$penulis', 
                penerbit='$penerbit', 
                tahun_terbit='$tahun',
                stok='$stok' 
                WHERE id_buku='$id'");

    if ($update) {
        echo "<script>alert('✨ Berhasil! Koleksi telah diperbarui.'); window.location='buku.php';</script>";
    } else {
        echo "<script>alert('Gagal Memperbarui: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Koleksi | Sakura Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { 
            font-family: 'Quicksand', sans-serif; 
            background: radial-gradient(circle at top right, #fff5f7 0%, #fed7e2 100%);
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
        }
        .card-header-sakura {
            background: linear-gradient(135deg, #fbb6ce 0%, #ed64a6 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 30px 30px 0 0;
            font-weight: 700;
        }
        .form-label {
            font-weight: 700;
            color: #b83280;
            font-size: 0.85rem;
            margin-left: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
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
        .btn-update { 
            background: #ed64a6;
            color: white; 
            border-radius: 15px; 
            font-weight: 700; 
            border: none; 
            padding: 14px;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(237, 100, 166, 0.3);
        }
        .btn-update:hover { 
            background: #d53f8c; 
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(213, 63, 140, 0.4);
            color: white;
        }
        .btn-back {
            border-radius: 15px;
            font-weight: 600;
            padding: 10px;
            color: #b83280 !important;
        }
        .input-group-text {
            border-radius: 15px 0 0 15px;
            background-color: #fff5f7;
            border: 2px solid #fed7e2;
            color: #ed64a6;
        }
        .has-icon .form-control {
            border-radius: 0 15px 15px 0;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card sakura-card">
                <div class="card-header-sakura shadow-sm">
                    <i class="bi bi-pencil-square fs-1 mb-2"></i>
                    <h3 class="mb-0">Edit Koleksi Buku</h3>
                    <p class="mb-0 opacity-75 small fw-medium text-white">Sesuaikan informasi buku dengan teliti</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="" method="POST">
                        <div class="mb-4">
                            <label class="form-label"><i class="bi bi-bookmark-star"></i> Judul Buku</label>
                            <input type="text" name="judul" class="form-control" 
                                   value="<?= htmlspecialchars($d['judul']); ?>" required placeholder="Masukkan judul buku...">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-person-badge"></i> Penulis</label>
                                <input type="text" name="penulis" class="form-control" 
                                       value="<?= htmlspecialchars($d['penulis']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label"><i class="bi bi-box-seam"></i> Stok Buku</label>
                                <input type="number" name="stok" class="form-control" 
                                       value="<?= $d['stok']; ?>" required min="0">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-7 mb-4 mb-md-0">
                                <label class="form-label"><i class="bi bi-building"></i> Penerbit</label>
                                <input type="text" name="penerbit" class="form-control" 
                                       value="<?= htmlspecialchars($d['penerbit']); ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label"><i class="bi bi-calendar-event"></i> Tahun Terbit</label>
                                <input type="number" name="tahun_terbit" class="form-control" 
                                       value="<?= $d['tahun_terbit']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-3 pt-2">
                            <button type="submit" name="update" class="btn btn-update">
                                <i class="bi bi-check-circle-fill me-2"></i> Simpan Perubahan
                            </button>
                            <a href="buku.php" class="btn btn-link btn-back text-decoration-none">
                                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="text-center mt-4">
                <span class="badge bg-white text-muted px-3 py-2 rounded-pill shadow-sm">
                    🌸 ID Buku: #<?= $id ?>
                </span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>