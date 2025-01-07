<?php
include 'config.php';

// Validasi apakah pengguna sudah login
if (!isset($_SESSION['username']) || !isset($_SESSION['id_penjual'])) {
    header("Location: index.php");
    exit;
}

// Ambil data pengguna dari sesi
$id_penjual = $_SESSION['id_penjual'];

// Ambil query pencarian jika ada
$search_query = isset($_GET['search_query']) ? mysqli_real_escape_string($koneksi, $_GET['search_query']) : '';

// Query untuk menampilkan ulasan berdasarkan id_penjual dan kata kunci pencarian
$query = "
    SELECT 
        ulasan.id_ulasan, 
        produk.nama_produk, 
        pelanggan.nama AS nama_pelanggan, 
        penjual.nama_toko, 
        ulasan.rating, 
        ulasan.komentar, 
        ulasan.tanggal_ulasan
    FROM ulasan
    JOIN produk ON ulasan.id_produk = produk.id_produk
    JOIN pelanggan ON ulasan.id_pelanggan = pelanggan.id_pelanggan
    JOIN penjual ON ulasan.id_penjual = penjual.id_penjual
    WHERE ulasan.id_penjual = '$id_penjual'";

// Jika ada kata kunci pencarian, tambahkan kondisi LIKE pada query
if (!empty($search_query)) {
    $query .= " AND (produk.nama_produk LIKE '%$search_query%' 
                  OR pelanggan.nama LIKE '%$search_query%' 
                  OR ulasan.komentar LIKE '%$search_query%')";
}

// Menjalankan query
$result = mysqli_query($koneksi, $query);

// Periksa apakah query berhasil
if (!$result) {
    die('Query Error: ' . mysqli_error($koneksi)); // Menampilkan pesan error jika query gagal
}
?>

<head>
    <title>Review List</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Font Awesome -->
</head>
<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Reviews List</h2>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <form class="searchform" method="GET" action="utama.php">
                <div class="input-group">
                    <!-- Input pencarian -->
                    <input list="search_terms" type="text" name="search_query" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search_query); ?>" />
                    <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? $_GET['page'] : 'product'; ?>" />
                    <button class="btn btn-light bg" type="submit"></button>
                </div>
            </form>
            </br>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#ID Ulasan</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Customer Name</th>
                            <th scope="col">Shop Name</th>
                            <th scope="col">Rating</th>
                            <th scope="col">Comment</th>
                            <th scope="col">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                        <tr>
                            <!-- ID Ulasan -->
                            <td><?php echo htmlspecialchars($row['id_ulasan']); ?></td>
                             <!-- Nama Produk -->
                            <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                             <!-- Nama Pelanggan -->
                            <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                             <!-- Nama Toko -->
                            <td><?php echo htmlspecialchars($row['nama_toko']); ?></td>
                             <!-- Rating -->
                            <td>
                            <?php
                                        $rating = (int)$row['rating']; // Pastikan rating dalam bentuk integer
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $rating) {
                                                echo '<i class="fas fa-star text-warning"></i>'; // Bintang penuh
                                            } else {
                                                echo '<i class="far fa-star text-warning"></i>'; // Bintang kosong
                                            }
                                        }
                                        ?>
                                    </td>

                                    <!-- Komentar -->
                                    <td><?php echo htmlspecialchars($row['komentar']); ?></td>

                                    <!-- Tanggal Ulasan -->
                                    <td><?php echo htmlspecialchars(date('d.m.Y', strtotime($row['tanggal_ulasan']))); ?></td>
                        </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='7'>No reviews found.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
