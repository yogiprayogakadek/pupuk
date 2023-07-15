<?php
require_once('../../config/config.php');

$db = databaseConnection();

$category = $_POST['category']; // Mengambil nilai 'category' dari input POST

$response = array(); // Membuat array kosong untuk menampung respon

if ($category == 'getData') { // Jika 'category' adalah 'getData'
    $id = $_POST['id']; // Mengambil nilai 'id' dari input POST
    $stmt = $db->prepare("SELECT b.harga_produk, b.nama_produk, a.kuantitas
                            FROM detail_transaksi a
                            JOIN produk b
                            ON a.id_produk=b.id
                            WHERE a.id_transaksi = :id ");
    $stmt->bindParam(':id', $id); // Mengikat parameter ':id' dengan nilai 'id'

    $stmt->execute(); // Menjalankan query

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Mengambil hasil query sebagai array asosiatif

    if ($data) { // Jika data ditemukan
        header('Content-Type: application/json'); // Mengatur header respon yang tepat
        echo json_encode($data); // Mengembalikan respon dalam format JSON dengan data
    } else {
        header('HTTP/1.1 404 Not Found'); // Mengatur status header menjadi 404 Not Found
        echo json_encode(array('message' => 'Data not found')); // Mengembalikan respon dalam format JSON dengan pesan bahwa data tidak ditemukan
    }
}
