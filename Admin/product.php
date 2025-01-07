<?php
// Masukkan konfigurasi database
include "config.php";

// Tangkap input pencarian dari URL
$search_query = isset($_GET['search_query']) ? $koneksi->real_escape_string($_GET['search_query']) : "";

// Query untuk mengambil produk milik penjual, termasuk fitur pencarian
$query_produk = "
SELECT 
    produk.id_produk, 
    produk.nama_produk, 
    produk.deskripsi, 
    produk.harga, 
    produk.gambar, 
    produk.id_penjual, 
    produk.id_kategori,
    penjual.nama_toko, 
    kategori_produk.nama_kategori,
    (stok_ukuran.small + stok_ukuran.medium + stok_ukuran.large + stok_ukuran.extralarge + stok_ukuran.doubleextralarge + stok_ukuran.stok) AS total_stok,
    pesanan1.status AS order_status
FROM produk 
LEFT JOIN penjual ON produk.id_penjual = penjual.id_penjual
LEFT JOIN kategori_produk ON produk.id_kategori = kategori_produk.id_kategori
LEFT JOIN stok_ukuran ON produk.id_produk = stok_ukuran.id_produk
LEFT JOIN detail_pesanan ON produk.id_produk = detail_pesanan.id_produk
LEFT JOIN pesanan1 ON detail_pesanan.id_pesanan = pesanan1.id_pesanan
";

// Tambahkan kondisi pencarian jika ada input
if (!empty($search_query)) {
    $query_produk .= " WHERE 
        produk.nama_produk LIKE '%$search_query%' OR 
        produk.deskripsi LIKE '%$search_query%' OR 
        kategori_produk.nama_kategori LIKE '%$search_query%' OR 
        penjual.nama_toko LIKE '%$search_query%'";
}

// Menjalankan query
$result_produk = $koneksi->query($query_produk);

// Debugging: Cek apakah query berhasil dijalankan
if (!$result_produk) {
    die("Query failed: " . $koneksi->error); // Menampilkan error query jika gagal
}
?>



<style>
    /* Styling for the header and item rows */
    .table-header {
        background-color: #5DB680;
        color: #f5f7fa;
        font-weight: bold;
        padding: 10px 0;
        border-bottom: 2px solid #5DB680;
        display: flex;
    }

    .table-header div {
        text-align: left;
        padding: 5px;
        flex: 1;
    }

    .itemlist .row {
        display: flex;
        border-bottom: 1px solid #ddd;
        align-items: center;
        padding: 5px 0;
    }

    .itemlist .row div {
        padding: 5px;
        flex: 1;
        text-align: left;
        white-space: nowrap;
    }

    .itemlist .row div:first-child {
        flex: 0.5;
        text-align: center;
    }

    .itemlist .row div:nth-child(2) {
        flex: 1;
        text-align: center;
    }

    .itemlist .row div img {
        max-width: 50px;
        height: auto;
    }

    .itemlist .row div:nth-child(3),
    .itemlist .row div:nth-child(4),
    .itemlist .row div:nth-child(5),
    .itemlist .row div:nth-child(6),
    .itemlist .row div:nth-child(7),
    .itemlist .row div:last-child {
        flex: 1.5;
    }

    .content-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .col-action {
        display: flex;
        justify-content: space-around;
        gap: 10px;
    }
</style>

<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Product List</h2>
        </div>
        <div>
            <a href="utama.php?page=addproduct" class="btn btn-primary btn-sm rounded">Create new</a>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
        <form class="searchform" method="GET" action="utama.php">
    <div class="input-group">
        <!-- Input pencarian -->
        <input list="search_terms" type="text" name="search_query" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search_query); ?>" />
        <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? $_GET['page'] : 'product'; ?>" />
        <button class="btn btn-light bg" type="submit"><i class="material-icons md-search"></i></button>
    </div>
</form>

</br>
</br>
            <div class="table-header">
                <div>No</div>
                <div>Gambar</div>
                <div>Nama Produk</div>
                <div>Harga</div>
                <div>Stok</div>
                <div>Kategori</div>
                <div>Nama Toko</div>
                <div>Aksi</div>
            </div>
            <?php
            // Memeriksa apakah ada hasil dari query
            if ($result_produk->num_rows > 0) {
                $nomor = 1;
                while ($produk = $result_produk->fetch_assoc()) {
                    ?>
                    <div class="itemlist">
                        <div class="row">
                            <!-- No -->
                            <div><?php echo $nomor++; ?></div>

                            <!-- Gambar -->
                            <div>
                                <?php if (!empty($produk['gambar']) && file_exists("../upload/" . $produk['gambar'])) { ?>
                                    <img src="../upload/<?php echo htmlspecialchars($produk['gambar']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" class="img-fluid">
                                <?php } else { ?>
                                    <img src="../assets/images/no-image.png" alt="No image available" class="img-fluid">
                                <?php } ?>
                            </div>

                            <!-- Nama Produk -->
                            <div>
                                <strong>
                                    <?php 
                                        // Membatasi nama produk hingga 3 kata
                                        $nama_produk = htmlspecialchars($produk['nama_produk']);
                                        $words_nama = explode(' ', $nama_produk); // Memisahkan nama produk menjadi array kata
                                        if (count($words_nama) > 3) {
                                            // Jika lebih dari 3 kata, batasi dan tambahkan '...'
                                            echo implode(' ', array_slice($words_nama, 0, 3)) . '...';
                                        } else {
                                            // Jika kurang dari atau sama dengan 3 kata, tampilkan semua
                                            echo implode(' ', $words_nama);
                                        }
                                    ?>
                                </strong>
                                <p class="text-muted">
                                    <?php 
                                        // Membatasi deskripsi hingga 3 kata
                                        $deskripsi = htmlspecialchars($produk['deskripsi']);
                                        $words_deskripsi = explode(' ', $deskripsi); // Memisahkan deskripsi menjadi array kata
                                        if (count($words_deskripsi) > 3) {
                                            // Jika lebih dari 3 kata, batasi dan tambahkan '...'
                                            echo implode(' ', array_slice($words_deskripsi, 0, 3)) . '...';
                                        } else {
                                            // Jika kurang dari atau sama dengan 3 kata, tampilkan semua
                                            echo implode(' ', $words_deskripsi);
                                        }
                                    ?>
                                </p>
                            </div>


                            <!-- Harga -->
                            <div>Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></div>

                            <!-- Stok -->
                            <div><?php echo $produk['total_stok'] !== null ? $produk['total_stok'] : '0'; ?></div>

                            <!-- Kategori -->
                            <div><?php echo htmlspecialchars($produk['nama_kategori']); ?></div>

                            <!-- Nama Toko -->
                            <div><?php echo htmlspecialchars($produk['nama_toko']); ?></div>

                            <!-- Aksi -->
                            <div class="col-action">
                                <a href="utama.php?page=editproduct&id=<?php echo $produk['id_produk']; ?>" class="btn btn-sm font-sm rounded btn-brand"> 
                                    <i class="material-icons md-edit"></i> Edit 
                                </a>
                                <a href="deleteproduct.php?id=<?php echo $produk['id_produk']; ?>" class="btn btn-sm font-sm btn-light rounded" onclick="return confirm('Are you sure you want to delete this product?');"> 
                                    <i class="material-icons md-delete_forever"></i> Delete 
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "No products found.";
            }
            ?>
        </div>
    </div>
</section>

