<?php
$server = "localhost";
$username = "root";
$password = "";
$database = "kasir_soleha";

// Membuat koneksi
$conn = mysqli_connect($server, $username, $password, $database);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>