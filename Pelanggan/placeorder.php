<?php
require_once 'config.php';

// Cukup require Midtrans.php saja karena sudah include file lainnya
require_once(__DIR__ . '/../Midtrans/Midtrans.php');

// Set your Merchant Server Key
\Midtrans\Config::$serverKey = 'SB-Mid-server-kUYrsyixIIl9PWbyHtj-mkhs';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

try {
    // Ambil data dari POST
    $products = json_decode($_POST['products'], true);
    $total_price = $_POST['total_price'];
    $shipping_cost = $_POST['shipping_cost'];
    $selected_service = $_POST['selected_service'];
    $courier = $_POST['courier'];
    
    // Hitung gross_amount (total + shipping cost)
    $gross_amount = (int)$total_price + (int)$shipping_cost;
    
    // Siapkan array untuk item_details
    $items = array();
    
    // Loop through products untuk membuat item_details
    foreach ($products as $product) {
        // Ambil detail produk dari database
        $query = "SELECT * FROM produk WHERE id_produk = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("i", $product['id_produk']);
        $stmt->execute();
        $result = $stmt->get_result();
        $prod_detail = $result->fetch_assoc();
        
        // Tambahkan ke array items
        $items[] = array(
            'id' => $product['id_produk'],
            'price' => (int)$prod_detail['harga'],
            'quantity' => (int)$product['quantity'],
            'name' => $prod_detail['nama_produk'],
        );
    }
    
    // Tambahkan shipping cost sebagai item
    if ($shipping_cost > 0) {
        $items[] = array(
            'id' => 'SHIPPING',
            'price' => (int)$shipping_cost,
            'quantity' => 1,
            'name' => 'Shipping Cost (' . strtoupper($courier) . ' - ' . $selected_service . ')',
        );
    }
    
    // Buat parameter untuk Midtrans
    $params = array(
        'transaction_details' => array(
            'order_id' => 'ORDER-' . time(),
            'gross_amount' => $gross_amount, // Menggunakan total yang sudah termasuk shipping cost
        ),
        'item_details' => $items,
        'customer_details' => array(
            'first_name' => $_POST['nama'],
            'email' => $_POST['email'],
            'phone' => $_POST['no_hp'],
            'shipping_address' => array(
                'first_name' => $_POST['nama'],
                'phone' => $_POST['no_hp'],
                'address' => $_POST['alamat'],
            ),
        ),
    );

    // Debug: print params
    // error_log(print_r($params, true));

    // Get Snap Payment Page URL
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    
    // Kirim token sebagai response
    echo $snapToken;
} catch (Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}
?>
