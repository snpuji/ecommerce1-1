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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    
    <!-- Skrip Midtrans -->
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="SB-Mid-client-FlK5zgkOkP5HyDTF"></script>
</head>
<body>


<!-- HTML untuk menampilkan checkout -->
<section class="purchase-section spad">
    <div class="container">
        <div class="row">
            <!-- Main Form Area -->
            <div class="col-lg-7">
                <div class="purchase__form">
                    <div id="debug-info" style="display: none; padding: 10px; background: #f8f9fa; margin-bottom: 20px;">
                        <h5>Debug Information</h5>
                        <pre id="debug-output"></pre>
                    </div>

                    <script>
                    // Fungsi helper untuk debugging
                    function debugLog(message) {
                        const debugOutput = document.getElementById('debug-output');
                        const debugInfo = document.getElementById('debug-info');
                        
                        if (debugOutput && debugInfo) {
                            debugInfo.style.display = 'block';
                            debugOutput.textContent += message + '\n';
                        }
                        console.log(message);
                    }
                    </script>

                    <form method="POST" action="process_order.php" id="checkout-form">
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
                        <div class="shipping-details">
                            <h4>Shipping Details</h4>
                            
                            <!-- Province -->
                            <div class="form-group">
                                <label>Province</label>
                                <select name="province" id="province" class="form-control" style="display: block !important;">
                                    <option value="">Select Province</option>
                                </select>
                            </div>

                            <!-- City -->
                            <div class="form-group">
                                <label>City</label>
                                <select name="city" id="city" class="form-control" style="display: block !important;">
                                    <option value="">Select City</option>
                                </select>
                            </div>

                            <!-- Courier -->
                            <div class="form-group">
                                <label>Courier Service</label>
                                <select name="courier" id="courier" class="form-control" style="display: block !important;">
                                    <option value="">Select Courier</option>
                                    <option value="jne">JNE</option>
                                    <option value="pos">POS Indonesia</option>
                                    <option value="tiki">TIKI</option>
                                </select>
                            </div>

                            <!-- Shipping Service -->
                            <div class="form-group">
                                <label>Shipping Service</label>
                                <select name="shipping_service" id="shipping_service" class="form-control" style="display: block !important;">
                                    <option value="">Select Service</option>
                                </select>
                            </div>
                        </div>
                        <button type="button" class="primary-btn" id="pay-button" onclick="processPayment()">Pay Now</button>
                        <input type="hidden" name="subtotal" value="<?php echo $total_price; ?>">
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
                    <h4>ORDER SUMMARY</h4>
                    <div class="subtotal">
                        <span>Subtotal</span>
                        <span class="price">Rp.<?php echo number_format($total_price, 0, ',', '.'); ?></span>
                    </div>
                    <div class="shipping-cost">
                        <span>Shipping Cost</span>
                        <span class="price" id="shipping-cost">Rp.0</span>
                    </div>
                    <div class="total">
                        <span>Total</span>
                        <span class="price" id="final-total">Rp.<?php echo number_format($total_price, 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
async function processPayment() {
    // Ambil data form
    const form = document.getElementById('checkout-form');
    const formData = new FormData(form);

    try {
        // Tambahkan data produk ke formData
        const products = <?php echo json_encode($products); ?>;
        formData.append('products', JSON.stringify(products));
        formData.append('total_price', '<?php echo $total_price; ?>');

        // Kirim request ke placeorder.php
        const response = await fetch('placeorder.php', {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const token = await response.text();
        
        if (token) {
            console.log('Received token:', token); // Debug log
            
            // Trigger snap popup
            window.snap.pay(token, {
                onSuccess: function(result) {
                    alert('Payment success!');
                    window.location.href = 'order-success.php';
                },
                onPending: function(result) {
                    alert('Payment pending. Please complete your payment.');
                },
                onError: function(result) {
                    alert('Payment failed. Please try again.');
                },
                onClose: function() {
                    alert('You closed the payment window without completing the payment.');
                }
            });
        } else {
            throw new Error('Empty token received');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while processing your payment. Please try again.');
    }
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hapus nice-select jika ada
    const niceSelects = document.querySelectorAll('.nice-select');
    niceSelects.forEach(el => el.remove());

    const provinceSelect = document.querySelector('select[name="province"]');
    const citySelect = document.querySelector('select[name="city"]');
    const courierSelect = document.querySelector('select[name="courier"]');
    const serviceSelect = document.querySelector('select[name="shipping_service"]');
    const shippingCostDisplay = document.getElementById('shipping-cost');
    const finalTotalDisplay = document.getElementById('final-total');
    const shippingRow = document.getElementById('shipping-row');
    
    // Load Provinces
    fetch('/ecommerce1/rajaongkir/provinsi.php')
        .then(response => response.json())
        .then(data => {
            provinceSelect.innerHTML = '<option value="">Select Province</option>';
            data.forEach(province => {
                provinceSelect.innerHTML += `
                    <option value="${province.province_id}">${province.province}</option>
                `;
            });
        })
        .catch(error => console.error('Error loading provinces:', error));

    // Province Change Event - Load Cities
    provinceSelect.addEventListener('change', function() {
        const selectedProvince = this.value;
        console.log('Selected Province:', selectedProvince); // Debug

        // Reset city dropdown
        citySelect.innerHTML = '<option value="">Select City</option>';
        
        if(selectedProvince) {
            // Show loading state
            citySelect.innerHTML = '<option value="">Loading cities...</option>';
            
            // Fetch cities for selected province
            fetch(`/ecommerce1/rajaongkir/kota.php?province=${selectedProvince}`)
                .then(response => response.json())
                .then(data => {
                    console.log('City Data:', data); // Debug
                    
                    // Reset and add default option
                    citySelect.innerHTML = '<option value="">Select City</option>';
                    
                    // Check if data has the expected structure
                    if(data.rajaongkir && data.rajaongkir.results) {
                        // Add city options
                        data.rajaongkir.results.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.city_id;
                            option.textContent = `${city.type} ${city.city_name}`;
                            citySelect.appendChild(option);
                        });
                        
                        console.log('Cities loaded:', citySelect.options.length - 1); // Debug
                    } else {
                        console.error('Invalid city data structure:', data);
                        citySelect.innerHTML = '<option value="">Error loading cities</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading cities:', error);
                    citySelect.innerHTML = '<option value="">Error loading cities</option>';
                });
        }

        // Reset dependent dropdowns
        courierSelect.value = '';
        serviceSelect.innerHTML = '<option value="">Select Service</option>';
    });

    // City Change Event
    citySelect.addEventListener('change', function() {
        const selectedCity = this.value;
        console.log('Selected City:', selectedCity); // Debug
        
        // Reset shipping service when city changes
        serviceSelect.innerHTML = '<option value="">Select Service</option>';
        courierSelect.value = '';
    });

    // Courier Change Event - Load Shipping Services
    courierSelect.addEventListener('change', function() {
        if(!citySelect.value) {
            alert('Please select a city first');
            this.value = '';
            return;
        }

        if(this.value) {
            const formData = new FormData();
            formData.append('destination', citySelect.value);
            formData.append('courier', this.value);
            formData.append('weight', '1000'); // Sesuaikan dengan berat produk

            fetch('/ecommerce1/rajaongkir/cost.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                serviceSelect.innerHTML = '<option value="">Select Service</option>';
                
                if(data.rajaongkir && data.rajaongkir.results && data.rajaongkir.results[0].costs) {
                    data.rajaongkir.results[0].costs.forEach(service => {
                        const option = document.createElement('option');
                        option.value = service.service;
                        option.dataset.cost = service.cost[0].value;
                        option.dataset.etd = service.cost[0].etd;
                        option.textContent = `${service.service} - Rp${parseInt(service.cost[0].value).toLocaleString()} (${service.cost[0].etd} hari)`;
                        serviceSelect.appendChild(option);
                    });
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });

    // Shipping Service Change Event
    serviceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if(selectedOption && selectedOption.dataset.cost) {
            const shippingCost = parseInt(selectedOption.dataset.cost);
            const subtotal = <?php echo $total_price; ?>;
            const total = subtotal + shippingCost;

            // Update shipping cost dengan format yang benar
            shippingCostDisplay.textContent = 'Rp.' + shippingCost.toLocaleString('id-ID');
            
            // Update total dengan format yang benar
            finalTotalDisplay.textContent = 'Rp.' + total.toLocaleString('id-ID');

            // Tambahkan hidden input untuk form submission
            let shippingInput = document.querySelector('input[name="shipping_cost"]');
            if (!shippingInput) {
                shippingInput = document.createElement('input');
                shippingInput.type = 'hidden';
                shippingInput.name = 'shipping_cost';
                document.querySelector('form').appendChild(shippingInput);
            }
            shippingInput.value = shippingCost;

            // Tambahkan hidden input untuk service yang dipilih
            let serviceInput = document.querySelector('input[name="selected_service"]');
            if (!serviceInput) {
                serviceInput = document.createElement('input');
                serviceInput.type = 'hidden';
                serviceInput.name = 'selected_service';
                document.querySelector('form').appendChild(serviceInput);
            }
            serviceInput.value = selectedOption.value;

            console.log('Selected service:', selectedOption.value);
            console.log('Shipping cost:', shippingCost);
            console.log('New total:', total);
        }
    });
});
</script>

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

    .shipping-details {
        margin-top: 20px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 5px;
    }

    .shipping-details h4 {
        margin-bottom: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .checkout__order__shipping {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e1e1e1;
    }

    .shipping-details select {
        display: block !important;
        width: 100%;
        padding: 8px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .shipping-details label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    /* Tambahkan CSS ini untuk menghilangkan nice-select */
    .nice-select {
        display: none !important;
    }

    /* Style untuk dropdown asli */
    .shipping-details {
        margin: 20px 0;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: normal;
    }

    .custom-select {
        position: relative;
        width: 100%;
    }

    .custom-select select {
        width: 100%;
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: white;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
    }

    /* Pastikan select asli tetap terlihat */
    select.form-control {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    /* Menghilangkan semua elemen nice-select yang mungkin dibuat oleh plugin */
    .nice-select,
    .nice-select *,
    .nice-select.form-control {
        display: none !important;
        opacity: 0 !important;
        visibility: hidden !important;
    }

    .order-summary {
        margin-top: 20px;
    }

    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 5px 0;
    }

    .summary-item.total {
        border-top: 1px solid #ddd;
        margin-top: 10px;
        padding-top: 10px;
        font-weight: bold;
    }

    .price {
        color: #e53637;
    }

    .subtotal, .shipping-cost, .total {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        padding: 5px 0;
    }

    .total {
        border-top: 1px solid #ddd;
        padding-top: 10px;
        margin-top: 5px;
        font-weight: bold;
    }

    .price {
        color: #e53637;
    }

    .shipping-cost {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
</style>

</body>
</html>
