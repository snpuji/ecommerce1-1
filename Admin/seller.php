<?php
include 'config.php'; // Pastikan koneksi database

// Inisialisasi variabel untuk pencarian
$search_query = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';

// Query untuk mengambil data dari tabel penjual dengan filter pencarian
$query_penjual = "SELECT id_penjual, username, nama_toko FROM penjual";
if ($search_query !== '') {
    $query_penjual .= " WHERE username LIKE '%" . mysqli_real_escape_string($koneksi, $search_query) . "%' 
                        OR nama_toko LIKE '%" . mysqli_real_escape_string($koneksi, $search_query) . "%'";
}
$result_penjual = mysqli_query($koneksi, $query_penjual);

?>

<section class="content-main">
    <div class="row">
        <div class="col-12">
            <div class="content-header d-flex justify-content-between align-items-center">
                <h2 class="content-title">Seller Management</h2>
                <a href="utama.php?page=addseller" class="btn btn-primary">Add New Seller</a>
            </div>
        </div>
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Seller List</h4>
                    <form class="searchform" method="GET" action="utama.php" style="margin: 0;">
                        <div class="input-group">
                            <input list="search_terms" type="text" name="search_query" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search_query); ?>" />
                            <input type="hidden" name="page" value="seller" />
                            <button class="btn btn-light bg" type="submit"><i class="material-icons md-search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Store Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Loop melalui hasil query untuk menampilkan data
                                if (mysqli_num_rows($result_penjual) > 0) {
                                    while ($row = mysqli_fetch_assoc($result_penjual)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['id_penjual']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nama_toko']) . "</td>";
                                        echo "<td>
                                                <a href='utama.php?page=sellerdetail&id=" . $row['id_penjual'] . "' class='btn btn-sm btn-warning'>Details</a>
                                                <a href='utama.php?page=deleteseller&id=" . $row['id_penjual'] . "' onclick='return confirm(\"Are you sure you want to delete this seller?\");' class='btn btn-sm btn-danger'>Delete</a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No sellers found for \"" . htmlspecialchars($search_query) . "\"</td></tr>";
                                }
                                
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
