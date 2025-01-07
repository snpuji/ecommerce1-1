<?php
include 'config.php'; // Pastikan sudah terkoneksi ke database

// Mendefinisikan nomor secara dinamis
$query_count = "SELECT COUNT(*) AS total FROM kategori_produk";
$result_count = mysqli_query($koneksi, $query_count);
$row_count = mysqli_fetch_assoc($result_count);
$no = $row_count['total'] + 1;

// Cek jika form dikirimkan
if (isset($_POST['submit'])) {
    // Mengambil data dari form
    $nama_kategori = $_POST['nama_kategori'];

    // Cek jika nama kategori tidak kosong
    if (!empty($nama_kategori)) {
        // Menambahkan data ke dalam tabel kategoriproduk
        $query = "INSERT INTO kategori_produk (nama_kategori) VALUES ('$nama_kategori')";
        $result = mysqli_query($koneksi, $query);

        if ($result) {
            echo '<script>alert("Berhasil menambahkan data."); document.location="utama.php?page=categories";</script>';
        } else {
            echo '<div style="color:red">Gagal menambahkan data.</div>';
        }
    } else {
        echo '<div style="color:red">Nama kategori tidak boleh kosong.</div>';
    }
}
?>

<form action="utama.php?page=addcategories" method="post">
<section class="content-main">
                <div class="row">
                    <div class="col-9">
                        <div class="content-header">
                            <h2 class="content-title">Add New Categories</h2>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4>Basic</h4>
                            </div>
                            <div class="card-body">
                                <form>
                                <div class="mb-4">
                                    <label for="no" class="form-label">No</label>
                                    <!-- Input nomor yang dinamis dan tidak dapat diedit -->
                                    <input type="text" placeholder="Auto-generated number" class="form-control" value="<?php echo $no; ?>" disabled />
                                </div>
                                    <div class="mb-4">
                                        <label class="form-label">categories</label>
                                        <textarea name="nama_kategori" placeholder="Type here" class="form-control" rows="4"></textarea>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div>
                            <button type="submit" name="submit" class="btn btn-md rounded font-sm hover-up">Save</button>
                            <a href="utama.php?page=categories" class="btn btn-light rounded font-sm mr-5 text-body hover-up">Back</a>
                        </div>
                    </div>
                </div>
            </section>
            <!-- content-main end// -->
            
