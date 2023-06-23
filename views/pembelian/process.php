<?php
session_start();
require_once('../../config/config.php');

// database
$db = databaseConnection();

$category = $_POST['category'];
$id_pengguna = $_SESSION['id_pengguna'];

// Create an empty response array
$response = array();

if ($category == 'addCart') {
    try {
        $id_produk = $_POST['id_produk'];
        $kuantitas = $_POST['kuantitas'];
        $is_done = false;
        $tanggal_transaksi = date('Y-m-d');
        $total = 0;

        // check if is_done exists
        $check = $db->prepare("SELECT * FROM transaksi WHERE id_pengguna = :id_pengguna AND is_done = :is_done");
        $check->bindParam(':id_pengguna', $id_pengguna);
        $check->bindParam(':is_done', $is_done);
        $check->execute();
        $dataCheck = $check->fetch(PDO::FETCH_ASSOC);
        if ($check->rowCount() == 0) {
            $stmt = $db->prepare("INSERT INTO transaksi (id_pengguna, tanggal_transaksi, total, is_done) VALUES (:id_pengguna, :tanggal_transaksi, :total, :is_done) ");
            $stmt->bindParam(':tanggal_transaksi', $tanggal_transaksi);
            $stmt->bindParam(':total', $total);
            $stmt->bindParam(':id_pengguna', $id_pengguna);
            $stmt->bindParam(':is_done', $is_done);
            $stmt->execute();

            $lastInsertedId = $db->lastInsertId();
            if ($stmt->rowCount() > 0) {
                $detail = $db->prepare("INSERT INTO detail_transaksi (id_transaksi, id_produk, kuantitas) VALUES (:id_transaksi, :id_produk, :kuantitas) ");
                $detail->bindParam(':id_transaksi', $lastInsertedId);
                $detail->bindParam(':id_produk', $id_produk);
                $detail->bindParam(':kuantitas', $kuantitas);

                $detail->execute();

                // Check if the operation was successful
                if ($detail->rowCount() > 0) {
                    $response['status'] = 'success';
                    $response['message'] = 'Produk berhasil ditambahkan ke keranjang';
                    $response['title'] = 'Berhasil';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Tidak ada perubahan data';
                    $response['title'] = 'Gagal';
                }
            }
        } else {
            // check id produk in detail transaksi
            $det = $db->prepare("SELECT * FROM detail_transaksi WHERE id_produk = :id_produk AND id_transaksi = :id_transaksi");
            $det->bindParam(':id_transaksi', $dataCheck['id']);
            $det->bindParam(':id_produk', $id_produk);

            $det->execute();
            if ($det->rowCount() > 0) {
                $response['status'] = 'info';
                $response['message'] = 'Produk sudah ada pada keranjang';
                $response['title'] = 'Info';
            } else {
                $sql = $db->prepare("INSERT INTO detail_transaksi (id_transaksi, id_produk, kuantitas) VALUES (:id_transaksi, :id_produk, :kuantitas) ");
                $sql->bindParam(':id_transaksi', $dataCheck['id']);
                $sql->bindParam(':id_produk', $id_produk);
                $sql->bindParam(':kuantitas', $kuantitas);
                $sql->execute();
                // Check if the operation was successful
                if ($sql->rowCount() > 0) {
                    $response['status'] = 'success';
                    $response['message'] = 'Produk berhasil ditambahkan ke keranjang';
                    $response['title'] = 'Berhasil';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Tidak ada perubahan data';
                    $response['title'] = 'Gagal';
                }
            }
        }
    } catch (\PDOException $e) {
        $response['title'] = 'error';
        $response['status'] = 'error';
        $response['message'] = 'Error occurred while saving data: ' . $e->getMessage();
    }

    // Set the appropriate response headers
    header('Content-Type: application/json');

    // Return the JSON response
    echo json_encode($response);
} elseif ($category == 'delete') {
    $id = $_POST['id'];
    $id_transaksi = $_POST['id_transaksi'];

    // check if last item in detail_transaksi
    $check = $db->prepare("SELECT * FROM detail_transaksi WHERE id_transaksi = :id_transaksi");
    $check->bindParam(':id_transaksi', $id_transaksi);
    $check->execute();

    if ($check->rowCount() == 1) {
        // delete transaksi
        $transaksi = $db->prepare("DELETE FROM transaksi WHERE id = :id_transaksi");
        $transaksi->bindParam(':id_transaksi', $id_transaksi);
        $transaksi->execute();
    }

    $stmt = $db->prepare("DELETE FROM detail_transaksi WHERE id = :id ");
    $stmt->bindParam(':id', $id);

    $stmt->execute();

    // Check if the operation was successful
    if ($stmt->rowCount() > 0) {
        $response['status'] = 'success';
        $response['message'] = 'Data berhasil dihapus';
        $response['title'] = 'Berhasil';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Tidak ada perubahan data';
        $response['title'] = 'Gagal';
    }

    // Set the appropriate response headers
    header('Content-Type: application/json');

    // Return the JSON response
    echo json_encode($response);
} elseif ($category == 'checkout') {
    try {
        // check data in transaksi
        $transaksi = $db->prepare("SELECT * FROM transaksi WHERE id_pengguna = :id_pengguna AND is_done = 0");
        $transaksi->bindParam(':id_pengguna', $id_pengguna);
        $transaksi->execute();
        $data = $transaksi->fetch(PDO::FETCH_ASSOC);

        // get data on detail transaksi
        $detail_transaksi = $db->prepare("SELECT a.kuantitas, b.harga_produk, b.id as id_produk
                                        FROM detail_transaksi a
                                        JOIN produk b
                                        ON a.id_produk=b.id
                                        WHERE id_transaksi = :id_transaksi");
        $detail_transaksi->bindParam(':id_transaksi', $data['id']);
        $detail_transaksi->execute();
        $detailData = $detail_transaksi->fetchAll(PDO::FETCH_ASSOC);

        $total = 0;
        foreach ($detailData as $row) {
            $total += $row['harga_produk'];

            // update stok in produk table
            $updateStok = $db->prepare("UPDATE produk SET jumlah_produk_kg = jumlah_produk_kg - :kuantitas WHERE id = :id_produk");
            $updateStok->bindParam(':kuantitas', $row['kuantitas']);
            $updateStok->bindParam(':id_produk', $row['id_produk']);
            $updateStok->execute();
        }

        // update total and is_done in transaksi
        $updateTransaksi = $db->prepare("UPDATE transaksi SET total = :total, is_done = 1 WHERE id = :id_transaksi");
        $updateTransaksi->bindParam(':total', $total);
        $updateTransaksi->bindParam(':id_transaksi', $data['id']);
        $updateTransaksi->execute();

        // Check if the operation was successful
        if ($updateTransaksi->rowCount() > 0) {
            $response['status'] = 'success';
            $response['message'] = 'Data berhasil di proses';
            $response['title'] = 'Berhasil';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Tidak ada perubahan data';
            $response['title'] = 'Gagal';
        }

        // Set the appropriate response headers
        header('Content-Type: application/json');

        // Return the JSON response
        echo json_encode($response);
    } catch (PDOException $e) {
        echo "Error occurred: " . $e->getMessage();
    }
}
