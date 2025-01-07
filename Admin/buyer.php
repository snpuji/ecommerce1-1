<?php
include 'config.php'; // Pastikan koneksi database tersedia

// Inisialisasi variabel untuk pencarian
$search_query = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';

// Query untuk mengambil data dari tabel pelanggan dengan filter pencarian
$query = "SELECT id_pelanggan, username, nama, email, no_hp, alamat FROM pelanggan";
if ($search_query !== '') {
    $search_query_escaped = mysqli_real_escape_string($koneksi, $search_query);
    $query .= " WHERE username LIKE '%$search_query_escaped%' 
                OR nama LIKE '%$search_query_escaped%' 
                OR email LIKE '%$search_query_escaped%'";
}
$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi)); // Menampilkan error jika query gagal
}

if (mysqli_num_rows($result) > 0) {
    // Inisialisasi nomor urut
    $nomor = 1;
?>
<section class="content-main">
    <div class="content-header">
        <div>
            <h2 class="content-title card-title">Buyer List</h2>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <!-- Form pencarian -->
            <form class="searchform" method="GET" action="utama.php" style="margin: 0;">
                <div class="input-group">
                    <input type="text" name="search_query" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search_query); ?>" />
                    <input type="hidden" name="page" value="buyer" />
                    <button class="btn btn-light bg" type="submit"><i class="material-icons md-search"></i></button>
                </div>
            </form>
</br>
            <!-- Header untuk judul kolom tabel -->
            <div class="table-header">
                <div class="row align-items-center">
                    <div class="col-lg-1 col-md-1 col-sm-1"><strong>No</strong></div>
                    <div class="col-lg-2 col-md-2 col-sm-2"><strong>ID Pelanggan</strong></div>
                    <div class="col-lg-2 col-md-2 col-sm-2"><strong>Username</strong></div>
                    <div class="col-lg-2 col-md-2 col-sm-2"><strong>Nama</strong></div>
                    <div class="col-lg-2 col-md-2 col-sm-2"><strong>Email</strong></div>
                    <div class="col-lg-1 col-md-1 col-sm-1"><strong>No. HP</strong></div>
                    <div class="col-lg-2 col-md-2 col-sm-2"><strong>Alamat</strong></div>
                </div>
            </div>
            <hr>
            <?php
            // Menampilkan hasil pencarian
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <article class="itemlist">
                    <div class="row align-items-center">
                        <div class="col-lg-1 col-md-1 col-sm-1">
                            <h6 class="mb-0"><?php echo $nomor++; ?></h6>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <span><?php echo htmlspecialchars($row['id_pelanggan']); ?></span>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <span><?php echo htmlspecialchars($row['username']); ?></span>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <span><?php echo htmlspecialchars($row['nama']); ?></span>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <span><?php echo htmlspecialchars($row['email']); ?></span>
                        </div>
                        <div class="col-lg-1 col-md-1 col-sm-1">
                            <span><?php echo htmlspecialchars($row['no_hp']); ?></span>
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-2">
                            <span><?php echo htmlspecialchars($row['alamat']); ?></span>
                        </div>
                    </div>
                </article>
                <?php
            }
            ?>
        </div>
    </div>
</section>
<?php
} else {
    echo "<p>Data tidak ditemukan.</p>";
}
?>
