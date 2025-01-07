<?php
include "config.php";

if (isset($_POST['submit'])) { 
    $email = $_POST['email']; 
    $password = md5($_POST['password']); 

    $login = mysqli_query($koneksi, "SELECT * FROM pelanggan WHERE email='$email' AND password='$password'");
    $hasil = mysqli_num_rows($login); 
    $r = mysqli_fetch_array($login); 
  
    if ($hasil > 0) { 
        session_start();
        $_SESSION['email'] = $r['email'];  
        header('Location: index.php'); // Arahkan ke halaman utama setelah login
        exit();
    } else { 
        echo ("<script>alert('email atau Password SALAH!'); window.location.href='login.php';</script>");
    } 
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">  
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        .login-container {
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

<div class="login-container">
    <h2 style="font-weight: bold; text-align: center; margin-bottom: 0.5rem;">SIGN IN</h2>
    <hr style="border: 2px solid black; width: 50%; margin: 0.5rem auto;">
    <br/>
    <form action="login.php" method="POST">
        <!-- email dan Password mb (margin botton) -->
        <div class="mb-3"> 
            <label for="email" class="form-label">Email</label>
            <input type="text" name="email" required="required" class="form-control" placeholder="Enter your email">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" required="required" class="form-control" placeholder="Enter your password">
        </div>

        <!--Lupa Password -->
        <div class="text-end mb-3">
            <a href="forgot_password.php" class="font-sm text-muted">Forget Password?</a>
        </div>
        
        <!-- Tombol Login -->
        <div class="mb-3 text-center">
            <input type="submit" name="submit" class="btn btn-custom" value="Sign In">
        </div>
        
        <!-- Tautan Daftar -->
        <div class="text-center">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>