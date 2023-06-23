<?php include('./templates/partials/head.php'); ?>
<?php include('./templates/partials/body.php'); ?>

<div class="main-content-wrap sidenav-open d-flex flex-column">
    <div class="main-content">
        <div class="breadcrumb">
            <h1>@yield('page-title')</h1>
            <ul>
                <li><a href="#">@yield('page-sub-title')</a></li>
                <li>@yield('page-title')</li>
            </ul>
        </div>

        <div class="separator-breadcrumb border-top"></div>
        <?= $baseUrl; ?>
        @yield('content')
    </div>

<?php include('./templates/partials/footer.php'); ?>