<!DOCTYPE html>
<html lang="en">
<head>
    <link href="../Pelanggan/assets/css/main.css?v=1.1" rel="stylesheet" type="text/css" />
</head>
<body>
<?php 
 include "config.php"; // Include konfigurasi database

 $email_pengguna = $_SESSION['email']; // Email pengguna dari sesi

 // Query untuk mendapatkan data pesanan dari tabel pesanan1 dan detail_pesanan berdasarkan email pengguna
 $query = "
     SELECT 
         pesanan1.id_pesanan, 
         detail_pesanan.id_produk, 
         produk.nama_produk,  -- Mengambil nama produk dari tabel produk
         pesanan1.total_harga, 
         pesanan1.status, 
         pesanan1.tanggal_pemesanan 
     FROM pesanan1 
     JOIN pelanggan ON pesanan1.id_pelanggan = pelanggan.id_pelanggan 
     JOIN detail_pesanan ON pesanan1.id_pesanan = detail_pesanan.id_pesanan 
     JOIN produk ON detail_pesanan.id_produk = produk.id_produk  -- Menggabungkan dengan tabel produk
     WHERE pelanggan.email = '$email_pengguna'
 ";

 $result = mysqli_query($koneksi, $query);

 // Cek hasil query
 if (!$result) {
     die("Query Error: " . mysqli_error($koneksi));
 }
 ?>

 <section class="content-main">
     <div class="content-header">
         <div>
             <h2 class="content-title card-title">Order History</h2>
         </div>
     </div>
     <div class="card mb-4">
         <div class="card-body">
             <div class="table-responsive">
                 <table class="table table-hover">
                     <thead>
                         <tr>
                             <th>No</th>
                             <th scope="col">Nama Produk</th>
                             <th scope="col">Total Harga</th>
                             <th scope="col">Status</th>
                             <th scope="col">Tanggal Pemesanan</th>
                             <th scope="col" class="text-end">Action</th>
                         </tr>
                     </thead>
                     <tbody>
                         <?php 
                             // Inisialisasi nomor urut
                             $no = 1;

                             // Loop untuk menampilkan data dari database
                             while ($row = mysqli_fetch_assoc($result)) {
                                 echo "<tr>";
                                 echo "<td>" . $no++ . "</td>";

                                 // Menampilkan nama produk
                                 echo "<td><b>" . $row['nama_produk'] . "</b></td>";
                                 echo "<td>Rp. " . number_format($row['total_harga']) . "</td>";

                                 // Status dengan badge warna
                                 $statusClass = ($row['status'] === 'Pending') ? 'alert-warning' : 'alert-success';
                                 echo "<td><span class='badge rounded-pill $statusClass'>" . $row['status'] . "</span></td>";

                                 echo "<td>" . $row['tanggal_pemesanan'] . "</td>";
                                 echo "<td class='text-end'>";
                                 echo "<a href='utama.php?page=orderdetail&id_pesanan=" . $row['id_pesanan'] . "' class='btn btn-md rounded font-sm'>Detail</a>";
                                 echo "</td>";
                                 echo "</tr>";
                             }
                         ?>
                     </tbody>
                 </table>
             </div>
             <!-- table-responsive //end -->
         </div>
         <!-- card-body end// -->
     </div>
 </section>
 </body>
 </html>
