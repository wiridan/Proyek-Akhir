<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna login
if ($_SESSION['status_login'] != true) {
    echo '<script>window.location="login.php"</script>';
}

// Ambil ID produk dari URL
$id = $_GET['id'];

// Ambil data produk berdasarkan ID
$query = "SELECT * FROM tb_product WHERE product_id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

// Ambil semua kategori untuk dropdown
$category_query = "SELECT * FROM tb_category";
$category_result = mysqli_query($conn, $category_query);

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_produk = $_POST['nama_produk'];
    $kategori_id = $_POST['kategori_id'];
    $harga_produk = $_POST['harga_produk'];
    $deskripsi_produk = $_POST['deskripsi_produk'];
    $status_produk = $_POST['status_produk'];
    $gambar_lama = $_POST['gambar_lama'];

    // Jika ada gambar baru diupload
    if ($_FILES['gambar_produk']['name'] != '') {
        $filename = $_FILES['gambar_produk']['name'];
        $tmp_name = $_FILES['gambar_produk']['tmp_name'];
        $file_type = pathinfo($filename, PATHINFO_EXTENSION);

        // Validasi ekstensi file
        $allowed_types = ['jpg', 'jpeg', 'png'];
        if (!in_array($file_type, $allowed_types)) {
            echo '<script>alert("Format file tidak valid! (JPG, JPEG, PNG saja)");</script>';
        } else {
            // Pindahkan file baru
            $new_image_name = time() . '_' . $filename;
            move_uploaded_file($tmp_name, 'produk/' . $new_image_name);

            // Hapus gambar lama
            if (file_exists('produk/' . $gambar_lama)) {
                unlink('produk/' . $gambar_lama);
            }
        }
    } else {
        $new_image_name = $gambar_lama; // Tetap gunakan gambar lama jika tidak ada file baru
    }

    // Query update data
    $update_query = "UPDATE tb_product SET 
                        product_name = '$nama_produk', 
                        category_id = $kategori_id, 
                        product_price = $harga_produk, 
                        product_description = '$deskripsi_produk', 
                        product_image = '$new_image_name', 
                        product_status = $status_produk 
                     WHERE product_id = $id";

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Data produk berhasil diperbarui!');window.location='data-produk.php';</script>";
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
    <title>Edit Produk</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<header>
    <div class="container">
        <h1><a href="dashboard.php">Belanjain</a></h1>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="profil.php">Profil</a></li>
            <li><a href="data-kategori.php">Kategori</a></li>
            <li><a href="data-produk.php">Produk</a></li>
            <li><a href="keluar.php">Keluar</a></li>
        </ul>
    </div>
</header>
<div class="section">
    <div class="container">
        <h3>Edit Produk</h3>
        <div class="box">
            <form method="POST" enctype="multipart/form-data">
                <label for="nama_produk">Nama Produk</label>
                <input type="text" name="nama_produk" id="nama_produk" value="<?php echo $data['product_name']; ?>" required>

                <label for="kategori_id">Kategori</label>
                <select name="kategori_id" id="kategori_id" required>
                    <option value="">-- Pilih Kategori --</option>
                    <?php while ($row = mysqli_fetch_assoc($category_result)) { ?>
                        <option value="<?php echo $row['category_id']; ?>" <?php if ($row['category_id'] == $data['category_id']) echo 'selected'; ?>>
                            <?php echo $row['category_name']; ?>
                        </option>
                    <?php } ?>
                </select>

                <label for="harga_produk">Harga Produk</label>
                <input type="number" name="harga_produk" id="harga_produk" value="<?php echo $data['product_price']; ?>" required>

                <label for="deskripsi_produk">Deskripsi Produk</label>
                <textarea name="deskripsi_produk" id="deskripsi_produk" required><?php echo $data['product_description']; ?></textarea>

                <label for="gambar_produk">Gambar Produk</label>
                <img src="produk/<?php echo $data['product_image']; ?>" width="100px">
                <input type="hidden" name="gambar_lama" value="<?php echo $data['product_image']; ?>">
                <input type="file" name="gambar_produk" id="gambar_produk">

                <label for="status_produk">Status Produk</label>
                <select name="status_produk" id="status_produk" required>
                    <option value="1" <?php if ($data['product_status'] == 1) echo 'selected'; ?>>Aktif</option>
                    <option value="0" <?php if ($data['product_status'] == 0) echo 'selected'; ?>>Tidak Aktif</option>
                </select>

                <input type="submit" value="Simpan">
                <a href="data-produk.php">Kembali</a>
            </form>
        </div>
    </div>
</div>
<footer>
    <div class="container">
        <small>Copyright &copy; 2024 - Belanjain.</small>
    </div>
</footer>
</body>
</html>
