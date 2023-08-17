<?php
session_start();
require_once('../../config/config.php');

$db = databaseConnection();

$category = $_POST['category']; // Mengambil nilai 'category' dari input POST

$response = array(); // Membuat array kosong untuk menampung respon

if ($category == 'tambah' || $category == 'edit') { // Jika 'category' adalah 'tambah' atau 'edit'
    try {
        $nama = $_POST['nama']; // Mengambil nilai 'nama' dari input POST
        $username = $_POST['username']; // Mengambil nilai 'username' dari input POST
        $alamat = $_POST['alamat']; // Mengambil nilai 'alamat' dari input POST
        $luas = $_POST['luas']; // Mengambil nilai 'luas' dari input POST

        if ($category == 'tambah') { // Jika 'category' adalah 'tambah'
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Mengenkripsi kata sandi menggunakan password_hash()
            $role = false;
            $stmt = $db->prepare("INSERT INTO pengguna (username, kata_sandi, role) VALUES (:username, :kata_sandi, :role) ");

            $stmt->bindParam(':role', $role); // Mengikat parameter ':role' dengan nilai 'role'
            $stmt->bindParam(':kata_sandi', $password); // Mengikat parameter ':kata_sandi' dengan nilai 'password'
            $stmt->bindParam(':username', $username); // Mengikat parameter ':username' dengan nilai 'username'
            $stmt->execute(); // Menjalankan query untuk menyimpan data pengguna

            $lastInsertedId = $db->lastInsertId(); // Mendapatkan ID terakhir yang dimasukkan ke database
            if ($stmt->rowCount() > 0) {
                $petani = $db->prepare("INSERT INTO petani (nama_lengkap, alamat, luas_tanah, id_pengguna) VALUES (:nama, :alamat, :luas_tanah, :id_pengguna) ");
                $petani->bindParam(':nama', $nama); // Mengikat parameter ':nama' dengan nilai 'nama'
                $petani->bindParam(':alamat', $alamat); // Mengikat parameter ':alamat' dengan nilai 'alamat'
                $petani->bindParam(':luas_tanah', $luas); // Mengikat parameter ':alamat' dengan nilai 'alamat'
                $petani->bindParam(':id_pengguna', $lastInsertedId); // Mengikat parameter ':id_pengguna' dengan nilai 'lastInsertedId'
                $petani->execute(); // Menjalankan query untuk menyimpan data petani
            }
        } elseif ($category == 'edit') { // Jika 'category' adalah 'edit'
            $id = $_POST['id']; // Mengambil nilai 'id' dari input POST

            $stmt = $db->prepare("UPDATE pengguna SET username = :username WHERE id = :id"); // Menyiapkan query untuk mengupdate 'username' pada 'pengguna' berdasarkan 'id'

            $stmt->bindParam(':id', $id); // Mengikat parameter ':id' dengan nilai 'id'
            $stmt->bindParam(':username', $username); // Mengikat parameter ':username' dengan nilai 'username'
            $stmt->execute(); // Menjalankan query untuk mengupdate 'username'

            $petani = $db->prepare("UPDATE petani SET nama_lengkap = :nama, alamat = :alamat, luas_tanah = :luas_tanah WHERE id_pengguna = :id_pengguna");
            $petani->bindParam(':nama', $nama); // Mengikat parameter ':nama' dengan nilai 'nama'
            $petani->bindParam(':alamat', $alamat); // Mengikat parameter ':alamat' dengan nilai 'alamat'
            $petani->bindParam(':luas_tanah', $luas); // Mengikat parameter ':alamat' dengan nilai 'alamat'
            $petani->bindParam(':id_pengguna', $id); // Mengikat parameter ':id_pengguna' dengan nilai 'id'
            $petani->execute(); // Menjalankan query untuk mengupdate data petani
        }

        $response['status'] = 'success'; // Menetapkan status 'success' pada respon
        $response['message'] = 'Data berhasil disimpan'; // Menetapkan pesan sukses
        $response['title'] = 'Berhasil'; // Menetapkan judul sukses
    } catch (\PDOException $e) {
        $response['title'] = 'error'; // Menetapkan judul kesalahan pada respon
        $response['status'] = 'error'; // Menetapkan status 'error' pada respon
        $response['message'] = 'Error occurred while saving data: ' . $e->getMessage(); // Menetapkan pesan kesalahan yang terjadi
    }

    header('Content-Type: application/json'); // Mengatur header respon yang tepat

    echo json_encode($response); // Mengembalikan respon dalam format JSON
} elseif ($category == 'getData') { // Jika 'category' adalah 'getData'
    $id = $_POST['id']; // Mengambil nilai 'id' dari input POST
    $stmt = $db->prepare("SELECT b.nama_lengkap, b.alamat, b.luas_tanah, a.username
                            FROM pengguna a
                            JOIN petani b
                            ON a.id = b.id_pengguna
                            WHERE a.id = :id ");
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
    $id_pengguna = $_POST['id']; // Mengambil nilai 'id' dari input POST

    $query = $db->prepare("DELETE FROM pengguna WHERE id = :id"); // Menyiapkan query untuk menghapus data pengguna berdasarkan 'id'
    $query->bindParam(':id', $id_pengguna); // Mengikat parameter ':id' dengan nilai 'id_pengguna'
    $query->execute(); // Menjalankan query untuk menghapus data pengguna

    if ($query->rowCount() > 0) { // Jika ada perubahan data
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
