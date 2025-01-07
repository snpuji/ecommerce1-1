<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Mulai sesi hanya jika belum dimulai
}

include 'config.php'; // Pastikan sudah terkoneksi ke database

// Pastikan ID penjual tersedia dalam sesi
if (!isset($_SESSION['id_penjual'])) {
    echo '<div style="color:red">Anda harus login terlebih dahulu.</div>';
    exit;
}
$id_penjual_sesi = $_SESSION['id_penjual']; // Ambil ID penjual dari sesi

// Mendapatkan ID produk dari URL
if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];

    // Query untuk mendapatkan data produk
    $query_produk = "SELECT * FROM produk WHERE id_produk = '$id_produk'";
    $result_produk = mysqli_query($koneksi, $query_produk);

    if (!$result_produk) {
        echo '<div style="color:red">Error: ' . mysqli_error($koneksi) . '</div>';
        exit;
    }

    $produk = mysqli_fetch_assoc($result_produk);

    if (!$produk) {
        echo '<div style="color:red">Produk tidak ditemukan.</div>';
        exit;
    }

    // Query untuk mendapatkan kategori produk
    $query_kategori = "SELECT id_kategori, nama_kategori FROM kategori_produk";
    $result_kategori = mysqli_query($koneksi, $query_kategori);
    
    if (!$result_kategori) {
        echo '<div style="color:red">Error: ' . mysqli_error($koneksi) . '</div>';
        exit;
    }

    // Query untuk mendapatkan penjual produk
    // Modifikasi query ini untuk hanya menampilkan penjual yang sesuai dengan ID penjual sesi
    $query_penjual = "SELECT id_penjual, nama_toko FROM penjual WHERE id_penjual = '$id_penjual_sesi'";
    $result_penjual = mysqli_query($koneksi, $query_penjual);
    
    if (!$result_penjual) {
        echo '<div style="color:red">Error: ' . mysqli_error($koneksi) . '</div>';
        exit;
    }

    // Query untuk mendapatkan stok produk berdasarkan id_produk
    $query_stok = "SELECT * FROM stok_ukuran WHERE id_produk = '$id_produk'";
    $result_stok = mysqli_query($koneksi, $query_stok);
    
    if (!$result_stok) {
        echo '<div style="color:red">Error: ' . mysqli_error($koneksi) . '</div>';
        exit;
    }

    $stok = mysqli_fetch_assoc($result_stok);

    // Proses saat form disubmit
    if (isset($_POST['submit'])) {
        // Mengambil data dari form
        $id_kategori = $_POST['id_kategori'];
        $id_penjual = $_POST['id_penjual']; // ID penjual tetap dari sesi, tapi form tetap menerima input
        $nama_produk = $_POST['nama_produk'];
        $deskripsi = $_POST['deskripsi'];
        $harga = $_POST['harga'];
        $gambar = $_FILES['gambar']['name'];

        // Validasi kategori
        if ($id_kategori != 17 && $id_kategori != 18 && $id_kategori != 19 && $id_kategori != 20) {
            echo '<div style="color:red">Kategori tidak valid. Pilih kategori dengan ID 17, 18, 19, atau 20.</div>';
            exit;
        }

        // Jika gambar baru di-upload
        if ($gambar) {
            $target_dir = "../upload/";
            $target_file = $target_dir . basename($gambar);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Validasi ekstensi file gambar
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowed_extensions)) {
                echo '<div style="color:red">Only JPG, JPEG, PNG & GIF files are allowed.</div>';
                exit;
            }

            // Mengupload gambar
            if ($_FILES['gambar']['error'] == 0) {
                move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file);
            } else {
                echo '<div style="color:red">Gagal mengupload gambar.</div>';
                exit;
            }
        } else {
            $gambar = $produk['gambar']; // Jika tidak ada gambar baru, gunakan gambar lama
        }

        // Mengupdate data produk
        $query_update = "UPDATE produk 
                         SET id_kategori = '$id_kategori', 
                             id_penjual = '$id_penjual', 
                             nama_produk = '$nama_produk', 
                             deskripsi = '$deskripsi', 
                             harga = '$harga', 
                             gambar = '$gambar' 
                         WHERE id_produk = '$id_produk'";
        $result_update = mysqli_query($koneksi, $query_update);

        if (!$result_update) {
            echo '<div style="color:red">Error: ' . mysqli_error($koneksi) . '</div>';
            exit;
        }

        // Mengupdate stok produk berdasarkan kategori
        if ($id_kategori == 17 || $id_kategori == 18) { // Kategori fashion
            $stok_small = $_POST['stok_small'];
            $stok_medium = $_POST['stok_medium'];
            $stok_large = $_POST['stok_large'];
            $stok_extralarge = $_POST['stok_extralarge'];
            $stok_doubleextralarge = $_POST['stok_doubleextralarge'];

            // Hitung total stok
            $total_stok = $stok_small + $stok_medium + $stok_large + $stok_extralarge + $stok_doubleextralarge;

            // Update stok produk untuk kategori fashion
            $query_stok_update = "UPDATE stok_ukuran 
                                  SET small = '$stok_small', 
                                      medium = '$stok_medium', 
                                      large = '$stok_large', 
                                      extralarge = '$stok_extralarge', 
                                      doubleextralarge = '$stok_doubleextralarge', 
                                      total_stok = '$total_stok' 
                                  WHERE id_produk = '$id_produk'";
            $result_stok_update = mysqli_query($koneksi, $query_stok_update);

            if (!$result_stok_update) {
                echo '<div style="color:red">Error: ' . mysqli_error($koneksi) . '</div>';
                exit;
            }
        } else { // Kategori selain fashion
            $total_stok = $_POST['total_stok'];

            // Update stok total untuk kategori selain fashion
            $query_stok_update = "UPDATE stok_ukuran
                                  SET total_stok = '$total_stok', 
                                      stok = '$total_stok' 
                                  WHERE id_produk = '$id_produk'";
            $result_stok_update = mysqli_query($koneksi, $query_stok_update);

            if (!$result_stok_update) {
                echo '<div style="color:red">Gagal memperbarui stok produk.</div>';
                exit;
            }
        }

        // Redirect setelah update sukses
        header("Location: utama.php?page=product");
        exit;
    }
}
?>

<form action="editproduct.php?id=<?php echo $id_produk; ?>" method="post" enctype="multipart/form-data">
<section class="content-main">
    <div class="row">
        <div class="col-9">
            <div class="content-header">
                <h2 class="content-title">Edit Product</h2>
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
                        <input type="text" class="form-control" value="<?php echo $id_produk; ?>" disabled />
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Category</label>
                        <select name="id_kategori" id="id_kategori" class="form-control" required>
                            <option value="" disabled>Select category</option>
                            <?php
                            while ($row_kategori = mysqli_fetch_assoc($result_kategori)) {
                                $selected = ($produk['id_kategori'] == $row_kategori['id_kategori']) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($row_kategori['id_kategori']) . "' $selected>" . htmlspecialchars($row_kategori['nama_kategori']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Seller</label>
                        <select name="id_penjual" class="form-control" required>
                            <option value="" disabled>Select seller</option>
                            <?php
                            while ($row_penjual = mysqli_fetch_assoc($result_penjual)) {
                                $selected = ($produk['id_penjual'] == $row_penjual['id_penjual']) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($row_penjual['id_penjual']) . "' $selected>" . htmlspecialchars($row_penjual['nama_toko']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="nama_produk" value="<?php echo htmlspecialchars($produk['nama_produk']); ?>" class="form-control" required />
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Description</label>
                        <textarea name="deskripsi" class="form-control" rows="4" required><?php echo htmlspecialchars($produk['deskripsi']); ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Price</label>
                        <input type="number" name="harga" value="<?php echo htmlspecialchars($produk['harga']); ?>" class="form-control" required />
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*" />
                        <small>Current image: <?php echo htmlspecialchars($produk['gambar']); ?></small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Total Stock</label>
                        <input type="number" name="total_stok" value="<?php echo htmlspecialchars($stok['total_stok']); ?>" class="form-control" />
                    </div>
                    <div id="stok_sizes" style="display:<?php echo (in_array($produk['id_kategori'], [17, 18])) ? 'block' : 'none'; ?>;">
                        <div class="mb-4">
                            <label class="form-label">Small Stock</label>
                            <input type="number" name="stok_small" value="<?php echo htmlspecialchars($stok['small']); ?>" class="form-control" />
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Medium Stock</label>
                            <input type="number" name="stok_medium" value="<?php echo htmlspecialchars($stok['medium']); ?>" class="form-control" />
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Large Stock</label>
                            <input type="number" name="stok_large" value="<?php echo htmlspecialchars($stok['large']); ?>" class="form-control" />
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Extra Large Stock</label>
                            <input type="number" name="stok_extralarge" value="<?php echo htmlspecialchars($stok['extralarge']); ?>" class="form-control" />
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Double Extra Large Stock</label>
                            <input type="number" name="stok_doubleextralarge" value="<?php echo htmlspecialchars($stok['doubleextralarge']); ?>" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary">Update Product</button>
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
        if (this.value == '17'||this.value == '18') { // Kategori fashion
            stokSizes.style.display = 'block'; // Tampilkan input stok ukuran
        } else {
            stokSizes.style.display = 'none'; // Sembunyikan input stok ukuran
        }
    });
</script>
