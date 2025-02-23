<?php
include "koneksi.php";

// Inisialisasi variabel
$nama = "";
$alamat = "";
$telepon = "";
$editMode = false;
$idPelanggan = "";


// Jika tombol "Tambah" atau "Update" ditekan
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    
    if ($_POST['idPelanggan'] == "") {
        $sql = "INSERT INTO pelanggan (NamaPelanggan, Alamat, NomorTelepon) VALUES ('$nama', '$alamat', '$telepon')";
    } else {
        $idPelanggan = $_POST['idPelanggan'];
        $sql = "UPDATE pelanggan SET NamaPelanggan='$nama', Alamat='$alamat', NomorTelepon='$telepon' WHERE PelangganID=$idPelanggan";
    }

    mysqli_query($conn, $sql);
    header("Location: pelanggan.php");
}

// Jika tombol "Hapus" ditekan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    
    try {
        // Coba hapus pelanggan
        $query = "DELETE FROM pelanggan WHERE PelangganID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Pelanggan berhasil dihapus.'); window.location='pelanggan.php';</script>";
        } else {
            echo "<script>alert('Pelanggan tidak ditemukan atau sudah dihapus.'); window.location='pelanggan.php';</script>";
        }
    } catch (mysqli_sql_exception $e) {
        // Cek apakah error karena foreign key constraint
        if (strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
            echo "<script>alert('Pelanggan tidak bisa dihapus karena memiliki transaksi di tabel penjualan.'); window.location='pelanggan.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan: " . addslashes($e->getMessage()) . "'); window.location='pelanggan.php';</script>";
        }
    }
}

// Jika tombol "Ubah" ditekan
if (isset($_GET['edit'])) {
    $idPelanggan = $_GET['edit'];
    $result = mysqli_query($conn, "SELECT * FROM pelanggan WHERE PelangganID = $idPelanggan");
    $data = mysqli_fetch_assoc($result);
    
    if ($data) {
        $nama = $data['NamaPelanggan'];
        $alamat = $data['Alamat'];
        $telepon = $data['NomorTelepon'];
        $editMode = true;
    }
}

// Ambil semua data pelanggan
$result = mysqli_query($conn, "SELECT * FROM pelanggan");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Pelanggan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <!-- Form Tambah / Edit Pelanggan -->
    <div class="card p-4 mb-4">
    <h2 class="text-center mb-4">Data Pelanggan</h2>
        <form method="POST">
            <input type="hidden" name="idPelanggan" value="<?= $idPelanggan ?>">
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="<?= $nama ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <input type="text" name="alamat" class="form-control" value="<?= $alamat ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Telepon</label>
                <input type="text" name="telepon" class="form-control" value="<?= $telepon ?>" required>
            </div>
            <button type="submit" name="simpan" class="btn btn-success"><?= $editMode ? "Simpan Perubahan" : "Tambah" ?></button>
            <?php if ($editMode) { ?>
                <a href="pelanggan.php" class="btn btn-danger">Batal</a>
            <?php } ?>
            <!-- Tombol Kembali ke Beranda -->
            <a href="index.php" class="btn btn-dark">Kembali</a>
        </form>
    </div>

    <!-- Tabel Data Pelanggan -->
    <div class="card p-4">
        <h5 class="mb-3">Daftar Pelanggan</h5>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['PelangganID'] ?></td>
                        <td><?= $row['NamaPelanggan'] ?></td>
                        <td><?= $row['Alamat'] ?></td>
                        <td><?= $row['NomorTelepon'] ?></td>
                        <td>
                            <a href="pelanggan.php?edit=<?= $row['PelangganID'] ?>" class="btn btn-success btn-sm">Ubah</a>
                            <a href="pelanggan.php?hapus=<?= $row['PelangganID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
