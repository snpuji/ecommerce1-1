<?php
include 'config.php';

// Ambil data pengguna dari sesi
$id_penjual = isset($_SESSION['id_penjual']) ? $_SESSION['id_penjual'] : null;

if (!$id_penjual) {
    die("ID penjual tidak ditemukan dalam sesi.");
}

// Ambil kata kunci pencarian dari form
$search_query = isset($_GET['search_query']) ? mysqli_real_escape_string($koneksi, $_GET['search_query']) : '';

// Query dasar untuk mengambil data pengiriman
$query = "SELECT 
    p.id_pengiriman,
    p.id_pesanan,
    p.id_jasa_pengiriman,
    j.jasa_pengiriman AS jasa_pengiriman,
    p.nomor_resi,
    pesanan1.alamat AS alamat,
    pesanan1.status AS status,  -- Mengambil status dari pesanan1
    p.tgl_dikirim,
    p.tgl_diterima
FROM pengiriman p
JOIN jasa_pengiriman j ON p.id_jasa_pengiriman = j.id_jasa_pengiriman
JOIN pesanan1 ON p.id_pesanan = pesanan1.id_pesanan
JOIN detail_pesanan dp ON pesanan1.id_pesanan = dp.id_pesanan
WHERE dp.id_penjual = '$id_penjual'";

// Menambahkan filter pencarian jika ada kata kunci pencarian
if ($search_query) {
    $query .= " AND (
        p.id_pesanan LIKE '%$search_query%' OR
        j.jasa_pengiriman LIKE '%$search_query%' OR
        p.nomor_resi LIKE '%$search_query%' OR
        pesanan1.alamat LIKE '%$search_query%' OR
        pesanan1.status LIKE '%$search_query%'
    )";
}

$result = mysqli_query($koneksi, $query);

// Periksa apakah query berhasil dijalankan
if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}
?>

<body>
    <form action="utama.php?page=pengiriman" method="get">
        <section class="content-main">
            <div class="content-header">
                <h2 class="content-title card-title">Shipping List</h2>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <form class="searchform" method="GET" action="utama.php">
                        <div class="input-group">
                            <!-- Input pencarian -->
                            <input list="search_terms" type="text" name="search_query" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars(str_replace('%', '', $search_query)); ?>" />
                            <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? $_GET['page'] : 'product'; ?>" />
                            <button class="btn btn-light bg" type="submit"><i class="material-icons md-search"></i></button>
                        </div>
                    </form>
                    </br>
                    <?php if ($result && mysqli_num_rows($result) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#ID</th>
                                        <th>Order ID</th>
                                        <th>Shipping Service</th>
                                        <th>Tracking No</th>
                                        <th>Shipping Address</th>
                                        <th>Status</th>
                                        <th>Shipped Date</th>
                                        <th>Received Date</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
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
                                        <tr>
                                            <td><?= htmlspecialchars($row['id_pengiriman']) ?></td>
                                            <td><?= htmlspecialchars($row['id_pesanan']) ?></td>
                                            <td><?= htmlspecialchars($row['jasa_pengiriman']) ?></td>
                                            <td><?= htmlspecialchars($row['nomor_resi']) ?></td>
                                            <td><?= htmlspecialchars($row['alamat']) ?></td>
                                            <td>
                                                <span class="badge rounded-pill <?= $badgeClass ?>">
                                                    <?= htmlspecialchars($row['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars(date('d.m.Y', strtotime($row['tgl_dikirim']))) ?></td>
                                            <td><?= htmlspecialchars(date('d.m.Y', strtotime($row['tgl_diterima']))) ?></td>
                                            <td class="text-end">
                                            <a href="utama.php?page=pengirimandetail&id=<?php echo urlencode($row['id_pesanan']); ?>" class="btn btn-md rounded font-sm">Detail</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-center">No shipping data found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </form>
</body>
