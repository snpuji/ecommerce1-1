<?php
include 'config.php'; // Koneksi database

// Cek apakah id_pesanan ada di URL
if (isset($_GET['id_pesanan'])) {
    $id_pesanan = intval($_GET['id_pesanan']);

    // Ambil data pesanan dari tabel pesanan1
    $query = "SELECT * FROM pesanan1 WHERE id_pesanan = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id_pesanan);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pesanan = $result->fetch_assoc();
    } else {
        die('Error: Pesanan tidak ditemukan.');
    }
} else {
    die('Error: ID pesanan tidak ditemukan.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>
    <link rel="stylesheet" href="style.css"> <!-- Ganti dengan file CSS Anda -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2, h3 {
            color: #333;
        }
        p {
            line-height: 1.6;
            color: #555;
        }
        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .order-table th, .order-table td {
            text-align: left;
            padding: 10px;
            font-size: 0.9rem;
            border-bottom: 1px solid #ddd;
        }
        .order-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .total {
            font-weight: bold;
            font-size: 1.1rem;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detail Pesanan</h1>
        <h2>Informasi Pesanan</h2>
        <p><strong>ID Pesanan:</strong> <?php echo htmlspecialchars($pesanan['id_pesanan']); ?></p>
        <p><strong>Nama:</strong> <?php echo htmlspecialchars($pesanan['nama']); ?></p>
        <p><strong>Alamat:</strong> <?php echo htmlspecialchars($pesanan['alamat']); ?></p>
        <p><strong>No HP:</strong> <?php echo htmlspecialchars($pesanan['no_hp']); ?></p>
        <p><strong>Total Harga:</strong> Rp. <?php echo number_format($pesanan['total_harga'], 2, ',', '.'); ?></p>
        <p><strong>Tanggal Pemesanan:</strong> <?php echo htmlspecialchars($pesanan['tanggal_pemesanan']); ?></p>
        <p><strong>Catatan:</strong> <?php echo htmlspecialchars($pesanan['notes']); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($pesanan['status']); ?></p>
    </div>
</body>
</html>
