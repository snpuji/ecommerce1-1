<?php
    include "config.php";
        session_start();
            if (!isset($_SESSION['email'])) {
                header('location:login.php');
            exit();
        }
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="Male_Fashion Template">
    <meta name="keywords" content="Male_Fashion, unica, creative, html">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>UNFORMAL</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="../css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="../css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="../css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="../css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="../css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="../css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="../css/style.css" type="text/css">
</head>

<body>
    <!-- Header Section Begin -->
    <header class="header">
        <div class="container2">
            <div class="row">
                <div class="col-lg-3 col-md-3">
                    <div class="header__logo">
                        <a href="./index.php"><img src="../picture/black.png" alt="Logo"></a>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <nav class="header__menu mobile-menu">
                        <ul>
                            <li class="active"><a href="./index.php">Home</a></li>
                            <li><a href="utama.php?page=shop">Shop</a></li>
                            <li><a href="utama.php?page=sellers">Sellers</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-3 col-md-3">
                    <div class="header__nav__option">
                        <a href="utama.php?page=cart">
                            <img src="../picture/cart.png" alt="Cart" style="width: 30px; height: 30px;">
                            <span></span>
                        </a>
                        <div class="profile-dropdown">
                            <a href="#">
                                <img src="../picture/profile.png" alt="Profile" style="width: 30px; height: 30px;">
                            </a>
                            <ul class="dropdown">
                                <li><a href="utama.php?page=profile">Profile</a></li>
                                <li><a href="utama.php?page=orderlist">Order History</a></li>
                                <li><a href="utama.php?page=faq">FAQ</a></li>
                                <li><a href="../Penjual/signup.php">Join Seller Now!</a></li>
                                <li><a href="logout.php">Log Out</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="canvas__open"><i class="fa fa-bars"></i></div>
        </div>
    </header>
    <!-- Header Section End -->

    <!-- Content Section Begin -->
    <section>
        <?php 
            if (isset($_GET['page'])) {
                if ($_GET['page'] == 'index') { 
                    include "index.php";  
                } elseif ($_GET['page'] == 'purchase') { 
                    include "purchase.php";
                } elseif ($_GET['page'] == 'shop') { 
                    include "shop.php";
                } elseif ($_GET['page'] == 'shopdetails') { 
                    include "shopdetails.php";
                } elseif ($_GET['page'] == 'cart') { 
                    include "cart.php";
                }  elseif ($_GET['page'] == 'sellers') { 
                    include "sellers.php";
                } elseif ($_GET['page'] == 'sellerdetails') { 
                    include "sellerdetails.php";
                } elseif ($_GET['page'] == 'profile') { 
                    include "profile.php";
                } elseif ($_GET['page'] == 'editprofile') { 
                    include "editprofile.php";
                } elseif ($_GET['page'] == 'orderlist') { 
                    include "orderlist.php";
                } elseif ($_GET['page'] == 'orderdetail') { 
                    include "orderdetail.php";
                } elseif ($_GET['page'] == 'custom') { 
                    include "custom.php";
                } elseif ($_GET['page'] == 'faq') { 
                    include "faq.php";
                } elseif ($_GET['page'] == 'about') { 
                    include "about.php";
                }else {
                    echo "<p>Halaman tidak ditemukan.</p>";
                }
            } else {
                include "login.php";  // Default ke login jika tidak ada parameter page
            }
        ?> 
    </section>
    <!-- Content Section End -->
           
    <!-- Footer Section Begin -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="footer__about">
                        <div class="footer__logo">
                            <a href="#"><img src="../picture/white.png" alt=""></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 col-sm-6">
                    <div class="footer__widget">
                        <h6>Quick Link</h6>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="utama.php?page=shop">Shop</a></li>
                            <li><a href="utama.php?page=custom">Custom</a></li>
                            <li><a href="utama.php?page=about">About Us</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="footer__widget">
                        <h6>Unformal Homestore</h6>
                        <div class="footer__newslatter">
                            <p>Jl. Cibogo Blok Balimbing No.42, Sukawarna, Kec.Sukajadi, Kota Bandung, Jawa Barat 40164</p>
                            <div class="footer__social">
                                <a href="https://wa.me/nomor-anda" target="_blank">
                                    <img src="../picture/wa.png" alt="WhatsApp" style="width:24px; margin-right:8px;">
                                </a>
                                <a href="https://www.instagram.com/akun-anda" target="_blank">
                                    <img src="../picture/ig.png" alt="Instagram" style="width:24px; margin-right:8px;">
                                </a>
                                <a href="https://www.youtube.com/channel/akun-anda" target="_blank">
                                    <img src="../picture/yt.png" alt="YouTube" style="width:24px; margin-right:8px;">
                                </a>
                                <a href="https://www.tiktok.com/@akun-anda" target="_blank">
                                    <img src="../picture/tiktok.png" alt="TikTok" style="width:24px; margin-right:8px;">
                                </a>
                                <a href="mailto:email-anda@example.com">
                                    <img src="../picture/email.png" alt="Email" style="width:24px; margin-right:8px;">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="footer__copyright__text">
                        <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                        <p>Copyright © UNFORMAL
                            <script>
                                document.write(new Date().getFullYear());
                            </script>
                            All rights reserved <i aria-hidden="true"></i>
                        </p>
                        <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section End -->

    <!-- Search Begin -->
    <div class="search-model">
        <div class="h-100 d-flex align-items-center justify-content-center">
            <div class="search-close-switch">+</div>
            <form class="search-model-form">
                <input type="text" id="search-input" placeholder="Search here.....">
            </form>
        </div>
    </div>
    <!-- Search End -->

    <!-- Js Plugins -->
    <script src="../js/jquery-3.3.1.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.nice-select.min.js"></script>
    <script src="../js/jquery.nicescroll.min.js"></script>
    <script src="../js/jquery.magnific-popup.min.js"></script>
    <script src="../js/jquery.countdown.min.js"></script>
    <script src="../js/jquery.slicknav.js"></script>
    <script src="../js/mixitup.min.js"></script>
    <script src="../js/owl.carousel.min.js"></script>
    <script src="../js/main.js"></script>
</body>

</html>
