<?php
include 'config.php'; // Menghubungkan ke database

// Cek apakah parameter `id` tersedia di URL dan tidak kosong
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Ambil ID kategori dari parameter `id`
    $id = $_GET['id'];

    // Query untuk menghapus kategori berdasarkan ID
    $deleteQuery = "DELETE FROM kategori_produk WHERE id_kategori = '$id'";
    $deleteResult = mysqli_query($koneksi, $deleteQuery);
    
    // Jika penghapusan berhasil
    if ($deleteResult) {
        // Redirect ke halaman daftar kategori
        header('Location: utama.php?page=categories');
        exit;
    } else {
        echo "Gagal menghapus kategori.";
    }
} else {
    echo "ID kategori tidak ditemukan.";
}
