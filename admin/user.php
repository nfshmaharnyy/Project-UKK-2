<?php
session_start();
include '../config/koneksi.php';

// Proteksi halaman
if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("location:../auth/login.php?pesan=belum_login");
    exit();
}

// Ambil data user dari database
$query = mysqli_query($koneksi, "SELECT * FROM user ORDER BY role ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota | Digital Library Sakura</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Quicksand', sans-serif; background-color: #fffafa; color: #4a4a4a; }
        .sakura-card { 
            background: white; 
            border-radius: 25px; 
            border: none; 
            box-shadow: 0 10px 30px rgba(214, 158, 172, 0.12); 
        }
        .sakura-text { color: #b83280; font-weight: 700; }
        
        /* Table Design */
        .table thead { background-color: #fed7e2; color: #b83280; border: none; }
        .table-hover tbody tr:hover { background-color: #fff5f7; transition: 0.3s; }
        
        .avatar-circle {
            width: 40px; height: 40px; background-color: #fbb6ce; color: #b83280;
            display: flex; align-items: center; justify-content: center;
            border-radius: 50%; font-weight: 700; margin-right: 12px;
        }

        .badge-role { font-size: 0.75rem; padding: 6px 15px; border-radius: 20px; font-weight: 700; }
        .bg-admin { background-color: #ed64a6; color: white; }
        .bg-petugas { background-color: #f6ad55; color: white; }
        .bg-peminjam { background-color: #edf2f7; color: #4a5568; }

        .btn-action { border-radius: 10px; transition: 0.3s; }
    </style>
</head>
<body class="p-4">

<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="sakura-text mb-0"><i class="bi bi-people-fill me-2"></i>Data Anggota & Staff</h2>
                <ol class="breadcrumb mb-0 mt-1" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none text-muted">Admin</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manajemen User</li>
                </ol>
            </div>
            <div class="d-flex gap-2">
                <a href="../auth/register.php" class="btn btn-primary px-4 shadow-sm" style="border-radius: 12px; background-color: #ed64a6; border: none;">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Petugas
                </a>
                <a href="index.php" class="btn btn-outline-secondary px-3 shadow-sm" style="border-radius: 12px;">
                    <i class="bi bi-house-door"></i>
                </a>
            </div>
        </div>
    </nav>

    <?php if(isset($_GET['pesan'])): ?>
        <div class="alert alert-dismissible fade show shadow-sm mb-4 <?php 
            if($_GET['pesan'] == 'hapus_berhasil') echo 'alert-success';
            elseif($_GET['pesan'] == 'gagal_hapus_sendiri') echo 'alert-warning';
            else echo 'alert-danger';
        ?>" role="alert" style="border-radius: 15px; border: none;">
            
            <div class="d-flex align-items-center">
                <div class="me-2 fs-4">
                    <?php 
                    if($_GET['pesan'] == 'hapus_berhasil') echo "✨";
                    elseif($_GET['pesan'] == 'gagal_hapus_sendiri') echo "⚠️";
                    else echo "❌";
                    ?>
                </div>
                <div>
                    <?php 
                    if($_GET['pesan'] == 'hapus_berhasil') {
                        echo "<strong>Berhasil!</strong> Data pengguna telah dihapus dari sistem.";
                    } elseif($_GET['pesan'] == 'gagal_hapus_sendiri') {
                        echo "<strong>Perhatian!</strong> Anda tidak diizinkan menghapus akun sendiri demi keamanan.";
                    } else {
                        echo "<strong>Gagal!</strong> Terjadi kesalahan saat memproses permintaan.";
                    }
                    ?>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <div class="card sakura-card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="text-center">
                        <th width="5%" class="py-3">No</th>
                        <th class="text-start">Informasi Pengguna</th>
                        <th>Username</th>
                        <th>Role / Jabatan</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if(mysqli_num_rows($query) > 0):
                        while($row = mysqli_fetch_assoc($query)) { 
                            $initial = strtoupper(substr($row['nama_lengkap'], 0, 1));
                    ?>
                    <tr>
                        <td class="text-center fw-bold text-muted small"><?= $no++; ?></td>
                        <td>
                            <div class="d-flex align-items-center text-start">
                                <div class="avatar-circle"><?= $initial; ?></div>
                                <div>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_lengkap']); ?></div>
                                    <span class="text-muted" style="font-size: 0.75rem;">ID User: #<?= $row['id_user']; ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <code class="px-2 py-1 bg-light text-dark rounded"><?= htmlspecialchars($row['username']); ?></code>
                        </td>
                        <td class="text-center">
                            <?php 
                            if($row['role'] == 'admin'){
                                echo '<span class="badge-role bg-admin"><i class="bi bi-shield-lock me-1"></i> Administrator</span>';
                            } else if($row['role'] == 'petugas'){
                                echo '<span class="badge-role bg-petugas"><i class="bi bi-person-badge me-1"></i> Petugas</span>';
                            } else {
                                echo '<span class="badge-role bg-peminjam"><i class="bi bi-person me-1"></i> Peminjam</span>';
                            }
                            ?>
                        </td>
                        <td class="text-center">
                            <?php if($row['id_user'] != $_SESSION['userid']): ?>
                                <a href="hapus_user.php?id=<?= $row['id_user']; ?>" 
                                   class="btn btn-sm btn-outline-danger btn-action px-3" 
                                   onclick="return confirm('Peringatan: Menghapus user bersifat permanen. Lanjutkan?')">
                                    <i class="bi bi-trash3 me-1"></i> Hapus
                                </a>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border px-3 py-2 rounded-pill shadow-sm">
                                    <i class="bi bi-person-check-fill me-1"></i> Akun Anda
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php } 
                    else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada data anggota.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>