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
    detail_pesanan.quantity AS quantity,
    detail_pesanan.harga AS harga,
    pesanan1.total_harga,
    pesanan1.status,
    pesanan1.tanggal_pemesanan AS tanggal,
    pesanan1.alamat AS alamat_pengiriman,
    pesanan1.notes AS notes  -- Menambahkan pengambilan data catatan
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

    // Ambil data dari hasil query
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        die("Order with ID $id_pesanan not found.");
    }
} else {
    die("Order ID not provided.");
}

if (isset($_POST['update_status'])) {
    $new_status = mysqli_real_escape_string($koneksi, $_POST['status']);
    
    // Query untuk memperbarui status pesanan
    $update_query = "UPDATE pesanan1 SET status = '$new_status' WHERE id_pesanan = $id_pesanan";
    
    if (mysqli_query($koneksi, $update_query)) {
        // Jika update berhasil, beri pesan atau lakukan tindakan lain
        echo '<div class="alert alert-success">Status updated successfully!</div>';
    } else {
        echo '<div class="alert alert-danger">Failed to update status: ' . mysqli_error($koneksi) . '</div>';
    }
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
                    <span><i class="material-icons md-calendar_today"></i><b><?php echo htmlspecialchars(date('D, M d, Y, h:iA', strtotime($row['tanggal']))); ?></b></span><br />
                    <small class="text-muted">Order ID: <?php echo htmlspecialchars($row['id_pesanan']); ?></small>
                </div>
                <div class="col-lg-6 col-md-6 ms-auto text-md-end">
                <form action="" method="POST">
                        <select class="form-select d-inline-block mb-lg-0 mr-5 mw-200" name="status">
                            <option value="Being Processed" <?php echo ($row['status'] == 'Being Processed') ? 'selected' : ''; ?>>Being Processed</option>
                            <option value="Received" <?php echo ($row['status'] == 'Received') ? 'selected' : ''; ?>>Received</option>
                            <option value="Shipped" <?php echo ($row['status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                            <option value="Delivered" <?php echo ($row['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                        </select>
                        <button type="submit" class="btn btn-primary" name="update_status">Save</button>
                    </form>
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
                                Shipping Address: <?php echo htmlspecialchars($row['alamat_pengiriman']); ?>
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
                                Address: <?php echo htmlspecialchars($row['alamat_pengiriman']); ?>
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
                                    <td class="text-end">Rp<?php echo number_format($row['harga'] * $row['quantity'], 0, ',', '.'); ?></td>
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
$badgeClass = "alert-secondary"; // Default value
switch ($row['status']) {
    case "Delivered":
        $badgeClass = "alert-success";
        break;
    case "Shipped":
        $badgeClass = "alert-danger";
        break;
    case "Received":
        $badgeClass = "alert-warning";
        break;
    case "Being Processed":
        $badgeClass = "alert-info";
        break;
}
?>

                            <span class="badge rounded-pill <?php echo $badgeClass; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                            
                        </p>
                    </div>
                    <div class="h-25 pt-4">
    <div class="mb-3">
        <label>Notes</label>
        <!-- Menampilkan catatan yang ada di dalam textarea -->
        <textarea class="form-control" name="notes" id="notes" placeholder="Type some note" disabled><?= htmlspecialchars($row['notes']) ?></textarea>
    </div>
    </div>

                </div>
            </div>
        </div>
    </div>
</section>
