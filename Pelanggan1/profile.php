<?php
include "config.php"; // Koneksi ke database sudah ada

// Cek apakah pengguna sudah login (sudah dicek di menu utama)
if (empty($_SESSION['email'])) {
    header('Location: login.php');
    exit();
}

// Ambil email dari sesi
$email = $_SESSION['email'];

// Menangani request POST (untuk update profil)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data yang dikirimkan dari form
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $no_hp = $_POST['no_hp'];
    $birthday = $_POST['birthday'];
    $alamat = $_POST['alamat'];
    $apartemen = $_POST['apartemen'];
    $kota = $_POST['kota'];
    $provinsi = $_POST['provinsi'];
    $postcode = $_POST['postcode'];

    // Proses upload foto
    $gambar = ''; // Inisialisasi gambar dengan nilai kosong
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../profilepelanggan/"; // Direktori untuk menyimpan gambar
        $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi file gambar (format dan ukuran)
        $allowed_types = ['jpg', 'jpeg', 'png'];
        if (in_array($imageFileType, $allowed_types) && $_FILES["gambar"]["size"] < 5000000) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = basename($_FILES["gambar"]["name"]);
            }
        }
    }

    // Jika gambar tidak di-upload, tetap menggunakan gambar lama
    if ($gambar === '') {
        // Query untuk mengambil gambar lama jika tidak ada gambar baru
        $query = "SELECT gambar FROM pelanggan WHERE email = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $data = $result->fetch_assoc();
            $gambar = $data['gambar']; // Tetap menggunakan gambar lama
        }
    }

    // Query untuk memperbarui data pelanggan
    $query = "UPDATE pelanggan SET nama = ?, username = ?, no_hp = ?, birthday = ?, alamat = ?, apartemen = ?, kota = ?, provinsi = ?, postcode = ?, gambar = ? WHERE email = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("sssssssssss", $nama, $username, $no_hp, $birthday, $alamat, $apartemen, $kota, $provinsi, $postcode, $gambar, $email);

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Failed to update profile, this username is already taken.";
    }
}

// Query untuk mengambil data pelanggan
$query = "SELECT nama, username, email, no_hp, birthday, alamat, apartemen, kota, provinsi, postcode, gambar FROM pelanggan WHERE email = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah data ditemukan
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
} else {
    // Jika data tidak ditemukan, berikan pesan kesalahan
    $message = "Data pelanggan tidak ditemukan.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="../Pelanggan/assets/css/main.css?v=1.1" rel="stylesheet" type="text/css" />
    <title>Profile Setting</title>
</head>
<body>
<br />
<br />
<section class="container2">
    <div class="card">
        <div class="card-body">
            <h1 class="card-title mt-50" style="font-size: 30px;">Profile Setting</h1>
            <br />
            <div class="row gx-5">
                <!-- Photo upload section -->
                <aside class="col-lg-4 order-lg-2 order-1 mb-3 mb-lg-0">
                    <figure class="text-lg-center">
                        <!-- Menampilkan foto profil lama -->
                        <img class="img-lg mb-3 img-avatar" src="../profilepelanggan/<?php echo htmlspecialchars($data['gambar']); ?>" alt="User Photo" />
                    </figure>
                    
                </aside>

                <div class="col-lg-8 order-lg-1 order-2">
                    <section class="content-body p-xl-4">
                        <!-- Form untuk mengedit profil -->
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row gx-3">
                                <!-- Input fields -->
                                <div class="col-6 mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input class="form-control" type="text" name="nama" value="<?php echo htmlspecialchars($data['nama']); ?>" />
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Username</label>
                                    <input class="form-control" type="text" name="username" value="<?php echo htmlspecialchars($data['username']); ?>" />
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input class="form-control" type="text" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" disabled />
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Phone</label>
                                    <input class="form-control" type="text" name="no_hp" value="<?php echo htmlspecialchars($data['no_hp']); ?>" />
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Address</label>
                                    <input class="form-control" type="text" name="alamat" value="<?php echo htmlspecialchars($data['alamat']); ?>" />
                                </div>
                                <div class="col-lg-12 mb-3">
                                    <label class="form-label">Apartment</label>
                                    <input class="form-control" type="text" name="apartemen" value="<?php echo htmlspecialchars($data['apartemen']); ?>" />
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Birthday</label>
                                    <input class="form-control" type="date" name="birthday" value="<?php echo htmlspecialchars($data['birthday']); ?>" />
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">City</label>
                                    <input class="form-control" type="text" name="kota" value="<?php echo htmlspecialchars($data['kota']); ?>" />
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Province</label>
                                    <input class="form-control" type="text" name="provinsi" value="<?php echo htmlspecialchars($data['provinsi']); ?>" />
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Postcode</label>
                                    <input class="form-control" type="text" name="postcode" value="<?php echo htmlspecialchars($data['postcode']); ?>" />
                                </div>
                                <!-- Input untuk memilih foto baru -->
                                <div class="col-6 mb-3">
                                    <label class="form-label">Change Profile Picture</label>
                                    <input class="form-control" type="file" name="gambar" />
                                </div>
                            </div>
                            <br />
                            <button class="btn btn-primary" type="submit">Save Changes</button>
                            <a class="btn btn-secondary" href="index.php">Back</a>
                        </form>
                        <?php if (isset($message)) { ?>
                            <div class="alert alert-info mt-3"><?php echo $message; ?></div>
                        <?php } ?>
                        <hr class="my-5" />
                        <div class="row" style="max-width: 920px">
                            <div class="col-md">
                                <article class="box mb-3 bg-light p-3 rounded shadow-sm" style="border: 1px solid #ddd;">
                                    <h6 class="mb-1">Password</h6>
                                    <small class="text-muted d-block mb-2">You can reset or change your password by clicking here</small>
                                    <a class="btn btn-light btn-sm rounded font-md" href="forgot_password.php">Change</a>
                                </article>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>
