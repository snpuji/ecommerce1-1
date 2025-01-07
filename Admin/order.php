<?php
include 'config.php';

// Ambil nilai pencarian dari parameter GET
$search_query = isset($_GET['search_query']) ? mysqli_real_escape_string($koneksi, $_GET['search_query']) : '';

// Query dasar
$query = "SELECT 
            pesanan1.id_pesanan, 
            pelanggan.nama AS nama_pelanggan, 
            pelanggan.email AS email_pelanggan,
            produk.nama_produk, 
            pesanan1.total_harga, 
            pesanan1.status, 
            pesanan1.tanggal_pemesanan,
            penjual.nama_toko
          FROM pesanan1
          JOIN detail_pesanan ON pesanan1.id_pesanan = detail_pesanan.id_pesanan
          JOIN produk ON detail_pesanan.id_produk = produk.id_produk
          JOIN pelanggan ON pesanan1.id_pelanggan = pelanggan.id_pelanggan
          JOIN penjual ON detail_pesanan.id_penjual = penjual.id_penjual";

// Jika ada pencarian, tambahkan klausa WHERE
if (!empty($search_query)) {
    $query .= " WHERE 
                pelanggan.nama LIKE '%$search_query%' OR 
                pelanggan.email LIKE '%$search_query%' OR
                penjual.nama_toko LIKE '%$search_query%' OR 
                pesanan1.id_pesanan LIKE '%$search_query%' OR
                pesanan1.status LIKE '%$search_query%'";
}

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}
?>

<form action="utama.php?page=order" method="get">
<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Orders List</h2>
        </div>
        <div>
            <a href="utama.php?page=addorder" class="btn btn-primary btn-sm rounded">Create new</a>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <!-- Form Pencarian -->
            <form class="searchform" method="GET" action="utama.php" style="margin: 0;">
                <div class="input-group">
                    <input type="text" name="search_query" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search_query); ?>" />
                    <input type="hidden" name="page" value="order" />
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
                            <th scope="col">Nama Toko</th>
                            <th scope="col">Total</th>
                            <th scope="col">Status</th>
                            <th scope="col">Date</th>
                            <th scope="col" class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            // Badge class based on status
                            $badgeClass = ""; // Default
                            $badgeTextColor = "text-dark"; // Default text color

                            switch ($row['status']) {
                                case "Delivered":
                                    $badgeClass = "alert-success";
                                    $badgeTextColor = "text-dark";
                                    break;
                                case "Canceled":
                                    $badgeClass = "alert-danger";
                                    $badgeTextColor = "text-white";
                                    break;
                                case "Shipped":
                                    $badgeClass = "alert-warning";
                                    $badgeTextColor = "text-dark";
                                    break;
                                case "Received":
                                    $badgeClass = "alert-info";
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
                            <td><?php echo htmlspecialchars($row['nama_toko']); ?></td>
                            <td>Rp<?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="badge rounded-pill <?php echo $badgeClass; ?> <?php echo $badgeTextColor; ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(date('d.m.Y', strtotime($row['tanggal_pemesanan']))); ?></td>
                            <td class="text-end">
                                <a href="utama.php?page=orderdetail&id_pesanan=<?php echo $row['id_pesanan']; ?>" class="btn btn-md rounded font-sm">Detail</a>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                        // Tampilkan baris kosong jika tidak ada data
                        echo '<tr><td colspan="8" class="text-center">No orders found.</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
