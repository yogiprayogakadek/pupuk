<?php
require_once('../../config/config.php');
$pageTitle = 'Produk';
$pageSub = 'Data';

$db = databaseConnection(); // Membuat koneksi ke database
$query = "SELECT * FROM produk"; // Membuat query untuk mengambil semua data produk
$stmt = $db->query($query); // Menjalankan query
$results = $stmt->fetchAll(); // Mengambil hasil query sebagai array

ob_start(); // Memulai penampungan output
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
                            <label for="nama">Nama Produk</label>
                            <input type="text" class="form-control nama" name="nama" id="nama" placeholder="masukkan nama produk">
                            <div class="invalid-feedback error-nama"></div>
                        </div>
                        <div class="form-group">
                            <label for="url">Url Gambar</label>
                            <input type="text" class="form-control url" name="url" id="url" placeholder="masukkan url produk">
                            <div class="invalid-feedback error-url"></div>
                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah Produk (kg)</label>
                            <input type="text" class="form-control jumlah" name="jumlah" id="jumlah" placeholder="masukkan jumlah produk">
                            <div class="invalid-feedback error-jumlah"></div>
                        </div>
                        <div class="form-group">
                            <label for="harga">Harga Jual Produk (kg)</label>
                            <input type="text" class="form-control harga" name="harga" id="harga" placeholder="masukkan harga produk">
                            <div class="invalid-feedback error-harga"></div>
                        </div>
                        <div class="form-group">
                            <label for="harga-asli">Harga Asli Produk (kg)</label>
                            <input type="text" class="form-control harga_asli" name="harga_asli" id="harga-asli" placeholder="masukkan harga asli produk">
                            <div class="invalid-feedback error-harga_asli"></div>
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
                        Data Produk
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
                            <th>Gambar Produk</th>
                            <th>Nama</th>
                            <th>Jumlah (kg)</th>
                            <th>Harga Asli(kg)</th>
                            <th>Harga Jual(kg)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $key => $value) : ?>
                            <tr>
                                <td><?= ($key + 1); ?></td>
                                <td><img src="<?= $value['gambar_produk']; ?>" width="100px"></td>
                                <td><?= $value['nama_produk']; ?></td>
                                <td><?= $value['jumlah_produk_kg']; ?>kg</td>
                                <td>Rp<?= number_format($value['harga_produk_asli'], 0, ",", ".") ?></td>
                                <td>Rp<?= number_format($value['harga_produk'], 0, ",", ".") ?></td>
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
                $('.id-produk').remove();
                var idProduk = '<input type="hidden" class="form-control id-produk" name="id" id="id">';
                $('.container-fluid').append(idProduk);
                $('.id-produk').val(id);
                // Jika kategori adalah 'edit', tambahkan input hidden dengan class "id-produk" dan mengisi nilainya dengan id produk

                $.ajax({
                    type: "POST",
                    url: "process.php",
                    data: {
                        id: id,
                        category: 'getData'
                    },
                    dataType: "json",
                    success: function(response) {
                        // AJAX request berhasil, menerima data JSON dari "process.php"

                        $('.nama').val(response.nama_produk);
                        $('.url').val(response.gambar_produk);
                        $('.jumlah').val(response.jumlah_produk_kg);
                        $('.harga').val(response.harga_produk);
                        $('.harga_asli').val(response.harga_produk_asli);
                        // Mengisi nilai pada elemen-elemen input dengan data yang diterima
                    }
                });
            } else {
                $("#form").trigger("reset");
                $('.id-produk').remove();
                // Jika kategori bukan 'edit', reset form dan hapus input hidden dengan class "id-produk"
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
                } else if (inputName === 'jumlah' || inputName === 'harga' || inputName === 'harga_asli') {
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
                text: "Hapus produk ini!",
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