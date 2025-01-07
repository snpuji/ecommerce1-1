<?php
include 'config.php'
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <link href="../Pelanggan/assets/css/main.css?v=1.1" rel="stylesheet" type="text/css" />
    </head>
            <section class="content-main">
                <div class="card mb-4">
                <header class="card-header">
                        <h3 style="text-align: center; font-family: 'Nunito Sans', sans-serif; font-weight: bold;">Our Sellers</h3>
                    </div>
                </header>
                    <!-- card-header end// -->
                <div class="container2">
                    <div class="card-body">
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4">
                            <?php
                            $sql = "SELECT id_penjual, nama_toko, email, gambar FROM penjual ORDER BY id_penjual DESC";
                            $result = $koneksi->query($sql);
                            if ($result->num_rows > 0){
                                while ($row = $result->fetch_assoc()) {
                                    // Periksa apakah gambar tersedia
                                    $gambarPath = "../profileseller/" . htmlspecialchars($row['gambar']);
                                    $defaultImage = "../Pelanggan/assets/imgs/people/avatar-1.png";

                                    if (!file_exists($gambarPath) || empty($row['gambar'])) {
                                        $gambarPath = $defaultImage;
                                    }
                                    ?>
                            <div class="col">
                                <div class="card card-user">
                                    <div class="card-header">
                                        <img class="img-md img-avatar" src="<?php echo $gambarPath; ?>" alt="User pic" />
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title mt-50"><?php echo htmlspecialchars($row['nama_toko']); ?></h5>
                                        <div class="card-text text-muted">
                                            <p>Seller ID: #<?php echo htmlspecialchars($row['id_penjual']); ?></p>
                                            <p><?php echo htmlspecialchars($row['email']); ?></p>
                                            <a href="utama.php?page=sellerdetails&id_penjual=<?php echo $row['id_penjual']; ?>" class="btn btn-sm btn-brand rounded font-sm mt-15">View details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                                }
                            } else {
                                echo "<p> Tidak ada seller ditemukan.</p>";
                            }
                            $koneksi->close();
                            ?>
                        </div>
                        <!-- row.// -->
                    </div>
                    <!-- card-body end// -->
                </div>
                </div>