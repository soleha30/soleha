<?php
include "koneksi.php";

if (!isset($_GET['id'])) {
    die("ID Penjualan tidak ditemukan!");
}

$penjualanID = $_GET['id'];

// Ambil data penjualan
$penjualan = mysqli_query($conn, "SELECT penjualan.*, pelanggan.NamaPelanggan, pelanggan.Alamat, pelanggan.NomorTelepon
                                  FROM penjualan 
                                  JOIN pelanggan ON penjualan.PelangganID = pelanggan.PelangganID
                                  WHERE penjualan.PenjualanID = $penjualanID");
$data_penjualan = mysqli_fetch_assoc($penjualan);

// Ambil data detail penjualan
$detail = mysqli_query($conn, "SELECT detailpenjualan.*, produk.NamaProduk, produk.Harga 
                               FROM detailpenjualan 
                               JOIN produk ON detailpenjualan.ProdukID = produk.ProdukID
                               WHERE detailpenjualan.PenjualanID = $penjualanID");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container mt-4">
    
    <!-- Informasi Penjualan -->
    <div class="card p-4 mb-4">
        <h5>Detail Penjualan</h5>
        <p><strong>ID Penjualan:</strong> <?= $data_penjualan['PenjualanID'] ?></p>
        <p><strong>Tanggal:</strong> <?= $data_penjualan['TanggalPenjualan'] ?></p>
        <p><strong>Pelanggan:</strong> <?= $data_penjualan['NamaPelanggan'] ?></p>
        <p><strong>Alamat:</strong> <?= $data_penjualan['Alamat'] ?></p>
        <p><strong>Nomor Telepon:</strong> <?= $data_penjualan['NomorTelepon'] ?></p>
        <p><strong>Total Harga:</strong> Rp <?= number_format($data_penjualan['TotalHarga']) ?></p>
    </div>

    <!-- Tabel Detail Penjualan -->
    <div class="card p-4">
        <h5>Detail Produk</h5>
        <table class="table table-bordered table-hover">
            <thead class="table-secondary">
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($detail)) { ?>
                    <tr>
                        <td><?= $row['NamaProduk'] ?></td>
                        <td>Rp <?= number_format($row['Harga']) ?></td>
                        <td><?= $row['JumlahProduk'] ?></td>
                        <td>Rp <?= number_format($row['Subtotal']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <a href="penjualan.php" class="btn btn-dark mt-3">Kembali ke Penjualan</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
 