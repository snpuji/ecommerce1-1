<?php
include 'config.php'; // Pastikan koneksi database

// Ambil ID penjual dari URL
if (isset($_GET['id'])) {
    $id_penjual = $_GET['id'];

    // Query untuk mengambil data penjual berdasarkan ID
    $query_penjual = "SELECT * FROM penjual WHERE id_penjual = '$id_penjual'";
    $result_penjual = mysqli_query($koneksi, $query_penjual);
    $penjual = mysqli_fetch_assoc($result_penjual);

    if (!$penjual) {
        echo "<script>alert('Seller not found!'); window.location='utama.php?page=seller';</script>";
        exit;
    }

    // Query untuk mengambil produk yang terkait dengan penjual
    $query_produk = "SELECT id_produk, nama_produk, deskripsi, harga, stok, gambar 
                     FROM produk 
                     WHERE id_penjual = '$id_penjual'";
    $result_produk = mysqli_query($koneksi, $query_produk);
} else {
    echo "<script>alert('Invalid seller ID!'); window.location='utama.php?page=seller';</script>";
    exit;
}
?>

<section class="content-main">
    <div class="row">
        <div class="col-12">
            <div class="content-header">
                <h2 class="content-title"><?php echo htmlspecialchars($penjual['nama_toko']); ?></h2>
                <a href="utama.php?page=seller" class="btn btn-primary">Back to Seller List</a>
            </div>
        </div>

        <!-- Detail Penjual -->
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Seller Details</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>ID</th>
                            <td><?php echo htmlspecialchars($penjual['id_penjual']); ?></td>
                        </tr>
                        <tr>
                            <th>Username</th>
                            <td><?php echo htmlspecialchars($penjual['username']); ?></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><?php echo htmlspecialchars($penjual['email']); ?></td>
                        </tr>
                        <tr>
                            <th>Phone Number</th>
                            <td><?php echo htmlspecialchars($penjual['no_hp']); ?></td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td><?php echo htmlspecialchars($penjual['alamat']); ?></td>
                        </tr>
                        <tr>
                            <th>Bank BCA</th>
                            <td><?php echo htmlspecialchars($penjual['rekening_bca']); ?></td>
                        </tr>
                        <tr>
                            <th>Virtual_account</th>
                            <td><?php echo htmlspecialchars($penjual['virtual_account']); ?></td>
                        </tr>
                        <tr>
                            <th>Qris</th>
                            <td><?php echo htmlspecialchars($penjual['qris']); ?></td>
                        </tr>
                        <tr>
                            <th>Store Name</th>
                            <td><?php echo htmlspecialchars($penjual['nama_toko']); ?></td>
                        </tr>
                        <tr>
                            <th>Store Description</th>
                            <td><?php echo htmlspecialchars($penjual['desc_toko']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Produk Penjual -->
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Products by <?php echo htmlspecialchars($penjual['nama_toko']); ?></h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Product Name</th>
                                    <th>Description</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result_produk) > 0) {
                                    while ($produk = mysqli_fetch_assoc($result_produk)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($produk['id_produk']) . "</td>";
                                        echo "<td>";
                                        if (!empty($produk['gambar']) && file_exists("../upload/" . $produk['gambar'])) {
                                            echo "<img src='../upload/" . htmlspecialchars($produk['gambar']) . "' alt='" . htmlspecialchars($produk['nama_produk']) . "' style='max-width: 100px;'>";
                                        } else {
                                            echo "<img src='../assets/images/no-image.png' alt='No image available' style='max-width: 100px;'>";
                                        }
                                        echo "</td>";
                                        echo "<td>" . htmlspecialchars($produk['nama_produk']) . "</td>";
                                        echo "<td>" . htmlspecialchars($produk['deskripsi']) . "</td>";
                                        echo "<td>Rp " . number_format($produk['harga'], 0, ',', '.') . "</td>";
                                        echo "<td>" . htmlspecialchars($produk['stok']) . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No products found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
