<?php
    session_start();

    // import config file
    require_once('../config/config.php');

    $db = databaseConnection(); // Membuat koneksi ke database

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username']; // Mengambil nilai 'username' dari input POST
        $password = $_POST['password']; // Mengambil nilai 'password' dari input POST

        $stmt = $db->prepare("SELECT * FROM pengguna WHERE username = :username"); // Menyiapkan query untuk memeriksa pengguna berdasarkan 'username'
        $stmt->bindParam(':username', $username); // Mengikat parameter ':username' dengan nilai 'username'
        $stmt->execute(); // Menjalankan query

        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Mengambil hasil query sebagai array asosiatif

        if($user && password_verify($password, $user['kata_sandi'])) { // Memeriksa apakah pengguna ada dan password yang diberikan cocok dengan password di database
            $_SESSION['username'] = $username; // Menyimpan 'username' dalam session
            $_SESSION['id_pengguna'] = $user['id']; // Menyimpan 'id_pengguna' dalam session
            $_SESSION['role'] = $user['role']; // Menyimpan 'role' dalam session

            $table = ($_SESSION['role'] == 1) ? 'admin' : 'petani'; // Menentukan tabel yang akan digunakan berdasarkan 'role' pengguna

            $query = $db->prepare("SELECT * FROM $table WHERE id_pengguna = :id_pengguna"); // Menyiapkan query untuk memilih data dari tabel yang sesuai dengan 'role' pengguna
            $query->bindParam(':id_pengguna', $user['id']); // Mengikat parameter ':id_pengguna' dengan nilai 'id_pengguna'
            $query->execute(); // Menjalankan query
            $_SESSION['nama_lengkap'] = $user['nama_lengkap']; // Menyimpan 'nama_lengkap' dalam session

            if($_SESSION['role'] == 0) {
                $petani = $query->fetch(PDO::FETCH_ASSOC);
                $_SESSION['luas_tanah'] = $petani['luas_tanah'];
            }

            header('Location: ' . $baseUrl); // Mengalihkan pengguna ke halaman beranda setelah berhasil login
            exit;
        } else {
            $_SESSION['error'] = 'Pengguna tidak ada, mohon periksa username dan kata sandi anda'; // Menyimpan pesan error dalam session
            header('Location:' . $baseUrl . '/auth/login.php'); // Mengalihkan pengguna ke halaman login dengan pesan error
            exit;
        }
    }

    $error = isset($_SESSION['error']) ? $_SESSION['error'] : ''; // Mengambil pesan error dari session (jika ada)
    unset($_SESSION['error']); // Menghapus pesan error dari session
