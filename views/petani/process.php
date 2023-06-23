<?php
require_once('../../config/config.php');

// database
$db = databaseConnection();

$category = $_POST['category'];

// Create an empty response array
$response = array();

if ($category == 'tambah' || $category == 'edit') {
    try {
        $nama = $_POST['nama'];
        $username = $_POST['username'];


        if ($category == 'tambah') {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role = false;
            $stmt = $db->prepare("INSERT INTO pengguna (nama_lengkap, username, kata_sandi, role) VALUES (:nama, :username, :kata_sandi, :role) ");

            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':kata_sandi', $password);
        } elseif ($category == 'edit') {
            $stmt = $db->prepare("UPDATE pengguna SET nama_lengkap = :nama, username = :username WHERE id = :id");

            $id = $_POST['id'];

            // binding
            $stmt->bindParam(':id', $id);
        }

        $stmt->bindParam(':nama', $nama);
        $stmt->bindParam(':username', $username);

        $stmt->execute();

        // Check if the operation was successful
        if ($stmt->rowCount() > 0) {
            $response['status'] = 'success';
            $response['message'] = 'Data berhasil disimpan';
            $response['title'] = 'Berhasil';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Tidak ada perubahan data';
            $response['title'] = 'Gagal';
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
} elseif($category == 'getData') {
    $id = $_POST['id'];
    $stmt = $db->prepare("SELECT * FROM pengguna WHERE id = :id ");
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
    $id = $_POST['id'];
    $stmt = $db->prepare("DELETE FROM pengguna WHERE id = :id ");
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
}