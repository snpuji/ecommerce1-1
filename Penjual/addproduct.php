<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Mulai sesi hanya jika belum dimulai
}

include 'config.php'; // Pastikan sudah terkoneksi ke database

// Pastikan id_penjual sudah ada dalam session
if (!isset($_SESSION['id_penjual'])) {
    echo '<div style="color:red">Session tidak valid. Harap login terlebih dahulu.</div>';
    exit;
}

// Ambil id_penjual dari session
$id_penjual = $_SESSION['id_penjual'];

// Query untuk mengambil semua kategori
$query_kategori = "SELECT id_kategori, nama_kategori FROM kategori_produk";
$result_kategori = mysqli_query($koneksi, $query_kategori);

// Query untuk mengambil semua penjual
$query_penjual = "SELECT id_penjual, nama_toko FROM penjual";
$result_penjual = mysqli_query($koneksi, $query_penjual);

// Mendefinisikan nomor produk secara dinamis
$query_count = "SELECT COUNT(*) AS total FROM produk";
$result_count = mysqli_query($koneksi, $query_count);
$row_count = mysqli_fetch_assoc($result_count);
$no = $row_count['total'] + 1;

// Cek jika form dikirimkan
if (isset($_POST['submit'])) {
    // Mengambil data dari form
    $id_kategori = $_POST['id_kategori'];
    $nama_produk = $_POST['nama_produk'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];

    // Validasi untuk kategori fashion (id_kategori = 17 atau 18)
    if ($id_kategori == 17 || $id_kategori == 18) {
        // Jika kategori fashion, cek stok per ukuran
        $stok_small = isset($_POST['stok_small']) ? $_POST['stok_small'] : 0;
        $stok_medium = isset($_POST['stok_medium']) ? $_POST['stok_medium'] : 0;
        $stok_large = isset($_POST['stok_large']) ? $_POST['stok_large'] : 0;
        $stok_extralarge = isset($_POST['stok_extralarge']) ? $_POST['stok_extralarge'] : 0;
        $stok_doubleextralarge = isset($_POST['stok_doubleextralarge']) ? $_POST['stok_doubleextralarge'] : 0;

        // Pastikan setidaknya satu stok ukuran diisi
        if (
            empty($stok_small) &&
            empty($stok_medium) &&
            empty($stok_large) &&
            empty($stok_extralarge) &&
            empty($stok_doubleextralarge)
        ) {
            echo '<div style="color:red">Please fill in at least one stock field for fashion or related categories.</div>';
            exit;
        }

        // Hitung total stok dari ukuran yang diisi
        $total_stok = $stok_small + $stok_medium + $stok_large + $stok_extralarge + $stok_doubleextralarge;
        $stok = 0; // Tidak digunakan untuk kategori fashion atau kategori 18
    } else {
        // Untuk kategori selain 17 dan 18, gunakan total stok
        $total_stok = isset($_POST['total_stok']) ? $_POST['total_stok'] : 0;
        $stok_small = $stok_medium = $stok_large = $stok_extralarge = $stok_doubleextralarge = 0; // Tidak ada stok ukuran
        $stok = $total_stok; // Total stok untuk kategori selain 17 dan 18
    }

    // Proses upload gambar
    $gambar = $_FILES['gambar']['name'];
    $target_dir = "../upload/";
    $target_file = $target_dir . basename($gambar);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validasi ekstensi file gambar
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowed_extensions)) {
        echo '<div style="color:red">Only JPG, JPEG, PNG & GIF files are allowed.</div>';
        exit;
    }

    // Pastikan file gambar berhasil di-upload
    if ($_FILES['gambar']['error'] != 0) {
        echo '<div style="color:red">Error uploading image: ' . $_FILES['gambar']['error'] . '</div>';
        exit;
    }

    // Mengupload gambar
    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
        // Menambahkan data ke dalam tabel produk
        $query = "INSERT INTO produk (id_kategori, id_penjual, nama_produk, deskripsi, harga, gambar) 
                  VALUES ('$id_kategori', '$id_penjual', '$nama_produk', '$deskripsi', '$harga', '$gambar')";
        $result = mysqli_query($koneksi, $query);

        if ($result) {
            $id_produk = mysqli_insert_id($koneksi); // Mendapatkan ID produk yang baru ditambahkan

            // Menambahkan stok per ukuran untuk kategori fashion
            if ($id_kategori == 17 || $id_kategori == 18) {
                $query_stok = "INSERT INTO stok_ukuran (id_produk, small, medium, large, extralarge, doubleextralarge, total_stok) 
                               VALUES ('$id_produk', '$stok_small', '$stok_medium', '$stok_large', '$stok_extralarge', '$stok_doubleextralarge', '$total_stok')";
                $result_stok = mysqli_query($koneksi, $query_stok);
                if (!$result_stok) {
                    echo '<div style="color:red">Gagal menambahkan stok produk: ' . mysqli_error($koneksi) . '</div>';
                    exit;
                }
            } else {
                // Menambahkan stok total untuk kategori selain fashion
                $query_stok = "INSERT INTO stok_ukuran (id_produk, small, medium, large, extralarge, doubleextralarge, stok, total_stok) 
                               VALUES ('$id_produk', '$stok_small', '$stok_medium', '$stok_large', '$stok_extralarge', '$stok_doubleextralarge', '$stok', '$total_stok')";
                $result_stok = mysqli_query($koneksi, $query_stok);
                if (!$result_stok) {
                    echo '<div style="color:red">Gagal menambahkan stok produk: ' . mysqli_error($koneksi) . '</div>';
                    exit;
                }
            }

            // Redirect ke halaman utama setelah berhasil
            header("Location: utama.php?page=product");
            exit; // Pastikan script tidak melanjutkan setelah redirect
        } else {
            echo '<div style="color:red">Gagal menambahkan produk: ' . mysqli_error($koneksi) . '</div>';
        }
    } else {
        echo '<div style="color:red">Gagal mengupload gambar.</div>';
        exit;
    }
}
?>

<!-- Form HTML -->
<form action="addproduct.php" method="post" enctype="multipart/form-data">
<section class="content-main">
    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Add New Product</h2>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Basic Information</h4>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="no" class="form-label">No</label>
                        <input type="text" placeholder="Auto-generated number" class="form-control" value="<?php echo $no; ?>" disabled />
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Category</label>
                        <select name="id_kategori" id="id_kategori" class="form-control" required>
                            <option value="" disabled selected>Select category</option>
                            <?php
                            while ($row_kategori = mysqli_fetch_assoc($result_kategori)) {
                                echo "<option value='" . htmlspecialchars($row_kategori['id_kategori']) . "'>" . htmlspecialchars($row_kategori['nama_kategori']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="nama_produk" placeholder="Enter product name" class="form-control" required />
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="deskripsi" placeholder="Enter product description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Price</label>
                        <input type="number" name="harga" placeholder="Enter price" class="form-control" required />
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*" required />
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Total Stock</label>
                        <input type="number" name="total_stok" class="form-control" placeholder="Enter total stock" />
                    </div>
                    <div id="stok_sizes" style="display:none;">
                        <div class="mb-4">
                            <label class="form-label">Small Stock</label>
                            <input type="number" name="stok_small" class="form-control" />
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Medium Stock</label>
                            <input type="number" name="stok_medium" class="form-control" />
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Large Stock</label>
                            <input type="number" name="stok_large" class="form-control" />
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Extra Large Stock</label>
                            <input type="number" name="stok_extralarge" class="form-control" />
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Double Extra Large Stock</label>
                            <input type="number" name="stok_doubleextralarge" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary">Save Product</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</form>

<script>
    document.getElementById('id_kategori').addEventListener('change', function () {
    var stokSizes = document.getElementById('stok_sizes');
    if (this.value == '17' || this.value == '18') { // Kategori fashion atau kategori 18
        stokSizes.style.display = 'block'; // Tampilkan input stok ukuran
    } else {
        stokSizes.style.display = 'none'; // Sembunyikan input stok ukuran
    }
});

</script>
