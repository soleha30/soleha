<?php
include "koneksi.php";

// Tambah Data Produk
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $sql = "INSERT INTO produk (NamaProduk, Harga, Stok) VALUES ('$nama', '$harga', '$stok')";
    mysqli_query($conn, $sql);
    header("Location: produk.php");
}

// Ubah Data Produk
if (isset($_POST['ubah'])) {
    $id = $_POST['produkID'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $sql = "UPDATE produk SET NamaProduk = '$nama', Harga = '$harga', Stok = '$stok' WHERE ProdukID = $id";
    mysqli_query($conn, $sql);
    header("Location: produk.php");
}

// Jika tombol "Hapus" ditekan untuk produk
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    try {
        $query = "DELETE FROM produk WHERE ProdukID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Produk berhasil dihapus.'); window.location='produk.php';</script>";
        } else {
            echo "<script>alert('Produk tidak ditemukan atau sudah dihapus.'); window.location='produk.php';</script>";
        }
    } catch (mysqli_sql_exception $e) {
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            echo "<script>alert('Produk tidak bisa dihapus karena memiliki transaksi terkait.'); window.location='produk.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: " . addslashes($e->getMessage()) . "'); window.location='produk.php';</script>";
        }
    }
}

// Ambil Semua Data Produk
$result = mysqli_query($conn, "SELECT * FROM produk");

// Ambil Data Produk untuk Ubah
if (isset($_GET['ubah_id'])) {
    $id = $_GET['ubah_id'];
    $produk = mysqli_query($conn, "SELECT * FROM produk WHERE ProdukID = $id");
    $data_produk = mysqli_fetch_assoc($produk);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <!-- Form Tambah Produk -->
    <div class="card p-4 mb-4">
    <h2 class="text-center mb-4">Produk</h2>
        <h5 class="mb-3">Tambah Produk</h5>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Produk</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Harga</label>
                <input type="number" name="harga" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Stok</label>
                <input type="number" name="stok" class="form-control" required>
            </div>
            <button type="submit" name="tambah" class="btn btn-success">Tambah</button>
            <a href="index.php" class="btn btn-dark">Kembali</a>
        </form>
    </div>

    <!-- Form Ubah Produk (Jika ada produk yang dipilih untuk diubah) -->
    <?php if (isset($data_produk)) { ?>
        <div class="card p-4 mb-4">
            <h5 class="mb-3">Ubah Produk</h5>
            <form method="POST">
                <input type="hidden" name="produkID" value="<?= $data_produk['ProdukID'] ?>">
                <div class="mb-3">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="nama" class="form-control" value="<?= $data_produk['NamaProduk'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="harga" class="form-control" value="<?= $data_produk['Harga'] ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" value="<?= $data_produk['Stok'] ?>" required>
                </div>
                <button type="submit" name="ubah" class="btn btn-success">Ubah</button>
                <a href="produk.php" class="btn btn-dark">Kembali</a>
            </form>
        </div>
    <?php } ?>

    <!-- Tabel Data Produk -->
    <div class="card p-4">
        <h5 class="mb-3">Daftar Produk</h5>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Produk</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['ProdukID'] ?></td>
                        <td><?= $row['NamaProduk'] ?></td>
                        <td>Rp <?= number_format($row['Harga'], ) ?></td>
                        <td><?= $row['Stok'] ?></td>
                        <td>
                            <a href="produk.php?ubah_id=<?= $row['ProdukID'] ?>" class="btn btn-success btn-sm">Ubah</a>
                            <a href="produk.php?hapus=<?= $row['ProdukID'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin ingin menghapus produk ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
