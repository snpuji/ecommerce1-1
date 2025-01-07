<?php
include 'config.php';

// Ambil input pencarian dari GET request
$search_query = isset($_GET['search_query']) ? mysqli_real_escape_string($koneksi, $_GET['search_query']) : '';

// Proses perubahan status pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id_pembayaran = $_POST['id_pembayaran'];
    $new_status = $_POST['new_status'];

    if (!empty($id_pembayaran) && !empty($new_status)) {
        $query_update = "UPDATE pembayaran SET status = '" . mysqli_real_escape_string($koneksi, $new_status) . "' WHERE id_pembayaran = '" . mysqli_real_escape_string($koneksi, $id_pembayaran) . "'";
        $update_result = mysqli_query($koneksi, $query_update);

        if ($update_result) {
            echo "<script>alert('Status berhasil diperbarui.');</script>";
        } else {
            echo "<script>alert('Gagal memperbarui status: " . mysqli_error($koneksi) . "');</script>";
        }
    } else {
        echo "<script>alert('ID Pembayaran atau status tidak boleh kosong.');</script>";
    }
}

// Query untuk menampilkan data dengan filter pencarian
$query = "SELECT 
            pembayaran.id_pembayaran, 
            pembayaran.id_pesanan, 
            pembayaran.metode_pembayaran, 
            pembayaran.status, 
            pembayaran.tanggal_pembayaran, 
            pesanan1.total_harga  AS total_harga
          FROM pembayaran
          JOIN pesanan1 ON pembayaran.id_pesanan = pesanan1.id_pesanan";

// Tambahkan filter pencarian jika ada input
if (!empty($search_query)) {
    $query .= " WHERE 
                pembayaran.id_pembayaran LIKE '%$search_query%' OR 
                pembayaran.id_pesanan LIKE '%$search_query%' OR 
                pembayaran.status LIKE '%$search_query%' OR 
                pembayaran.metode_pembayaran LIKE '%$search_query%'";
}

$result = mysqli_query($koneksi, $query);

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
            <div>
                <a href="utama.php?page=addtransaksi" class="btn btn-primary btn-sm rounded">Create new</a>
            </div>
        </div>

        <div class="card mb-4">
        <div class="card-body">
            <form class="searchform" method="GET" action="utama.php" style="margin: 0;">
                <div class="input-group">
                    <input type="text" name="search_query" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search_query); ?>" />
                    <input type="hidden" name="page" value="transaksi" />
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
                    while ($row = mysqli_fetch_assoc($result)) {
                        $badgeClass = "";
                        $badgeTextColor = "text-dark";

                        switch ($row['status']) {
                            case "Paid":
                                $badgeClass = "alert-success";
                                $badgeTextColor = "text-dark";
                                break;
                            case "Waiting for Payment":
                                $badgeClass = "alert-warning";
                                $badgeTextColor = "text-dark";
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
                            <td><?php echo htmlspecialchars(date('d.m.Y', strtotime($row['tanggal_pembayaran']))); ?></td>
                            <td><?php echo htmlspecialchars(number_format($row['total_harga'], 2, ',', '.')); ?></td>
                            <td class="text-end">
                                <form method="post" onsubmit="return confirm('Apakah Anda yakin ingin mengubah status?');">
                                    <input type="hidden" name="id_pembayaran" value="<?php echo htmlspecialchars($row['id_pembayaran']); ?>">
                                    <select name="new_status" class="form-select d-inline-block mb-lg-0 mr-5 mw-200" required>
                                        <option selected>Change status</option>
                                        <option value="Waiting for Payment">Awaiting payment</option>
                                        <option value="Paid">Paid</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary btn-sm PX-3">Update</button>
                                </form>
                                <td class="text-end">
                                    <a href="utama.php?page=transaksidetail&id_pembayaran=<?php echo $row['id_pembayaran']; ?>" class="btn btn-md rounded font-sm">Detail</a>
                                </td>
                            </td>
                            
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</form>
