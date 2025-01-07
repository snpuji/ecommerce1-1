<?php
include 'config.php'; // Koneksi database

if (!isset($_SESSION['email'])) {
    die('Error: Anda harus login untuk mengakses halaman ini.');
}

$email = $_SESSION['email']; // Ambil email dari sesi

// Ambil data pelanggan berdasarkan email dari database
$query = "SELECT id_pelanggan, nama, alamat, no_hp FROM pelanggan WHERE email = '$email'";
$result = mysqli_query($koneksi, $query);
if (!$result) {
    die('Query Error: ' . mysqli_error($koneksi));
}

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $id_pelanggan = $row['id_pelanggan'];
    $nama_pelanggan = $row['nama'];
    $alamat_pelanggan = $row['alamat'];
    $telepon_pelanggan = $row['no_hp'];
} else {
    die('Error: Pengguna tidak ditemukan.');
}

// Inisialisasi variabel untuk menyimpan produk
$products = [];
$total_price = 0;

// Periksa apakah permintaan berasal dari shopdetails.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_produk'])) {
    // Ambil data dari shopdetails.php
    $id_produk = $_POST['id_produk'];
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $gambar = $_POST['gambar']; // Pastikan ini ada

    // Ambil data produk berdasarkan id_produk
    $query_produk = "SELECT id_kategori FROM produk WHERE id_produk = ?";
    $stmt_produk = $koneksi->prepare($query_produk);
    $stmt_produk->bind_param("i", $id_produk);
    $stmt_produk->execute();
    $result_produk = $stmt_produk->get_result();

    if ($result_produk->num_rows > 0) {
        $row_produk = $result_produk->fetch_assoc(); // Ambil data produk
    } else {
        die('Error: Produk tidak ditemukan.');
    }

    // Inisialisasi variabel untuk ukuran
    $s = $m = $l = $extralarge = $doubleextralarge = 0;

    // Untuk kategori 17 dan 18
    if ($row_produk['id_kategori'] == 17 || $row_produk['id_kategori'] == 18) {
        $s = isset($_POST['s']) ? (int)$_POST['s'] : 0;
        $m = isset($_POST['m']) ? (int)$_POST['m'] : 0;
        $l = isset($_POST['l']) ? (int)$_POST['l'] : 0;
        $extralarge = isset($_POST['extralarge']) ? (int)$_POST['extralarge'] : 0;
        $doubleextralarge = isset($_POST['doubleextralarge']) ? (int)$_POST['doubleextralarge'] : 0;

        // Hitung total kuantitas
        $total_quantity = $s + $m + $l + $extralarge + $doubleextralarge;
    } else {
        // Untuk kategori lain
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        $total_quantity = $quantity;
    }

    // Pastikan total_quantity lebih dari 0
    if ($total_quantity > 0) {
        // Tambahkan produk ke array jika kuantitas lebih dari 0
        $products[] = [
            'id_produk' => $id_produk,
            'nama_produk' => $nama_produk,
            'harga' => $harga,
            'quantity' => $total_quantity,
            'small' => $s,
            'medium' => $m,
            'large' => $l,
            'extralarge' => $extralarge,
            'doubleextralarge' => $doubleextralarge,
            'gambar' => $gambar // Pastikan gambar juga ditambahkan ke produk
        ];
    }
} else {
    // Ambil produk yang dipilih berdasarkan id_produk yang diterima dari keranjang
    $selected_ids = isset($_GET['id_produk']) ? $_GET['id_produk'] : '';
    $selected_ids = explode(',', $selected_ids);

    // Query untuk mengambil data produk dari keranjang
    $query = "SELECT k.id_produk, k.nama_produk, k.gambar, k.harga, k.quantity, 
                     k.small, k.medium, k.large, k.extralarge, k.doubleextralarge
              FROM keranjang k
              WHERE k.id_pelanggan = '$id_pelanggan' AND k.id_produk IN (" . implode(",", array_map('intval', $selected_ids)) . ")";
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        die('Query Error: ' . mysqli_error($koneksi));
    }

    // Ambil data produk dari keranjang
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

// Hitung total harga dari produk
foreach ($products as $product) {
    $total_price += $product['harga'] * $product['quantity'];
}
// Proses checkout jika tombol "Proceed to Checkout" diklik
if (isset($_POST['proceed_to_checkout'])) {
    // Simpan data pesanan ke tabel pesanan1
    $tanggal_pemesanan = date('Y-m-d H:i:s');
    $notes = isset($_POST['notes']) ? $_POST['notes'] : '';

    // Debug: Cek apakah variabel sudah terisi dengan benar
    echo "Tanggal Pemesanan: $tanggal_pemesanan, Notes: $notes<br>";

    $query_pesanan = "INSERT INTO pesanan1 (id_pelanggan, nama, alamat, no_hp, total_harga, tanggal_pemesanan, notes, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, 'Being Processed')";
    $stmt_pesanan = $koneksi->prepare($query_pesanan);
    
    // Periksa jika terjadi kesalahan pada prepare
    if (!$stmt_pesanan) {
        die('Prepare Error (Pesanan): ' . mysqli_error($koneksi));
    }

    // Binding parameter dengan total_price sebagai float
    $stmt_pesanan->bind_param("issdsss", $id_pelanggan, $nama_pelanggan, $alamat_pelanggan, $telepon_pelanggan, $total_price, $tanggal_pemesanan, $notes);
    
    // Eksekusi query dan periksa kesalahan
    if (!$stmt_pesanan->execute()) {
        die('Query Insert Pesanan Error: ' . mysqli_error($koneksi));
    }

    // Ambil id_pesanan yang baru saja dimasukkan
    $id_pesanan = $stmt_pesanan->insert_id;
    echo "ID Pesanan yang baru dimasukkan: $id_pesanan<br>";

    // Simpan detail pesanan ke tabel detail_pesanan
    foreach ($products as $product) {
        // Inisialisasi kolom ukuran dengan nilai default 0
        $small = 0;
        $medium = 0;
        $large = 0;
        $extralarge = 0;
        $doubleextralarge = 0;

        // Debug: Cek data produk yang akan diproses
        echo "Processing product: ID: {$product['id_produk']}, Size: {$product['size']}, Quantity: {$product['quantity']}<br>";

        // Tentukan kolom yang sesuai berdasarkan ukuran yang dipilih
        if ($product['size'] == 'small') {
            $small = $product['quantity']; // Masukkan quantity ke kolom small
        } elseif ($product['size'] == 'medium') {
            $medium = $product['quantity']; // Masukkan quantity ke kolom medium
        } elseif ($product['size'] == 'large') {
            $large = $product['quantity']; // Masukkan quantity ke kolom large
        } elseif ($product['size'] == 'extralarge') {
            $extralarge = $product['quantity']; // Masukkan quantity ke kolom extralarge
        } elseif ($product['size'] == 'doubleextralarge') {
            $doubleextralarge = $product['quantity']; // Masukkan quantity ke kolom doubleextralarge
        }

        // Hitung subtotal untuk produk
        $subtotal = $product['harga'] * $product['quantity'];
        echo "Subtotal for product {$product['id_produk']}: $subtotal<br>";

        // Query untuk menyimpan detail pesanan
        $query_detail = "INSERT INTO detail_pesanan (id_produk, id_pesanan, id_penjual, quantity, subtotal, small, medium, large, extralarge, doubleextralarge) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_detail = $koneksi->prepare($query_detail);

        // Periksa jika terjadi kesalahan pada prepare
        if (!$stmt_detail) {
            die('Prepare Error (Detail Pesanan): ' . mysqli_error($koneksi));
        }

        $id_penjual = 1; // Ganti dengan id_penjual yang sesuai
        $stmt_detail->bind_param("iiidiiiiii", $product['id_produk'], $id_pesanan, $id_penjual, $product['quantity'], $subtotal, $small, $medium, $large, $extralarge, $doubleextralarge);

        // Eksekusi query dan periksa kesalahan
        if (!$stmt_detail->execute()) {
            die('Query Insert Detail Pesanan Error: ' . mysqli_error($koneksi));
        }
        
        echo "Detail pesanan untuk produk ID {$product['id_produk']} berhasil disimpan.<br>";
    }

    // Redirect ke halaman konfirmasi atau detail pesanan
    header("Location: checkout.php?id_pesanan=$id_pesanan");
    exit();
}
?>

<!-- HTML untuk menampilkan checkout -->
<section class="purchase-section spad">
    <div class="container">
        <div class="row">
            <!-- Main Form Area -->
            <div class="col-lg-7">
                <div class="purchase__form">
                    <form method="POST" action="">
                        <!-- Billing Details Section -->
                        <div class="billing-details">
                            <h4><strong>Billing Details</strong></h4>
                            <hr>
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" id="full_name" name="nama" class="form-control" 
                                       value="<?php echo htmlspecialchars($nama_pelanggan); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" id="address" name="alamat" class="form-control" 
                                       value="<?php echo htmlspecialchars($alamat_pelanggan); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="no_hp" class="form-control" 
                                       value="<?php echo htmlspecialchars($telepon_pelanggan); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="notes">Notes (Optional)</label>
                                <textarea id="notes" name="notes" class="form-control" placeholder="Write any special request or notes here..."></textarea>
                            </div>
                        </div>
                        <button type="submit" class="primary-btn" name="proceed_to_checkout">Proceed to Checkout</button>
                    </form>
                </div>
            </div>

            <!-- Order Summary Area -->
            <div class="col-lg-5">
                <div class="cart__total">
                    <div class="your-order">
                        <h6 class="order-title">Your Order</h6>
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($products)) {
                                    foreach ($products as $product) {
                                        // Menentukan total berdasarkan harga dan kuantitas
                                        $total = $product['harga'] * $product['quantity'];

                                        // Menampilkan ukuran
                                        $sizes = [];
                                        if ($product['small'] > 0) {
                                            $sizes[] = 'S (' . $product['small'] . ')';
                                        }
                                        if ($product['medium'] > 0) {
                                            $sizes[] = 'M (' . $product['medium'] . ')';
                                        }
                                        if ($product['large'] > 0) {
                                            $sizes[] = 'L (' . $product['large'] . ')';
                                        }
                                        if ($product['extralarge'] > 0) {
                                            $sizes[] = 'XL (' . $product['extralarge'] . ')';
                                        }
                                        if ($product['doubleextralarge'] > 0) {
                                            $sizes[] = 'XXL (' . $product['doubleextralarge'] . ')';
                                        }

                                        // Gabungkan ukuran menjadi string
                                        $size_display = implode(', ', $sizes);

                                        echo '
                                        <tr>
                                            <td class="product__item">
                                                <img src="../upload/' . htmlspecialchars($product['gambar']) . '" alt="Product Image" class="product-image">
                                                <div class="product-details">
                                                    <h6>' . htmlspecialchars($product['nama_produk']) . '</h6>
                                                    <h5>Rp.' . number_format($product['harga'], 0, ',', '.') . '</h5>
                                                </div>
                                            </td>
                                            <td class="size__item" style="text-align: center; padding: 10px;">' . $size_display . '</td>
                                            <td class="quantity__item" style="text-align: center; padding: 10px;">' . $product['quantity'] . '</td>
                                            <td class="cart__price">Rp.' . number_format($total, 0, ',', '.') . '</td>
                                        </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="no-products">No selected products in your cart.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <h6 class="order-summary-title">Order Summary</h6>
                    <ul class="order-summary">
                        <li>Subtotal <span id="subtotal-display">Rp.<?php echo number_format($total_price, 0, ',', '.'); ?></span></li>
                        <li>Total <span id="total-display">Rp.<?php echo number_format($total_price, 0, ',', '.'); ?></span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>


<style>
    #notes {
        border: 2px solid #ccc;
        padding: 10px;
        width: 100%;
        height: 100px;
        border-radius: 8px;
        font-size: 14px;
        resize: none;
    }
    #notes:focus {
        border-color: #007BFF;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    .cart__total {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .order-title, .order-summary-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 15px;
    }
    .order-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .order-table th, .order-table td {
        text-align: center;
        padding: 10px;
        font-size: 0.9rem;
    }
    .order-table thead th {
        border-bottom: 2px solid #ddd;
        font-size: 1rem;
        font-weight: bold;
    }
    .product__item {
        display: flex;
        align-items: center;
    }
    .product-image {
        width: 60px;
        height: 60px;
        margin-right: 10px;
        border-radius: 5px;
        object-fit: cover;
    }
    .product-details h6 {
        font-size: 1rem;
        margin: 0;
        font-weight: 600;
    }
    .product-details h5 {
        font-size: 0.9rem;
        margin: 0;
        color: #888;
    }
    .quantity__item {
        font-size: 0.9rem;
    }
    .cart__price {
        font-size: 0.9rem;
        font-weight: bold;
    }
    .no-products {
        text-align: center;
        font-size: 0.9rem;
        color: #888;
    }
    .order-summary {
        list-style: none;
        padding: 0;
        margin: 0;
        font-size: 1rem;
    }
    .order-summary li {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-weight: bold;
    }
    .primary-btn {
        background-color: #28a745;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
    }
    .primary-btn:hover {
        background-color: #218838;
    }
</style>
