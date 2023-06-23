<?php
require_once('../../config/config.php');

// database
$db = databaseConnection();

$category = $_POST['category'];

// Create an empty response array
$response = array();

if($category == 'getData') {
    $id = $_POST['id'];
    $stmt = $db->prepare("SELECT b.harga_produk, b.nama_produk, a.kuantitas
                            FROM detail_transaksi a
                            JOIN produk b
                            ON a.id_produk=b.id
                            WHERE a.id_transaksi = :id ");
    $stmt->bindParam(':id', $id);

    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if data was found
    if ($data) {
        // Return the data as JSON
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        // Return an error message if data was not found
        header('HTTP/1.1 404 Not Found');
        echo json_encode(array('message' => 'Data not found'));
    }
}