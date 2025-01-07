<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id_kategori = $_GET['id'];

    // Query untuk mengambil data kategori berdasarkan ID
    $query = "SELECT * FROM kategori_produk WHERE id_kategori = '$id_kategori'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "Kategori tidak ditemukan.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = mysqli_real_escape_string($koneksi, $_POST['nama_kategori']);
    
    // Query untuk update kategori
    $update_query = "UPDATE kategori_produk SET nama_kategori = '$nama_kategori' WHERE id_kategori = '$id_kategori'";
    
    if (mysqli_query($koneksi, $update_query)) {
        header("Location: utama.php?page=categories");
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($koneksi);
    }
}
?>

<!-- HTML Form untuk edit kategori -->
<form action="editcategories.php?id=<?php echo $id_kategori; ?>" method="post">
    <section class="content-main">
        <div class="content-header">
            <div>
                <h2 class="content-title card-title">Edit Category</h2>
            </div>
            <div>
                <a href="utama.php?page=categories" class="btn btn-secondary btn-sm rounded">Back to Categories</a>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="form-group">
                    <label for="nama_kategori">Category Name</label>
                    <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" value="<?php echo htmlspecialchars($row['nama_kategori']); ?>" required>
                </div>
                <div class="form-group text-end">
                    <button type="submit" class="btn btn-primary btn-sm rounded">Save Changes</button>
                </div>
            </div>
        </div>
    </section>
</form>

