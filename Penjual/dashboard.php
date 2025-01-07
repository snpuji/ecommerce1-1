<?php
include 'config.php';

$id_penjual = isset($_SESSION['id_penjual']) ? $_SESSION['id_penjual'] : null;

if (!$id_penjual) {
    die('Session id_penjual is not set.');
}

// Variabel untuk menyimpan error query
$errors = [];

// Query untuk mendapatkan daftar pesanan terbaru
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
          JOIN pelanggan ON pesanan1.id_pelanggan = pelanggan.id_pelanggan
          JOIN detail_pesanan ON detail_pesanan.id_pesanan = pesanan1.id_pesanan
          JOIN produk ON detail_pesanan.id_produk = produk.id_produk
          JOIN penjual ON penjual.id_penjual = detail_pesanan.id_penjual
          WHERE detail_pesanan.id_penjual = '$id_penjual'
          ORDER BY pesanan1.tanggal_pemesanan DESC LIMIT 5";

$result = mysqli_query($koneksi, $query);
if (!$result) {
    $errors[] = "Error in query pesanan1: " . mysqli_error($koneksi);
}

// Ambil nilai pencarian jika ada
$search_query = isset($_GET['search_query']) ? mysqli_real_escape_string($koneksi, $_GET['search_query']) : '';

// Query utama dengan pencarian
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
          JOIN pelanggan ON pesanan1.id_pelanggan = pelanggan.id_pelanggan
          JOIN detail_pesanan ON detail_pesanan.id_pesanan = pesanan1.id_pesanan
          JOIN produk ON detail_pesanan.id_produk = produk.id_produk
          JOIN penjual ON penjual.id_penjual = detail_pesanan.id_penjual
          WHERE detail_pesanan.id_penjual = '$id_penjual'";

// Tambahkan kondisi pencarian jika ada
if (!empty($search_query)) {
    $query .= " AND (
                    pelanggan.nama LIKE '%$search_query%' OR 
                    pelanggan.email LIKE '%$search_query%' OR 
                    penjual.nama_toko LIKE '%$search_query%'
                )";
}

// Tambahkan pengurutan dan limit
$query .= " ORDER BY pesanan1.tanggal_pemesanan DESC LIMIT 5";

// Eksekusi query
$result = mysqli_query($koneksi, $query);

if (!$result) {
    $errors[] = "Error in query pesanan1: " . mysqli_error($koneksi);
}


// Query untuk mendapatkan jumlah pelanggan unik
$query_pelanggan = "SELECT COUNT(DISTINCT pelanggan.id_pelanggan) AS total_pelanggan 
                    FROM pesanan1
                    JOIN pelanggan ON pesanan1.id_pelanggan = pelanggan.id_pelanggan
                    JOIN detail_pesanan ON detail_pesanan.id_pesanan = pesanan1.id_pesanan
                    WHERE detail_pesanan.id_penjual = '$id_penjual'";
$result_pelanggan = mysqli_query($koneksi, $query_pelanggan);
$row_pelanggan = $result_pelanggan ? mysqli_fetch_assoc($result_pelanggan) : [];
if (!$result_pelanggan) {
    $errors[] = "Error in query pelanggan: " . mysqli_error($koneksi);
}

// Query untuk mendapatkan jumlah total pesanan
$query_pesanan = "SELECT COUNT(*) AS total_pesanan 
                  FROM pesanan1 
                  JOIN detail_pesanan ON detail_pesanan.id_pesanan = pesanan1.id_pesanan
                  WHERE detail_pesanan.id_penjual = '$id_penjual'";
$result_pesanan = mysqli_query($koneksi, $query_pesanan);
$row_pesanan = $result_pesanan ? mysqli_fetch_assoc($result_pesanan) : [];
if (!$result_pesanan) {
    $errors[] = "Error in query pesanan: " . mysqli_error($koneksi);
}

// Query untuk mendapatkan jumlah produk
$query_produk = "SELECT COUNT(*) AS total_produk 
                 FROM produk 
                 WHERE id_penjual = '$id_penjual'";
$result_produk = mysqli_query($koneksi, $query_produk);
$row_produk = $result_produk ? mysqli_fetch_assoc($result_produk) : [];
if (!$result_produk) {
    $errors[] = "Error in query produk: " . mysqli_error($koneksi);
}

// Query untuk menghitung total revenue
$query_revenue = "SELECT SUM(pesanan1.total_harga) AS total_revenue 
                  FROM pesanan1 
                  JOIN detail_pesanan ON detail_pesanan.id_pesanan = pesanan1.id_pesanan 
                  WHERE detail_pesanan.id_penjual = '$id_penjual' AND pesanan1.status = 'Paid'";
$result_revenue = mysqli_query($koneksi, $query_revenue);
$row_revenue = $result_revenue ? mysqli_fetch_assoc($result_revenue) : [];
if (!$result_revenue) {
    $errors[] = "Error in query revenue: " . mysqli_error($koneksi);
}

// Query untuk jumlah pengiriman yang sedang dikirim
$query_pengiriman = "SELECT COUNT(*) AS total_pengiriman 
                     FROM pengiriman 
                     JOIN pesanan1 ON pengiriman.id_pesanan = pesanan1.id_pesanan
                     JOIN detail_pesanan ON detail_pesanan.id_pesanan = pesanan1.id_pesanan 
                     WHERE detail_pesanan.id_penjual = '$id_penjual' AND pengiriman.status_pengiriman = 'Send'";
$result_pengiriman = mysqli_query($koneksi, $query_pengiriman);
$row_pengiriman = $result_pengiriman ? mysqli_fetch_assoc($result_pengiriman) : [];
if (!$result_pengiriman) {
    $errors[] = "Error in query pengiriman: " . mysqli_error($koneksi);
}

// Menampilkan error jika ada
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p>Error: $error</p>";
    }
    exit;
}
?>


   <style>
        .card-dashboard {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .icontext {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .icon-container {
            font-size: 2rem;
            color: #fff;
            border-radius: 50%;
            padding: 20px;
            width: 60px;
            height: 60px;
            text-align: center;
            line-height: 60px;
            margin-right: 15px;
        }

        .bg-info { background-color: #4db8ff; }
        .bg-success { background-color: #28a745; }
        .bg-warning { background-color: #ffc107; }
        .bg-primary { background-color: #007bff; }
    </style>
</head>
    </br>
    </br>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-body card-dashboard bg-white">
                    <article class="icontext">
                        <span class="icon-container bg-info">
                            <i class="fas fa-users"></i>
                        </span>
                        <div class="text">
                            <h6>Total Pelanggan</h6>
                            <p><?php echo number_format(isset($row_pelanggan['total_pelanggan']) ? $row_pelanggan['total_pelanggan'] : 0); ?></p>
                        </div>
                    </article>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-body card-dashboard bg-white">
                    <article class="icontext">
                        <span class="icon-container bg-success">
                            <i class="fas fa-shopping-cart"></i>
                        </span>
                        <div class="text">
                            <h6>Total Pesanan</h6>
                            <p><?php echo number_format(isset($row_pesanan['total_pesanan']) ? $row_pesanan['total_pesanan'] : 0); ?></p>
                        </div>
                    </article>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-body card-dashboard bg-white">
                    <article class="icontext">
                        <span class="icon-container bg-warning">
                            <i class="fas fa-box"></i>
                        </span>
                        <div class="text">
                            <h6>Total Produk</h6>
                            <p><?php echo number_format(isset($row_produk['total_produk']) ? $row_produk['total_produk'] : 0); ?></p>
                        </div>
                    </article>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-body card-dashboard bg-white">
                    <article class="icontext">
                        <span class="icon-container bg-primary">
                            <i class="fas fa-dollar-sign"></i>
                        </span>
                        <div class="text">
                            <h6>Revenue</h6>
                            <p><?php echo number_format(isset($row_revenue['total_revenue']) ? $row_revenue['total_revenue'] : 0, 0, ',', '.'); ?> IDR</p>
                        </div>
                    </article>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-body card-dashboard bg-white">
                    <article class="icontext">
                        <span class="icon-container bg-warning">
                            <i class="fas fa-truck"></i>
                        </span>
                        <div class="text">
                            <h6>Pengiriman Dikirim</h6>
                            <p><?php echo number_format(isset($row_pengiriman['total_pengiriman']) ? $row_pengiriman['total_pengiriman'] : 0); ?></p>
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
            <form class="searchform" method="GET" action="utama.php">
    <div class="input-group">
        <!-- Input pencarian -->
        <input list="search_terms" type="text" name="search_query" class="form-control" placeholder="Search..." />
        <input type="hidden" name="page" value="dashboard" />
        <button class="btn btn-light bg" type="submit"><i class="material-icons md-search"></i></button>
    </div>
</form>

    </br>
                <h5>Pesanan Terbaru</h5>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Nama Toko</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0) { ?>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id_pesanan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email_pelanggan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_toko']); ?></td>
                                    <td>Rp<?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d.m.Y', strtotime($row['tanggal_pemesanan']))); ?></td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="text-center">Data tidak ditemukan.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
