<?php
include 'config.php';

// Tangkap parameter pencarian dari URL
$search_query = isset($_GET['search_query']) ? mysqli_real_escape_string($koneksi, $_GET['search_query']) : '';

// Query dengan filter pencarian
$query = "SELECT 
            p.id_pengiriman, 
            p.id_pesanan, 
            p.id_jasa_pengiriman, 
            p.nomor_resi, 
            pesanan1.alamat AS alamat_pengiriman, 
            pesanan1.status AS status_pesanan,  
            p.tgl_dikirim, 
            p.tgl_diterima, 
            j.jasa_pengiriman
          FROM pengiriman p
          JOIN jasa_pengiriman j ON p.id_jasa_pengiriman = j.id_jasa_pengiriman
          JOIN pesanan1 ON p.id_pesanan = pesanan1.id_pesanan";

// Tambahkan filter pencarian jika ada parameter pencarian
if (!empty($search_query)) {
    $query .= " WHERE 
                p.id_pengiriman LIKE '%$search_query%' OR
                p.id_pesanan LIKE '%$search_query%' OR
                pesanan1.alamat LIKE '%$search_query%' OR
                j.jasa_pengiriman LIKE '%$search_query%' OR
                p.nomor_resi LIKE '%$search_query%' OR
                pesanan1.status LIKE '%$search_query%'";
}

// Eksekusi query
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}
?>

<form action="utama.php?page=pengiriman" method="get">
    <section class="content-main">
        <div class="content-header">
            <div>
                <h2 class="content-title card-title">Shipping List</h2>
            </div>
            <div>
                <a href="utama.php?page=addpengiriman" class="btn btn-primary btn-sm rounded">Create new</a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <!-- Form Pencarian -->
                <form class="searchform" method="GET" action="utama.php" style="margin: 0;">
                    <div class="input-group">
                        <input type="text" name="search_query" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search_query); ?>" />
                        <input type="hidden" name="page" value="pengiriman" />
                        <button class="btn btn-light bg" type="submit"><i class="material-icons md-search"></i></button>
                    </div>
                </form>
                <br>

                <!-- Tabel Data -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th scope="col">Order ID</th>
                                <th scope="col">Shipping Service</th>
                                <th scope="col">Tracking No</th>
                                <th scope="col">Shipping Address</th>
                                <th scope="col">Status</th>
                                <th scope="col">Shipped Date</th>
                                <th scope="col">Received Date</th>
                                <th scope="col" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $badgeClass = "";
                                $badgeTextColor = "text-dark";

                                switch ($row['status_pesanan']) {
                                    case "Received":
                                        $badgeClass = "alert-success";
                                        break;
                                    case "Being Processed":
                                        $badgeClass = "alert-warning";
                                        break;
                                    case "Shipped":
                                    case "Delivered":
                                        $badgeClass = "alert-info";
                                        break;
                                }
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id_pengiriman']); ?></td>
                                <td><?php echo htmlspecialchars($row['id_pesanan']); ?></td>
                                <td><?php echo htmlspecialchars($row['jasa_pengiriman']); ?></td>
                                <td><?php echo htmlspecialchars($row['nomor_resi']); ?></td>
                                <td><?php echo htmlspecialchars($row['alamat_pengiriman']); ?></td>
                                <td>
                                    <span class="badge rounded-pill <?php echo $badgeClass; ?> <?php echo $badgeTextColor; ?>">
                                        <?php echo htmlspecialchars($row['status_pesanan']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(date('d.m.Y', strtotime($row['tgl_dikirim']))); ?></td>
                                <td><?php echo htmlspecialchars(date('d.m.Y', strtotime($row['tgl_diterima']))); ?></td>
                                <td class="text-end">
                                    <a href="utama.php?page=pengirimandetail&id_pengiriman=<?php echo $row['id_pengiriman']; ?>" class="btn btn-md rounded font-sm">Detail</a>
                                </td>
                            </tr>
                        <?php
                            }
                        } else {
                            echo '<tr><td colspan="9" class="text-center">No shipments found.</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</form>
