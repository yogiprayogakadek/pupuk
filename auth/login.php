<?php 
require_once('../config/config.php'); 
unset($_SESSION['error']);
session_start();
// unset($_SESSION['error']);
    if (isset($_SESSION['username'])) {
        // Redirect the user to the login page
        header('Location: ' . $baseUrl . '/index.php');
        exit;
    }
?>


<!doctype html>
<html lang="en">

<head>

        <meta charset="utf-8" />
        <title>Subak Desa Bongan | Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
        <meta content="Themesbrand" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?= $baseUrl; ?>/templates/assets/images/logo.png" type="image/png" />

        <!-- Bootstrap Css -->
        <link href="<?= $baseUrl; ?>/templates/assets/styles/css/login/auth/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="<?= $baseUrl; ?>/templates/assets/styles/css/login/auth/icons.min.css" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="<?= $baseUrl; ?>/templates/assets/styles/css/login/auth/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.2.1/dist/sweetalert2.all.min.js"></script>
        <style>
            body {
                background-image: url('https://awsimages.detik.net.id/community/media/visual/2022/05/01/jatiluwih_169.jpeg?w=620');
                background-repeat: no-repeat;
                background-size: cover;
                background-position: center;
            }
        </style>

    </head>

    <body>
        <?php if(isset($_SESSION['error'])) { unset($_SESSION['error']);?>
            <script>
                Swal.fire('Gagal','Pengguna tidak ada, mohon periksa username dan kata sandi anda','error')
            </script>
        <?php } ?>
        <div class="account-pages my-5 pt-sm-5">
            <div class="container mt-5">
                <div class="row justify-content-center mt-5">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card overflow-hidden">
                            <div class="bg-primary bg-soft">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="text-primary p-4">
                                            <h5 class="text-primary">Subak Desa Bongan | Login</h5>
                                            <p>Log in dengan identitas anda</p>
                                        </div>
                                    </div>
                                    <div class="col-5 align-self-end">
                                        <img src="<?= $baseUrl; ?>/templates/assets/images/logo.png" alt="" class="img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0">
                                <div class="auth-logo">
                                    <a href="login.php" class="auth-logo-light">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="<?= $baseUrl; ?>/templates/assets/images/logo.png" alt="" class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>

                                    <a href="login.php" class="auth-logo-dark">
                                        <div class="avatar-md profile-user-wid mb-4">
                                            <span class="avatar-title rounded-circle bg-light">
                                                <img src="<?= $baseUrl; ?>/templates/assets/images/logo.png" alt="" class="rounded-circle" height="34">
                                            </span>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-2">
                                    <form role="form" action="process.php" method="POST">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" placeholder="masukkan username" name="username" id="username">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Password</label>
                                            <div class="input-group auth-pass-inputgroup">
                                                <input type="password" class="form-control" placeholder="Enter password" name="password" aria-label="Password" aria-describedby="password-addon">
                                                <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                            </div>
                                        </div>

                                        <div class="mt-3 d-grid">
                                            <button class="btn btn-primary waves-effect waves-light" type="submit">Log In</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- end account-pages -->

        <!-- JAVASCRIPT -->
        <script src="<?= $baseUrl; ?>/templates/assets/styles/css/login/auth/jquery.min.js"></script>
        <script src="<?= $baseUrl; ?>/templates/assets/styles/css/login/auth/bootstrap.bundle.min.js"></script>
        <script src="<?= $baseUrl; ?>/templates/assets/styles/css/login/auth/metisMenu.min.js"></script>
        <script src="<?= $baseUrl; ?>/templates/assets/styles/css/login/auth/simplebar.min.js"></script>
        <script src="<?= $baseUrl; ?>/templates/assets/styles/css/login/auth/waves.min.js"></script>

        <!-- App js -->
        <script src="<?= $baseUrl; ?>/templates/assets/styles/css/login/auth/app.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.2.1/dist/sweetalert2.all.min.js"></script>
    </body>
</html>
