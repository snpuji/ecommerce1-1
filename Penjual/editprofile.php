<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id_penjual = $_GET['id'];

    // Query untuk mengambil data penjual berdasarkan ID
    $query = "SELECT * FROM penjual WHERE id_penjual = '$id_penjual'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "Penjual tidak ditemukan.";
        exit;
    }
}
// Cek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sambungkan ke database (pastikan koneksi sudah ada di $koneksi)
    // $koneksi = mysqli_connect('localhost', 'user', 'password', 'database');

    // Ambil data dari form dan pastikan tidak ada karakter khusus dengan mysqli_real_escape_string
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $rekening_bca = mysqli_real_escape_string($koneksi, $_POST['rekening_bca']);
    $virtual_account = mysqli_real_escape_string($koneksi, $_POST['virtual_account']);
    $qris = mysqli_real_escape_string($koneksi, $_POST['qris']);  // Nilai rekening yang dipilih
    $desc_toko = mysqli_real_escape_string($koneksi, $_POST['desc_toko']);
    $nama_toko = mysqli_real_escape_string($koneksi, $_POST['nama_toko']);

    // Menangani upload gambar
    $gambar_baru = $_FILES['gambar']['name'];
    $gambar_lama = '';  // Jika gambar lama ada, dapat diisi dengan gambar lama di database sebelumnya

    // Jika gambar baru diupload
    if ($gambar_baru != '') {
        $target_dir = "../profileseller/";
        $target_file = $target_dir . basename($gambar_baru);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Cek apakah file adalah gambar yang valid
        $valid_extensions = array("jpg", "jpeg", "png", "gif");
        if (!in_array($imageFileType, $valid_extensions)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            exit;
        }

        // Cek apakah file sudah ada
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            exit;
        }

        // Upload file gambar
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file)) {
            // Update query dengan gambar baru dan rekening yang dipilih
            $update_query = "UPDATE penjual SET 
                                email = '$email',
                                no_hp = '$no_hp',
                                alamat = '$alamat',
                                rekening_bca = '$rekening_bca',
                                virtual_account = '$virtual_account',
                                qris = '$qris',
                                desc_toko = '$desc_toko',
                                nama_toko = '$nama_toko',
                                gambar = '$gambar_baru'
                            WHERE id_penjual = '$id_penjual'"; // Pastikan $id_penjual sudah ada
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    } else {
        // Jika tidak ada gambar baru, gunakan gambar lama (atau nilai lainnya)
        $update_query = "UPDATE penjual SET 
                            email = '$email',
                            no_hp = '$no_hp',
                            alamat = '$alamat',
                            rekening_bca = '$rekening_bca',
                            virtual_account = '$virtual_account',
                            qris = '$qris',
                            desc_toko = '$desc_toko',
                            nama_toko = '$nama_toko'
                        WHERE id_penjual = '$id_penjual'"; // Pastikan $id_penjual sudah ada
    }

    // Eksekusi query update penjual
    if (mysqli_query($koneksi, $update_query)) {
        // Jika berhasil, arahkan ke halaman profile
        header("Location: utama.php?page=profile");
        exit;
    } else {
        // Jika ada error dalam eksekusi
        echo "Error updating record: " . mysqli_error($koneksi);
    }
}
?>

<body>

<!-- HTML Form untuk edit profile -->
<form action="editprofile.php?id=<?php echo $id_penjual; ?>" method="post" enctype="multipart/form-data">
    <section class="content-main">
        <div class="content-header">
            <div>
                <h2 class="content-title card-title">Edit Profile</h2>
            </div>
            <div>
                <a href="utama.php?page=penjual" class="btn btn-secondary btn-sm rounded">Back to Seller List</a>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="no_hp">Phone Number</label>
                    <input type="text" name="no_hp" id="no_hp" class="form-control" value="<?php echo htmlspecialchars($row['no_hp']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="alamat">Address</label>
                    <textarea name="alamat" id="alamat" class="form-control" rows="3" required><?php echo htmlspecialchars($row['alamat']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="rekening_bca">Bank BCA</label>
                    <input type="text" name="rekening_bca" id="rekening_bca" class="form-control" value="<?php echo htmlspecialchars($row['rekening_bca']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="virtual_account">Virtual Account</label>
                    <input type="text" name="virtual_account" id="virtual_account" class="form-control" value="<?php echo htmlspecialchars($row['virtual_account']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="qris">Qris</label>
                    <input type="text" name="qris" id="qris" class="form-control" value="<?php echo htmlspecialchars($row['qris']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="desc_toko">Store Description</label>
                    <textarea name="desc_toko" id="desc_toko" class="form-control" rows="3" required><?php echo htmlspecialchars($row['desc_toko']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="nama_toko">Store Name</label>
                    <input type="text" name="nama_toko" id="nama_toko" class="form-control" value="<?php echo htmlspecialchars($row['nama_toko']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="gambar">Profile Image</label>
                    <div>
                        <?php if (!empty($row['gambar']) && file_exists("../profileseller/" . $row['gambar'])) { ?>
                            <img src="../profileseller/<?php echo htmlspecialchars($row['gambar']); ?>" alt="Profile Image" class="img-fluid" style="max-width: 100px; height: auto;">
                        <?php } else { ?>
                            <img src="../assets/images/no-image.png" alt="No image available" class="img-fluid" style="max-width: 100px; height: auto;">
                        <?php } ?>
                    </div>
                    <input type="file" name="gambar" id="gambar" class="form-control mt-2">
                    <small class="form-text text-muted">Leave blank if you don't want to change the image.</small>
                </div>
                <div class="form-group text-end">
                    <button type="submit" class="btn btn-primary btn-sm rounded">Save Changes</button>
                </div>
            </div>
        </div>
    </section>
</form>


<script src="../assets/js/vendors/jquery-3.6.0.min.js"></script>
<script src="../assets/js/vendors/bootstrap.bundle.min.js"></script>
<script src="../assets/js/vendors/select2.min.js"></script>
<script src="../assets/js/vendors/perfect-scrollbar.js"></script>
<script src="../assets/js/vendors/jquery.fullscreen.min.js"></script>
<script src="../assets/js/main.js" type="text/javascript"></script>
</body>
</html>
