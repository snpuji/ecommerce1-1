<?php
header('Content-Type: text/html; charset=utf-8');

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

// Ambil data provinsi dan kota
$provinces = fetchFromRajaOngkir('province');
$cities = fetchFromRajaOngkir('city');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RajaOngkir Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { margin-top: 30px; }
        .table-responsive { margin: 20px 0; }
        .nav-tabs { margin-bottom: 20px; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; }
        .copy-btn {
            float: right;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">RajaOngkir Data</h1>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="view-tab" data-bs-toggle="tab" data-bs-target="#view" type="button">View Data</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="code-tab" data-bs-toggle="tab" data-bs-target="#code" type="button">PHP Array</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- View Data Tab -->
            <div class="tab-pane fade show active" id="view">
                <div class="row">
                    <!-- Provinces Table -->
                    <div class="col-md-6">
                        <h3>Provinces</h3>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Province Name</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($provinces['rajaongkir']['results'])) {
                                        foreach ($provinces['rajaongkir']['results'] as $province) {
                                            echo "<tr>";
                                            echo "<td>{$province['province_id']}</td>";
                                            echo "<td>{$province['province']}</td>";
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Cities Table -->
                    <div class="col-md-6">
                        <h3>Cities</h3>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>City Name</th>
                                        <th>Province</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (isset($cities['rajaongkir']['results'])) {
                                        foreach ($cities['rajaongkir']['results'] as $city) {
                                            echo "<tr>";
                                            echo "<td>{$city['city_id']}</td>";
                                            echo "<td>{$city['type']} {$city['city_name']}</td>";
                                            echo "<td>{$city['province']}</td>";
                                            echo "</tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PHP Array Tab -->
            <div class="tab-pane fade" id="code">
                <button class="btn btn-primary copy-btn" onclick="copyToClipboard('mapping-code')">Copy Code</button>
                <pre id="mapping-code">
<?php
echo "<?php\nreturn [\n";
echo "    'provinces' => [\n";
if (isset($provinces['rajaongkir']['results'])) {
    foreach ($provinces['rajaongkir']['results'] as $province) {
        echo "        '" . addslashes($province['province']) . "' => '" . $province['province_id'] . "',\n";
    }
}
echo "    ],\n";

echo "    'cities' => [\n";
if (isset($cities['rajaongkir']['results'])) {
    foreach ($cities['rajaongkir']['results'] as $city) {
        $cityName = $city['type'] . ' ' . $city['city_name'];
        echo "        '" . addslashes($cityName) . "' => '" . $city['city_id'] . "',\n";
    }
}
echo "    ]\n";
echo "];\n";
?>
</pre>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const textArea = document.createElement('textarea');
            textArea.value = element.textContent;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            // Show feedback
            const btn = document.querySelector('.copy-btn');
            const originalText = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(() => {
                btn.textContent = originalText;
            }, 2000);
        }
    </script>
</body>
</html> 