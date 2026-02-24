<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
    echo "<script>alert('Hanya Admin!'); location.href='login.php';</script>";
    exit;
}

if (isset($_POST['daftar'])) {
    $username     = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password     = mysqli_real_escape_string($koneksi, $_POST['password']);
    $email        = mysqli_real_escape_string($koneksi, $_POST['email']);
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $alamat       = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $role         = $_POST['role']; 

    $cek_user = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
    if (mysqli_num_rows($cek_user) > 0) {
        $error = "Username sudah terdaftar!";
    } else {
        $insert = mysqli_query($koneksi, "INSERT INTO user (username, password, email, nama_lengkap, alamat, role) 
                                          VALUES ('$username', '$password', '$email', '$nama_lengkap', '$alamat', '$role')");
        if ($insert) {
            echo "<script>alert('User berhasil ditambahkan!'); location.href='../admin/user.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User | Sakura Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { 
            font-family: 'Quicksand', sans-serif; 
            background: linear-gradient(135deg, #fff5f5 0%, #fed7e2 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;
        }
        .register-card { 
            background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);
            border-radius: 30px; box-shadow: 0 15px 35px rgba(214, 158, 172, 0.3); 
            border: 1px solid rgba(255, 255, 255, 0.5); width: 100%; max-width: 550px; overflow: hidden;
        }
        .card-header-sakura {
            background: linear-gradient(to right, #ed64a6, #d53f8c);
            color: white; padding: 30px; text-align: center;
        }
        .sakura-icon { font-size: 2.5rem; display: block; margin-bottom: 10px; }
        .btn-sakura { 
            background: linear-gradient(to right, #ed64a6, #d53f8c); border: none; 
            border-radius: 15px; color: white; font-weight: 700; padding: 14px; 
            transition: 0.3s; box-shadow: 0 4px 15px rgba(213, 63, 140, 0.3);
        }
        .btn-sakura:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(213, 63, 140, 0.4); color: white; }
        .form-label { color: #b83280; font-weight: 700; font-size: 0.85rem; margin-left: 5px; }
        .form-control, .form-select { border-radius: 15px; border: 2px solid #fed7e2; padding: 12px 15px; }
        a { color: #d53f8c; text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>

<div class="register-card">
    <div class="card-header-sakura">
        <span class="sakura-icon">🌸</span>
        <h3 class="mb-0 fw-bold">Tambah User Baru</h3>
        <p class="small mb-0 opacity-75">Registrasi Petugas atau Peminjam Baru</p>
    </div>

    <div class="p-4 p-md-5">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 small d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $error; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="bi bi-person-fill"></i> Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="bi bi-lock-fill"></i> Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label"><i class="bi bi-shield-lock-fill"></i> Pilih Role</label>
                <select name="role" class="form-select shadow-none" required>
                    <option value="petugas">Petugas (Staff)</option>
                    <option value="peminjam">Peminjam (Member)</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="bi bi-person-vcard-fill"></i> Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="bi bi-envelope-at-fill"></i> Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-4">
                <label class="form-label"><i class="bi bi-geo-alt-fill"></i> Alamat</label>
                <textarea name="alamat" class="form-control" rows="2"></textarea>
            </div>

            <button type="submit" name="daftar" class="btn btn-sakura w-100">
                Simpan User Baru <i class="bi bi-save ms-1"></i>
            </button>
            <a href="../admin/user.php" class="btn btn-link w-100 mt-2 text-secondary">Kembali</a>
        </form>
    </div>
</div>

</body>
</html>