<?php 
include "config.php";

if(isset($_POST['signup'])) { 
    $username = $_POST['username']; // Ambil username dari form
    $email = $_POST['email']; 
    $nohp = $_POST['no_hp']; 
    $password = md5($_POST['password']); 

    // Cek apakah username atau email sudah ada
    $cek = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE username='$username' OR email='$email'");
    
    if (mysqli_num_rows($cek) == 0) {
        // Jika tidak ada, masukkan data baru
        $query = "INSERT INTO pelanggan (username, email, no_hp, password) VALUES ('$username', '$email', '$nohp', '$password')";
        
        // Menjalankan query
        if(mysqli_query($koneksi, $query)) {
            echo "<script>alert('Pendaftaran berhasil! Silakan login.'); window.location.href='index.php';</script>";
        } else {
            echo "<script>alert('Pendaftaran gagal!'); window.location.href='signup.php';</script>";
        }
    } else {
        echo "<script>alert('Username atau Email sudah terdaftar!'); window.location.href='signup.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #d9d9d9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        .signup-container {
            padding: 2rem;
            width: 100%;
            max-width: 420px;
            font-family: 'Poppins', sans-serif;
        }
        .form-control {
            padding: 0.6rem 1.5rem;
            background-color: white;
            border-radius: 0; /* Remove rounded corners */
            transition: box-shadow 0.3s ease;
            border: 1px solid #ccc; /* Optional: adds a border to match the style */
        }

        .form-control:focus {
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.6);
            outline: none;
            border-color: #007bff; /* Optional: highlight border color on focus */
        }
        .btn-custom {
            background-color: black;
            color: white;
            padding: 0.6rem 1.5rem;
            width: 100%;
            border-radius: 0;
            transition: background-color 0.3s, transform 0.3s;
        }
        .btn-custom:hover {
            background-color: black;
            color: white;
            transform: scale(1.05);
        }
        .form-check {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Sign up link styling */
        .text-center p a {
            color: #007bff;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .text-center p a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>

<div class="signup-container">
    <h2 style="font-weight: bold; text-align: center; margin-bottom: 0.5rem;">CREATE ACCOUNT</h2>
    <hr style="border: 2px solid black; width: 50%; margin: 0.5rem auto;">
    <br/>
    <form action="" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" required class="form-control" placeholder="Enter your username">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" required class="form-control" placeholder="Enter your email">
        </div>
        <div class="mb-3">
            <label for="no_hp" class="form-label">Nomor Handphone</label>
            <input type="text" name="no_hp" required class="form-control" placeholder="Enter your phone number">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" required class="form-control" placeholder="Enter your password">
        </div>
        <br/>
        <div class="mb-3 text-center">
            <input type="submit" name="signup" class="btn btn-custom" value="Sign Up">
        </div>
        <div class="text-center">
            <p>Already have an account? <a href="login.php">Sign In</a></p>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
