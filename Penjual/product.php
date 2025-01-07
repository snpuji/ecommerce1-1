<?php
// Masukkan konfigurasi database
include "config.php";

$id_penjual = $_SESSION['id_penjual'];

// Ambil query pencarian dari URL, jika ada
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

// Query untuk mengambil produk milik penjual
$query_produk = "SELECT 
                    produk.id_produk, 
                    produk.nama_produk, 
                    produk.deskripsi, 
                    produk.harga, 
                    produk.gambar, 
                    produk.id_penjual, 
                    produk.id_kategori,
                    penjual.nama_toko, 
                    kategori_produk.nama_kategori,
                    (stok_ukuran.small + stok_ukuran.medium + stok_ukuran.large + stok_ukuran.extralarge + stok_ukuran.doubleextralarge + stok_ukuran.stok) AS total_stok
                 FROM produk 
                 LEFT JOIN penjual ON produk.id_penjual = penjual.id_penjual
                 LEFT JOIN kategori_produk ON produk.id_kategori = kategori_produk.id_kategori
                 LEFT JOIN stok_ukuran ON produk.id_produk = stok_ukuran.id_produk
                 WHERE produk.id_penjual = $id_penjual"; // Directly use the session variable

// Menambahkan pencarian berdasarkan query
if ($search_query != '') {
    $query_produk .= " AND (produk.nama_produk LIKE '%$search_query%' 
                        OR kategori_produk.nama_kategori LIKE '%$search_query%' 
                        OR produk.deskripsi LIKE '%$search_query%')";
}

$result_produk = $koneksi->query($query_produk); // Execute the query

// Check if the query was successful and result is valid
if (!$result_produk) {
    die("Error in query execution: " . $koneksi->error); // Display query error if it fails
}
?>


<style>
    /* Styling untuk Judul dan Tabel */
    h2, h3 {
        font-family: 'Arial', sans-serif;
        color: #333;
    }

    .container {
        background-color: #f9f9f9;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        margin: 20px auto;
        max-width: 1200px;
    }

    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .content-header h2 {
        font-size: 24px;
        margin: 0;
    }

    .content-header a {
        background-color: #5DB680;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
    }

    .table {
        width: 100%;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    .table th, .table td {
        text-align: center;
        vertical-align: middle;
        padding: 15px;
    }

    .table thead {
        background-color: #5DB680;
        color: white;
        font-size: 16px;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .table tbody tr:hover {
        background-color: #e9ecef;
        cursor: pointer;
    }

    .table img {
        max-width: 100px;
        height: auto;
        border-radius: 8px;
    }

    .no-products {
        color: #ff6f61;
        font-size: 18px;
        font-weight: bold;
    }

    /* Styling untuk tombol aksi */
    .btn-sm {
        font-size: 12px;
        padding: 5px 10px;
    }

    .btn-warning {
        background-color: #f0ad4e;
        color: white;
    }

    .btn-danger {
        background-color: #d9534f;
        color: white;
    }

    .btn-warning:hover, .btn-danger:hover {
        opacity: 0.8;
    }
</style>

<body>
    <div class="container">
        <section class="content-main">
            <div class="content-header">
                <form class="searchform" method="GET" action="utama.php">
                    <div class="input-group">
                        <!-- Input pencarian -->
                        <input list="search_terms" type="text" name="search_query" class="form-control" placeholder="Search..." />
                        <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? $_GET['page'] : 'product'; ?>" />
                        <button class="btn btn-light bg" type="submit"><i class="material-icons md-search"></i></button>
                    </div>
                </form>
                <div>
                    <a href="utama.php?page=addproduct" class="btn btn-primary btn-sm rounded">Create new</a>
                </div>
                
            </div>

            <!-- Tabel Produk -->
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID Produk</th>
                        <th>Nama Produk</th>
                        <th>Deskripsi</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Kategori</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_produk->num_rows > 0): ?>
                        <?php while ($produk = $result_produk->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($produk['id_produk']); ?></td>
                                <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                                <td><?php echo htmlspecialchars($produk['deskripsi']); ?></td>
                                <td>Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($produk['total_stok']); ?></td>
                                <td><?php echo htmlspecialchars($produk['nama_kategori']); ?></td>
                                <td>
                                    <div>
                                        <?php if (!empty($produk['gambar']) && file_exists("../upload/" . $produk['gambar'])) { ?>
                                            <img src="../upload/<?php echo htmlspecialchars($produk['gambar']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>" class="img-fluid">
                                        <?php } else { ?>
                                            <img src="../assets/images/no-image.png" alt="No image available" class="img-fluid">
                                        <?php } ?>      
                                    </div>
                                </td>
                                <td>
                                    <!-- Action Edit and Delete -->
                                    <a href="utama.php?page=editproduct&id=<?php echo $produk['id_produk']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="utama.php?page=deleteproduct&id=<?php echo $produk['id_produk']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center no-products">No products found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
