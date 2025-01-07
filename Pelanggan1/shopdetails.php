<?php
// Koneksi ke database
include 'config.php';

// Ambil ID produk dari URL dengan aman
$id_produk = isset($_GET['id_produk']) ? (int)$_GET['id_produk'] : 0;

$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';

// Cek apakah pengguna sudah login
if (!isset($email)) {
    die("Anda belum login. Silakan login terlebih dahulu.");
}

// Cari id_pelanggan berdasarkan email
$sql_pelanggan = "SELECT id_pelanggan FROM Pelanggan WHERE email = ?";
$stmt_pelanggan = $koneksi->prepare($sql_pelanggan);
$stmt_pelanggan->bind_param("s", $email);
$stmt_pelanggan->execute();
$result_pelanggan = $stmt_pelanggan->get_result();

if ($result_pelanggan->num_rows > 0) {
    $row_pelanggan = $result_pelanggan->fetch_assoc();
    $id_pelanggan = $row_pelanggan['id_pelanggan'];
} else {
    die("Data pelanggan tidak ditemukan.");
}

// Ambil detail produk untuk ditampilkan
$sql_produk = "SELECT * FROM produk WHERE id_produk = ?";
$stmt_produk = $koneksi->prepare($sql_produk);
$stmt_produk->bind_param("i", $id_produk);
$stmt_produk->execute();
$result_produk = $stmt_produk->get_result();

if ($result_produk->num_rows === 0) {
    die("Produk tidak ditemukan.");
}

$row_produk = $result_produk->fetch_assoc();
$id_kategori = $row_produk['id_kategori']; // Ambil id_kategori produk

// Ambil stok berdasarkan ukuran dan total stok
$sql_stok = "SELECT small, medium, large, extralarge, doubleextralarge, total_stok FROM stok_ukuran WHERE id_produk = ?";
$stmt_stok = $koneksi->prepare($sql_stok);
$stmt_stok->bind_param("i", $id_produk);
$stmt_stok->execute();
$result_stok = $stmt_stok->get_result();
$stok_row = $result_stok->fetch_assoc();

// Ambil ulasan produk
$sql_ulasan = "SELECT u.id_pelanggan, u.rating, u.komentar, u.tanggal_ulasan, p.nama 
               FROM ulasan u
               JOIN pelanggan p ON u.id_pelanggan = p.id_pelanggan
               WHERE u.id_produk = ? ORDER BY u.tanggal_ulasan DESC LIMIT 5";
$stmt_ulasan = $koneksi->prepare($sql_ulasan);
$stmt_ulasan->bind_param("i", $id_produk); // Bind parameter untuk ID produk
$stmt_ulasan->execute();
$result_ulasan = $stmt_ulasan->get_result(); // Ambil hasil

// Proses jika formulir dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil kuantitas untuk setiap ukuran
    if ($row_produk['id_kategori'] == 17 || $row_produk['id_kategori'] == 18) {
        $s = isset($_POST['s']) ? (int)$_POST['s'] : 0;
        $m = isset($_POST['m']) ? (int)$_POST['m'] : 0;
        $l = isset($_POST['l']) ? (int)$_POST['l'] : 0;
        $extralarge = isset($_POST['extralarge']) ? (int)$_POST['extralarge'] : 0;
        $doubleextralarge = isset($_POST['doubleextralarge']) ? (int)$_POST['doubleextralarge'] : 0;

        // Hitung total kuantitas
        $quantity = $s + $m + $l + $extralarge + $doubleextralarge;

        if ($s + $m + $l + $extralarge + $doubleextralarge <= 0) {
            die("Harap masukkan kuantitas untuk setidaknya satu ukuran.");
        }
     
    } else {
        // Untuk kategori lain, ambil kuantitas dari input
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

        if ($quantity <= 0) {
            die("Harap masukkan kuantitas yang valid.");
        }

        // Set ukuran lain ke 0
        $s = 0;
        $m = 0;
        $l = 0;
        $extralarge = 0;
        $doubleextralarge = 0;
    }

    // Cek apakah produk sudah ada di keranjang
    $query_check = "SELECT * FROM keranjang WHERE id_pelanggan = ? AND id_produk = ?";
    $stmt_check = $koneksi->prepare($query_check);
    $stmt_check->bind_param("ii", $id_pelanggan, $id_produk);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Jika produk sudah ada, tambahkan quantity
        $query_update = "UPDATE keranjang 
                         SET small = small + ?, medium = medium + ?, large = large + ?, extralarge = extralarge + ?, doubleextralarge = doubleextralarge + ?, updated_at = NOW()
                         WHERE id_pelanggan = ? AND id_produk = ?";
        $stmt_update = $koneksi->prepare($query_update);
        $stmt_update->bind_param("iiiiiii", $s, $m, $l, $extralarge, $doubleextralarge, $id_pelanggan, $id_produk);
        $stmt_update->execute();
    } else {
        // Jika produk belum ada, tambahkan ke keranjang
        $query_insert = "INSERT INTO keranjang (id_pelanggan, id_produk, nama_produk, gambar, harga, quantity, small, medium, large, extralarge, doubleextralarge, created_at, updated_at)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt_insert = $koneksi->prepare($query_insert);
        $nama_produk = $row_produk['nama_produk'];
        $gambar = $row_produk['gambar'];
        $harga = $row_produk['harga'];

        $stmt_insert->bind_param("iissiissiii", $id_pelanggan, $id_produk, $nama_produk, $gambar, $harga, $quantity, $s, $m, $l, $extralarge, $doubleextralarge);
        $stmt_insert->execute();
    }

    // Redirect setelah berhasil menambahkan ke keranjang
    header('Location: utama.php?page=cart');
    exit();
}
?>

<style>
    .fa-star {
        font-size: 20px; /* Ukuran bintang */
        color: #ccc; /* Warna default bintang (abu-abu) */
        margin-right: 5px;
    }

    .fa-star.checked {
        color: gold; /* Warna bintang aktif (emas) */
    }
    .product__details__option__size {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 10px;
}

.product__details__option__size div {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background-color: #f7f7f7;
    border: 1px solid #ddd;
    border-radius: 5px;
    text-align: center;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    position: relative;
    margin-bottom: 10px; /* Jarak bawah */
    transition: all 0.3s ease;
}

.product__details__option__size div:hover {
    background-color: #eaeaea;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.product__details__option__size input[type="number"] {
    margin-top: 5px;
    width: 50px;
    height: 30px;
    font-size: 14px;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 3px;
    background-color: #fff;
}

.product__details__option__size span {
    font-size: 12px;
    color: #888;
    margin-top: 5px;
}

</style>

<!-- Shop Details Section Begin -->
<section class="shop-details">
    <div class="product__details__pic">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="product__details__breadcrumb">
                        <a href="index.php">Home</a>
                        <a href="utama.php?page=shop">Shop</a>
                        <span>Product Details</span>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-6 col-md-9">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tabs-1" role="tabpanel">
                            <div class="product__details__pic__item">
                                <!-- Dynamically Fetch Product Image -->
                                <?php
                                $sql = "SELECT gambar FROM produk WHERE id_produk = $id_produk";
                                $result = $koneksi->query($sql);

                                if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $gambar = $row['gambar'];

                                    // Tentukan path direktori upload
                                    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Ecommerce1/upload/';
                                    $imagePath = $uploadDir . $gambar;

                                    // Cek apakah file gambar ada
                                    if (file_exists($imagePath)) {
                                        // Jika file ada, tampilkan gambar
                                        echo '<img src="/Ecommerce1/upload/' . htmlspecialchars($gambar) . '" alt="Product Image">';
                                    } else {
                                        // Jika file tidak ada, tampilkan gambar default
                                        echo '<img src="/Ecommerce1/upload/default.jpg" alt="Default Image">';
                                    }
                                } else {
                                    // Jika tidak ada data produk, tampilkan gambar default
                                    echo '<img src="/Ecommerce1/upload/default.jpg" alt="Default Image">';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-9">
                    <div class="product__details__text">
                        <?php
                        // Fetch detail produk
                        $sql = "SELECT id_produk, nama_produk, harga, deskripsi, id_kategori, gambar FROM produk WHERE id_produk = $id_produk";
                        $result = $koneksi->query($sql);

                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $nama_produk = $row['nama_produk'];
                            $gambar = $row['gambar']; // Pastikan gambar diambil dari database
                        ?>
                            <h4><?php echo htmlspecialchars($row['nama_produk']); ?></h4>
                            <h3>Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?></h3>

                            <form action="" method="POST">
                                <!-- Hidden Input Fields -->
                                <input type="hidden" name="id_produk" value="<?php echo $id_produk; ?>">
                                <input type="hidden" name="harga" value="<?php echo $row['harga']; ?>">
                                <input type="hidden" name="nama_produk" value="<?php echo htmlspecialchars($nama_produk); ?>">
                                <input type="hidden" name="gambar" value="<?php echo htmlspecialchars($gambar); ?>">
                                <input type="hidden" name="size" id="selected-size" value=""> <!-- Hidden input untuk ukuran -->

                                <?php if ($row['id_kategori'] == 17 || $row['id_kategori'] == 18): ?>
                                    <div class="product__details__option">
                                        <h5>Enter Quantity for Each Size</h5>
                                </br>
                                        <div class="product__details__option__size">
                                            <?php if (isset($stok_row['small']) && $stok_row['small'] > 0): ?>
                                                <div>
                                                    <strong>S</strong>
                                                    <input type="number" name="s" id="s" min="0" max="<?php echo $stok_row['small']; ?>" value="0">
                                                    <span>Stok: <?php echo $stok_row['small']; ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (isset($stok_row['medium']) && $stok_row['medium'] > 0): ?>
                                                <div>
                                                    <strong>M</strong>
                                                    <input type="number" name="m" id="m" min="0" max="<?php echo $stok_row['medium']; ?>" value="0">
                                                    <span>Stok: <?php echo $stok_row['medium']; ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (isset($stok_row['large']) && $stok_row['large'] > 0): ?>
                                                <div>
                                                    <strong>L</strong>
                                                    <input type="number" name="l" id="l" min="0" max="<?php echo $stok_row['large']; ?>" value="0">
                                                    <span>Stok: <?php echo $stok_row['large']; ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (isset($stok_row['extralarge']) && $stok_row['extralarge'] > 0): ?>
                                                <div>
                                                    <strong>XL</strong>
                                                    <input type="number" name="extralarge" id="extralarge" min="0" max="<?php echo $stok_row['extralarge']; ?>" value="0">
                                                    <span>Stok: <?php echo $stok_row['extralarge']; ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (isset($stok_row['doubleextralarge']) && $stok_row['doubleextralarge'] > 0): ?>
                                                <div>
                                                    <strong>XXL</strong>
                                                    <input type="number" name="doubleextralarge" id="doubleextralarge" min="0" max="<?php echo $stok_row['doubleextralarge']; ?>" value="0">
                                                    <span>Stok: <?php echo $stok_row['doubleextralarge']; ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- Untuk kategori lain, tampilkan input kuantitas saja -->
                                    <div class="product__details__option">
                                        <h5>Enter Quantity</h5>
                                        <input type="number" name="quantity" min="1" max="<?php echo $stok_row['total_stok']; ?>" value="1">
                                        <span>Total Stok: <?php echo $stok_row['total_stok']; ?></span>
                                    </div>
                                <?php endif; ?>

                                <!-- Submit Button -->
                                <button type="submit" class="primary-btn checkout-btn" name="add_to_cart">Add To Cart</button>
                            </form>
                            <br>
                        <?php } ?>

                        <form id="product-form" action="utama.php?page=purchase" method="POST">
    <!-- Hidden Input Fields -->
    <input type="hidden" name="id_produk" value="<?php echo $id_produk; ?>">
    <input type="hidden" name="harga" value="<?php echo $row['harga']; ?>">
    <input type="hidden" name="nama_produk" value="<?php echo htmlspecialchars($nama_produk); ?>">
    <input type="hidden" name="gambar" value="<?php echo htmlspecialchars($gambar); ?>">

    <?php if ($row['id_kategori'] == 17 || $row['id_kategori'] == 18): ?>
        <div class="product__details__option">
            <h5>Enter Quantity for Each Size</h5>
            <div class="product__details__option__size">
                <div>
                    <strong>S</strong>
                    <input type="number" name="s" min="0" max="<?php echo $stok_row['small']; ?>" value="0">
                    <span>Stok: <?php echo $stok_row['small']; ?></span>
                </div>
                <div>
                    <strong>M</strong>
                    <input type="number" name="m" min="0" max="<?php echo $stok_row['medium']; ?>" value="0">
                    <span>Stok: <?php echo $stok_row['medium']; ?></span>
                </div>
                <div>
                    <strong>L</strong>
                    <input type="number" name="l" min="0" max="<?php echo $stok_row['large']; ?>" value="0">
                    <span>Stok: <?php echo $stok_row['large']; ?></span>
                </div>
                <div>
                    <strong>XL</strong>
                    <input type="number" name="extralarge" min="0" max="<?php echo $stok_row['extralarge']; ?>" value="0">
                    <span>Stok: <?php echo $stok_row['extralarge']; ?></span>
                </div>
                <div>
                    <strong>XXL</strong>
                    <input type="number" name="doubleextralarge" min="0" max="<?php echo $stok_row['doubleextralarge']; ?>" value="0">
                    <span>Stok: <?php echo $stok_row['doubleextralarge']; ?></span>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="product__details__option">
            <h5>Enter Quantity</h5>
            <input type="number" name="quantity" min="1" max="<?php echo $stok_row['total_stok']; ?>" value="1">
            <span>Total Stok: <?php echo $stok_row['total_stok']; ?></span>
        </div>
    <?php endif; ?>

    <!-- Button untuk Checkout -->
    <button type="submit" class="primary-btn checkout-btn">Checkout</button>
</form>


<script>
function checkout() {
    // Set action untuk checkout
    document.getElementById('product-form').action = 'utama.php?page=purchase';
    // Set action value untuk checkout
    const form = document.getElementById('product-form');
    const checkoutButton = document.createElement('input');
    checkoutButton.type = 'hidden';
    checkoutButton.name = 'action';
    checkoutButton.value = 'checkout';
    form.appendChild(checkoutButton);
    // Submit form
    form.submit();
}
</script>



                        <script>
                            // Update hidden fields when size or quantity changes
                            const sizeInputs = document.querySelectorAll('input[name="size"]');
                            const sizeHiddenInput = document.getElementById('selected-size');
                            const quantityInput = document.getElementById('max-stok-input');
                            const quantityHiddenInput = document.getElementById('selected-quantity');

                            sizeInputs.forEach(radio => {
                                radio.addEventListener('change', function () {
                                    sizeHiddenInput.value = this.value;
                                });
                            });

                            quantityInput.addEventListener('input', function () {
                                quantityHiddenInput.value = this.value;
                            });
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tab Deskripsi dan Ulasan -->
    <div class="row">
        <div class="col-lg-12">
            <div class="product__details__tab">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#description-tab" role="tab">Description</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#reviews-tab" role="tab">Customer Reviews</a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- Deskripsi -->
                    <div class="tab-pane fade show active" id="description-tab" role="tabpanel">
                        <div class="product__details__tab__content">
                            <p><?= htmlspecialchars(isset($row['deskripsi']) ? $row['deskripsi'] : 'No description available.'); ?></p>
                        </div>
                    </div>
                    <br>
                    <br>
                    <!-- Ulasan -->
                    <div class="tab-pane fade" id="reviews-tab" role="tabpanel">
                        <div class="row">
                            <!-- List Ulasan -->
                            <div class="col-md-5" style="margin-left: 40px;">
                                <h4 class="mb-4">Customer Reviews</h4>
                                <?php
                                if (isset($result_ulasan) && $result_ulasan->num_rows > 0) {
                                    while ($ulasan = $result_ulasan->fetch_assoc()) {
                                        echo '<div class="review">';
                                        echo '<p><strong>Rating:</strong> ';

                                        // Menampilkan bintang berdasarkan rating
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $ulasan['rating']) {
                                                echo '<span class="fa fa-star checked"></span>'; // Bintang aktif
                                            } else {
                                                echo '<span class="fa fa-star"></span>'; // Bintang tidak aktif
                                            }
                                        }

                                        echo '</p>';
                                        echo '<p><strong>' . htmlspecialchars($ulasan['nama']) . '</strong></p>';
                                        echo '<p>' . htmlspecialchars($ulasan['komentar']) . '</p>';
                                        echo '<p><small>' . htmlspecialchars($ulasan['tanggal_ulasan']) . '</small></p>';
                                        echo '</div><hr>';
                                    }
                                } else {
                                    echo '<p>No reviews yet.</p>';
                                }
                                ?>
                            </div>

                            <!-- Form Tambah Ulasan -->
                            <div class="col-md-6">
                                <h4 class="mb-4">Leave a Review</h4>
                                <form action="" method="POST">
                                    <input type="hidden" name="id_produk" value="<?= htmlspecialchars($id_produk); ?>">
                                    <input type="hidden" name="id_pelanggan" value="<?= htmlspecialchars($id_pelanggan); ?>"> <!-- ID pelanggan -->
                                    <input type="hidden" name="id_penjual" value="<?= htmlspecialchars($id_penjual); ?>"> <!-- id_penjual dinamis -->
                                    <div class="form-group">
                                        <select id="rating" name="rating" class="form-control" required>
                                            <option value="">Rating (1-5) *</option>
                                            <?php for ($i = 1; $i <= 5; $i++) : ?>
                                                <option value="<?= $i ?>"><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <textarea id="komentar" name="komentar" cols="30" rows="5" class="form-control" required placeholder="Write your review here..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary px-3">Submit Review</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

