<?php
require_once('../../config/config.php');
session_start();
$pageTitle = 'Pembelian';
$pageSub = 'Data';

$db = databaseConnection();

// data produk
$query = "SELECT * FROM produk";
$stmt = $db->query($query);
$results = $stmt->fetchAll();

// data keranjang
$query2 = "SELECT a.id, a.kuantitas, c.nama_produk, c.harga_produk, c.gambar_produk, b.id as id_transaksi
            FROM detail_transaksi a
            JOIN transaksi b ON a.id_transaksi=b.id
            JOIN produk c ON c.id=a.id_produk
            WHERE b.id_pengguna = " . $_SESSION['id_pengguna'] . " AND b.is_done = 0";
$prep = $db->query($query2);
$data = $prep->fetchAll();

ob_start();
?>

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">Kuantitas Barang</h5>
            </div>
            <form id="form">
                <div class="modal-body">
                    <div class="container-fluid">
                        <input type="hidden" class="form-control id-produk" name="id_produk">
                        <div class="form-group">
                            <label for="kuantitas">Kuantitas</label>
                            <input type="text" class="form-control kuantitas" name="kuantitas" id="kuantitas" placeholder="masukkan kuantitas" max="10">
                            <div class="invalid-feedback error-kuantitas"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary btn-save" disabled>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-5">
        <div class="card mb-4">
            <div class="card-body">
                <table class="table table-hover table-stripped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar Produk</th>
                            <th>Nama</th>
                            <th>Stok</th>
                            <th>Harga (kg)</th>
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
                                <td>Rp<?= number_format($value['harga_produk'], 0, ",", ".") ?></td>
                                <td>
                                    <button class="btn btn-cart btn-primary" data-id="<?= $value['id']; ?>" data-max="<?= $value['jumlah_produk_kg']; ?>" <?= $value['jumlah_produk_kg'] == 0 ? 'disabled' : ''; ?>>
                                        <i class="fa fa-cart-plus text-white pointer"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-7">
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col-12">
                        Keranjang Belanja
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-hover table-stripped" id="keranjang">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar Produk</th>
                            <th>Nama</th>
                            <th>Jumlah</th>
                            <th>Harga (kg)</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (count($data) > 0) {
                            foreach ($data as $index => $v) :
                        ?>
                                <tr>
                                    <td><?= ($index + 1); ?></td>
                                    <td><img src="<?= $v['gambar_produk']; ?>" width="100px"></td>
                                    <td><?= $v['nama_produk']; ?></td>
                                    <td><?= $v['kuantitas']; ?>kg</td>
                                    <td>Rp<?= number_format($v['harga_produk'], 0, ",", ".") ?>/kg</td>
                                    <td>Rp<?= number_format(($v['harga_produk'] * $v['kuantitas']), 0, ",", ".") ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-delete btn-danger" data-transaksi="<?= $v['id_transaksi']; ?>" data-id="<?= $v['id']; ?>">
                                            <i class="fa fa-trash text-white pointer"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php
                            endforeach;
                        } else {
                            ?>
                            <tr>
                                <td colspan="6" class="text-center">
                                    <h1>Tidak ada data</h1>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        <?php foreach ($data as $index => $v) : ?>

                        <?php endforeach; ?>
                    </tbody>
                    <?php if (count($data) > 0) { ?>
                    <tfoot>
                        <tr class="font-weight-bold font-italic">
                            <td colspan="3" class="text-center">Total</td>
                            <td id="sum-jumlah"></td>
                            <td id="sum-harga"></td>
                            <td id="sum-total"></td>
                            <td class="text-center">
                                <button class="btn btn-primary btn-proses"><i class="fa fa-arrow-right"></i> Proses</button>
                            </td>
                        </tr>
                    </tfoot>
                    <?php } ?>
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
        $('body').on('click', '.btn-cart', function() {
            $('#modal').modal('show')

            $('.id-produk').val($(this).data('id'))

            // set max weigth each item
            localStorage.setItem('maxWeight', parseInt($(this).data('max')));

            $('#form').trigger('reset')
            $('.invalid-feedback').html('')
            $('.kuantitas').removeClass('is-invalid');
        })

        $('body').on('keyup', '.kuantitas', function() {
            var value = parseInt($(this).val());
            var maxWeight = localStorage.getItem('maxWeight');

            if (value > maxWeight) {
                $(this).addClass('is-invalid');
                $('.error-kuantitas').html('Kuantitas melebihi stok yang tersedia');

                $('.btn-save').prop('disabled', true)
            } else {
                $(this).removeClass('is-invalid');
                $('.error-kuantitas').html('');

                $('.btn-save').prop('disabled', false)
            }

            if (isNaN(value)) {
                $(this).addClass('is-invalid');
                $('.error-kuantitas').html('Field tidak boleh kosong dan hanya angka yang diperbolehkan');

                $('.btn-save').prop('disabled', true)
            }
        });

        $('body').on('click', '.btn-save', function(event) {
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
                data.append('category', 'addCart')
                $.ajax({
                    type: "POST",
                    url: 'process.php',
                    data: data,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(response) {
                        Swal.fire(response.title, response.message, response.status);
                        $('#modal').modal('hide')
                        $("#form").trigger("reset");

                        if (response.status == 'success') {
                            localStorage.clear();
                            setTimeout(() => {
                                location.reload()
                            }, 1500)
                        }
                    },
                    error: function(error) {
                        // 
                    },
                });
            }
        })

        $('body').on('click', '.btn-delete', function() {
            Swal.fire({
                title: 'Anda yakin?',
                text: "Hapus produk ini dari keranjang!",
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
                            id_transaksi: $(this).data('transaksi'),
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

        $('body').on('click', '.btn-proses', function() {
            Swal.fire({
                title: 'Anda yakin?',
                text: "Proses belanjaan ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Proses!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: 'process.php',
                        type: 'POST',
                        data: {
                            category: 'checkout'
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

        // Calculate sum of "Jumlah" and "Total" columns
        var sumJumlah = 0;
        var sumTotal = 0;
        var sumHarga = 0;

        // Loop through each row in the table body
        $('#keranjang tbody tr').each(function(index) {
            var jumlah = $(this).find('td:nth-child(4)').text();
            var harga = $(this).find('td:nth-child(5)').text();
            var total = $(this).find('td:nth-child(6)').text();

            // Remove any non-numeric characters and convert to a number
            var jumlahValue = parseFloat(jumlah.replace(/[^0-9.-]+/g, ''));
            var totalValue = parseFloat(total.replace(/[^0-9]+/g, ''));
            var hargaValue = parseFloat(harga.replace(/[^0-9]+/g, ''));

            // Add to the sums
            sumJumlah += jumlahValue;
            sumTotal += totalValue;
            sumHarga += hargaValue;
        });

        // Update the total values in the table footer
        $('#sum-jumlah').text(sumJumlah + 'kg');
        $('#sum-total').text('Rp' + sumTotal.toLocaleString('id-ID', { minimumFractionDigits: 0 }));
        $('#sum-harga').text('Rp' + sumHarga.toLocaleString('id-ID', { minimumFractionDigits: 0 }));
    });
</script>