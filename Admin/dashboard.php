<?php
include 'config.php';

// Inisialisasi array untuk menyimpan pesan kesalahan
$errors = [];

// Query untuk mendapatkan 5 pesanan terbaru
$query = "SELECT 
            pesanan1.id_pesanan, 
            pelanggan.nama AS nama_pelanggan, 
            pelanggan.email AS email_pelanggan,
            produk.nama_produk, 
            pesanan1.total_harga, 
            pesanan1.status, 
            pesanan1.tanggal_pemesanan,
            detail_pesanan.id_penjual AS nama_toko
          FROM pesanan1
          JOIN pelanggan ON pesanan1.id_pelanggan = pelanggan.id_pelanggan
          JOIN detail_pesanan ON pesanan1.id_pesanan = detail_pesanan.id_pesanan
          JOIN produk ON detail_pesanan.id_produk = produk.id_produk
          ORDER BY pesanan1.tanggal_pemesanan DESC LIMIT 5";

$result = mysqli_query($koneksi, $query);
if (!$result) {
    $errors[] = "Error in query pesanan1: " . mysqli_error($koneksi);
}

// Query untuk mendapatkan jumlah pelanggan
$query_pelanggan = "SELECT COUNT(*) AS total_pelanggan FROM pelanggan";
$result_pelanggan = mysqli_query($koneksi, $query_pelanggan);
if (!$result_pelanggan) {
    $errors[] = "Error in query pelanggan: " . mysqli_error($koneksi);
} else {
    $row_pelanggan = mysqli_fetch_assoc($result_pelanggan);
}

// Query untuk mendapatkan jumlah pesanan
$query_pesanan = "SELECT COUNT(*) AS total_pesanan FROM pesanan1";
$result_pesanan = mysqli_query($koneksi, $query_pesanan);
if (!$result_pesanan) {
    $errors[] = "Error in query pesanan1: " . mysqli_error($koneksi);
} else {
    $row_pesanan = mysqli_fetch_assoc($result_pesanan);
}

// Query untuk mendapatkan jumlah kategori produk
$query_kategori = "SELECT COUNT(*) AS total_kategori FROM kategori_produk";
$result_kategori = mysqli_query($koneksi, $query_kategori);
if (!$result_kategori) {
    $errors[] = "Error in query kategori_produk: " . mysqli_error($koneksi);
} else {
    $row_kategori = mysqli_fetch_assoc($result_kategori);
}

// Query untuk mendapatkan jumlah produk
$query_produk = "SELECT COUNT(*) AS total_produk FROM produk";
$result_produk = mysqli_query($koneksi, $query_produk);
if (!$result_produk) {
    $errors[] = "Error in query produk: " . mysqli_error($koneksi);
} else {
    $row_produk = mysqli_fetch_assoc($result_produk);
}

// Query untuk menghitung total revenue
$query_revenue = "
    SELECT SUM(pesanan1.total_harga) AS total_revenue
    FROM pembayaran
    JOIN pesanan1 ON pembayaran.id_pesanan = pesanan1.id_pesanan
    WHERE pembayaran.status = 'Paid'
";
$result_revenue = mysqli_query($koneksi, $query_revenue);
if (!$result_revenue) {
    $errors[] = "Error in query pembayaran: " . mysqli_error($koneksi);
} else {
    $row_revenue = mysqli_fetch_assoc($result_revenue);
}

// Query untuk mendapatkan total penjual
$query_penjual = "SELECT COUNT(*) AS total_penjual FROM penjual";
$result_penjual = mysqli_query($koneksi, $query_penjual);
if (!$result_penjual) {
    $errors[] = "Error in query penjual: " . mysqli_error($koneksi);
} else {
    $row_penjual = mysqli_fetch_assoc($result_penjual);
}

// Query untuk mendapatkan jumlah pengiriman dengan status "Delivered" dari pesanan1
$query_pengiriman = "SELECT COUNT(*) AS total_pengiriman FROM pesanan1 WHERE status = 'Delivered'";
$result_pengiriman = mysqli_query($koneksi, $query_pengiriman);
if (!$result_pengiriman) {
    $errors[] = "Error in query pengiriman: " . mysqli_error($koneksi);
} else {
    $row_pengiriman = mysqli_fetch_assoc($result_pengiriman);
}


// Tampilkan error jika ada
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color:red;'>$error</p>";
    }
}
?>




<style>
        /* Custom styles for dashboard cards */
        .card-dashboard {
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-weight: 500;
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .card-text {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .icontext {
            display: flex;
            align-items: center;
            justify-content: flex-start; /* Align items to the left */
            margin-bottom: 10px;
        }

        .icon-container {
            font-size: 2rem; /* Ukuran ikon lebih besar untuk menonjol */
            color: #fff;
            border-radius: 50%;
            padding: 20px;
            width: 60px; /* Ukuran lingkaran lebih besar */
            height: 60px;
            text-align: center;
            line-height: 60px;
            transition: transform 0.3s ease-in-out;
            margin-right: 15px; /* Add margin to the right to separate the icon and text */
        }

        /* Soft colors for each icon */
        .bg-info {
            background-color: #4db8ff; /* Soft info color */
        }

        .bg-success {
            background-color: #28a745; /* Soft success color */
        }

        .bg-warning {
            background-color: #ffc107; /* Soft warning color */
        }

        .bg-primary {
            background-color: #007bff; /* Soft primary color */
        }

        .stat-card {
            text-align: center;
            padding: 20px;
        }

        .stat-card:hover {
            transform: scale(1.05);
        }

        /* Responsive styling */
        @media (max-width: 576px) {
            .card-dashboard {
                margin-bottom: 20px;
            }
        }
    </style>

    </br>
    </br>

    <div class="container mt-5">
        <div class="row">
            <!-- Total Pelanggan Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-body card-dashboard bg-white">
                    <article class="icontext">
                        <!-- Icon -->
                        <span class="icon-container bg-info">
                            <i class="fas fa-users"></i>
                        </span>
                        <div class="text">
                            <h6 class="card-title">Total Pelanggan</h6>
                            <p class="card-text"><?php echo number_format($row_pelanggan['total_pelanggan']); ?></p>
                        </div>
                    </article>
                </div>
            </div>

              <!-- Total Penjual Card -->
              <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-body card-dashboard bg-white">
                    <article class="icontext">
                        <!-- Icon -->
                        <span class="icon-container bg-info">
                            <i class="fas fa-store"></i> <!-- Icon for sellers -->
                        </span>
                        <div class="text">
                            <h6 class="card-title">Total Penjual</h6>
                            <p class="card-text"><?php echo number_format($row_penjual['total_penjual']); ?></p>
                        </div>
                    </article>
                </div>
            </div>

            <!-- Total Pesanan Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-body card-dashboard bg-white">
                    <article class="icontext">
                        <!-- Icon -->
                        <span class="icon-container bg-success">
                            <i class="fas fa-shopping-cart"></i> <!-- Changed to a shopping cart icon -->
                        </span>
                        <div class="text">
                            <h6 class="card-title">Total Pesanan</h6>
                            <p class="card-text"><?php echo number_format($row_pesanan['total_pesanan']); ?></p>
                        </div>
                    </article>
                </div>
            </div>

            <!-- Total Kategori Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-body card-dashboard bg-white">
                    <article class="icontext">
                        <!-- Icon -->
                        <span class="icon-container bg-primary">
                            <i class="fas fa-th-large"></i>
                        </span>
                        <div class="text">
                            <h6 class="card-title">Total Kategori</h6>
                            <p class="card-text"><?php echo number_format($row_kategori['total_kategori']); ?></p>
                        </div>
                    </article>
                </div>
            </div>

            <!-- Total Produk Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-body card-dashboard bg-white">
                    <article class="icontext">
                        <!-- Icon -->
                        <span class="icon-container bg-warning">
                            <i class="fas fa-box"></i> <!-- Changed to a box icon for product -->
                        </span>
                        <div class="text">
                            <h6 class="card-title">Total Produk</h6>
                            <p class="card-text"><?php echo number_format($row_produk['total_produk']); ?></p>
                        </div>
                    </article>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-body card-dashboard bg-white">
                    <article class="icontext">
                        <!-- Icon -->
                        <span class="icon-container bg-success">
                            <i class="fas fa-dollar-sign"></i> <!-- Icon for revenue -->
                        </span>
                        <div class="text">
                            <h6 class="card-title">Revenue</h6>
                            <p class="card-text"><?php echo number_format($row_revenue['total_revenue'], 0, ',', '.'); ?> IDR</p>
                        </div>
                    </article>
                </div>
            </div>

            <!-- Pengiriman Dikirim Card -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card card-body card-dashboard bg-white">
                    <article class="icontext">
                        <!-- Icon -->
                        <span class="icon-container bg-warning">
                            <i class="fas fa-truck"></i> <!-- Icon for shipping -->
                        </span>
                        <div class="text">
                            <h6 class="card-title">Pengiriman Dikirim</h6>
                            <p class="card-text"><?php echo number_format($row_pengiriman['total_pengiriman']); ?></p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
    <section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Latest Orders</h2>
        </div>
        <div>
            <a href="utama.php?page=order" class="btn btn-primary btn-sm rounded">See All Orders</a>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
        <form class="searchform" method="GET" action="utama.php">
    <div class="input-group">
        <input list="search_terms" type="text" name="search_query" class="form-control" placeholder="Search..." value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>" />
        <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'product'; ?>" />
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
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Badge class based on status
                        $badgeClass = ""; // Default
                        $badgeTextColor = "text-dark"; // Default text color

                        switch ($row['status']) {
                            case "Delivered":
                                $badgeClass = "alert-success"; // Green
                                $badgeTextColor = "text-dark"; // Dark text for better contrast
                                break;
                            case "Canceled":
                                $badgeClass = "alert-danger"; // Red
                                $badgeTextColor = "text-white"; // White text for better contrast
                                break;
                            case "Shipped":
                                $badgeClass = "alert-warning"; // Yellow
                                $badgeTextColor = "text-dark"; // Dark text for better contrast
                                break;
                            case "Being Processed":
                                $badgeClass = "alert-info"; // Blue
                                $badgeTextColor = "text-dark"; // Dark text for better contrast
                                break;
                                case "Received":
                                    $badgeClass = "alert-info"; // Blue
                                    $badgeTextColor = "text-dark"; // Dark text for better contrast
                                    break;
                            }
                    ?>
                        <tr>
                            <!-- ID Pesanan -->
                            <td><?php echo htmlspecialchars($row['id_pesanan']); ?></td>
                            

                            <!-- Nama Pelanggan -->
                            <td><b><?php echo htmlspecialchars($row['nama_pelanggan']); ?></b></td>
                            
                            <!-- Email Pelanggan -->
                            <td><?php echo htmlspecialchars($row['email_pelanggan']); ?></td>
                            
                            <!-- Nama Toko -->
                            <td><?php echo htmlspecialchars($row['nama_toko']); ?></td>

                            <!-- Total Harga -->
                            <td>Rp<?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                            
                            <!-- Status -->
                            <td>
                                <span class="badge rounded-pill <?php echo $badgeClass; ?> <?php echo $badgeTextColor; ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            
                            <!-- Tanggal -->
                            <td><?php echo htmlspecialchars(date('d.m.Y', strtotime($row['tanggal_pemesanan']))); ?></td>
                            
                            <!-- Aksi -->
                            <td class="text-end">
                                <!-- Tautan untuk detail sesuai id pesanan -->
                                <a href="utama.php?page=orderdetail&id_pesanan=<?php echo $row['id_pesanan']; ?>" class="btn btn-md rounded font-sm">Detail</a>
                                <div class="dropdown">
                                    <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm">
                                        <i class="material-icons md-more_horiz"></i>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#">View detail</a>
                                        <a class="dropdown-item" href="#">Edit info</a>
                                        <a class="dropdown-item text-danger" href="#">Cancel order</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

