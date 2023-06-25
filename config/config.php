<?php

$baseUrl = 'http://localhost/pupuk2';

// Database Connection
$dbHost = 'localhost';
$dbName = 'pupuk-main';
$dbUser = 'root';
$dbPassword = '';

function databaseConnection()
{
    global $dbHost, $dbName, $dbUser, $dbPassword;

    try {
        // Create a new PDO instance
        $db = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPassword);

        // Set PDO error mode to exception
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Additional database configuration (optional)
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Return the PDO object
        return $db;
    } catch (PDOException $e) {
        // Handle database connection errors
        echo "Failed to connect to the database: " . $e->getMessage();
        die();
    }
}

function checkUrl($url)
{
    echo strpos($_SERVER['REQUEST_URI'], $url) !== false ? 'active' : '';
}

function getLengthData($id) {
    $db = databaseConnection();
    $data = $db->prepare("SELECT * FROM detail_transaksi WHERE id_transaksi = :id");
    $data->bindParam(':id', $id);
    $data->execute();
    
    return $data->rowCount();
}

$category = $_POST['category'] ?? '';
if($category == 'updatePassword') {
    $messages = [];
    $current_password = $_POST['current_password'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $id_pengguna = $_POST['id_pengguna'];

    $db = databaseConnection();

    $checkData = $db->prepare("SELECT * FROM pengguna WHERE id = :id_pengguna");
    $checkData->bindParam(':id_pengguna', $id_pengguna);
    $checkData->execute();

    $user = $checkData->fetch(PDO::FETCH_ASSOC);

    if($user && password_verify($current_password, $user['kata_sandi'])) {
        try {
            $queryUpdate = $db->prepare('UPDATE `pengguna` SET kata_sandi = :new_password WHERE id = :id_pengguna');
            $queryUpdate->bindParam(':new_password', $new_password);
            $queryUpdate->bindParam(':id_pengguna', $id_pengguna);
            $queryUpdate->execute();

            if ($queryUpdate->rowCount() > 0) {
                $messages['status'] = 'success';
                $messages['message'] = 'Password berhasil di update';
                $messages['title'] = 'Berhasil';
            } else {
                $messages['status'] = 'error';
                $messages['message'] = 'Terjadi kesalahan';
                $messages['title'] = 'Gagal';
            }
        } catch (PDOException $e) {
            $messages['title'] = 'error';
            $messages['status'] = 'error';
            $messages['message'] = 'Error occurred while saving data: ' . $e->getMessage();
        }
    } else {
        $messages['status'] = 'info';
        $messages['message'] = 'Password lama tidak sesuai';
        $messages['title'] = 'Gagal';
    }

    // Set the appropriate response headers
    header('Content-Type: application/json');

    // Return the JSON response
    echo json_encode($messages);
}
