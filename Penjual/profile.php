<?php
// Masukkan konfigurasi database
include "config.php";

$id_penjual = $_SESSION['id_penjual'];

// Query untuk mengambil data profil penjual
$query = "SELECT * FROM penjual WHERE id_penjual = $id_penjual";
$result = $koneksi->query($query);

// Periksa apakah data profil ditemukan
if ($result->num_rows > 0) {
    $penjual = $result->fetch_assoc();
} else {
    echo "Profil tidak ditemukan.";
    exit();
}
?>

   <style>
        .card-header {
            height: 150px;
            background-color: #5DB680; /* Ganti dengan warna brand */
        }

        ..profile-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 100%;
    margin-top: -90px;
    border: 5px solid white;
}


        .card-body .row {
            align-items: center;
        }

        .card-body h3 {
            font-size: 1.75rem;
            font-weight: bold;
        }

        .text-muted {
            color: #6c757d;
        }

        .card-footer {
            padding-top: 20px;
        }

        .card-footer .btn {
            font-size: 14px;
        }

        .box {
            background-color: #f1f3f5;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card mb-4">
            <div class="card-header"></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-xl col-lg flex-grow-0" style="flex-basis: 230px">
                    <div class="img-thumbnail shadow w-100 bg-white position-relative text-center" style="height: 190px; width: 190px; margin-top: -120px;">
    <?php if (!empty($penjual['gambar']) && file_exists("../profileseller/" . $penjual['gambar'])): ?>
        <img src="../profileseller/<?php echo htmlspecialchars($penjual['gambar']); ?>" class="profile-img img-fluid" alt="Gambar Profil" />
    <?php else: ?>
        <div class="no-image">No Image</div>
    <?php endif; ?>
</div>

                    </div>
                    <div class="col-xl col-lg">
                        <h3><?php echo htmlspecialchars($penjual['nama_toko']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($penjual['desc_toko'])); ?></p>
                    </div>
                </div>
                <hr class="my-4" />
                <div class="row g-4">
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <h6>ID Seller</h6>
                        <p>
                        <?php echo htmlspecialchars($penjual['id_penjual']); ?> <br />
                        </p>
                    </div>
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <h6>Email</h6>
                        <p>
                        <?php echo htmlspecialchars($penjual['email']); ?> <br />
                        </p>
                    </div>
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <h6>Contact Information</h6>
                        <p>
                        <?php echo htmlspecialchars($penjual['no_hp']); ?> <br />
                        </p>
                    </div>
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <h6>Bank Account</h6>
                        <p>
                            <strong>BCA:</strong> <?php echo htmlspecialchars($penjual['rekening_bca']); ?><br />
                            <strong>Virtual Account:</strong> <?php echo htmlspecialchars($penjual['virtual_account']); ?><br />
                            <strong>QRIS:</strong> <?php echo htmlspecialchars($penjual['qris']); ?>
                        </p>
                    </div>
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <h6>Location</h6>
                        <p><?php echo nl2br(htmlspecialchars($penjual['alamat'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="card-footer text-center">
    <a href="utama.php?page=editprofile&id=<?php echo htmlspecialchars($penjual['id_penjual']); ?>" class="btn btn-secondary btn-sm">Edit Profil</a>
</div>

        </div>
    </div>

