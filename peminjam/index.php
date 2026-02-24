<?php
session_start();
include '../config/koneksi.php'; 

// Proteksi: Pastikan hanya Peminjam yang bisa masuk
if (!isset($_SESSION['role']) || $_SESSION['role'] != "peminjam") {
    header("location:../auth/login.php");
    exit();
}

$id_user = $_SESSION['userid']; 

// 1. Inisialisasi Logika Filter & Pencarian
$where_clauses = [];
$kategori_aktif = isset($_GET['kategori']) ? mysqli_real_escape_string($koneksi, $_GET['kategori']) : "";
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : "";

if(!empty($search_query)) {
    $where_clauses[] = "buku.judul LIKE '%$search_query%'";
}

if(!empty($kategori_aktif)) {
    $where_clauses[] = "buku.id_buku IN (SELECT id_buku FROM kategori_buku_relasi WHERE id_kategori = '$kategori_aktif')";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = " WHERE " . implode(" AND ", $where_clauses);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku | Sakura Library</title>
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

        /* --- BAGIAN NAVBAR (DISESUAIKAN) --- */
        .navbar-sakura {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid #fbb6ce;
        }
        .navbar-brand, .nav-link {
            color: #b83280 !important;
            font-weight: 700;
        }
        /* ---------------------------------- */

        /* Search & Filter Section */
        .search-container { max-width: 600px; margin: 0 auto 30px; }
        .search-input { 
            border-radius: 20px 0 0 20px; 
            border: 2px solid #ed64a6; 
            padding: 12px 25px;
            background: rgba(255, 255, 255, 0.9);
        }
        .search-btn { 
            border-radius: 0 20px 20px 0; 
            background: linear-gradient(to right, #ed64a6, #d53f8c); 
            color: white; 
            border: none; 
            padding: 0 25px;
        }

        .btn-filter {
            border: 2px solid #ed64a6;
            color: #ed64a6;
            border-radius: 15px;
            font-weight: 700;
            padding: 8px 20px;
            transition: 0.3s;
            background: white;
            text-decoration: none;
        }
        .btn-filter:hover, .btn-filter.active { 
            background: #ed64a6; 
            color: white; 
            box-shadow: 0 4px 12px rgba(237, 100, 166, 0.3);
        }

        /* Card Book Styling */
        .card-sakura {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 25px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 25px rgba(214, 158, 172, 0.2);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
        }
        .card-sakura:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(214, 158, 172, 0.3);
        }

        .book-icon-wrapper {
            background: linear-gradient(135deg, #fff5f5 0%, #fed7e2 100%);
            border-radius: 20px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 3.5rem;
        }

        .badge-kategori {
            background: #fff5f5;
            color: #ed64a6;
            border: 1px solid #fed7e2;
            font-weight: 700;
            font-size: 0.65rem;
        }

        .btn-sakura {
            background: linear-gradient(to right, #ed64a6, #d53f8c);
            color: white;
            border-radius: 15px;
            font-weight: 700;
            border: none;
            transition: 0.3s;
        }
        .btn-sakura:hover {
            transform: scale(1.05);
            color: white;
            box-shadow: 0 5px 15px rgba(211, 63, 140, 0.4);
        }

        .status-badge {
            font-size: 0.7rem;
            padding: 5px 12px;
            border-radius: 10px;
        }
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
                <a class="nav-link active" href="index.php">Daftar Buku</a>
                <a class="nav-link" href="pinjaman.php">Pinjaman Saya</a>
                <a class="nav-link text-danger fw-bold" href="../auth/logout.php" onclick="return confirm('Yakin ingin logout?')">Logout</a>
            </div>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-5 p-4 bg-white rounded-4 shadow-sm border-start border-5" style="border-color: #ed64a6 !important;">
        <div>
            <h2 class="mb-1" style="color: #b83280; font-weight: 800;">Jelajahi Dunia Novel 📖</h2>
            <p class="text-muted mb-0">Halo, <strong><?= htmlspecialchars($_SESSION['nama']); ?></strong>! Mau baca apa hari ini?</p>
        </div>
        <i class="bi bi-stars fs-1 text-sakura opacity-50"></i>
    </div>

    <div class="search-container">
        <form action="index.php" method="GET" class="input-group">
            <?php if(!empty($kategori_aktif)): ?>
                <input type="hidden" name="kategori" value="<?= $kategori_aktif ?>">
            <?php endif; ?>
            <input type="text" name="search" class="form-control search-input" 
                   placeholder="Cari judul novel favoritmu..." value="<?= htmlspecialchars($search_query); ?>">
            <button class="btn search-btn" type="submit">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <div class="mb-5 d-flex flex-wrap gap-2 justify-content-center">
        <a href="index.php<?= !empty($search_query) ? '?search='.urlencode($search_query) : '' ?>" 
           class="btn-filter <?= ($kategori_aktif == "") ? 'active' : ''; ?>">Semua</a>
        
        <?php
        $kat_query = mysqli_query($koneksi, "SELECT * FROM kategori_buku ORDER BY nama_kategori ASC");
        while($k = mysqli_fetch_array($kat_query)) {
            $active_class = ($kategori_aktif == $k['id_kategori']) ? 'active' : '';
            $url = "index.php?kategori=" . $k['id_kategori'];
            if(!empty($search_query)) $url .= "&search=" . urlencode($search_query);
            echo "<a href='$url' class='btn-filter $active_class'>".$k['nama_kategori']."</a>";
        }
        ?>
    </div>

    <div class="row g-4">
        <?php 
        $sql = "SELECT buku.*, 
                GROUP_CONCAT(DISTINCT kategori_buku.nama_kategori SEPARATOR ', ') AS daftar_kategori
                FROM buku 
                LEFT JOIN kategori_buku_relasi ON buku.id_buku = kategori_buku_relasi.id_buku
                LEFT JOIN kategori_buku ON kategori_buku_relasi.id_kategori = kategori_buku.id_kategori
                $where_sql
                GROUP BY buku.id_buku
                ORDER BY buku.id_buku DESC";
        
        $query_buku = mysqli_query($koneksi, $sql);

        if(mysqli_num_rows($query_buku) > 0) {
            while($row = mysqli_fetch_array($query_buku)) { 
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card card-sakura p-3">
                <div class="book-icon-wrapper">
                    <i class="bi bi-journal-bookmark text-sakura"></i>
                </div>
                
                <div class="card-body p-0 d-flex flex-column">
                    <h6 class="fw-bold text-dark text-truncate mb-1" title="<?= $row['judul']; ?>">
                        <?= htmlspecialchars($row['judul']); ?>
                    </h6>
                    <p class="text-muted mb-2" style="font-size: 0.75rem;">Oleh: <?= htmlspecialchars($row['penulis']); ?></p>
                    
                    <div class="mb-3 d-flex flex-wrap gap-1" style="min-height: 40px;">
                        <?php 
                        if(!empty($row['daftar_kategori'])){
                            $kat = explode(', ', $row['daftar_kategori']);
                            foreach($kat as $k) echo '<span class="badge rounded-pill badge-kategori">'.htmlspecialchars($k).'</span>';
                        } else { echo '<span class="badge rounded-pill bg-light text-muted" style="font-size: 0.65rem;">Umum</span>'; }
                        ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-secondary small fw-bold"><i class="bi bi-calendar3"></i> <?= $row['tahun_terbit']; ?></span>
                        <?php if($row['stok'] > 0): ?>
                            <span class="status-badge bg-success-subtle text-success border border-success-subtle">
                                Stok: <?= $row['stok']; ?>
                            </span>
                        <?php else: ?>
                            <span class="status-badge bg-danger-subtle text-danger border border-danger-subtle">Habis</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($row['stok'] > 0): ?>
                        <a href="pinjam_buku.php?id=<?= $row['id_buku']; ?>" class="btn btn-sakura w-100 py-2">
                            Pinjam <i class="bi bi-arrow-right-short"></i>
                        </a>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100 py-2 disabled" style="border-radius: 15px;">
                            Tidak Tersedia
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php 
            } 
        } else {
            echo "<div class='col-12 text-center py-5'>
                    <i class='bi bi-search display-1 text-muted opacity-25'></i>
                    <h5 class='text-muted mt-3'>Buku yang kamu cari tidak ditemukan.</h5>
                    <a href='index.php' class='btn btn-sakura mt-2 px-4'>Reset Katalog</a>
                  </div>";
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>