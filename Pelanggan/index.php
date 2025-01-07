<?php
include 'config.php';
session_start();
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
    <!-- Header -->
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
                            <li><a href="../Penjual/signup.php">Join Seller Now!</a></li>
                        </ul>
                    </nav>
                </div>
                <div class="col-lg-3 col-md-3">
                    <div class="header__nav__option">
                        <a href="utama.php?page=cart">
                            <img src="../picture/cart.png" alt="Cart" style="width: 30px; height: 30px;">
                        </a>
                        <div class="profile-dropdown">
                            <a href="#">
                                <img src="../picture/profile.png" alt="Profile" style="width: 30px; height: 30px;">
                            </a>
                            <ul class="dropdown">
                                <?php if (isset($_SESSION['email'])): ?>
                                    <!-- Menu untuk pengguna yang sudah login -->
                                    <li><a href="utama.php?page=profile">Profile</a></li>
                                    <li><a href="utama.php?page=orderlist">Order History</a></li>
                                    <li><a href="utama.php?page=faq">FAQ</a></li>
                                <?php else: ?>
                                    <!-- Menu untuk pengguna yang belum login -->
                                    <li><a href="login.php">Log In</a></li>
                                    <li><a href="signup.php">Register</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="canvas__open"><i class="fa fa-bars"></i></div>
        </div>
    </header>
   <section class="hero">
        <div class="hero__slider owl-carousel">
            <div class="hero__items set-bg" data-setbg="../img/hero/hero-1.jpg">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                <h6>Summer Collection</h6>
                                <h2>Fall - Winter Collections 2030</h2>
                                <p>A specialist label creating luxury essentials. Ethically crafted with an unwavering
                                commitment to exceptional quality.</p>
                                <a href="#" class="primary-btn">Shop now <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="hero__items set-bg" data-setbg="../img/hero/hero-2.jpg">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-5 col-lg-7 col-md-8">
                            <div class="hero__text">
                                <h6>Summer Collection</h6>
                                <h2>Fall - Winter Collections 2030</h2>
                                <p>A specialist label creating luxury essentials. Ethically crafted with an unwavering
                                commitment to exceptional quality.</p>
                                <a href="#" class="primary-btn">Shop now <span class="arrow_right"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Product Section Begin -->
    <section class="product spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-mt-100">
                <ul class="filter__controls mt-5">
                    <li class="active" data-filter="*">FEATURED PRODUCTS</li>
                </ul>
            </div>
        </div>

        <div class="row product__filter mt-4">
            <?php
            // Query untuk mengambil data produk
            $sql = "SELECT id_produk, nama_produk, harga, gambar FROM produk ORDER BY id_produk DESC LIMIT 12";
            $result = $koneksi->query($sql);

            // Cek jika ada produk
            if ($result->num_rows > 0) {
                // Loop setiap produk
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="col-lg-3 col-md-6 col-sm-6 mix <?php echo $categoryClass; ?>">
                        <div class="product__item">
                            <a href="utama.php?page=shopdetails&id_produk=<?php echo $row['id_produk']; ?>">
                                <div class="product__item__pic" style='background-image: url("../upload/<?php echo $row["gambar"]; ?>");'>
                                    <span class="label">New</span>
                                </div>
                            </a>
                            <div class="product__item__text">
                                <h6><?php echo $row['nama_produk']; ?></h6>
                                <h5>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></h5>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p>Tidak ada produk ditemukan.</p>";
            }

            // Tutup koneksi (opsional)
            $koneksi->close();
            ?>
        </div>
    </div>
    <div class="col-lg-12 text-center mt-4">
                <!-- See More Button -->
                <a href="utama.php?page=shop" class="btn btn-primary btn-lg">
                    See More
                </a>
    </div>
</section>
    <!-- Product Section End -->
<!-- Categories Section Begin -->
<section class="categories spad">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <h2>Only at the Official Store</h2>
                <p>Discover a wide range of products from Beauty and Fashion to Electronics—all available at Unformal, the trusted e-commerce platform offering quality products to meet your every need!</p>
            </div>
        </div>
        <div class="row text-center categories__icons">
            <div class="col-lg-3 col-md-6">
                <div class="icon-box">
                    <img src="../picture/centang.png" alt="Icon 1" class="icon-img">
                    <h4>Guaranteed Comfort</h4>
                    <p>Shop with ease, assured of authentic products. Every item on Unformal is carefully selected for quality you can trust.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="icon-box">
                    <img src="../picture/offer.png" alt="Icon 2" class="icon-img">
                    <h4>Exclusive Deals</h4>
                    <p>Enjoy special offers across various product categories. Shop smarter and find exactly what you’re looking for at great prices!</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="icon-box">
                    <img src="../picture/trusted.png" alt="Icon 3" class="icon-img">
                    <h4>Trusted Seller</h4>
                    <p>Shop confidently on a reliable e-commerce platform. Product quality and transaction security are our top priorities.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="icon-box">
                    <img src="../picture/unfor.png" alt="Icon 4" class="icon-img">
                    <h4>Original and Quality Products</h4>
                    <p>Count on Unformal as your source for authentic products. We offer only the finest selections to fulfill your needs.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Categories Section End -->

               <!-- Footer Section Begin -->
               <footer class="footer" style="padding: 0px 0;">
        <div class="container">
            <div class="col-lg-12 text-center">
                <div class="footer__copyright__text">
                    <p>Copyright © UNFORMAL
                        <script>
                            document.write(new Date().getFullYear());
                        </script>
                        All rights reserved <i aria-hidden="true"></i>
                    </p>
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
