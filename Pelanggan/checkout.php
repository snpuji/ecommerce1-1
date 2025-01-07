<?php
include 'config.php'; // Koneksi database

// Set konfigurasi Midtrans
$serverKey = 'SB-Mid-server-kUYrsyixIIl9PWbyHtj-mkhs'; // Ganti dengan server key Anda
$clientKey = 'SB-Mid-client-FlK5zgkOkP5HyDTF'; // Ganti dengan client key Anda
$isProduction = false; // Set true jika di production

$id_pesanan = $_GET['id_pesanan'] ?? null;

if ($id_pesanan) {
    // Ambil data pesanan
    $query = "SELECT * FROM pesanan1 WHERE id_pesanan = ?";
    $stmt = $koneksi->prepare($query);
    
    if (!$stmt) {
        die('Prepare Error: ' . mysqli_error($koneksi));
    }

    $stmt->bind_param("i", $id_pesanan);
    $stmt->execute();
    $result = $stmt->get_result();
    $order_data = $result->fetch_assoc();

    // Pastikan order_data ada sebelum melanjutkan
    if (!$order_data) {
        die('Error: Pesanan tidak ditemukan.');
    }

    // Ambil detail pesanan
    $query_detail = "SELECT dp.*, p.nama_produk FROM detail_pesanan dp JOIN produk p ON dp.id_produk = p.id_produk WHERE dp.id_pesanan = ?";
    $stmt_detail = $koneksi->prepare($query_detail);
    
    if (!$stmt_detail) {
        die('Prepare Error: ' . mysqli_error($koneksi));
    }

    $stmt_detail->bind_param("i", $id_pesanan);
    $stmt_detail->execute();
    $result_detail = $stmt_detail->get_result();
    $products = $result_detail->fetch_all(MYSQLI_ASSOC);

    // Pastikan ada produk dalam pesanan
    if (empty($products)) {
        die('Error: Tidak ada produk dalam pesanan.');
    }
} else {
    die('Error: ID pesanan tidak diberikan.');
}

// Siapkan data untuk Midtrans
$transaction_details = [
    'order_id' => uniqid(), // ID unik untuk transaksi
    'gross_amount' => 0, // Inisialisasi gross_amount
];

$item_details = [];
$total_amount = 0; // Inisialisasi total_amount

foreach ($products as $product) {
    $subtotal = (float)$product['subtotal']; // Ambil subtotal dari detail pesanan dan pastikan dalam format float
    $quantity = (int)$product['quantity']; // Pastikan quantity dalam format integer
    $total_amount += $subtotal * $quantity; // Hitung total dengan mengalikan subtotal dengan quantity

    $item_details[] = [
        'id' => $product['id_produk'],
        'price' => $subtotal, // Gunakan subtotal dari detail pesanan
        'quantity' => $quantity, // Pastikan quantity dalam format integer
        'name' => $product['nama_produk'], // Pastikan ini ada
    ];
}

// Set gross_amount sesuai dengan total_amount yang dihitung
$transaction_details['gross_amount'] = $total_amount;

// Debugging: Tampilkan item_details dan total_amount
echo "Item Details: <pre>";
print_r($item_details);
echo "</pre>";
echo "Total Amount: " . $total_amount . "<br>";
echo "Gross Amount: " . $transaction_details['gross_amount'] . "<br>";

// Data untuk Midtrans
$midtrans_data = [
    'transaction_details' => $transaction_details,
    'item_details' => $item_details,
    'customer_details' => [
        'first_name' => $order_data['nama'],
        'last_name' => '',
        'email' => $order_data['no_hp'], // Pastikan ini sesuai
        'phone' => $order_data['no_hp'],
        'billing_address' => [
            'address' => $order_data['alamat'],
            'city' => 'Jakarta',
            'postal_code' => '12345',
            'country' => 'Indonesia',
        ],
    ],
    'payment_type' => 'bank_transfer', // Tambahkan jenis pembayaran
    'bank_transfer' => [
        'bank' => 'bca' // Misalnya, jika Anda ingin menampilkan BCA
    ],
];

// Mengambil Snap Token dari Midtrans
$snap_token = '';
try {
    $url = $isProduction ? 'https://api.midtrans.com/v2/charge' : 'https://api.sandbox.midtrans.com/v2/charge';
    
    // Inisialisasi cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($serverKey . ':')
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($midtrans_data));

    // Eksekusi cURL
    $response = curl_exec($ch);
    $result = json_decode($response, true);
    
    // Debugging: Tampilkan respons dari Midtrans
    echo '<pre>';
    print_r($result);
    echo '</pre>';

    if (isset($result['token'])) {
        $snap_token = $result['token'];
    } else {
        $error_message = isset($result['message']) ? $result['message'] : 'Tidak ada pesan kesalahan yang tersedia.';
        throw new Exception('Error: ' . $error_message);
    }

    curl_close($ch);
} catch (Exception $e) {
    echo $e->getMessage();
    exit;
}

// Menampilkan token untuk digunakan di frontend
echo '<input type="hidden" id="snapToken" name="token" value="'.$snap_token.'"/>';

?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
</head>
<body>
    <h1>Checkout</h1>
    <button id="pay-button" 
        style="padding: 10px 20px; background-color: #81C408; color: white; 
        border: none; cursor: pointer; border-radius: 12px; font-weight: bold"> 
        Bayar Sekarang 
    </button> 
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo $clientKey; ?>"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function(){
            snap.pay('<?php echo $snap_token; ?>', {
                onSuccess: function(result) {
                    alert('Pembayaran berhasil!');
                    window.location.href = 'success.php'; // Redirect ke halaman sukses
                },
                onPending: function(result) {
                    alert('Pembayaran sedang diproses!');
                },
                onError: function(result) {
                    alert('Pembayaran gagal!');
                }
            });
        };
    </script>
</body>
</html>
