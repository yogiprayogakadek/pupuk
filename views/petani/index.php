<?php
require_once('../../config/config.php');
$pageTitle = 'Petani';
$pageSub = 'Data';

$db = databaseConnection();
$query = "SELECT * FROM pengguna WHERE role = 0";
$stmt = $db->query($query);
$results = $stmt->fetchAll();

ob_start();
?>

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" class="form-control nama" name="nama" id="nama" placeholder="masukkan nama lengkap">
                            <div class="invalid-feedback error-nama"></div>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control username" name="username" id="username" placeholder="masukkan username">
                            <div class="invalid-feedback error-username"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary btn-save" name="button">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        Data Petani
                    </div>
                    <div class="col-6 d-flex align-items-center">
                        <div class="m-auto"></div>
                        <button type="button" class="btn btn-outline-primary btn-add btn-modal" data-cat="tambah">
                            <i class="nav-icon i-Pen-2 font-weight-bold"></i> Tambah
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-hover table-stripped" id="tableData">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <!-- <th>Jumlah (kg)</th>
                            <th>Harga (kg)</th> -->
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $key => $value) : ?>
                            <tr>
                                <td><?= ($key + 1); ?></td>
                                <td><?= $value['nama_lengkap']; ?></td>
                                <td><?= $value['username']; ?></td>
                                <td>
                                    <button class="btn btn-modal btn-default" data-id="<?= $value['id']; ?>" data-cat='edit'>
                                        <i class="fa fa-pencil text-success mr-2 pointer"></i>
                                    </button>
                                    <button class="btn btn-delete btn-danger" data-id="<?= $value['id']; ?>">
                                        <i class="fa fa-trash text-white mr-2 pointer"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once('../../templates/master.php');
?>

<script>
    $(document).ready(function() {
        $('body').on('click', '.btn-modal', function() {
            $('#modal').modal('show')

            let cat = $(this).data('cat')
            let id = $(this).data('id')

            var formCat = '<input type="hidden" class="form-control category" name="category" id="category">';
            $('.category').remove();
            $('.container-fluid').append(formCat)
            $('.category').val(cat);
            if (cat == 'edit') {
                $('.id-pengguna').remove();
                var idPengguna = '<input type="hidden" class="form-control id-pengguna" name="id" id="id">';
                $('.container-fluid').append(idPengguna)
                $('.id-pengguna').val(id);

                $('.group-password').remove();

                $.ajax({
                    type: "POST",
                    url: "process.php",
                    data: {
                        id: id,
                        category: 'getData'
                    },
                    dataType: "json",
                    success: function (response) {
                        $('.nama').val(response.nama_lengkap)
                        $('.username').val(response.username)
                    }
                });
            } else {
                var password = '<div class="form-group group-password">' +
                                    '<label for="password">Password</label>' +
                                    '<input type="password" class="form-control password" name="password" id="password" placeholder="masukkan password">' +
                                    '<div class="invalid-feedback error-password"></div>' +
                                '</div>';
                $('#modal .container-fluid').append(password)
                $("#form").trigger("reset");
                $('.id-pengguna').remove();
            }
        })

        $('body').on('click', '.btn-save', function(event) {
            // event.preventDefault();

            $('.invalid-feedback').empty();
            $('.form-control').removeClass('is-invalid');

            var isValid = true;

            $('#modal .form-control').each(function() {
                var $input = $(this);
                var inputName = $input.attr('name');
                var inputValue = $input.val().trim();

                if (inputValue === '') {
                    isValid = false;
                    $input.addClass('is-invalid');
                    $('.error-' + inputName).text('Field harus diisi.');
                } else if (inputName === 'jumlah' || inputName === 'harga') {
                    if (!$.isNumeric(inputValue)) {
                        isValid = false;
                        $input.addClass('is-invalid');
                        $('.error-' + inputName).text('Field harus berisi angka.');
                    }
                }
            });

            if (isValid) {
                $('.form-control').removeClass('is-invalid');
                let form = $("#form")[0];
                let data = new FormData(form);
                $.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(response) {
                        $("#form").trigger("reset");
                        $('#modal').modal('hide')

                        Swal.fire(response.title, response.message, response.status);
                        setTimeout(() => {
                            location.reload()
                        }, 1500)
                    },
                    error: function(error) {
                        // 
                    },
                });
            }
        });

        $('body').on('click', '.btn-delete', function() {
            Swal.fire({
                title: 'Anda yakin?',
                text: "Hapus pengguna ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: 'process.php',
                        type: 'POST',
                        data: {
                            id: $(this).data('id'),
                            category: 'delete'
                        },
                        success: function(response) {
                            Swal.fire(response.title, response.message, response.status);
                            setTimeout(() => {
                                location.reload()
                            }, 1500)
                        }
                    });
                }
            })
        });
    });
</script>