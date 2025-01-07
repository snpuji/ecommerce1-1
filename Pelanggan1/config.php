<?php 

$server = 'localhost'; 
$email= 'root'; 
$password = ''; 
$database = 'dblogin'; 
$koneksi = mysqli_connect($server, $email, $password, $database); 


if(mysqli_connect_errno()){ 
    echo 'Koneksi gagal'; 
}  
// else { 
//     echo 'Koneksi berhasil'; 
// } 
?>
