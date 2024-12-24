<?php
include 'koneksi.php'; // File koneksi ke database

// Ambil ID dari URL
$id = $_GET['id'];

// Ambil data kategori berdasarkan ID
$query = "SELECT * FROM tb_category WHERE category_id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kategori = $_POST['nama_kategori'];

    // Query update data
    $update_query = "UPDATE tb_category SET category_name = '$nama_kategori' WHERE category_id = $id";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Data berhasil diperbarui!');window.location='data-kategori.php';</script>";
    } else {
        echo "Gagal memperbarui data: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori</title>
</head>
<body>
    <h2>Edit Kategori</h2>
    <form method="POST">
        <label for="nama_kategori">Nama Kategori:</label><br>
        <input type="text" name="nama_kategori" id="nama_kategori" value="<?php echo $data['category_name']; ?>" required><br><br>
        <button type="submit">Simpan</button>
        <a href="data-kategori.php">Kembali</a>
    </form>
</body>
</html>
