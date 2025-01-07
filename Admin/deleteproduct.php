<?php
include 'config.php';

// Memeriksa apakah parameter 'id' ada di URL
if (isset($_GET['id'])) {
    $id_produk = $_GET['id'];

    // Query untuk mengambil data produk berdasarkan ID
    $query = "SELECT * FROM produk WHERE id_produk = '$id_produk'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        // Mengambil data produk yang akan dihapus
        $row = mysqli_fetch_assoc($result);

        // Menghapus gambar produk jika ada
        if (!empty($row['gambar']) && file_exists("../upload/" . $row['gambar'])) {
            unlink("../upload/" . $row['gambar']);
        }

        // Query untuk menghapus produk dari tabel
        $delete_query = "DELETE FROM produk WHERE id_produk = '$id_produk'";

        if (mysqli_query($koneksi, $delete_query)) {
            // Jika berhasil dihapus, arahkan ke halaman product list
            header("Location: utama.php?page=product");
            exit;
        } else {
            // Jika query gagal, tampilkan pesan error
            echo "Error deleting product: " . mysqli_error($koneksi);
        }
    } else {
        echo "Produk tidak ditemukan.";
    }
} else {
    echo "ID produk tidak tersedia.";
}
?>
