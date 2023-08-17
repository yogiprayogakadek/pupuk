<?php
require_once('../../config/config.php');
$pageTitle = 'Petani';
$pageSub = 'Data';

$db = databaseConnection(); // Membuat koneksi ke database
$query = "SELECT a.username, b.nama_lengkap, b.alamat, b.luas_tanah, a.id as id_pengguna
            FROM pengguna a
            JOIN petani b
            ON a.id = b.id_pengguna
            WHERE a.role = 0"; // Membuat query untuk mengambil data petani dengan peran 0 (non-admin)
$stmt = $db->query($query); // Menjalankan query
$results = $stmt->fetchAll(); // Mengambil hasil query sebagai array

ob_start(); // Memulai penampungan output
?>


<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Data Petani</h5>
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
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <input type="text" class="form-control alamat" name="alamat" id="alamat" placeholder="masukkan alamat">
                            <div class="invalid-feedback error-alamat"></div>
                        </div>
                        <div class="form-group">
                            <label for="luas">Luas Tanah (dalam are)</label>
                            <input type="text" class="form-control luas" name="luas" id="luas" placeholder="masukkan luas tanah">
                            <div class="invalid-feedback error-luas"></div>
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
                            <th>Alamat</th>
                            <th>Luas Tanah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $key => $value) : ?>
                            <tr>
                                <td><?= ($key + 1); ?></td>
                                <td><?= $value['nama_lengkap']; ?></td>
                                <td><?= $value['username']; ?></td>
                                <td><?= $value['alamat']; ?></td>
                                <td><?= $value['luas_tanah']; ?> are</td>
                                <td>
                                    <button class="btn btn-modal btn-default" data-id="<?= $value['id_pengguna']; ?>" data-cat='edit'>
                                        <i class="fa fa-pencil text-success mr-2 pointer"></i>
                                    </button>
                                    <button class="btn btn-delete btn-danger" data-id="<?= $value['id_pengguna']; ?>">
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
    // Kode di dalam blok ini akan dieksekusi ketika halaman selesai dimuat

    $('body').on('click', '.btn-modal', function() {
        // Ketika tombol dengan class "btn-modal" di klik

        $('#modal').modal('show');
        // Menampilkan modal dengan id "modal"

        let cat = $(this).data('cat');
        let id = $(this).data('id');
        // Mengambil data kategori dan id dari tombol yang diklik

        var formCat = '<input type="hidden" class="form-control category" name="category" id="category">';
        $('.category').remove();
        $('.container-fluid').append(formCat);
        $('.category').val(cat);
        // Menambahkan input hidden dengan class "category" ke dalam container dan mengisi nilainya dengan kategori

        if (cat == 'edit') {
            $('.id-pengguna').remove();
            var idPengguna = '<input type="hidden" class="form-control id-pengguna" name="id" id="id">';
            $('.container-fluid').append(idPengguna);
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
                    $('.nama').val(response.nama_lengkap);
                    $('.username').val(response.username);
                    $('.alamat').val(response.alamat);
                    $('.luas').val(response.luas_tanah);
                }
            });
        } else {
            var password = '<div class="form-group group-password">' +
                                '<label for="password">Password</label>' +
                                '<input type="password" class="form-control password" name="password" id="password" placeholder="masukkan password">' +
                                '<div class="invalid-feedback error-password"></div>' +
                            '</div>';
            $('#modal .container-fluid').append(password);
            $("#form").trigger("reset");
            $('.id-pengguna').remove();
            // Jika kategori bukan 'edit', tambahkan input hidden dengan class "id-pengguna", tambahkan input password, reset form, dan hapus input hidden dengan class "id-pengguna"
        }
    });

    $('body').on('click', '.btn-save', function(event) {
        // Ketika tombol dengan class "btn-save" di klik

        $('.invalid-feedback').empty();
        $('.form-control').removeClass('is-invalid');

        var isValid = true;

        $('#modal .form-control').each(function() {
            // Melakukan iterasi pada setiap elemen dengan class "form-control" di dalam modal

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
            // Memvalidasi setiap elemen input, memeriksa jika kosong atau tidak berisi angka
        });

        if (isValid) {
            $('.form-control').removeClass('is-invalid');
            // Jika validasi berhasil, hapus kelas "is-invalid" dari elemen input

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
                    $('#modal').modal('hide');
                    // Reset form dan sembunyikan modal

                    Swal.fire(response.title, response.message, response.status);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                    // Tampilkan pesan sukses menggunakan Swal (SweetAlert) dan refresh halaman setelah 1,5 detik
                },
                error: function(error) {
                    // Tangani kesalahan jika terjadi
                },
            });
        }
    });

    $('body').on('click', '.btn-delete', function() {
        // Ketika tombol dengan class "btn-delete" di klik

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
                            location.reload();
                        }, 1500);
                        // Tampilkan pesan konfirmasi menggunakan Swal (SweetAlert) dan refresh halaman setelah 1,5 detik
                    }
                });
            }
        })
    });
});

</script>