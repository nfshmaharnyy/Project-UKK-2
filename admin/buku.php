<?php 
session_start();
include '../config/koneksi.php';

// Proteksi: Admin DAN Petugas boleh masuk ke halaman ini
if (!isset($_SESSION['role']) || ($_SESSION['role'] != "admin" && $_SESSION['role'] != "petugas")) {
    header("location:../auth/login.php?pesan=belum_login");
    exit();
}

// Ambil data buku dari database
$query = mysqli_query($koneksi, "SELECT * FROM buku ORDER BY id_buku DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku | Digital Library Sakura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sakura-pink: #ed64a6;
            --sakura-dark: #b83280;
            --sakura-light: #fed7e2;
            --sakura-bg: #fff5f5;
        }

        body { 
            font-family: 'Quicksand', sans-serif; 
            background: linear-gradient(135deg, #fff5f5 0%, #fed7e2 100%);
            min-height: 100vh;
            color: #4a5568;
        }

        /* Navbar Glassmorphism */
        .navbar-sakura {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid var(--sakura-light);
            border-radius: 0 0 20px 20px;
            margin-bottom: 30px;
        }

        /* Card Customization */
        .sakura-card { 
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 25px; 
            border: 1px solid rgba(255, 255, 255, 0.5); 
            box-shadow: 0 15px 35px rgba(214, 158, 172, 0.2); 
            overflow: hidden;
        }

        .card-header-sakura {
            background: white;
            padding: 25px;
            border-bottom: 1px solid var(--sakura-light);
        }

        /* Search Input */
        .search-container {
            max-width: 400px;
            position: relative;
        }
        .search-input {
            border-radius: 15px;
            border: 2px solid var(--sakura-light);
            padding: 12px 15px 12px 45px;
            transition: 0.3s;
            background: white;
        }
        .search-input:focus {
            border-color: var(--sakura-pink);
            box-shadow: 0 0 0 0.25rem rgba(237, 100, 166, 0.1);
        }
        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--sakura-pink);
            font-size: 1.2rem;
        }

        /* Buttons */
        .btn-sakura { 
            background: linear-gradient(to right, var(--sakura-pink), var(--sakura-dark)); 
            color: white; 
            border-radius: 12px; 
            font-weight: 700; 
            border: none; 
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(237, 100, 166, 0.3);
        }
        .btn-sakura:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(237, 100, 166, 0.4);
            color: white;
        }

        /* Table Design */
        .table thead th { 
            background-color: var(--sakura-light); 
            color: var(--sakura-dark); 
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 1px;
            padding: 15px;
            border: none;
        }
        .table tbody td { padding: 18px 15px; border-color: rgba(254, 215, 226, 0.5); }
        .table tbody tr:hover { background-color: rgba(255, 245, 245, 0.8); }

        /* Badges */
        .badge-stok { 
            font-weight: 700; 
            padding: 8px 12px; 
            border-radius: 10px;
            font-size: 0.75rem;
            display: inline-block;
        }
        .stok-aman { background: #c6f6d5; color: #22543d; border: 1px solid #9ae6b4; }
        .stok-habis { background: #fed7d7; color: #822727; border: 1px solid #feb2b2; }

        .btn-action {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            transition: 0.2s;
            color: white !important;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .main-content { animation: fadeIn 0.6s ease-out; }
    </style>
</head>
<body class="pb-5">

<nav class="navbar navbar-sakura sticky-top shadow-sm">
    <div class="container">
        <span class="navbar-brand fw-bold" style="color: var(--sakura-dark);">
            <i class="bi bi-flower1"></i> Sakura Library <span class="badge bg-danger ms-2" style="font-size: 0.6rem;">ADMIN</span>
        </span>
        <a href="index.php" class="btn btn-sm btn-outline-danger" style="border-radius: 10px;">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>
    </div>
</nav>

<div class="container main-content">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold" style="color: var(--sakura-dark);"><i class="bi bi-journal-bookmark-fill me-2"></i> Kelola Koleksi Buku</h2>
            <p class="text-muted">Manajemen data buku Digital Library Sakura</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="tambah_buku.php" class="btn btn-sakura px-4 py-2">
                <i class="bi bi-plus-circle-fill me-2"></i> Tambah Buku Baru
            </a>
        </div>
    </div>

    <div class="row mb-4 g-3 align-items-center">
        <div class="col-md-6">
            <div class="search-container shadow-sm w-100">
                <i class="bi bi-search search-icon"></i>
                <input type="text" id="searchInput" class="form-control search-input" placeholder="Cari judul, penulis, atau penerbit...">
            </div>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-inline-block p-2 px-3 bg-white rounded-4 shadow-sm border border-light">
                <span class="text-muted small">Total Koleksi:</span> 
                <span class="fw-bold text-dark ms-1"><?= mysqli_num_rows($query); ?> Buku</span>
            </div>
        </div>
    </div>

    <div class="card sakura-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="bukuTable">
                <thead>
                    <tr class="text-center">
                        <th width="5%">No</th>
                        <th class="text-start">Informasi Buku</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($query) > 0) {
                        while($row = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-sakura-light p-2 rounded-3 me-3 d-none d-sm-block" style="background: var(--sakura-light); min-width: 45px; text-align: center;">
                                        <i class="bi bi-book fs-4" style="color: var(--sakura-dark);"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($row['judul']); ?></div>
                                        <div class="small text-muted"><i class="bi bi-person me-1"></i><?= htmlspecialchars($row['penulis']); ?></div>
                                        <span class="badge bg-light text-secondary mt-1" style="font-size: 0.65rem; border: 1px solid #eee;">ID: #<?= $row['id_buku']; ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="small fw-semibold"><?= htmlspecialchars($row['penerbit']); ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border"><?= $row['tahun_terbit']; ?></span>
                            </td>
                            <td class="text-center">
                                <?php if($row['stok'] > 0): ?>
                                    <span class="badge-stok stok-aman">
                                        <i class="bi bi-check2-circle me-1"></i><?= $row['stok']; ?> unit
                                    </span>
                                <?php else: ?>
                                    <span class="badge-stok stok-habis">
                                        <i class="bi bi-x-circle me-1"></i>Habis
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="edit_buku.php?id=<?= $row['id_buku']; ?>" class="btn btn-warning btn-action shadow-sm" title="Edit Data">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="hapus_buku.php?id=<?= $row['id_buku']; ?>" 
                                       class="btn btn-danger btn-action shadow-sm" 
                                       title="Hapus Buku"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                        <i class="bi bi-trash3-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php } 
                    } else { ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-3 text-muted opacity-50"></i>
                                <span class="text-muted">Belum ada koleksi buku yang terdaftar.</span>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <footer class="text-center mt-5 text-muted small">
        <p>&copy; 2026 🌸 Sakura Digital Library - Admin Panel v2.0</p>
    </footer>
</div>

<script>
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelector('#bukuTable tbody').rows;

        for (let i = 0; i < rows.length; i++) {
            let judul = rows[i].cells[1].textContent.toLowerCase();
            let penulis = rows[i].cells[1].textContent.toLowerCase(); // Penulis sekarang ada di sel yang sama dengan judul
            let penerbit = rows[i].cells[2].textContent.toLowerCase();

            if (judul.includes(filter) || penulis.includes(filter) || penerbit.includes(filter)) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>