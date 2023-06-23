<?php
require_once('./config/config.php');
$pageTitle = 'Dashboard';
$pageSub = 'Data';
session_start();
ob_start();
?>
<div class="row d-flex align-items-center justify-content-center">
    <div class="col-5">
        <div class="card mb-4">
            <div class="card-header text-center bg-primary">
                <h3 class="text-white">Selamat Datang</h3>
            </div>
            <div class="card-body text-center">
                <img src="<?= $baseUrl; ?>/templates/assets/uploads/users/default.png" class="img-rounded">
                <h3><?= $_SESSION['username']; ?></h3>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once('./templates/master.php');
?>