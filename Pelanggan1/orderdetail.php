<?php
// Sertakan file koneksi
include "config.php";

// Ambil ID pesanan dari URL
$id_pesanan = isset($_GET['id_pesanan']) ? intval($_GET['id_pesanan']) : 0;

// Query data pesanan gabungan
$query = "
SELECT 
    pesanan1.id_pesanan,
    pesanan1.tanggal_pemesanan,
    pesanan1.nama,
    pesanan1.no_hp,
    pesanan1.alamat AS alamat_pengiriman,
    pesanan1.status AS status_pesanan, -- Alias untuk status dari tabel pesanan1
    pembayaran.status AS status_pembayaran, -- Alias untuk status dari tabel pembayaran
    pembayaran.metode_pembayaran,
    penjual.rekening_bca,
    penjual.qris,
    penjual.virtual_account,
    jasa_pengiriman.jasa_pengiriman AS jasa_pengiriman,
    produk.nama_produk,
    produk.gambar,
    detail_pesanan.harga,
    detail_pesanan.quantity,
    detail_pesanan.subtotal,
    jasa_pengiriman.biaya_pengiriman,
    penjual.no_hp AS penjual_no_hp,
    pesanan1.notes, -- Catatan pesanan
    pesanan1.total_harga
FROM pesanan1
JOIN pembayaran ON pesanan1.id_pesanan = pembayaran.id_pesanan
JOIN detail_pesanan ON pesanan1.id_pesanan = detail_pesanan.id_pesanan
JOIN produk ON detail_pesanan.id_produk = produk.id_produk
JOIN jasa_pengiriman ON detail_pesanan.id_jasa_pengiriman = jasa_pengiriman.id_jasa_pengiriman
JOIN penjual ON detail_pesanan.id_penjual = penjual.id_penjual -- Mengambil data penjual
WHERE pesanan1.id_pesanan = ?";

$stmt = $koneksi->prepare($query);
if (!$stmt) {
    die("Error preparing statement: " . $koneksi->error);
}
$stmt->bind_param("i", $id_pesanan);
$stmt->execute();
$result = $stmt->get_result();

// Proses data
$data_pesanan = [];
while ($row = $result->fetch_assoc()) {
    $data_pesanan[] = $row;
}

// Ambil data utama jika tersedia
if (empty($data_pesanan)) {
    die("Pesanan tidak ditemukan.");
}

// Data utama untuk kemudahan akses
$data_utama = $data_pesanan[0];
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <link href="../Pelanggan/assets/css/main.css?v=1.1" rel="stylesheet" type="text/css" />
    </head>
            <section class="content-main">
                <div class="content-header">
                    <div>
                        <h2 class="content-title card-title">Order detail</h2>
                    </div>
                </div>
                <div class="card">
                    <header class="card-header">
                        <div class="row align-items-center">
                            <div class="col-lg-6 col-md-6 mb-lg-0 mb-15">
                                <span><b><?= date("D, M d, Y, h:i A", strtotime($data_utama['tanggal_pemesanan'])) ?></b></span><br />
                                <small class="text-muted">Order ID: <?= $data_utama['id_pesanan'] ?></small>
                            </div>
                        </div>
                    </header>
                    <!-- card-header end// -->
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
                                            <?= htmlspecialchars($data_utama['nama']) ?><br />
                                            <?= htmlspecialchars($data_utama['no_hp']) ?>
                                        </p>
                                    </div>
                                </article>
                            </div>
                            <!-- col// -->
                            <div class="col-md-4">
                                <article class="icontext align-items-start">
                                    <span class="icon icon-sm rounded-circle bg-primary-light">
                                        <i class="text-primary material-icons md-local_shipping"></i>
                                    </span>
                                    <div class="text">
                                        <h6 class="mb-1">Order info</h6>
                                        <p class="mb-1">
                                            Shipping: <?= htmlspecialchars($data_utama['jasa_pengiriman']) ?><br />
                                            Pay method: <?= htmlspecialchars($data_utama['metode_pembayaran']) ?><br />
                                            Status: <?= htmlspecialchars($data_utama['status_pesanan']) ?>
                                        </p>
                                    </div>
                                </article>
                            </div>
                            <!-- col// -->
                            <div class="col-md-4">
                                <article class="icontext align-items-start">
                                    <span class="icon icon-sm rounded-circle bg-primary-light">
                                        <i class="text-primary material-icons md-place"></i>
                                    </span>
                                    <div class="text">
                                        <h6 class="mb-1">Deliver to</h6>
                                        <p class="mb-1">
                                            <?= htmlspecialchars($data_utama['alamat_pengiriman']) ?>
                                        </p>
                                    </div>
                                </article>
                            </div>
                            <!-- col// -->
                        </div>
                        <!-- row // -->
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
                    <?php
                    $subtotal = 0;
                    foreach ($data_pesanan as $pesanan) {
                        $subtotal += $pesanan['subtotal'];
                        echo '
                        <tr>
                            <td>
                                <a class="itemside" href="#">
                                    <div class="left">
                                        <img src="../upload/' . $pesanan['gambar'] . '" width="40" height="40" class="img-xs" alt="Item" />
                                    </div>
                                    <div class="info">' . htmlspecialchars($pesanan['nama_produk']) . '</div>
                                </a>
                            </td>
                            <td>Rp.' . number_format($pesanan['harga']) . '</td>
                            <td>' . htmlspecialchars($pesanan['quantity']) . '</td>
                            <td class="text-end">Rp.' . number_format($pesanan['subtotal']) . '</td>
                        </tr>';
                    }
                    ?>
                    <tr>
                        <td colspan="4">
                            <article class="float-end">
                                <dl class="dlist">
                                    <dt>Subtotal:</dt>
                                    <dd>Rp.<?php echo number_format($subtotal); ?></dd>
                                </dl>
                                <dl class="dlist">
                                    <dt>Shipping cost:</dt>
                                    <dd>Rp.<?php echo number_format($data_pesanan[0]['biaya_pengiriman']); ?></dd>
                                </dl>
                                <dl class="dlist">
                                    <dt>Grand total:</dt>
                                    <dd><b class="h5">Rp.<?php echo number_format($subtotal + $data_pesanan[0]['biaya_pengiriman']); ?></b></dd>
                                </dl>
                                <dl class="dlist">
                                    <dt class="text-muted">Status Pembayaran:</dt>
                                    <dd>
                                        <span class="badge rounded-pill alert-success text-success">
                                            <?php echo htmlspecialchars($data_utama['status_pembayaran']); ?>
                                        </span>
                                    </dd>
                                </dl>
                            </article>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <!-- table-responsive// -->
                            </div>
                            <!-- col// -->
                            <div class="col-lg-1"></div> 
<div class="col-lg-4">
    <div class="box shadow-sm bg-light">
        <h6 class="mb-15">Payment info</h6>
        <p>
            BCA: <?= htmlspecialchars($data_utama['rekening_bca']); ?> <br />
            QRIS: <?= htmlspecialchars($data_utama['qris']); ?> <br />
            Virtual Account: <?= htmlspecialchars($data_utama['virtual_account']); ?> <br />
            Phone (Seller): <?= htmlspecialchars($data_utama['penjual_no_hp']); ?> <br />
        </p>
    </div>
    <div class="h-25 pt-4">
        <div class="mb-3">
            <label>Notes</label>  
            <textarea class="form-control" name="notes" id="notes" placeholder="" disabled><?= htmlspecialchars($data_utama['notes']); ?></textarea>
        </div>
    </div>
</div>

                            <!-- col// -->
                        </div>
                    </div>
                    <!-- card-body end// -->
                </div>
                <!-- card end// -->
            </section>
           
</html>
