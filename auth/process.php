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
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['id_pengguna'] = $user['id'];
            $_SESSION['role'] = $user['role'];

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