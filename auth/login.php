<?php
session_start();
ob_start(); // Tambahkan ini di baris paling atas setelah session_start
include '../config/koneksi.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    
    if (mysqli_num_rows($query) === 1) {
        $data = mysqli_fetch_assoc($query);
        
        // Simpan data ke session
        $_SESSION['userid'] = $data['id_user'];
        $_SESSION['nama']   = $data['nama_lengkap'];
        $_SESSION['role']   = $data['role'];
        $_SESSION['status'] = "login"; // *** INI KUNCINYA: Harus ada agar dibaca oleh index.php

        // REDIRECT BERDASARKAN ROLE
        if ($data['role'] == "admin") {
            header("location:../admin/index.php");
        } else if ($data['role'] == "petugas") {
            header("location:../petugas/index.php");
        } else {
            header("location:../peminjam/index.php");
        }
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sakura Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { 
            font-family: 'Quicksand', sans-serif; 
            background: linear-gradient(135deg, #fff5f5 0%, #fed7e2 100%);
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 20px;
        }
        .login-card { 
            background: rgba(255, 255, 255, 0.9); 
            backdrop-filter: blur(10px);
            border-radius: 30px; 
            box-shadow: 0 15px 35px rgba(214, 158, 172, 0.3); 
            border: 1px solid rgba(255, 255, 255, 0.5); 
            width: 100%; 
            max-width: 420px; 
            overflow: hidden;
        }
        .card-header-sakura {
            background: linear-gradient(to right, #ed64a6, #d53f8c);
            color: white; 
            padding: 35px 20px; 
            text-align: center;
        }
        .sakura-icon { font-size: 2.8rem; display: block; margin-bottom: 5px; }
        
        .btn-sakura { 
            background: linear-gradient(to right, #ed64a6, #d53f8c); 
            border: none; 
            border-radius: 15px; 
            color: white; 
            font-weight: 700; 
            padding: 14px; 
            transition: 0.3s; 
            box-shadow: 0 4px 15px rgba(213, 63, 140, 0.3);
        }
        .btn-sakura:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 8px 20px rgba(213, 63, 140, 0.4); 
            color: white; 
        }

        .form-label { 
            color: #b83280; 
            font-weight: 700; 
            font-size: 0.9rem; 
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .form-control { 
            border-radius: 15px; 
            border: 2px solid #fed7e2; 
            padding: 12px 18px; 
            background: white;
            transition: 0.3s;
        }
        .form-control:focus { 
            border-color: #ed64a6; 
            box-shadow: 0 0 0 0.25rem rgba(237, 100, 166, 0.1); 
            background: white;
        }
        
        .text-pink { color: #d53f8c; }
        a { color: #d53f8c; text-decoration: none; font-weight: 700; transition: 0.2s; }
        a:hover { color: #b83280; text-decoration: underline; }

        /* Animasi masuk */
        .login-card {
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="card-header-sakura">
        <span class="sakura-icon">🌸</span>
        <h3 class="mb-1 fw-bold">Sakura Library</h3>
        <p class="small mb-0 opacity-75">Silakan masuk untuk akses koleksi digital</p>
    </div>

    <div class="p-4 p-md-5">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 small d-flex align-items-center">
                <i class="bi bi-exclamation-circle-fill me-2"></i> <?= $error; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label"><i class="bi bi-person-heart"></i> Username</label>
                <input type="text" name="username" class="form-control shadow-none" placeholder="Username Anda" required autofocus>
            </div>

            <div class="mb-4">
                <label class="form-label"><i class="bi bi-shield-lock-fill"></i> Password</label>
                <input type="password" name="password" class="form-control shadow-none" placeholder="••••••••" required>
            </div>

            <button type="submit" name="login" class="btn btn-sakura w-100 mb-3">
                Masuk Ke Panel <i class="bi bi-arrow-right-short fs-5"></i>
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="small text-muted mb-0">Belum memiliki akun? <br> 
                <a href="daftar_peminjam.php" class="mt-2 d-inline-block">Daftar Peminjam Baru</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>