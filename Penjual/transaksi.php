<?php
include 'config.php';

// Pastikan id_penjual tersedia di sesi
$id_penjual = isset($_SESSION['id_penjual']) ? $_SESSION['id_penjual'] : null;

if (!$id_penjual) {
    die('Anda harus login terlebih dahulu sebagai penjual.');
}

// Mendapatkan input pencarian jika ada
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';

// Query dasar untuk menampilkan data pembayaran berdasarkan id_penjual
$query = "
    SELECT 
        pembayaran.id_pembayaran,
        pembayaran.id_pesanan,
        pembayaran.metode_pembayaran,
        pembayaran.status,
        pembayaran.tanggal_pembayaran AS tanggal,
        produk.nama_produk,
        pesanan1.total_harga AS jumlah
    FROM pembayaran
    JOIN pesanan1 ON pembayaran.id_pesanan = pesanan1.id_pesanan
    JOIN detail_pesanan ON pesanan1.id_pesanan = detail_pesanan.id_pesanan
    JOIN produk ON detail_pesanan.id_produk = produk.id_produk
    WHERE detail_pesanan.id_penjual = '$id_penjual'  -- Memperbaiki kolom id_penjual di tabel detail_pesanan
";

// Menambahkan kondisi pencarian jika ada query pencarian
if (!empty($search_query)) {
    $search_query = "%" . $search_query . "%"; // Wildcard untuk pencarian
    $query .= " AND (pembayaran.metode_pembayaran LIKE ? OR pembayaran.status LIKE ? OR produk.nama_produk LIKE ?)";
}

// Siapkan statement
$stmt = mysqli_prepare($koneksi, $query);

// Cek apakah prepare berhasil
if ($stmt === false) {
    die('Error preparing the SQL statement: ' . mysqli_error($koneksi));
}

// Bind parameter jika ada pencarian
if (!empty($search_query)) {
    mysqli_stmt_bind_param($stmt, "sss", $search_query, $search_query, $search_query);
}

// Jalankan query
mysqli_stmt_execute($stmt);

// Ambil hasilnya
$result = mysqli_stmt_get_result($stmt);

// Periksa apakah query berhasil
if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}
?>

<form action="utama.php?page=transaksi" method="get">
    <section class="content-main">
        <div class="content-header">
            <div>
                <h2 class="content-title card-title">Payment List</h2>
            </div>
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
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th scope="col">Order ID</th>
                                <th scope="col">Payment Method</th>
                                <th scope="col">Status</th>
                                <th scope="col">Date</th>
                                <th scope="col">Amount</th>
                                <th scope="col" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Badge class based on payment status
                                $badgeClass = ""; // Default
                                $badgeTextColor = "text-dark"; // Default text color

                                switch ($row['status']) {
                                    case "Paid":
                                        $badgeClass = "alert-success"; // Green for paid
                                        $badgeTextColor = "text-dark"; // Dark text for better contrast
                                        break;
                                    case "Waiting for Payment":
                                        $badgeClass = "alert-warning"; // Yellow for unpaid
                                        $badgeTextColor = "text-dark"; // Dark text for better contrast
                                        break;
                                }
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id_pembayaran']); ?></td>
                                <td><?php echo htmlspecialchars($row['id_pesanan']); ?></td>
                                <td><?php echo htmlspecialchars($row['metode_pembayaran']); ?></td>
                                <td>
                                    <span class="badge rounded-pill <?php echo $badgeClass; ?> <?php echo $badgeTextColor; ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(date('d.m.Y', strtotime($row['tanggal']))); ?></td>
                                <td><?php echo htmlspecialchars(number_format($row['jumlah'], 2, ',', '.')); ?></td>
                                <td class="text-end">
                                    <a href="utama.php?page=transaksidetail&id_pembayaran=<?php echo $row['id_pembayaran']; ?>" class="btn btn-md rounded font-sm">Detail</a>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="7" class="text-center">No payment data found.</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</form>
