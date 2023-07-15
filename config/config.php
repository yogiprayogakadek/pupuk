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
    $db = databaseConnection(); // Membuat koneksi ke database

    $data = $db->prepare("SELECT * FROM detail_transaksi WHERE id_transaksi = :id"); // Menyiapkan query untuk memilih data dari tabel 'detail_transaksi' dimana 'id_transaksi' cocok dengan parameter yang diberikan
    $data->bindParam(':id', $id); // Mengikat parameter ':id' dengan ID yang diberikan
    $data->execute(); // Menjalankan query

    return $data->rowCount(); // Mengembalikan jumlah baris yang dihasilkan oleh query
}

$cate = $_POST['category'] ?? ''; // Mengambil nilai 'category' dari input POST dan menggunakan nilai default kosong ('') jika tidak ada nilai yang diberikan
if($cate == 'updatePassword') { // Memeriksa apakah nilai 'category' adalah 'updatePassword'
    $messages = []; // Mendefinisikan array kosong untuk menyimpan pesan-pesan
    
    $current_password = $_POST['current_password']; // Mengambil nilai 'current_password' dari input POST
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Mengenkripsi nilai 'new_password' menggunakan fungsi password_hash()
    $id_pengguna = $_POST['id_pengguna']; // Mengambil nilai 'id_pengguna' dari input POST

    $db = databaseConnection(); // Membuat koneksi ke database

    $checkData = $db->prepare("SELECT * FROM pengguna WHERE id = :id_pengguna"); // Menyiapkan query untuk memeriksa data pengguna berdasarkan 'id_pengguna'
    $checkData->bindParam(':id_pengguna', $id_pengguna); // Mengikat parameter ':id_pengguna' dengan nilai 'id_pengguna'
    $checkData->execute(); // Menjalankan query

    $user = $checkData->fetch(PDO::FETCH_ASSOC); // Mengambil hasil query sebagai array asosiatif

    if($user && password_verify($current_password, $user['kata_sandi'])) { // Memeriksa apakah pengguna ada dan password yang diberikan cocok dengan password di database
        try {
            $queryUpdate = $db->prepare("UPDATE pengguna SET kata_sandi = :new_password WHERE id = :id_pengguna"); // Menyiapkan query untuk mengupdate password pengguna
            $queryUpdate->bindParam(':new_password', $new_password); // Mengikat parameter ':new_password' dengan nilai 'new_password'
            $queryUpdate->bindParam(':id_pengguna', $id_pengguna); // Mengikat parameter ':id_pengguna' dengan nilai 'id_pengguna'
            $queryUpdate->execute(); // Menjalankan query update

            if ($queryUpdate->rowCount() > 0) { // Memeriksa apakah terjadi pembaruan pada baris data
                $messages['status'] = 'success'; // Menetapkan status 'success' pada pesan
                $messages['message'] = 'Password berhasil di update'; // Menetapkan pesan sukses
                $messages['title'] = 'Berhasil'; // Menetapkan judul sukses
            } else {
                $messages['status'] = 'error'; // Menetapkan status 'error' pada pesan
                $messages['message'] = 'Terjadi kesalahan'; // Menetapkan pesan kesalahan
                $messages['title'] = 'Gagal'; // Menetapkan judul kesalahan
            }
        } catch (PDOException $e) { // Menangkap kesalahan PDOException jika terjadi
            $messages['title'] = 'error'; // Menetapkan judul kesalahan
            $messages['status'] = 'error'; // Menetapkan status 'error' pada pesan
            $messages['message'] = 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(); // Menetapkan pesan kesalahan dengan informasi tambahan
        }
    } else {
        $messages['status'] = 'info'; // Menetapkan status 'info' pada pesan
        $messages['message'] = 'Password lama tidak sesuai'; // Menetapkan pesan info
        $messages['title'] = 'Gagal'; // Menetapkan judul info
    }

    // Mengatur header respon yang tepat
    header('Content-Type: application/json');

    // Mengembalikan respon dalam format JSON
    echo json_encode($messages);
}

