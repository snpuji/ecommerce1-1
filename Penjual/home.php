<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header('location:index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseller Home</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #e0e4eb9c;
        }
        /* Sidebar */
        .sidebar {
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background-color: #556b8c;
            color: white;
            width: 250px;
            padding-top: 20px;
            border-right: 3px solid black;
        }

        .sidebar a {
            color: white;
            display: flex;
            align-items: center;
            padding: 10px;
            text-decoration: none;
            transition: 0.3s;
        }

        .sidebar a:hover {
            color: white;
            background-color: #495057;
            text-decoration: none;
        }

        .sidebar .emoji {
            margin-right: 10px; /* Spacing between emoji and text */
        }

        /* Navbar */
        .navbar {
            color: white;
            background-color: #556b8c;
            position: fixed;
            margin-left: 250px;
            width: calc(100% - 250px);
        }
        /* Dashboard styling */
        .dashboard {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .text-center {
            color: white;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-center">Menu</h4>
        <a href="#dashboard"><span class="emoji">📊</span>Dashboard</a>
        <a href="#reference" data-toggle="collapse"><span class="emoji">📚</span>Referensi</a>
        <div id="reference" class="collapse">
            <a href="#rekening" class="pl-3"><span class="emoji">🏦</span>No Rekening Anda</a>
            <a href="#info" class="pl-3"><span class="emoji">ℹ</span>Info/Keterangan</a>
            <a href="#data_produk" class="pl-3"><span class="emoji">📦</span>Data Produk Anda</a>
        </div>
        <a href="#transaction" data-toggle="collapse"><span class="emoji">💼</span>Transaksi</a>
        <div id="transaction" class="collapse">
            <a href="#penjualan" class="pl-3"><span class="emoji">🛒</span>Penjualan ke Konsumen</a>
            <a href="#pembayaran" class="pl-3"><span class="emoji">💰</span>Pembayaran ke Konsumen</a>
        </div>
        <a href="#report" data-toggle="collapse"><span class="emoji">📈</span>Laporan</a>
        <div id="report" class="collapse">
            <a href="#data_keuangan" class="pl-3"><span class="emoji">📊</span>Data Keuangan</a>
        </div>
        <a href="logout.php"><span class="emoji">🚪</span>Logout</a>
    </div>

    <!-- Navbar -->
    <nav class="navbar">
        <span class="navbar-brand mx-auto">Seller</span>
    </nav>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>