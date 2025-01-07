<?php
include 'config.php'; // Koneksi database

if (!isset($_SESSION['email'])) {
    die('Error: Anda harus login untuk mengakses keranjang.');
}

$email = $_SESSION['email']; // Ambil email dari sesi

// Ambil id_pelanggan berdasarkan email dari database
$query = "SELECT id_pelanggan FROM pelanggan WHERE email = '$email'";
$result = mysqli_query($koneksi, $query);
if (!$result) {
    die('Query Error: ' . mysqli_error($koneksi));
}

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $id_pelanggan = $row['id_pelanggan']; // Ambil id_pelanggan untuk memastikan pengguna yang login memiliki id_pelanggan
} else {
    die('Error: Pengguna tidak ditemukan.');
}

// Menangani POST request untuk menambahkan produk ke keranjang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_produk'])) {
    $id_produk = $_POST['id_produk'];
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1; // Default 1 jika quantity tidak dikirim
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $gambar = $_POST['gambar'];
    $size = $_POST['size']; // Ambil ukuran yang dipilih

    $created_at = date('Y-m-d H:i:s');
    $updated_at = date('Y-m-d H:i:s');

    // Cek apakah produk sudah ada di keranjang
    $query_check = "SELECT * FROM keranjang WHERE id_pelanggan = '$id_pelanggan' AND id_produk = '$id_produk'";
    $result_check = mysqli_query($koneksi, $query_check);

    if (!$result_check) {
        die('Query Check Error: ' . mysqli_error($koneksi));
    }

    if (mysqli_num_rows($result_check) > 0) {
        // Jika produk sudah ada, tambahkan quantity
        $query_update = "UPDATE keranjang 
                         SET quantity = quantity + $quantity, updated_at = '$updated_at'
                         WHERE id_pelanggan = '$id_pelanggan' AND id_produk = '$id_produk'";
        if (!mysqli_query($koneksi, $query_update)) {
            die('Query Update Error: ' . mysqli_error($koneksi));
        }
    } else {
        // Jika produk belum ada, tambahkan ke keranjang
        // Inisialisasi kolom ukuran
        $small = 0;
        $medium = 0;
        $large = 0;
        $extralarge = 0;
        $doubleextralarge = 0;

        // Tentukan kolom yang sesuai berdasarkan ukuran
        switch ($size) {
            case 'small':
                $small = $quantity; // Masukkan quantity ke kolom small
                break;
            case 'medium':
                $medium = $quantity; // Masukkan quantity ke kolom medium
                break;
            case 'large':
                $large = $quantity; // Masukkan quantity ke kolom large
                break;
            case 'extralarge':
                $extralarge = $quantity; // Masukkan quantity ke kolom extralarge
                break;
            case 'doubleextralarge':
                $doubleextralarge = $quantity; // Masukkan quantity ke kolom doubleextralarge
                break;
        }

        $query_insert = "INSERT INTO keranjang (id_pelanggan, id_produk, nama_produk, harga, gambar, quantity, created_at, updated_at, small, medium, large, extralarge, doubleextralarge)
                         VALUES ('$id_pelanggan', '$id_produk', '$nama_produk', '$harga', '$gambar', '$quantity', '$created_at', '$updated_at', '$small', '$medium', '$large', '$extralarge', '$doubleextralarge')";
        if (!mysqli_query($koneksi, $query_insert)) {
            die('Query Insert Error: ' . mysqli_error($koneksi));
        }
    }
}

// Menangani GET request untuk menghapus produk dari keranjang
if (isset($_GET['remove_id'])) {
    $id_produk = $_GET['remove_id'];
    $query_remove = "DELETE FROM keranjang WHERE id_pelanggan = '$id_pelanggan' AND id_produk = '$id_produk'";
    if (!mysqli_query($koneksi, $query_remove)) {
        die('Query Remove Error: ' . mysqli_error($koneksi));
    }

    // Redirect untuk mencegah refresh yang menyebabkan penghapusan ulang
    header('Location: utama.php?page=cart');
    exit();
}

// Menangani POST request untuk memperbarui quantity produk di keranjang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $id_produk = $_POST['id_produk'];
    $new_quantity = intval($_POST['quantity']); // Ambil quantity baru dari form

    // Update quantity di keranjang
    $query_update_quantity = "UPDATE keranjang 
                               SET quantity = '$new_quantity' 
                               WHERE id_pelanggan = '$id_pelanggan' AND id_produk = '$id_produk'";
    if (!mysqli_query($koneksi, $query_update_quantity)) {
        die('Query Update Quantity Error: ' . mysqli_error($koneksi));
    }

    // Redirect untuk mencegah refresh yang menyebabkan penghapusan ulang
    header('Location: utama.php?page=cart');
    exit();
}
?>

<!-- Breadcrumb Section Begin -->
<section class="breadcrumb-option">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb__text">
                    <h4>Shopping Cart</h4>
                    <div class="breadcrumb__links">
                        <a href="index.php">Home</a>
                        <a href="utama.php?page=shop">Shop</a>
                        <span>Shopping Cart</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->
<!-- Shopping Cart Section Begin -->
<section class="shopping-cart spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="shopping__cart__table">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Size</th> <!-- Tambahkan kolom untuk ukuran -->
                                <th>Quantity</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT k.id_produk, k.nama_produk, k.gambar, k.harga, k.quantity, 
                                             k.small, k.medium, k.large, k.extralarge, k.doubleextralarge
                                      FROM keranjang k
                                      WHERE k.id_pelanggan = '$id_pelanggan'";
                            $result = mysqli_query($koneksi, $query);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    // Menentukan total berdasarkan harga dan kuantitas
                                    $total = $row['harga'] * $row['quantity'];

                                    // Menampilkan ukuran dan kuantitas
                                    $sizes = [];
                                    if ($row['small'] > 0) {
                                        $sizes[] = 'S (' . $row['small'] . ')';
                                    }
                                    if ($row['medium'] > 0) {
                                        $sizes[] = 'M (' . $row['medium'] . ')';
                                    }
                                    if ($row['large'] > 0) {
                                        $sizes[] = 'L (' . $row['large'] . ')';
                                    }
                                    if ($row['extralarge'] > 0) {
                                        $sizes[] = 'XL (' . $row['extralarge'] . ')';
                                    }
                                    if ($row['doubleextralarge'] > 0) {
                                        $sizes[] = 'XXL (' . $row['doubleextralarge'] . ')';
                                    }

                                    // Gabungkan ukuran menjadi string
                                    $size_display = implode(', ', $sizes);

                                    echo '
                                    <tr>
                                        <td class="product_cart_item">
                                            <input type="checkbox" class="product-checkbox" data-total="' . $total . '" data-id="' . $row['id_produk'] . '">
                                            <div class="product_cartitem_text">
                                                <img src="../upload/' . htmlspecialchars($row['gambar']) . '" alt="Product Image" width="80" height="80">
                                                <h6>' . htmlspecialchars($row['nama_produk']) . '</h6>
                                                <h5>Rp.' . number_format($row['harga'], 0, ',', '.') . '</h5>
                                            </div>
                                        </td>
                                        <td class="size__item" style="text-align: center; padding: 10px;">' . $size_display . '</td> <!-- Tampilkan ukuran -->
                                        <td class="quantity__item" style="text-align: center; padding: 10px;">
                                            <form action="" method="POST">
                                                <input type="hidden" name="id_produk" value="' . $row['id_produk'] . '">
                                                <input type="number" name="quantity" value="' . $row['quantity'] . '" style="width: 60px; height: 30px; text-align: center; font-size: 16px; border: 1px solid #ccc; border-radius: 5px; padding: 5px; outline: none; transition: border-color 0.3s ease;" min="1">
                                                <button type="submit" name="update_quantity" class="btn btn-primary">Update</button>
                                            </form>
                                        </td>
                                        <td class="cart__price">Rp.' . number_format($total, 0, ',', '.') . '</td>
                                        <td class="cart__check">
                                            <a href="utama.php?page=cart&remove_id=' . $row['id_produk'] . '" class="btn btn-danger">Remove</a>
                                        </td>
                                    </tr>';
                                }
                            } else {
                                echo '<tr><td colspan="5">Your shopping cart is empty.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="continue__btn">
                    <a href="utama.php?page=shop">Continue Shopping</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="cart__total">
                    <h6>Cart total</h6>
                    <ul>
                        <li>Subtotal <span id="subtotal-display">Rp.0</span></li>
                        <li>Total <span id="total-display">Rp.0</span></li>
                    </ul>
                    <a href="utama.php?page=purchase" id="checkout-link" class="primary-btn">Proceed to checkout</a>
                </div>
                
                <script>
                document.addEventListener("DOMContentLoaded", function () {
                    const checkboxes = document.querySelectorAll('.product-checkbox');
                    const subtotalDisplay = document.getElementById('subtotal-display');
                    const totalDisplay = document.getElementById('total-display');
                    const checkoutLink = document.getElementById('checkout-link');

                    let selectedProductIds = [];

                    function calculateTotal() {
                        let subtotal = 0;
                        selectedProductIds = [];
                        checkboxes.forEach(checkbox => {
                            if (checkbox.checked) {
                                subtotal += parseInt(checkbox.getAttribute('data-total'));
                                selectedProductIds.push(checkbox.getAttribute('data-id'));
                            }
                        });

                        subtotalDisplay.textContent = `Rp.${subtotal.toLocaleString('id-ID')}`;
                        totalDisplay.textContent = `Rp.${subtotal.toLocaleString('id-ID')}`;

                        // Update the checkout link to include the selected product IDs
                        if (selectedProductIds.length > 0) {
                            checkoutLink.href = "utama.php?page=purchase&id_produk=" + selectedProductIds.join(',');
                        } else {
                            checkoutLink.href = "#"; // Disable the link if no product is selected
                        }
                    }

                    checkboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', calculateTotal);
                    });
                });
                </script>
            </div>
        </div>
    </div>
</section>



