<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="../Pelanggan/assets/css/main.css?v=1.1" rel="stylesheet" type="text/css" />
</head>
<body>
<section class="content-main">
    <div class="card mb-4">
        <?php
        // Query untuk mengambil data penjual
        $id_penjual = isset($_GET['id_penjual']) ? $_GET['id_penjual'] : 1; // Default ke 1 jika id_penjual tidak diberikan
        $sql = "SELECT gambar, nama_toko, alamat, kota, postcode, email, no_hp, desc_toko FROM penjual WHERE id_penjual = $id_penjual LIMIT 1";
        $result = $koneksi->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            // Cek apakah gambar tersedia, jika tidak gunakan default
            $gambarPath = "../profileseller/" . htmlspecialchars($row['gambar']);
            $defaultImage = "../Pelanggan/assets/imgs/people/avatar-1.png";

            if (!file_exists($gambarPath) || empty($row['gambar'])) {
                $gambarPath = $defaultImage;
            }
        ?>
        <div class="card-header bg-brand-2" style="height: 150px"></div>
        <div class="card-body">
            <div class="row">
                <div class="col-xl col-lg flex-grow-0" style="flex-basis: 230px">
                    <div class="img-thumbnail shadow w-100 bg-white position-relative text-center" style="height: 190px; width: 200px; margin-top: -120px">
                        <img src="<?php echo $gambarPath; ?>" class="center-xy img-fluid" alt="Logo Brand" />
                    </div>
                </div>
                <div class="col-xl col-lg">
                    <h3><?php echo htmlspecialchars($row['nama_toko']); ?></h3>
                    <p><?php echo htmlspecialchars($row['desc_toko']); ?></p>
                </div>
            </div>
            <hr class="my-4" />
            <div class="row g-4">
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <h6>Contacts</h6>
                    <p>Email : <?php echo htmlspecialchars($row['email']); ?></p>
                    <p>Phone : <?php echo htmlspecialchars($row['no_hp']); ?></p>
                </div>
                <div class="col-sm-6 col-lg-4 col-xl-3">
                    <h6>Address</h6>
                    <p>City : <?php echo htmlspecialchars($row['kota']); ?></p>
                    <p>Address : <?php echo htmlspecialchars($row['alamat']); ?></p>
                    <p>Postcode : <?php echo htmlspecialchars($row['postcode']); ?></p>
                </div>
            </div>
        </div>
        <?php
        } else {
            echo "<p>Data penjual tidak ditemukan.</p>";
        }
        ?>
    </div>

    <!-- Produk -->
    <div class="container2">
        <div class="card mb-4">
            <div class="card-body">
                <h1 class="card-title mt-50">Products By Seller</h1> <br />
                <div class="row product__filter">
                    <?php
                    // Query untuk mengambil data produk berdasarkan id_penjual
                    $sqlProduk = "SELECT id_produk, nama_produk, harga, gambar FROM produk WHERE id_penjual = $id_penjual";
                    $resultProduk = $koneksi->query($sqlProduk);

                    if (!$resultProduk) {
                        echo "<p>Error in query: " . $koneksi->error . "</p>";
                    } elseif ($resultProduk->num_rows > 0) {
                        while ($produk = $resultProduk->fetch_assoc()) {
                            $gambarProdukPath = "../upload/" . htmlspecialchars($produk['gambar']);
                            $defaultProdukImage = "../Pelanggan/assets/imgs/default-product.png";

                            if (!file_exists($gambarProdukPath) || empty($produk['gambar'])) {
                                $gambarProdukPath = $defaultProdukImage;
                            }
                            ?>
                            <div class="col-lg-3 col-md-6 col-sm-6 mix new-arrivals">
                                <div class="product__item">
                                    <!-- Make the product image clickable to go to shopdetails page -->
                                    <a href="utama.php?page=shopdetails&id_produk=<?php echo $produk['id_produk']; ?>">
                                        <div class="product__item__pic" style="background-image: url('<?php echo $gambarProdukPath; ?>');">
                                            <!-- Image is set as background, no need to include <img> tag -->
                                        </div>
                                    </a>
                                    <div class="product__item__text">
                                        <h6><?php echo htmlspecialchars($produk['nama_produk']); ?></h6>
                                        <h5>Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></h5>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>Tidak ada produk ditemukan untuk penjual ini.</p>";
                    }
                    $koneksi->close();
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>
