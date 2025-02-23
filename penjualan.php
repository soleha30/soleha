<?php
include "koneksi.php";

// Tambah Data Penjualan
if (isset($_POST['tambah'])) {
    $tanggal = $_POST['tanggal'];
    $pelangganID = $_POST['pelangganID'];
    $produkID = $_POST['produkID'];
    $jumlah = $_POST['jumlah'];

    // Ambil data produk
    $queryProduk = mysqli_query($conn, "SELECT Harga, Stok FROM produk WHERE ProdukID = '$produkID'");
    $dataProduk = mysqli_fetch_assoc($queryProduk);
    $harga = $dataProduk['Harga'];
    $stokTersedia = $dataProduk['Stok'];

    // Cek apakah stok cukup
    if ($stokTersedia < $jumlah) {
        echo "<script>alert('Stok tidak mencukupi!'); window.location='penjualan.php';</script>";
        exit;
    }

    // Hitung total harga
    $subtotal = $harga * $jumlah;

    // Simpan ke tabel penjualan
    $sqlPenjualan = "INSERT INTO penjualan (TanggalPenjualan, PelangganID, TotalHarga) 
                     VALUES ('$tanggal', '$pelangganID', '$subtotal')";
    mysqli_query($conn, $sqlPenjualan);

    // Ambil ID penjualan terakhir
    $penjualanID = mysqli_insert_id($conn);

    // Simpan detail penjualan
    $sqlDetail = "INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal) 
                  VALUES ('$penjualanID', '$produkID', '$jumlah', '$subtotal')";
    mysqli_query($conn, $sqlDetail);

    // Kurangi stok produk
    $stokBaru = $stokTersedia - $jumlah;
    mysqli_query($conn, "UPDATE produk SET Stok = '$stokBaru' WHERE ProdukID = '$produkID'");

    // Redirect setelah berhasil
    header("Location: penjualan.php");
}

// Hapus Data Penjualan
if (isset($_GET['hapus'])) {
    $penjualanID = $_GET['hapus'];

    // Ambil data detail penjualan
    $queryDetail = mysqli_query($conn, "SELECT ProdukID, JumlahProduk FROM detailpenjualan WHERE PenjualanID = '$penjualanID'");
    
    // Kembalikan stok produk
    while ($row = mysqli_fetch_assoc($queryDetail)) {
        $produkID = $row['ProdukID'];
        $jumlah = $row['JumlahProduk'];

        // Ambil stok produk saat ini
        $queryProduk = mysqli_query($conn, "SELECT Stok FROM produk WHERE ProdukID = '$produkID'");
        $stokProduk = mysqli_fetch_assoc($queryProduk)['Stok'];

        // Update stok kembali
        $stokBaru = $stokProduk + $jumlah;
        mysqli_query($conn, "UPDATE produk SET Stok = '$stokBaru' WHERE ProdukID = '$produkID'");
    }

    // Hapus detail penjualan
    mysqli_query($conn, "DELETE FROM detailpenjualan WHERE PenjualanID = '$penjualanID'");

    // Hapus dari tabel penjualan
    mysqli_query($conn, "DELETE FROM penjualan WHERE PenjualanID = '$penjualanID'");

    header("Location: penjualan.php");
}

// Ambil data penjualan dengan detail pelanggan
$result = mysqli_query($conn, "SELECT p.*, pel.NamaPelanggan, pel.Alamat, pel.NomorTelepon 
    FROM penjualan p 
    JOIN pelanggan pel ON p.PelangganID = pel.PelangganID");

$pelanggan = mysqli_query($conn, "SELECT * FROM pelanggan");
$produk = mysqli_query($conn, "SELECT * FROM produk");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    
    
    <!-- Form Tambah Penjualan -->
    <div class="card p-4 mb-4">
    <h2 class="text-center mb-4">Penjualan</h2>
        <h5 class="mb-3">Tambah Penjualan</h5>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Pelanggan</label>
                <select name="pelangganID" class="form-control">
                    <?php while ($row = mysqli_fetch_assoc($pelanggan)) { ?>
                        <option value="<?= $row['PelangganID'] ?>"><?= $row['NamaPelanggan'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Produk</label>
                <select name="produkID" class="form-control" id="produk">
                    <?php while ($row = mysqli_fetch_assoc($produk)) { ?>
                        <option value="<?= $row['ProdukID'] ?>" data-harga="<?= $row['Harga'] ?>">
                            <?= $row['NamaProduk'] ?> - Rp <?= number_format($row['Harga']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Jumlah Produk</label>
                <input type="number" name="jumlah" class="form-control" id="jumlah" required>
            </div>
            <button type="submit" name="tambah" class="btn btn-success">Tambah</button>
            <a href="index.php" class="btn btn-dark">Kembali</a>
        </form>
    </div>

    <!-- Tabel Data Penjualan -->
    <div class="card p-4">
        <h5 class="mb-3">Daftar Penjualan</h5>
        <table class="table table-bordered table-hover">
            <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Pelanggan</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['PenjualanID'] ?></td>
                        <td><?= $row['TanggalPenjualan'] ?></td>
                        <td><?= $row['NamaPelanggan'] ?></td>
                        <td>Rp <?= number_format($row['TotalHarga']) ?></td>
                        <td>
                            <a href="detailpenjualan.php?id=<?= $row['PenjualanID'] ?>" class="btn btn-success btn-sm">Detail</a>
                            <a href="penjualan.php?hapus=<?= $row['PenjualanID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus penjualan ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>