<?php
include "config.php";
session_start();
if (!isset($_SESSION['username'])) {
    header('location:index.php');
    exit(); // Penutup yang benar
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>UNFORMAL</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:title" content="" />
    <meta property="og:type" content="" />
    <meta property="og:url" content="" />
    <meta property="og:image" content="" />
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="../picture/black.png" />
    <!-- Template CSS -->
    <link href="../assets/css/main.css?v=1.1" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="screen-overlay"></div>
    <aside class="navbar-aside" id="offcanvas_aside">
        <div class="aside-top">
            <a href="dashboard.php" class="brand-wrap">
                <img src="../picture/black.png" class="logo" alt="Unformal" />
            </a>
            <div>
                <button class="btn btn-icon btn-aside-minimize"><i class="text-muted material-icons md-menu_open"></i></button>
            </div>
        </div>
        <nav>
            <ul class="menu-aside">
                <li class="menu-item">
                    <a class="menu-link" href="utama.php?page=dashboard">
                        <i class="icon material-icons md-home"></i>
                        <span class="text">Dashboard</span>
                    </a>
                </li>
                <li class="menu-item has-submenu">
                        <a class="menu-link" href="utama.php?page=product">
                            <i class="icon material-icons md-shopping_bag"></i>
                            <span class="text">Products</span>
                        </a>
                        <div class="submenu">
                            <a href="utama.php?page=product">Product</a>
                            <a href="utama.php?page=categories">Categories</a>
                        </div>
                    </li>
                    <li class="menu-item has-submenu">
                        <a class="menu-link" href="utama.php?page=buyer">
                            <i class="icon material-icons md-person"></i>
                            <span class="text">User</span>
                        </a>
                        <div class="submenu">
                            <a href="utama.php?page=buyer">Buyer</a>
                            <a href="utama.php?page=seller">Seller</a>
                    </div>
                    </li>
                    <li class="menu-item">
                    <a class="menu-link" href="utama.php?page=order">
                        <i class="icon material-icons md-shopping_cart"></i>
                        <span class="text">Orders</span>
                    </a>
                </li>
                </li>
                    <li class="menu-item">
                    <a class="menu-link" href="utama.php?page=pengiriman">
                        <i class="icon material-icons md-add_box"></i>
                        <span class="text">Shipping</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a class="menu-link" href="utama.php?page=transaksi">
                        <i class="icon material-icons md-monetization_on"></i>
                        <span class="text">Transaction</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a class="menu-link" href="utama.php?page=ulasan">
                        <i class="icon material-icons md-comment"></i>
                        <span class="text">Reviews</span>
                    </a>
                </li>
                </ul>
        </nav>
    </aside>
    <main class="main-wrap">
        <header class="main-header navbar">
            <div class="container-fluid d-flex justify-content-end">
                <button class="btn btn-icon btn-mobile me-auto" data-trigger="#offcanvas_aside"><i class="material-icons md-apps"></i></button>
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link btn-icon" href="#">
                            <i class="material-icons md-notifications animation-shake"></i>
                            <span class="badge rounded-pill">3</span>
                        </a>
                    </li>
                    <li class="dropdown nav-item">
                        <a class="dropdown-toggle" data-bs-toggle="dropdown" href="#" id="dropdownAccount" aria-expanded="false">
                            <img class="img-xs rounded-circle" src="../profileseller/<?php echo $gambar; ?>" alt="User" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownAccount">
                            <a class="dropdown-item" href="utama.php?page=profile"><i class="material-icons md-perm_identity"></i>Profile</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="logout.php"><i class="material-icons md-exit_to_app"></i>Logout</a>
                        </div>
                    </li>
                </ul>
            </div>
        </header>

        <!-- Bagian untuk menampilkan konten halaman -->
        <section class="content">
            <?php 
            if (isset($_GET['page'])) {
                if ($_GET['page'] == 'home') { 
                    include "home.php"; 
                } elseif ($_GET['page'] == 'logout') { 
                    include "logout.php"; 
                } elseif ($_GET['page'] == 'dashboard') { 
                    include "dashboard.php"; 
                } elseif ($_GET['page'] == 'categories') { 
                    include "categories.php"; 
                } elseif ($_GET['page'] == 'addcategories') { 
                    include "addcategories.php"; 
                } elseif ($_GET['page'] == 'editcategories') { 
                    include "editcategories.php"; 
                } elseif ($_GET['page'] == 'deletecategories') { 
                    include "deletecategories.php"; 
                } elseif ($_GET['page'] == 'product') { 
                    include "product.php";
                } elseif ($_GET['page'] == 'editproduct') { 
                    include "editproduct.php";  
                } elseif ($_GET['page'] == 'deleteproduct') { 
                    include "deleteproduct.php"; 
                } elseif ($_GET['page'] == 'addproduct') { 
                    include "addproduct.php"; 
                } elseif ($_GET['page'] == 'buyer') { 
                    include "buyer.php";
                } elseif ($_GET['page'] == 'seller') { 
                    include "seller.php";
                } elseif ($_GET['page'] == 'sellerdetail') { 
                    include "sellerdetail.php"; 
                } elseif ($_GET['page'] == 'order') { 
                    include "order.php";  
                } elseif ($_GET['page'] == 'orderdetail') { 
                    include "orderdetail.php";
                } elseif ($_GET['page'] == 'ulasan') { 
                    include "ulasan.php";
                } elseif ($_GET['page'] == 'pengiriman') { 
                    include "pengiriman.php";
                } elseif ($_GET['page'] == 'pengirimandetail') { 
                    include "pengirimandetail.php";
                } elseif ($_GET['page'] == 'transaksi') { 
                    include "transaksi.php";
                } elseif ($_GET['page'] == 'transaksidetail') { 
                    include "transaksidetail.php";
                } elseif ($_GET['page'] == 'ulasan') { 
                    include "ulasan.php";
                }
                else {
                    include "index.php"; // Jika page tidak ditemukan, tampilkan index.php
                }
            } else {
                include "index.php"; // Jika tidak ada page yang dipilih, tampilkan index.php
            }
            ?> 
        </section>
        
    </main>

    <!-- JavaScript Files -->
    <script src="../assets/js/vendors/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/vendors/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/vendors/select2.min.js"></script>
    <script src="../assets/js/vendors/perfect-scrollbar.js"></script>
    <script src="../assets/js/vendors/jquery.fullscreen.min.js"></script>
    <script src="../assets/js/vendors/chart.js"></script>
    <script src="../assets/js/main.js?v=1.1"></script>
    <script src="../assets/js/custom-chart.js"></script>
</body>
</html>
