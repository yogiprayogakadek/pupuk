<?php
error_reporting(0);
session_start();
if (!isset($_SESSION['username'])) {
    // Redirect the user to the login page
    header('Location: ' . $baseUrl . '/' . 'auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->


<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="aGL9mgKAjitF0e3BQVfxLGjyunp6mtfBGqik1DZZ" />
    <title>Subak Desa Bongan</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet" />

    <link id="gull-theme" rel="stylesheet" href="<?= $baseUrl; ?>/templates/assets/styles/css/themes/lite-purple.min.css" />
    <link rel="stylesheet" href="<?= $baseUrl; ?>/templates/assets/styles/vendor/perfect-scrollbar.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <link rel="stylesheet" href="http://gull-html-laravel.ui-lib.com/assets/styles/vendor/datatables.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="shortcut icon" href="<?= $baseUrl; ?>/templates/assets/images/logo.png" type="image/x-icon">
</head>

<body class="text-left">
    <!-- Pre Loader Strat  -->
    <div class="loadscreen" id="preloader">
        <div class="loader spinner-bubble spinner-bubble-primary"></div>
    </div>
    <div class="app-admin-wrap layout-sidebar-large clearfix">
        <div class="main-header">
            <div class="logo">
                <a href="/">
                    <img src="<?= $baseUrl; ?>/templates/assets/images/logo.png" alt="" />
                </a>
            </div>

            <div class="menu-toggle">
                <div></div>
                <div></div>
                <div></div>
            </div>
            <div style="margin: auto"></div>

            <div class="header-part-right">
                <!-- User avatar dropdown -->
                <div class="dropdown">
                    <div class="user col align-self-end">
                        Welcome, <strong><?= $_SESSION['username']; ?></strong>!
                        <img src="<?= $baseUrl; ?>/templates/assets/uploads/users/default.png" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" />

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            <div class="dropdown-header">
                                <i class="i-Lock-User mr-1"></i> <?= $_SESSION['username']; ?>
                            </div>
                            <a class="dropdown-item" onclick="passwordModal();" href="javascript:void(0);">Change Password</a>
                            <a class="dropdown-item" onclick="event.preventDefault(); logout();" href="javascript:void(0);">Sign out</a>
                            <form id="logout-form" action="<?= $baseUrl; ?>/auth/logout.php" method="POST" class="d-none">
                                <!-- Include any additional input fields if required -->
                                <input type="hidden" name="logout" value="1">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="side-content-wrap">
            <div class="sidebar-left open rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
                <div class="navigation-left">
                    <li class="nav-item">
                        <a class="nav-item-hold" href="<?= $baseUrl; ?>">
                            <i class="nav-icon i-Bar-Chart"></i>
                            <span class="nav-text">Dashoard</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php if($_SESSION['role'] == 1) { ?>
                    <li class="nav-item <?php checkUrl('petani');?>">
                        <a class="nav-item-hold" href="<?= $baseUrl; ?>/views/petani">
                            <i class="nav-icon i-Administrator"></i>
                            <span class="nav-text">Petani</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <li class="nav-item <?php checkUrl('produk');?>">
                        <a class="nav-item-hold" href="<?= $baseUrl; ?>/views/produk">
                            <i class="nav-icon i-Bag"></i>
                            <span class="nav-text">Produk</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php } ?>

                    <?php if($_SESSION['role'] == 0) { ?>
                    <li class="nav-item <?php checkUrl('pembelian');?>">
                        <a class="nav-item-hold" href="<?= $baseUrl; ?>/views/pembelian">
                            <i class="nav-icon i-Add-Cart"></i>
                            <span class="nav-text">Pembelian</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                    <?php } ?>
                    <li class="nav-item <?php checkUrl('transaksi');?>">
                        <a class="nav-item-hold" href="<?= $baseUrl; ?>/views/transaksi">
                            <i class="nav-icon i-Money-Bag"></i>
                            <span class="nav-text">Transaksi</span>
                        </a>
                        <div class="triangle"></div>
                    </li>
                </div>
            </div>

            <div class="sidebar-overlay"></div>
        </div>

        <div class="main-content-wrap sidenav-open d-flex flex-column">
            <div class="main-content">
                <div class="breadcrumb">
                    <h1><?= $pageTitle; ?></h1>
                    <ul>
                        <li><a href="#"><?= $pageSub; ?></a></li>
                        <li><?= $pageTitle; ?></li>
                    </ul>
                </div>

                <div class="separator-breadcrumb border-top"></div>
                <!-- Modal -->
                <div class="modal fade" id="modalPassword" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Ubah password</h5>
                            </div>
                            <div class="modal-body">
                                <form id="form_password">
                                    <div class="form-group">
                                        <input type="hidden" value="<?= $_SESSION['id_pengguna']; ?>" name="id_pengguna">
                                        <label for="current_password">Password Lama</label>
                                        <input type="password" class="form-control current-password" id="current_password" name="current_password">
                                    </div>
                                    <div class="form-group">
                                        <label for="new_password">Password Baru</label>
                                        <input type="password" class="form-control new-password" id="new_password" name="new_password">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="button" class="btn btn-primary btn-password">Update</button>
                            </div>
                        </div>
                    </div>
                </div>

                <?= $content; ?>
            </div>


            <!-- Footer Start -->
            <div class="flex-grow-1"></div>
            <!-- fotter end -->
        </div>
        <!-- ============ Body content End ============= -->
    </div>
    <!--=============== End app-admin-wrap ================-->

    <!-- ============ Large Sidebar Layout End ============= -->

    <script src="<?= $baseUrl; ?>/templates/assets/js/common-bundle-script.js"></script>
    <script src="<?= $baseUrl; ?>/templates/assets/js/script.js"></script>
    <script src="<?= $baseUrl; ?>/templates/assets/js/sidebar.large.script.js"></script>
    <script src="<?= $baseUrl; ?>/templates/assets/js/customizer.script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.2.1/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script src="http://gull-html-laravel.ui-lib.com/assets/js/vendor/datatables.min.js"></script>
    <script src="http://gull-html-laravel.ui-lib.com/assets/js/datatables.script.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <script>
        var table = $('#tableData').DataTable({
            language: {
                paginate: {
                    previous: "Previous",
                    next: "Next"
                },
                info: "Showing _START_ to _END_ from _TOTAL_ data",
                infoEmpty: "Showing 0 to 0 from 0 data",
                lengthMenu: "Showing _MENU_ data",
                search: "Search:",
                emptyTable: "Data doesn't exists",
                zeroRecords: "Data doesn't match",
                loadingRecords: "Loading..",
                processing: "Processing...",
                infoFiltered: "(filtered from _MAX_ total data)"
            },
            lengthMenu: [
                [5, 10, 15, 20, -1],
                [5, 10, 15, 20, "All"]
            ],
            order: [
                [0, 'desc']
            ],
            "rowCallback": function(row, data, index) {
                // Set the row number as the first cell in each row
                $('td:eq(0)', row).html(index + 1);
            }
        });

        // Update row numbers when the table is sorted
        table.on('order.dt search.dt', function() {
            table.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        function passwordModal() {
            $('#modalPassword').modal({
                backdrop:'static',
                keyboard: false
            })
        }

        $('body').on('click', '.btn-password', function() {
            let form = $("#form_password")[0];
            let data = new FormData(form);
            data.append('category', 'updatePassword');
            $.ajax({
                type: "POST",
                url: '<?= $baseUrl; ?>/config/config.php',
                data: data,
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    $("#formPassword").trigger("reset");
                    $('#modalPassword').modal('hide')

                    Swal.fire(response.title, response.message, response.status);
                    if(response.status == 'success') {
                        setTimeout(() => {
                            location.reload()
                        }, 1500)
                    }
                },
                error: function(error) {
                    // 
                },
            });
        })

        function logout() {
            document.getElementById('logout-form').submit();
        }
    </script>

</body>

</html>