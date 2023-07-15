<?php
require_once('../../config/config.php');

$db = databaseConnection();

$category = $_POST['category']; // Mengambil nilai 'category' dari input POST

$response = array(); // Membuat array kosong untuk menampung respon

if ($category == 'tambah' || $category == 'edit') { // Jika 'category' adalah 'tambah' atau 'edit'
    try {
        $nama = $_POST['nama']; // Mengambil nilai 'nama' dari input POST
        $url = $_POST['url']; // Mengambil nilai 'url' dari input POST
        $jumlah = $_POST['jumlah']; // Mengambil nilai 'jumlah' dari input POST
        $harga = $_POST['harga']; // Mengambil nilai 'harga' dari input POST

        if ($category == 'tambah') { // Jika 'category' adalah 'tambah'
            $stmt = $db->prepare("INSERT INTO produk (nama_produk, gambar_produk, jumlah_produk_kg, harga_produk) VALUES (:nama, :gambar, :jumlah, :harga) ");
        } elseif ($category == 'edit') { // Jika 'category' adalah 'edit'
            $stmt = $db->prepare("UPDATE produk SET nama_produk = :nama, gambar_produk = :gambar, jumlah_produk_kg = :jumlah, harga_produk = :harga WHERE id = :id");

            $id = $_POST['id']; // Mengambil nilai 'id' dari input POST
            $stmt->bindParam(':id', $id); // Mengikat parameter ':id' dengan nilai 'id'
        }

        $stmt->bindParam(':nama', $nama); // Mengikat parameter ':nama' dengan nilai 'nama'
        $stmt->bindParam(':gambar', $url); // Mengikat parameter ':gambar' dengan nilai 'url'
        $stmt->bindParam(':jumlah', $jumlah); // Mengikat parameter ':jumlah' dengan nilai 'jumlah'
        $stmt->bindParam(':harga', $harga); // Mengikat parameter ':harga' dengan nilai 'harga'

        $stmt->execute(); // Menjalankan query untuk menyimpan data produk

        if ($stmt->rowCount() > 0) { // Jika ada perubahan data
            $response['status'] = 'success'; // Menetapkan status 'success' pada respon
            $response['message'] = 'Data berhasil disimpan'; // Menetapkan pesan sukses
            $response['title'] = 'Berhasil'; // Menetapkan judul sukses
        } else {
            $response['status'] = 'error'; // Menetapkan status 'error' pada respon
            $response['message'] = 'Tidak ada perubahan data'; // Menetapkan pesan kesalahan
            $response['title'] = 'Gagal'; // Menetapkan judul kesalahan
        }
    } catch (\PDOException $e) {
        $response['title'] = 'error'; // Menetapkan judul kesalahan pada respon
        $response['status'] = 'error'; // Menetapkan status 'error' pada respon
        $response['message'] = 'Error occurred while saving data: ' . $e->getMessage(); // Menetapkan pesan kesalahan yang terjadi
    }

    header('Content-Type: application/json'); // Mengatur header respon yang tepat

    echo json_encode($response); // Mengembalikan respon dalam format JSON
} elseif ($category == 'getData') { // Jika 'category' adalah 'getData'
    $id = $_POST['id']; // Mengambil nilai 'id' dari input POST
    $stmt = $db->prepare("SELECT * FROM produk WHERE id = :id ");
    $stmt->bindParam(':id', $id); // Mengikat parameter ':id' dengan nilai 'id'

    $stmt->execute(); // Menjalankan query

    $data = $stmt->fetch(PDO::FETCH_ASSOC); // Mengambil hasil query sebagai array asosiatif

    if ($data) { // Jika data ditemukan
        header('Content-Type: application/json'); // Mengatur header respon yang tepat
        echo json_encode($data); // Mengembalikan respon dalam format JSON dengan data
    } else {
        header('HTTP/1.1 404 Not Found'); // Mengatur status header menjadi 404 Not Found
        echo json_encode(array('message' => 'Data not found')); // Mengembalikan respon dalam format JSON dengan pesan bahwa data tidak ditemukan
    }
} elseif ($category == 'delete') { // Jika 'category' adalah 'delete'
    $id = $_POST['id']; // Mengambil nilai 'id' dari input POST
    $stmt = $db->prepare("DELETE FROM produk WHERE id = :id ");
    $stmt->bindParam(':id', $id); // Mengikat parameter ':id' dengan nilai 'id'

    $stmt->execute(); // Menjalankan query untuk menghapus data produk

    if ($stmt->rowCount() > 0) { // Jika ada perubahan data
        $response['status'] = 'success'; // Menetapkan status 'success' pada respon
        $response['message'] = 'Data berhasil dihapus'; // Menetapkan pesan sukses
        $response['title'] = 'Berhasil'; // Menetapkan judul sukses
    } else {
        $response['status'] = 'error'; // Menetapkan status 'error' pada respon
        $response['message'] = 'Tidak ada perubahan data'; // Menetapkan pesan kesalahan
        $response['title'] = 'Gagal'; // Menetapkan judul kesalahan
    }

    header('Content-Type: application/json'); // Mengatur header respon yang tepat

    echo json_encode($response); // Mengembalikan respon dalam format JSON
}
