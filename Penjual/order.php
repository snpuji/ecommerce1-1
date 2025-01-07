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
            pesanan1.id_pesanan, 
            pelanggan.nama AS nama_pelanggan, 
            pelanggan.email AS email_pelanggan,
            produk.nama_produk, 
            pesanan1.total_harga, 
            pesanan1.status, 
            pesanan1.tanggal_pemesanan AS tanggal
        FROM detail_pesanan
        JOIN pesanan1 ON detail_pesanan.id_pesanan = pesanan1.id_pesanan
        JOIN pelanggan ON pesanan1.id_pelanggan = pelanggan.id_pelanggan
        JOIN produk ON detail_pesanan.id_produk = produk.id_produk
        WHERE detail_pesanan.id_penjual = ?";

// Menambahkan kondisi pencarian jika ada query pencarian
if (!empty($search_query)) {
    $search_query = "%" . $search_query . "%"; // Wildcard untuk pencarian
    $query .= " AND (pelanggan.nama LIKE ? OR pelanggan.email LIKE ? OR produk.nama_produk LIKE ?)";
}
// Siapkan statement
$stmt = $koneksi->prepare($query);

// Cek apakah prepare berhasil
if ($stmt === false) {
    die('Error preparing the SQL statement: ' . $koneksi->error);
}

// Bind parameter (tanda ? digantikan dengan $id_penjual)
if (!empty($search_query)) {
    // Jika ada pencarian, kita mengikat 4 parameter (id_penjual dan 3 search_query)
    $stmt->bind_param("isss", $id_penjual, $search_query, $search_query, $search_query);
} else {
    // Jika tidak ada pencarian, kita hanya mengikat 1 parameter (id_penjual)
    $stmt->bind_param("i", $id_penjual);
}

// Jalankan query
$stmt->execute();

// Ambil hasilnya
$result = $stmt->get_result();

// Periksa apakah query berhasil
if (!$result) {
    $error_message = $stmt->error;
    die("Query error: $error_message.");
}
?>

<form action="utama.php?page=order" method="get">
<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Orders List</h2>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
        <form class="searchform" method="GET" action="utama.php">
            <div class="input-group">
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
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Product</th>
                        <th scope="col">Total</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date</th>
                        <th scope="col" class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($result) {
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Badge class based on status
                            $badgeClass = "";
                            $badgeTextColor = "text-dark";

                            switch ($row['status']) {
                                case "Delivered":
                                    $badgeClass = "alert-success";
                                    $badgeTextColor = "text-dark";
                                    break;
                                case "Shipped":
                                    $badgeClass = "alert-danger";
                                    $badgeTextColor = "text-white";
                                    break;
                                case "Received":
                                    $badgeClass = "alert-warning";
                                    $badgeTextColor = "text-dark";
                                    break;
                                case "Being Processed":
                                    $badgeClass = "alert-info";
                                    $badgeTextColor = "text-dark";
                                    break;
                            }
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id_pesanan']); ?></td>
                        <td><b><?php echo htmlspecialchars($row['nama_pelanggan']); ?></b></td>
                        <td><?php echo htmlspecialchars($row['email_pelanggan']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                        <td>Rp<?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                        <td>
                            <span class="badge rounded-pill <?php echo $badgeClass; ?> <?php echo $badgeTextColor; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars(date('d.m.Y', strtotime($row['tanggal']))); ?></td>
                        <td class="text-end">
                            <a href="utama.php?page=orderdetail&id_pesanan=<?php echo $row['id_pesanan']; ?>" class="btn btn-md rounded font-sm">Detail</a>
                        </td>
                    </tr>
                <?php
                        }
                    } else {
                        echo '<tr><td colspan="8" class="text-center">No orders available yet.</td></tr>';
                    }
                } else {
                    echo '<tr><td colspan="8" class="text-center">Error fetching orders. Please check your database structure.</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</section>
</form>
