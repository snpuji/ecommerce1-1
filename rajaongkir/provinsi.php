<?php
// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.rajaongkir.com/starter/province",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "key: 09b883787a774850ee4775ee045abb8f"
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

header('Content-Type: application/json');

if ($err) {
    echo json_encode(['error' => $err]);
} else {
    // Debug: lihat response mentah
    error_log("RajaOngkir Response: " . $response);
    
    // Parse response
    $result = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'Failed to parse JSON response']);
        exit;
    }
    
    if (isset($result['rajaongkir']['results'])) {
        // Return only the results array
        echo json_encode($result['rajaongkir']['results']);
    } else {
        echo json_encode(['error' => 'Invalid response structure from RajaOngkir']);
    }
}