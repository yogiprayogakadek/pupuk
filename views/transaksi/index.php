<?php
require_once('../../config/config.php');
$pageTitle = 'Produk';
$pageSub = 'Data';

session_start();
$db = databaseConnection();
$role = $_SESSION['role']; // Mengambil nilai 'role' dari sesi saat ini

if ($role == 0) { // Jika 'role' adalah 0
    $query = "SELECT * FROM transaksi WHERE is_done = 1 AND id_pengguna = " . $_SESSION['id_pengguna']; // Membuat query untuk mengambil transaksi yang selesai untuk pengguna dengan ID tertentu
} else {
    $query = "SELECT c.nama_lengkap, a.id, a.total, a.tanggal_transaksi
                FROM transaksi a
                JOIN pengguna b
                ON a.id_pengguna=b.id
                JOIN petani c 
                ON c.id_pengguna = b.id
                WHERE a.is_done = 1"; // Membuat query untuk mengambil transaksi yang selesai bersama dengan informasi petani terkait
}

$stmt = $db->query($query); // Menjalankan query
$results = $stmt->fetchAll(); // Mengambil hasil query sebagai array

ob_start(); // Memulai penampungan output
?>

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-hover" id="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Harga Produk</th>
                            <th>Kuantitas</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right">Total</td>
                            <td id="total" class="font-weight-bold font-italic text-right"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Keluar</button>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        Data Transaksi
                    </div>
                    <div class="col-6 d-flex align-items-center">
                        <div class="m-auto"></div>
                        <a href="generate-pdf.php">
                            <button type="button" class="btn btn-outline-primary btn-print">
                                <i class="nav-icon i-Download1 font-weight-bold"></i> Print
                            </button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-hover table-stripped" id="tableData">
                    <thead>
                        <tr>
                            <th>No</th>
                            <?php if ($role == 1) { ?>
                                <th>Nama Pelanggan</th>
                            <?php } ?>
                            <th>Tanggal Transaksi</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $key => $value) : ?>
                            <tr>
                                <td><?= ($key + 1); ?></td>
                                <?php if ($role == 1) { ?>
                                    <td><?= $value['nama_lengkap']; ?></td>
                                <?php } ?>
                                <td><?= $value['tanggal_transaksi']; ?></td>
                                <td>Rp<?= number_format($value['total'], 0, ",", ".") ?></td>
                                <td>
                                    <button class="btn btn-modal btn-primary" data-id="<?= $value['id']; ?>" data-cat='detail'>
                                        <i class="fa fa-eye text-white pointer"></i>
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
            // Ketika tombol dengan class "btn-modal" di klik

            $('#modal').modal('show'); // Menampilkan modal dengan id "modal"

            let id = $(this).data('id'); // Mengambil data id dari tombol yang diklik

            $('#table tbody').empty(); // Mengosongkan isi tabel dengan id "table" pada bagian body

            var grandTotal = 0; // Variabel untuk menyimpan total harga

            $.ajax({
                type: "POST",
                url: "process.php",
                data: {
                    id: id,
                    category: 'getData'
                },
                dataType: "json",
                success: function(data) {
                    // AJAX request berhasil, menerima data JSON dari "process.php"

                    $.each(data, function(index, value) {
                        // Melakukan iterasi pada setiap data dalam array "data"

                        var tr_list = '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td>' + value.nama_produk + '</td>' +
                            '<td class="text-right">' + 'Rp' + value.harga_produk.toLocaleString('id-ID', {
                                minimumFractionDigits: 0
                            }) + '</td>' +
                            '<td>' + value.kuantitas + 'kg' + '</td>' +
                            '<td class="text-right">' + 'Rp' + (value.harga_produk * value.kuantitas).toLocaleString('id-ID', {
                                minimumFractionDigits: 0
                            }) + '</td>' +
                            '</tr>';
                        // Membuat baris tabel berdasarkan data yang diterima

                        $('#table tbody').append(tr_list); // Menambahkan baris tabel ke dalam tbody pada tabel dengan id "table"
                    });

                    $('#table tbody tr').each(function(key) {
                        // Melakukan iterasi pada setiap baris dalam tbody tabel dengan id "table"

                        var subtotal = (parseFloat($(this).find('td:nth-child(5)').text().replace(/[^0-9]+/g, '')));
                        // Mengambil subtotal dari kolom kelima pada baris saat ini

                        grandTotal += subtotal; // Menambahkan subtotal ke grandTotal
                    });

                    $('#total').text('Rp' + grandTotal.toLocaleString('id-ID', {
                        minimumFractionDigits: 0
                    }));
                    // Menampilkan grandTotal pada elemen dengan id "total"
                }
            });
        });
    });
</script>