<?php
include 'config.php';

$id_keranjang = $_POST['id_keranjang'];
$sql = "DELETE FROM keranjang WHERE id_keranjang = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_keranjang);
$stmt->execute();

header("Location: utama.php?page=cart");
exit();
?>
