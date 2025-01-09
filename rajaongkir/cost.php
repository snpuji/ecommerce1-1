<?php
header('Content-Type: application/json');

// Terima parameter
$origin = isset($_POST['origin']) ? $_POST['origin'] : '501'; // ID kota asal (sesuaikan dengan lokasi toko)
$destination = isset($_POST['destination']) ? $_POST['destination'] : '';
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$courier = isset($_POST['courier']) ? $_POST['courier'] : '';

// Hitung berat berdasarkan quantity
// Setiap 3 quantity = 1000 gram
$weight_per_three_items = 1000;
$total_weight = ceil($quantity / 3) * $weight_per_three_items;

if(empty($destination) || empty($courier)) {
    echo json_encode(['error' => 'Destination and courier are required']);
    exit;
}

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.rajaongkir.com/starter/cost",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => "origin=" . $origin . "&destination=" . $destination . "&weight=" . $total_weight . "&courier=" . $courier,
    CURLOPT_HTTPHEADER => array(
        "content-type: application/x-www-form-urlencoded",
        "key: 09b883787a774850ee4775ee045abb8f"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo json_encode(['error' => $err]);
} else {
    echo $response;
}