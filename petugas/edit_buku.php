<?php
session_start();
include '../config/koneksi.php';

// 1. Proteksi Akses
if (!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "petugas")) {
    header("location:../auth/login.php?pesan=belum_login");
    exit();
}

// 2. Ambil ID dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location:buku.php");
    exit();
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// 3. Ambil data buku
$query_data = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku='$id'");
$d = mysqli_fetch_array($query_data);

if (!$d) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='buku.php';</script>";
    exit();
}

// 4. Proses Update Data
if (isset($_POST['update'])) {
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $penulis  = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $penerbit = mysqli_real_escape_string($koneksi, $_POST['penerbit']);
    $tahun    = mysqli_real_escape_string($koneksi, $_POST['tahun_terbit']);
    $stok     = mysqli_real_escape_string($koneksi, $_POST['stok']); // Tambahan Stok

    $update = mysqli_query($koneksi, "UPDATE buku SET 
                judul='$judul', 
                penulis='$penulis', 
                penerbit='$penerbit', 
                tahun_terbit='$tahun',
                stok='$stok' 
                WHERE id_buku='$id'");

    if ($update) {
        echo "<script>alert('✨ Berhasil! Data Buku & Stok Telah Diperbarui.'); window.location='buku.php';</script>";
    } else {
        echo "<script>alert('Gagal Memperbarui Data: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku | Sakura Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Quicksand', sans-serif; 
            background: linear-gradient(135deg, #fff5f5 0%, #fed7e2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .sakura-card { 
            background: white; border-radius: 25px; border: none; 
            box-shadow: 0 15px 35px rgba(214, 158, 172, 0.2); 
            overflow: hidden;
        }
        .card-header-sakura {
            background-color: #fbb6ce; color: #b83280; padding: 20px;
            text-align: center; font-weight: 700;
        }
        .btn-update { 
            background: linear-gradient(to right, #ed64a6, #d53f8c);
            color: white; border-radius: 12px; font-weight: 700; border: none; padding: 12px;
            transition: 0.3s;
        }
        .btn-update:hover { 
            background: #d53f8c; transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(213, 63, 140, 0.3); color: white;
        }
        .form-control { 
            border-radius: 12px; border: 1.5px solid #fbb6ce; 
            padding: 10px 15px; background-color: #fffafb;
        }
        .form-control:focus {
            border-color: #ed64a6; box-shadow: 0 0 0 0.25rem rgba(237, 100, 166, 0.15);
        }
        .btn-back { border-radius: 12px; font-weight: 600; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-5">
            <div class="card sakura-card">
                <div class="card-header-sakura">
                    <h4 class="mb-0">✨ Edit Koleksi Buku</h4>
                </div>
                <div class="card-body p-4 p-md-5">
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Judul Buku</label>
                            <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($d['judul']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Penulis</label>
                            <input type="text" name="penulis" class="form-control" value="<?= htmlspecialchars($d['penulis']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Stok Saat Ini</label>
                            <input type="number" name="stok" class="form-control" value="<?= $d['stok']; ?>" required min="0">
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-7 mb-3 mb-md-0">
                                <label class="form-label small fw-bold text-secondary">Penerbit</label>
                                <input type="text" name="penerbit" class="form-control" value="<?= htmlspecialchars($d['penerbit']); ?>" required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-secondary">Tahun Terbit</label>
                                <input type="number" name="tahun_terbit" class="form-control" value="<?= $d['tahun_terbit']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" name="update" class="btn btn-update">Simpan Perubahan</button>
                            <a href="buku.php" class="btn btn-light btn-back text-secondary mt-1">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>