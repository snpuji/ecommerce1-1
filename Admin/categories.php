<?php
include 'config.php';

$no = 1; // Inisialisasi nomor urut
$query = "SELECT * FROM kategori_produk";
$result = mysqli_query($koneksi, $query);
?>


    <form action="utama.php?page=categories" method="get">
        <section class="content-main">
            <div class="content-header">
                <div>
                    <h2 class="content-title card-title">Categories List</h2>
                </div>
                <div>
                    <a href="utama.php?page=addcategories" class="btn btn-primary btn-sm rounded">Create new</a>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    // Tampilkan data jika ada
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <article class="itemlist">
                            <div class="row align-items-center">
                                <div class="col-lg-1 col-sm-1 col-2">
                                    <h6 class="mb-0">
                                        <?php echo $no++; ?>
                                    </h6>
                                </div>
                                <div class="col-lg-4 col-sm-4 col-8 flex-grow-1 col-name">
                                    <a class="itemside" href="#">
                                        <div class="info">
                                            <h6 class="mb-0">
                                                <?php echo htmlspecialchars($row['nama_kategori']); ?>
                                            </h6>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-lg-2 col-sm-2 col-4 col-action text-end">
                                    <a href="utama.php?page=editcategories&id=<?php echo $row['id_kategori']; ?>" class="btn btn-sm font-sm rounded btn-brand"> 
                                        <i class="material-icons md-edit"></i> Edit 
                                    </a>
                                    <a href="deletecategories.php?id=<?php echo $row['id_kategori']; ?>" class="btn btn-sm font-sm btn-light rounded" onclick="return confirm('Are you sure you want to delete this category?');"> 
                                        <i class="material-icons md-delete_forever"></i> Delete 
                                    </a>
                                </div>
                            </div>
                        </article>
                        <?php
                    }
                } else {
                    // Jika tidak ada data atau query gagal
                    ?>
                    <div class="alert alert-warning" role="alert">
                        No categories found or an error occurred. Please check your database connection or add new categories.
                    </div>
                    <?php
                }
                ?>
                </div>
            </div>
        </section>
    </form>

