<?php
// Set header untuk menampilkan output yang rapi
header('Content-Type: text/plain');

// Konfigurasi API Key RajaOngkir
$api_key = '09b883787a774850ee4775ee045abb8f';

// Fungsi untuk mengambil data dari RajaOngkir
function fetchFromRajaOngkir($endpoint) {
    global $api_key;
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.rajaongkir.com/starter/" . $endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "key: " . $api_key
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        return "Error #:" . $err;
    }
    
    return json_decode($response, true);
}

// Ambil data provinsi
echo "Fetching provinces...\n";
$provinces = fetchFromRajaOngkir('province');
$province_mapping = [];

if (isset($provinces['rajaongkir']['results'])) {
    foreach ($provinces['rajaongkir']['results'] as $province) {
        $province_mapping[$province['province']] = $province['province_id'];
    }
}

// Ambil data kota
echo "Fetching cities...\n";
$cities = fetchFromRajaOngkir('city');
$city_mapping = [];

if (isset($cities['rajaongkir']['results'])) {
    foreach ($cities['rajaongkir']['results'] as $city) {
        $city_name = $city['type'] . ' ' . $city['city_name'];
        $city_mapping[$city_name] = $city['city_id'];
    }
}

// Format data sebagai array PHP
$mapping_data = "<?php\nreturn [\n";
$mapping_data .= "    'provinces' => [\n";
foreach ($province_mapping as $name => $id) {
    $mapping_data .= "        '" . addslashes($name) . "' => '" . $id . "',\n";
}
$mapping_data .= "    ],\n";

$mapping_data .= "    'cities' => [\n";
foreach ($city_mapping as $name => $id) {
    $mapping_data .= "        '" . addslashes($name) . "' => '" . $id . "',\n";
}
$mapping_data .= "    ]\n";
$mapping_data .= "];\n";

// Simpan ke file
$file = '../rajaongkir/rajaongkir_mapping.php';
if (file_put_contents($file, $mapping_data)) {
    echo "Data saved successfully to $file\n";
    echo "Total provinces: " . count($province_mapping) . "\n";
    echo "Total cities: " . count($city_mapping) . "\n";
} else {
    echo "Error saving data\n";
}
