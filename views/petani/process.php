<?php
session_start();
require_once('../../config/config.php');

// database
$db = databaseConnection();

$category = $_POST['category'];

// Create an empty response array
$response = array();

if ($category == 'tambah' || $category == 'edit') {
    try {
        // get request data form
        $nama = $_POST['nama'];
        $username = $_POST['username'];
        $alamat = $_POST['alamat'];

        // $table = ($_SESSION['role'] == 1) ? 'admin' : 'petani';


        if ($category == 'tambah') {
            // insert new data when category (request is tambah)
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = false;
            $stmt = $db->prepare("INSERT INTO pengguna (username, kata_sandi, role) VALUES (:username, :kata_sandi, :role) ");
            // $stmt = $db->prepare("INSERT INTO pengguna (nama_lengkap, username, kata_sandi, role) VALUES (:nama, :username, :kata_sandi, :role) ");

            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':kata_sandi', $password);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $lastInsertedId = $db->lastInsertId();
            if($stmt->rowCount() > 0) {
                $petani = $db->prepare("INSERT INTO petani (nama_lengkap, alamat, id_pengguna) VALUES (:nama, :alamat, :id_pengguna) ");
                $petani->bindParam(':nama', $nama);
                $petani->bindParam(':alamat', $alamat);
                $petani->bindParam(':id_pengguna', $lastInsertedId);
                $petani->execute();
            }
            
        } elseif ($category == 'edit') {
            $id = $_POST['id'];

            // update existing data when category is edit and the id are match in the database table
            $stmt = $db->prepare("UPDATE pengguna SET username = :username WHERE id = :id");


            // binding
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $petani = $db->prepare("UPDATE petani SET nama_lengkap = :nama, alamat = :alamat WHERE id_pengguna = :id_pengguna");
            $petani->bindParam(':nama', $nama);
            $petani->bindParam(':alamat', $alamat);
            $petani->bindParam(':id_pengguna', $id);
            $petani->execute();
        }

        // Check if the operation was successful
        $response['status'] = 'success';
        $response['message'] = 'Data berhasil disimpan';
        $response['title'] = 'Berhasil';
    } catch (\PDOException $e) {
        $response['title'] = 'error';
        $response['status'] = 'error';
        $response['message'] = 'Error occurred while saving data: ' . $e->getMessage();
    }

    // Set the appropriate response headers
    header('Content-Type: application/json');

    // Return the JSON response
    echo json_encode($response);
} elseif($category == 'getData') {
    // get data in database
    $id = $_POST['id'];
    $stmt = $db->prepare("SELECT b.nama_lengkap, b.alamat, a.username
                            FROM pengguna a
                            JOIN petani b
                            ON a.id = b.id_pengguna
                            WHERE a.id = :id ");
    $stmt->bindParam(':id', $id);

    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

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
} elseif($category == 'delete') {
    // delete data which match with selected id
    $id_pengguna = $_POST['id'];
    // $stmt = $db->prepare("DELETE FROM petani WHERE id_pengguna = :id_pengguna ");
    // $stmt->bindParam(':id_pengguna', $id_pengguna);
    // $stmt->execute();

    $query = $db->prepare("DELETE FROM pengguna WHERE id = :id");
    $query->bindParam(':id', $id_pengguna);
    $query->execute();


    // Check if the operation was successful
    if ($query->rowCount() > 0) {
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
}