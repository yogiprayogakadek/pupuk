<?php
    session_start();

    require_once('../config/config.php');

    $db = databaseConnection();

    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $db->prepare("SELECT * FROM pengguna WHERE username = :username");

        $stmt->bindParam(':username', $username);

        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['kata_sandi'])) {
            $_SESSION['username'] = $username;
            $_SESSION['id_pengguna'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            $table = ($_SESSION['role'] == 1) ? 'admin' : 'petani';

            $query = $db->prepare("SELECT * FROM $table WHERE id_pengguna = :id_pengguna");
            // if($user['role'] == 1) {
            //     $query = $db->prepare("SELECT * FROM admin WHERE id_pengguna = :id_pengguna");
            // } else {
            //     $query = $db->prepare("SELECT * FROM petani WHERE id_pengguna = :id_pengguna");
            // }
            $query->bindParam(':id_pengguna', $user['id']);
            $query->execute();
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

            header('Location: ' . $baseUrl);
            // header('Location: ' . $baseUrl . '/' . 'index.php');
            exit;
        } else {
            $_SESSION['error'] = 'Pengguna tidak ada, mohon periksa username dan kata sandi anda';
            header('Location:' . $baseUrl . '/auth/login.php');
            exit;
        }
    }

    $error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
    unset($_SESSION['error']);