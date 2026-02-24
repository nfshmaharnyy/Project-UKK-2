<?php 
session_start();
include '../config/koneksi.php';

// Proteksi Halaman: Petugas & Admin
if(!isset($_SESSION['role']) || ($_SESSION['role'] != "petugas" && $_SESSION['role'] != "admin")){
    header("location:../auth/login.php?pesan=belum_login");
    exit();
}

// Menangkap data filter
$tgl_mulai = isset($_GET['tgl_mulai']) ? $_GET['tgl_mulai'] : "";
$tgl_selesai = isset($_GET['tgl_selesai']) ? $_GET['tgl_selesai'] : "";

// Query SQL JOIN
$sql = "SELECT peminjaman.*, user.nama_lengkap, buku.judul 
        FROM peminjaman 
        INNER JOIN user ON peminjaman.id_user = user.id_user 
        INNER JOIN buku ON peminjaman.id_buku = buku.id_buku";

if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
    $start = mysqli_real_escape_string($koneksi, $tgl_mulai);
    $end = mysqli_real_escape_string($koneksi, $tgl_selesai);
    $sql .= " WHERE tanggal_peminjaman BETWEEN '$start' AND '$end'";
}

$sql .= " ORDER BY peminjaman.id_peminjaman DESC";
$query = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman (Petugas) | Sakura Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { 
            font-family: 'Quicksand', sans-serif; 
            background: radial-gradient(circle at top right, #fffafa 0%, #fed7e2 100%);
            min-height: 100vh;
        }

        /* Card Styling - Meniru Admin Style */
        .sakura-card { 
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: blur(10px);
            border-radius: 25px; 
            border: 2px solid white; 
            box-shadow: 0 10px 30px rgba(214, 158, 172, 0.15); 
        }

        .sakura-text { color: #b83280; font-weight: 700; }
        .text-pink { color: #ed64a6; }
        
        /* Table Styling */
        .table { border-radius: 15px; overflow: hidden; }
        .table thead { 
            background: linear-gradient(to right, #ed64a6, #d53f8c);
            color: white; 
        }
        .table thead th { border: none; padding: 15px; }
        
        /* Button Styling */
        .btn-sakura { 
            background: #ed64a6; 
            color: white; 
            border-radius: 12px; 
            font-weight: 700; 
            border: none; 
            transition: 0.3s;
        }
        .btn-sakura:hover { background: #d53f8c; color: white; transform: translateY(-2px); }
        
        .btn-print {
            background: #4a5568;
            color: white;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            transition: 0.3s;
        }
        .btn-print:hover { background: #2d3748; color: white; }

        /* Form Control */
        .form-control {
            border-radius: 12px;
            border: 2px solid #fed7e2;
            padding: 10px 15px;
        }
        .form-control:focus {
            border-color: #ed64a6;
            box-shadow: 0 0 0 0.25rem rgba(237, 100, 166, 0.1);
        }

        /* Badge Custom */
        .badge-dipinjam { background-color: #fffaf0; color: #975a16; border: 1px solid #fbd38d; }
        .badge-kembali { background-color: #f0fff4; color: #276749; border: 1px solid #9ae6b4; }

        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .sakura-card { box-shadow: none !important; border: 1px solid #eee !important; width: 100% !important; }
            .table thead { background: #f8f9fa !important; color: black !important; border-bottom: 2px solid black !important; }
            th, td { border: 1px solid #dee2e6 !important; color: black !important; }
            .sakura-text { color: black !important; }
        }
    </style>
</head>
<body class="p-2 p-md-4">

<div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 no-print">
        <h2 class="sakura-text mb-3 mb-md-0"><i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Laporan Peminjaman (Petugas)</h2>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-print px-4">
                <i class="bi bi-printer-fill me-2"></i>Cetak Laporan
            </button>
            <a href="index.php" class="btn btn-outline-secondary px-4" style="border-radius: 12px;">
                <i class="bi bi-house-door-fill me-2"></i>Beranda
            </a>
        </div>
    </div>

    <div class="card sakura-card p-4 mb-4 no-print">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted">DARI TANGGAL</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-event text-pink"></i></span>
                    <input type="date" name="tgl_mulai" class="form-control border-start-0" value="<?= htmlspecialchars($tgl_mulai) ?>">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted">SAMPAI TANGGAL</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar-check text-pink"></i></span>
                    <input type="date" name="tgl_selesai" class="form-control border-start-0" value="<?= htmlspecialchars($tgl_selesai) ?>">
                </div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-sakura w-100 p-2 py-2.5">
                    <i class="bi bi-filter-circle me-2"></i>Filter Laporan
                </button>
            </div>
        </form>
    </div>

    <div class="card sakura-card p-4 shadow-sm">
        <div class="text-center mb-5">
            <h3 class="sakura-text mb-0 text-uppercase">Rekapitulasi Transaksi Buku</h3>
            <p class="text-muted small">Sakura Digital Library - Petugas Panel</p>
            <div class="d-inline-block px-4 py-2 rounded-pill bg-light border mt-2">
                <?php if(!empty($tgl_mulai)): ?>
                    <span class="small fw-bold text-dark">Periode: <span class="text-pink"><?= date('d/m/Y', strtotime($tgl_mulai)) ?></span> - <span class="text-pink"><?= date('d/m/Y', strtotime($tgl_selesai)) ?></span></span>
                <?php else: ?>
                    <span class="small text-muted italic">Menampilkan Semua Transaksi</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-center">
                        <th width="5%">NO</th>
                        <th>PEMINJAM</th>
                        <th>JUDUL BUKU</th>
                        <th>TGL PINJAM</th>
                        <th>TGL KEMBALI</th>
                        <th>STATUS</th>
                        <th class="no-print">AKSI</th> 
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($query) > 0):
                        while($row = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?= $no++; ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-pink-light p-2 me-2 d-none d-md-block" style="background: #fff5f7;">
                                        <i class="bi bi-person-badge text-pink"></i>
                                    </div>
                                    <div>
                                        <strong><?= htmlspecialchars($row['nama_lengkap']); ?></strong>
                                    </div>
                                </div>
                            </td>
                            <td><i class="bi bi-book me-2 text-muted"></i><?= htmlspecialchars($row['judul']); ?></td>
                            <td class="text-center"><?= date('d/m/Y', strtotime($row['tanggal_peminjaman'])); ?></td>
                            <td class="text-center">
                                <?php if ($row['tanggal_pengembalian'] != '0000-00-00' && !empty($row['tanggal_pengembalian'])): ?>
                                    <span class="text-dark"><?= date('d/m/Y', strtotime($row['tanggal_pengembalian'])); ?></span>
                                <?php else: ?>
                                    <span class="text-muted italic small">Belum Kembali</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($row['status_peminjaman'] == 'Dipinjam'): ?>
                                    <span class="badge badge-dipinjam px-3 py-2 rounded-pill">Dipinjam</span>
                                <?php else: ?>
                                    <span class="badge badge-kembali px-3 py-2 rounded-pill">Dikembalikan</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center no-print"> 
                                <?php if($row['status_peminjaman'] == 'Dipinjam'): ?>
                                    <a href="proses_kembali.php?id=<?= $row['id_peminjaman']; ?>" 
                                       class="btn btn-sm btn-sakura px-3" 
                                       onclick="return confirm('Konfirmasi pengembalian buku ini?')">
                                       Konfirmasi
                                    </a>
                                <?php else: ?>
                                    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill"><i class="bi bi-check-all me-1"></i>Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } 
                    else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                Tidak ada data transaksi ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-5 d-none d-print-block">
            <div class="row">
                <div class="col-8"></div>
                <div class="col-4 text-center">
                    <p class="mb-1">Makassar, <?= date('d F Y') ?></p>
                    <p>Petugas Perpustakaan,</p>
                    <div style="height: 80px;"></div>
                    <p class="mb-0"><strong>( <?= htmlspecialchars($_SESSION['nama'] ?? 'Petugas'); ?> )</strong></p>
                    <p class="small text-muted">Sakura Digital Library</p>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-muted small no-print">
    &copy; 2026 🌸 Sakura Digital Library | <span class="sakura-text">Petugas Dashboard</span>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>