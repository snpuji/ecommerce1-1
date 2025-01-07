<?php
include 'config.php';

// Pastikan `id_pesanan` dikirimkan melalui GET
if (isset($_GET['id_pesanan'])) {
    $id_pesanan = intval($_GET['id_pesanan']); // Sanitasi input

    // Query untuk mendapatkan detail pesanan
    $query = "SELECT 
                pesanan1.id_pesanan,
                pelanggan.nama AS nama_pelanggan,
                pelanggan.email AS email_pelanggan,
                pelanggan.no_hp AS no_hp_pelanggan,
                penjual.nama_toko AS nama_toko,
                produk.nama_produk,
                produk.gambar AS gambar,
                detail_pesanan.quantity,
                detail_pesanan.harga,
                (detail_pesanan.quantity * detail_pesanan.harga) AS total_harga,
                pesanan1.status,
                pesanan1.tanggal_pemesanan,
                pesanan1.alamat
              FROM pesanan1
              JOIN pelanggan ON pesanan1.id_pelanggan = pelanggan.id_pelanggan
              JOIN detail_pesanan ON pesanan1.id_pesanan = detail_pesanan.id_pesanan
              JOIN penjual ON detail_pesanan.id_penjual = penjual.id_penjual
              JOIN produk ON detail_pesanan.id_produk = produk.id_produk
              WHERE pesanan1.id_pesanan = $id_pesanan";

    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        die("Query error: " . mysqli_error($koneksi));
    }

    $row = mysqli_fetch_assoc($result); // Ambil data hasil query
}
?>



<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Order detail</h2>
            <p>Details for Order ID: <?php echo htmlspecialchars($row['id_pesanan']); ?></p>
        </div>
    </div>
    <div class="card">
        <header class="card-header">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                    <span><i class="material-icons md-calendar_today"></i><b><?php echo htmlspecialchars(date('D, M d, Y, h:iA', strtotime($row['tanggal_pemesanan']))); ?></b></span><br />
                    <small class="text-muted">Order ID: <?php echo htmlspecialchars($row['id_pesanan']); ?></small>
                </div>
            </div>
        </header>
        <div class="card-body">
            <div class="row mb-50 mt-20 order-info-wrap">
                <div class="col-md-4">
                    <article class="icontext align-items-start">
                        <span class="icon icon-sm rounded-circle bg-primary-light">
                            <i class="text-primary material-icons md-person"></i>
                        </span>
                        <div class="text">
                            <h6 class="mb-1">Customer</h6>
                            <p class="mb-1">
                                <?php echo htmlspecialchars($row['nama_pelanggan']); ?><br />
                                <?php echo htmlspecialchars($row['email_pelanggan']); ?><br />
                                <?php echo htmlspecialchars($row['no_hp_pelanggan']); ?>
                            </p>
                        </div>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="icontext align-items-start">
                        <span class="icon icon-sm rounded-circle bg-primary-light">
                            <i class="text-primary material-icons md-local_shipping"></i>
                        </span>
                        <div class="text">
                            <h6 class="mb-1">Order info</h6>
                            <p class="mb-1">
                                Seller: <?php echo htmlspecialchars($row['nama_toko']); ?><br />
                                Status: <?php echo htmlspecialchars($row['status']); ?><br />
                                Shipping Address: <?php echo htmlspecialchars($row['alamat']); ?>
                            </p>
                        </div>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="icontext align-items-start">
                        <span class="icon icon-sm rounded-circle bg-primary-light">
                            <i class="text-primary material-icons md-place"></i>
                        </span>
                        <div class="text">
                            <h6 class="mb-1">Deliver to</h6>
                            <p class="mb-1">
                                Address: <?php echo htmlspecialchars($row['alamat']); ?>
                            </p>
                        </div>
                    </article>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-7">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="40%">Product</th>
                                    <th width="20%">Unit Price</th>
                                    <th width="20%">Quantity</th>
                                    <th width="20%" class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div>
                                            <?php if (!empty($row['gambar']) && file_exists("../upload/" . $row['gambar'])) { ?>
                                                <img src="../upload/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>" class="img-fluid" style="width: 100px; height: auto; object-fit: cover;">
                                            <?php } else { ?>
                                                <img src="../assets/images/no-image.png" alt="No image available" class="img-fluid" style="width: 100px; height: auto; object-fit: cover;">
                                            <?php } ?>
                                        </div>
                                        <?php echo htmlspecialchars($row['nama_produk']); ?>
                                    </td>
                                    <td>Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                    <td class="text-end">Rp<?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="box shadow-sm bg-light">
                        <h6 class="mb-15">Payment Info</h6>
                        <p>
                            <?php
                            // Badge class based on status
                            $badgeClass = ""; // Default
                            $badgeTextColor = "text-dark"; // Default text color

                            switch ($row['status']) {
                                case "Diterima":
                                    $badgeClass = "alert-success";
                                    $badgeTextColor = "text-dark"; // Darker text for better contrast
                                    break;
                                case "Dibatalkan":
                                    $badgeClass = "alert-danger";
                                    $badgeTextColor = "text-white"; // White text for dark background
                                    break;
                                case "Ditunda":
                                    $badgeClass = "alert-warning";
                                    $badgeTextColor = "text-dark"; // Darker text for better contrast
                                    break;
                                case "Diproses":
                                    $badgeClass = "alert-info";
                                    $badgeTextColor = "text-dark"; // Darker text for better contrast
                                    break;
                            }
                            ?>
                            <span class="badge rounded-pill <?php echo $badgeClass; ?> <?php echo $badgeTextColor; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
