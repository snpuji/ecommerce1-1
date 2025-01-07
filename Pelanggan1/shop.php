<?php
include 'config.php';
?>


<!-- Shop Section Begin -->
<section class="shop spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="shop__sidebar">
                    <!-- Form Pencarian -->
                    <div class="shop__sidebar__search">
                        <form action="utama.php?page=shop" method="GET">
                            <input type="hidden" name="page" value="shop">
                            <input type="text" name="search" placeholder="Search..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                            <button type="submit"><span class="icon_search"></span></button>
                        </form>
                    </div>
                    <!-- Sidebar Kategori -->
                    <div class="shop__sidebar__accordion">
                        <div class="accordion" id="accordionExample">
                            <div class="card">
                                <div class="card-heading">
                                    <a data-toggle="collapse" data-target="#collapseOne">Categories</a>
                                </div>
                                <div id="collapseOne" class="collapse show" data-parent="#accordionExample">
                                    <div class="card-body">
                                        <div class="shop__sidebar__categories">
                                            <ul class="nice-scroll">
                                                <?php
                                                $query = "SELECT id_kategori, nama_kategori FROM kategori_produk";
                                                $result = mysqli_query($koneksi, $query);

                                                if (mysqli_num_rows($result) > 0) {
                                                    echo '<li><a href="utama.php?page=shop">All</a></li>';
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        echo '<li><a href="utama.php?page=shop&id_kategori=' . $row['id_kategori'] . '">' . $row['nama_kategori'] . '</a></li>';
                                                    }
                                                } else {
                                                    echo "<li>Tidak ada kategori tersedia.</li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bagian Produk -->
            <div class="col-lg-9">
                <div class="shop__product__option">
                    <div class="row product__filter">
                        <?php
                        $search = isset($_GET['search']) ? $_GET['search'] : '';
                        $id_kategori = isset($_GET['id_kategori']) ? intval($_GET['id_kategori']) : null;

                        $sql = "SELECT id_produk, nama_produk, harga, gambar FROM produk";

                        if ($search) {
                            $search = mysqli_real_escape_string($koneksi, $search);
                            $sql .= " WHERE nama_produk LIKE '%$search%'";
                        }

                        if ($id_kategori) {
                            $sql .= $search ? " AND id_kategori = $id_kategori" : " WHERE id_kategori = $id_kategori";
                        }

                        $result = mysqli_query($koneksi, $sql);

                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="product__item">
                                        <a href="utama.php?page=shopdetails&id_produk=<?php echo $row['id_produk']; ?>">
                                            <div class="product__item__pic" style="background-image: url('/Ecommerce/upload/<?php echo $row['gambar']; ?>'); background-size: cover; background-position: center;">
                                            </div>
                                        </a>
                                        <?php if ($id_kategori == 17): ?>
                                        <?php endif; ?>
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
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Shop Section End -->
<?php
if (isset($koneksi)) {
    mysqli_close($koneksi);
}
?>
