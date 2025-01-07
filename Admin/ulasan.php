<?php
include 'config.php';

// Tangani input pencarian
$search_query = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';

// Query dengan filter pencarian
$query = "SELECT 
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
          JOIN penjual ON ulasan.id_penjual = penjual.id_penjual";

// Tambahkan kondisi pencarian jika ada input
if (!empty($search_query)) {
    $search_query_escaped = mysqli_real_escape_string($koneksi, $search_query);
    $query .= " WHERE 
                produk.nama_produk LIKE '%$search_query_escaped%' OR
                pelanggan.nama LIKE '%$search_query_escaped%' OR
                penjual.nama_toko LIKE '%$search_query_escaped%' OR
                ulasan.komentar LIKE '%$search_query_escaped%'";
}

$result = mysqli_query($koneksi, $query);

if ($result) { // Pastikan query berhasil
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review List</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Font Awesome -->
</head>
<body>
    <form action="utama.php?page=ulasan" method="get">
        <section class="content-main">
            <div class="content-header">
                <div>
                    <h2 class="content-title card-title">Review List</h2>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <form class="searchform" method="GET" action="utama.php" style="margin: 0;">
                        <div class="input-group">
                            <input type="text" name="search_query" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search_query); ?>" />
                            <input type="hidden" name="page" value="ulasan" />
                            <button class="btn btn-light bg" type="submit"><i class="material-icons md-search"></i></button>
                        </div>
                    </form>
                    </br>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#ID</th>
                                    <th scope="col">Product Name</th>
                                    <th scope="col">Customer Name</th>
                                    <th scope="col">Seller Store</th>
                                    <th scope="col">Rating</th>
                                    <th scope="col">Comment</th>
                                    <th scope="col">Review Date</th>
                                    <th scope="col" class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id_ulasan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_produk']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_toko']); ?></td>
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
                                    <td><?php echo htmlspecialchars($row['komentar']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d.m.Y', strtotime($row['tanggal_ulasan']))); ?></td>
                                    <td class="text-end">
                                        <a href="utama.php?page=ulasantdetail&id_ulasan=<?php echo $row['id_ulasan']; ?>" class="btn btn-md rounded font-sm">Delete</a>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </form>
</body>
</html>
<?php
} else {
    echo "Data tidak ditemukan.";
}
?>
