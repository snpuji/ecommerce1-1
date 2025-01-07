<?php
// Koneksi ke database
include 'config.php';

// Ambil id_pembayaran dari URL atau input
$id_pembayaran = isset($_GET['id_pembayaran']) ? (int)$_GET['id_pembayaran'] : 0;

// Query untuk mengambil id_pesanan yang terkait dengan id_pembayaran dari tabel pembayaran
$sql_id_pesanan = "SELECT id_pesanan FROM pembayaran WHERE id_pembayaran = ?";
$stmt_id_pesanan = $koneksi->prepare($sql_id_pesanan);

if (!$stmt_id_pesanan) {
    die('Query preparation failed: ' . $koneksi->error);
}

$stmt_id_pesanan->bind_param("i", $id_pembayaran);
$stmt_id_pesanan->execute();
$result_id_pesanan = $stmt_id_pesanan->get_result();
$row_id_pesanan = $result_id_pesanan->fetch_assoc();

// Ambil id_pesanan yang sudah diambil
$id_pesanan = $row_id_pesanan['id_pesanan'];

// Query untuk mengambil nama toko berdasarkan id_penjual dari tabel detail_pesanan
$sql_nama_toko = "SELECT d.id_penjual, p.nama_toko FROM detail_pesanan d
                  JOIN penjual p ON d.id_penjual = p.id_penjual
                  WHERE d.id_pesanan = ?";
$stmt_nama_toko = $koneksi->prepare($sql_nama_toko);

if (!$stmt_nama_toko) {
    die('Query preparation failed: ' . $koneksi->error);
}

$stmt_nama_toko->bind_param("i", $id_pesanan);
$stmt_nama_toko->execute();
$result_nama_toko = $stmt_nama_toko->get_result();
$nama_toko = $result_nama_toko->fetch_assoc()['nama_toko'];

// Query untuk mengambil tanggal pembayaran dari tabel pembayaran
$sql_tanggal_pembayaran = "SELECT tanggal_pembayaran, metode_pembayaran FROM pembayaran WHERE id_pembayaran = ?";
$stmt_tanggal_pembayaran = $koneksi->prepare($sql_tanggal_pembayaran);

if (!$stmt_tanggal_pembayaran) {
    die('Query preparation failed: ' . $koneksi->error);
}

$stmt_tanggal_pembayaran->bind_param("i", $id_pembayaran);
$stmt_tanggal_pembayaran->execute();
$result_tanggal_pembayaran = $stmt_tanggal_pembayaran->get_result();
$row_tanggal_pembayaran = $result_tanggal_pembayaran->fetch_assoc();
$tanggal_pembayaran = $row_tanggal_pembayaran['tanggal_pembayaran'];
$metode_pembayaran = $row_tanggal_pembayaran['metode_pembayaran'];

// Query untuk mengambil alamat dari tabel pesanan1
$sql_alamat = "SELECT alamat FROM pesanan1 WHERE id_pesanan = ?";
$stmt_alamat = $koneksi->prepare($sql_alamat);

if (!$stmt_alamat) {
    die('Query preparation failed: ' . $koneksi->error);
}

$stmt_alamat->bind_param("i", $id_pesanan);
$stmt_alamat->execute();
$result_alamat = $stmt_alamat->get_result();
$alamat = $result_alamat->fetch_assoc()['alamat'];

// Query untuk mengambil no resi dari tabel pengiriman
$sql_nomor_resi = "SELECT nomor_resi FROM pengiriman WHERE id_pesanan = ?";
$stmt_nomor_resi = $koneksi->prepare($sql_nomor_resi);

if (!$stmt_nomor_resi) {
    die('Query preparation failed: ' . $koneksi->error);
}

$stmt_nomor_resi->bind_param("i", $id_pesanan);
$stmt_nomor_resi->execute();
$result_nomor_resi = $stmt_nomor_resi->get_result();
$nomor_resi = $result_nomor_resi->fetch_assoc()['nomor_resi'];

// Query untuk mengambil total harga dari tabel pesanan1 (bukan pesanan)
$sql_total_harga = "SELECT total_harga FROM pesanan1 WHERE id_pesanan = ?";
$stmt_total_harga = $koneksi->prepare($sql_total_harga);

if (!$stmt_total_harga) {
    die('Query preparation failed: ' . $koneksi->error);
}

$stmt_total_harga->bind_param("i", $id_pesanan);
$stmt_total_harga->execute();
$result_total_harga = $stmt_total_harga->get_result();
$total_harga = $result_total_harga->fetch_assoc()['total_harga'];

// Query untuk mengambil detail pesanan seperti gambar, nama_produk, harga, quantity, dan subtotal
$sql_detail_pesanan = "SELECT dp.id_produk, dp.harga, dp.quantity, dp.subtotal, dp.biaya_pengiriman, p.gambar, p.nama_produk
                       FROM detail_pesanan dp
                       JOIN produk p ON dp.id_produk = p.id_produk
                       WHERE dp.id_pesanan = ?";
$stmt_detail_pesanan = $koneksi->prepare($sql_detail_pesanan);

if (!$stmt_detail_pesanan) {
    die('Query preparation failed: ' . $koneksi->error);
}

$stmt_detail_pesanan->bind_param("i", $id_pesanan);
$stmt_detail_pesanan->execute();
$result_detail_pesanan = $stmt_detail_pesanan->get_result();

$data_pesanan = [];
while ($row = $result_detail_pesanan->fetch_assoc()) {
    $data_pesanan[] = $row;
}

?>

<section class="content-main">
    <div class="content-header">
        <h2 class="content-title">Transactions</h2>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-lg-6">
                    <div class="box bg-light p-3" style="min-height: 100%;">
                        <h6 class="mt-15">Transaction Details</h6>
                        <hr />
                        <h6 class="mb-0">Seller:</h6>
                        <p><?php echo htmlspecialchars($nama_toko); ?></p>
                        <h6 class="mb-0">Date:</h6>
                        <p><?php echo date('F j, Y', strtotime($tanggal_pembayaran)); ?></p>
                        <h6 class="mb-0">Billing Address</h6>
                        <p><?php echo htmlspecialchars($alamat); ?></p>
                        <h6 class="mb-0">No Resi</h6>
                        <p><?php echo htmlspecialchars($nomor_resi); ?></p> <!-- No Resi -->
                        <h6 class="mb-0">Payment Method</h6>
                        <p><?php echo htmlspecialchars($metode_pembayaran); ?></p> <!-- Payment Method -->
                        <hr />
                        <a class="btn btn-light" href="#">Download Receipt</a>
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div class="col-lg-6">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="40%">Product</th>
                                    <th width="20%">Unit Price</th>
                                    <th width="20%">Quantity</th>
                                    <th width="20%" class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $subtotal = 0;
                                foreach ($data_pesanan as $pesanan) {
                                    $subtotal += $pesanan['subtotal'];
                                    $total = $pesanan['harga'] * $pesanan['quantity'];
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
                                        <td class="text-end">Rp.' . number_format($total) . '</td>
                                    </tr>';
                                }
                                ?>
                                <tr>
                                    <td colspan="4">
                                        <article class="float-end">
                                            <dl class="dlist">
                                                <dt>Shipping cost:</dt>
                                                <dd>Rp.<?php echo number_format($data_pesanan[0]['biaya_pengiriman']); ?></dd>
                                            </dl>
                                            <dl class="dlist">
                                                <dt>Grand total:</dt>
                                                <dd><b class="h5">Rp.<?php echo number_format($subtotal + $data_pesanan[0]['biaya_pengiriman']); ?></b></dd>
                                            </dl>
                                        </article>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
