<?php
session_start();
require_once('../../config/config.php');

// database
$db = databaseConnection();

$category = $_POST['category'];
$id_pengguna = $_SESSION['id_pengguna'];

// Create an empty response array
$response = array();

if ($category == 'addCart') {
    try {
        $id_produk = $_POST['id_produk']; // Mengambil nilai 'id_produk' dari input POST
        $kuantitas = $_POST['kuantitas']; // Mengambil nilai 'kuantitas' dari input POST
        $is_done = false; // Menginisialisasi variabel 'is_done' sebagai false
        $tanggal_transaksi = date('Y-m-d'); // Mengambil tanggal saat ini dalam format 'YYYY-MM-DD'
        $total = 0; // Menginisialisasi variabel 'total' sebagai 0

        $check = $db->prepare("SELECT * FROM transaksi WHERE id_pengguna = :id_pengguna AND is_done = :is_done"); // Menyiapkan query untuk memeriksa transaksi yang belum selesai berdasarkan 'id_pengguna'
        $check->bindParam(':id_pengguna', $id_pengguna); // Mengikat parameter ':id_pengguna' dengan nilai 'id_pengguna'
        $check->bindParam(':is_done', $is_done); // Mengikat parameter ':is_done' dengan nilai 'is_done'
        $check->execute(); // Menjalankan query
        $dataCheck = $check->fetch(PDO::FETCH_ASSOC); // Mengambil hasil query sebagai array asosiatif

        if ($check->rowCount() == 0) { // Jika tidak ada transaksi yang belum selesai untuk pengguna ini
            $stmt = $db->prepare("INSERT INTO transaksi (id_pengguna, tanggal_transaksi, total, is_done) VALUES (:id_pengguna, :tanggal_transaksi, :total, :is_done) "); // Menyiapkan query untuk menyisipkan data baru ke dalam tabel 'transaksi'
            $stmt->bindParam(':tanggal_transaksi', $tanggal_transaksi); // Mengikat parameter ':tanggal_transaksi' dengan nilai 'tanggal_transaksi'
            $stmt->bindParam(':total', $total); // Mengikat parameter ':total' dengan nilai 'total'
            $stmt->bindParam(':id_pengguna', $id_pengguna); // Mengikat parameter ':id_pengguna' dengan nilai 'id_pengguna'
            $stmt->bindParam(':is_done', $is_done); // Mengikat parameter ':is_done' dengan nilai 'is_done'
            $stmt->execute(); // Menjalankan query

            $lastInsertedId = $db->lastInsertId(); // Mendapatkan ID terakhir yang disisipkan ke dalam tabel 'transaksi'
            if ($stmt->rowCount() > 0) { // Jika data berhasil disisipkan ke dalam tabel 'transaksi'
                $detail = $db->prepare("INSERT INTO detail_transaksi (id_transaksi, id_produk, kuantitas) VALUES (:id_transaksi, :id_produk, :kuantitas) "); // Menyiapkan query untuk menyisipkan data baru ke dalam tabel 'detail_transaksi'
                $detail->bindParam(':id_transaksi', $lastInsertedId); // Mengikat parameter ':id_transaksi' dengan nilai 'lastInsertedId'
                $detail->bindParam(':id_produk', $id_produk); // Mengikat parameter ':id_produk' dengan nilai 'id_produk'
                $detail->bindParam(':kuantitas', $kuantitas); // Mengikat parameter ':kuantitas' dengan nilai 'kuantitas'

                $detail->execute(); // Menjalankan query

                if ($detail->rowCount() > 0) { // Jika data berhasil disisipkan ke dalam tabel 'detail_transaksi'
                    $response['status'] = 'success'; // Menetapkan status 'success' pada respon
                    $response['message'] = 'Produk berhasil ditambahkan ke keranjang'; // Menetapkan pesan sukses
                    $response['title'] = 'Berhasil'; // Menetapkan judul sukses
                } else {
                    $response['status'] = 'error'; // Menetapkan status 'error' pada respon
                    $response['message'] = 'Tidak ada perubahan data'; // Menetapkan pesan kesalahan
                    $response['title'] = 'Gagal'; // Menetapkan judul kesalahan
                }
            }
        } else {
            $det = $db->prepare("SELECT * FROM detail_transaksi WHERE id_produk = :id_produk AND id_transaksi = :id_transaksi"); // Menyiapkan query untuk memeriksa apakah produk sudah ada dalam keranjang
            $det->bindParam(':id_transaksi', $dataCheck['id']); // Mengikat parameter ':id_transaksi' dengan nilai 'id' dari hasil pemeriksaan transaksi sebelumnya
            $det->bindParam(':id_produk', $id_produk); // Mengikat parameter ':id_produk' dengan nilai 'id_produk'

            $det->execute(); // Menjalankan query
            if ($det->rowCount() > 0) { // Jika ada hasil dari query (produk sudah ada dalam keranjang)
                $response['status'] = 'info'; // Menetapkan status 'info' pada respon
                $response['message'] = 'Produk sudah ada pada keranjang'; // Menetapkan pesan info
                $response['title'] = 'Info'; // Menetapkan judul info
            } else {
                $tot = 0;
                // check total quantity
                $newQuery = $db->prepare("SELECT SUM(b.kuantitas) as total
                                FROM transaksi a 
                                JOIN detail_transaksi b ON a.id = b.id_transaksi
                                WHERE a.id_pengguna = :id_pengguna AND a.is_done = 0");
                $newQuery->bindParam(':id_pengguna', $_SESSION['id_pengguna']);
                $newQuery->execute();
                $checkQtyTotal = $newQuery->fetch(PDO::FETCH_ASSOC);
                if(($checkQtyTotal['total'] + $kuantitas) > ($_SESSION['luas_tanah']/2)) {
                    $response['status'] = 'info'; // Menetapkan status 'error' pada respon
                    $response['message'] = 'Kapasitas belanja bulanan tidak mencukupi'; // Menetapkan pesan kesalahan
                    $response['title'] = 'Gagal'; // Menetapkan judul kesalahan
                } else {
                    $sql = $db->prepare("INSERT INTO detail_transaksi (id_transaksi, id_produk, kuantitas) VALUES (:id_transaksi, :id_produk, :kuantitas) "); // Menyiapkan query untuk menyisipkan data baru ke dalam tabel 'detail_transaksi'
                    $sql->bindParam(':id_transaksi', $dataCheck['id']); // Mengikat parameter ':id_transaksi' dengan nilai 'id' dari hasil pemeriksaan transaksi sebelumnya
                    $sql->bindParam(':id_produk', $id_produk); // Mengikat parameter ':id_produk' dengan nilai 'id_produk'
                    $sql->bindParam(':kuantitas', $kuantitas); // Mengikat parameter ':kuantitas' dengan nilai 'kuantitas'
                    $sql->execute(); // Menjalankan query
    
                    // Periksa apakah operasi berhasil
                    if ($sql->rowCount() > 0) { // Jika penyisipan data berhasil
                        $response['status'] = 'success'; // Menetapkan status 'success' pada respon
                        $response['message'] = 'Produk berhasil ditambahkan ke keranjang'; // Menetapkan pesan sukses
                        $response['title'] = 'Berhasil'; // Menetapkan judul sukses
                    } else {
                        $response['status'] = 'error'; // Menetapkan status 'error' pada respon
                        $response['message'] = 'Tidak ada perubahan data'; // Menetapkan pesan kesalahan
                        $response['title'] = 'Gagal'; // Menetapkan judul kesalahan
                    }
                }
            }
        }
    } catch (\PDOException $e) {
        $response['title'] = 'error';
        $response['status'] = 'error';
        $response['message'] = 'Error occurred while saving data: ' . $e->getMessage();
    }

    // Set the appropriate response headers
    header('Content-Type: application/json');

    // Return the JSON response
    echo json_encode($response);
} elseif ($category == 'delete') {
    $id = $_POST['id']; // Mengambil nilai 'id' dari input POST
    $id_transaksi = $_POST['id_transaksi']; // Mengambil nilai 'id_transaksi' dari input POST

    $check = $db->prepare("SELECT * FROM detail_transaksi WHERE id_transaksi = :id_transaksi"); // Menyiapkan query untuk memeriksa apakah ada detail_transaksi dengan 'id_transaksi' yang diberikan
    $check->bindParam(':id_transaksi', $id_transaksi); // Mengikat parameter ':id_transaksi' dengan nilai 'id_transaksi'
    $check->execute(); // Menjalankan query

    if ($check->rowCount() == 1) { // Jika hanya ada 1 baris hasil dari query (hanya ada 1 detail_transaksi dengan 'id_transaksi' yang diberikan)
        $transaksi = $db->prepare("DELETE FROM transaksi WHERE id = :id_transaksi"); // Menyiapkan query untuk menghapus transaksi berdasarkan 'id_transaksi'
        $transaksi->bindParam(':id_transaksi', $id_transaksi); // Mengikat parameter ':id_transaksi' dengan nilai 'id_transaksi'
        $transaksi->execute(); // Menjalankan query
    }

    $stmt = $db->prepare("DELETE FROM detail_transaksi WHERE id = :id "); // Menyiapkan query untuk menghapus detail_transaksi berdasarkan 'id'
    $stmt->bindParam(':id', $id); // Mengikat parameter ':id' dengan nilai 'id'

    $stmt->execute(); // Menjalankan query

    $response['status'] = 'success'; // Menetapkan status 'success' pada respon
    $response['message'] = 'Data berhasil dihapus'; // Menetapkan pesan sukses
    $response['title'] = 'Berhasil'; // Menetapkan judul sukses

    // Mengatur header respon yang tepat
    header('Content-Type: application/json');

    // Mengembalikan respon dalam format JSON
    echo json_encode($response);
} elseif ($category == 'checkout') {
    try {
        $transaksi = $db->prepare("SELECT * FROM transaksi WHERE id_pengguna = :id_pengguna AND is_done = 0"); // Menyiapkan query untuk memilih transaksi yang belum selesai berdasarkan 'id_pengguna'
        $transaksi->bindParam(':id_pengguna', $id_pengguna); // Mengikat parameter ':id_pengguna' dengan nilai 'id_pengguna'
        $transaksi->execute(); // Menjalankan query
        $data = $transaksi->fetch(PDO::FETCH_ASSOC); // Mengambil hasil query sebagai array asosiatif

        $detail_transaksi = $db->prepare("SELECT a.kuantitas, b.harga_produk, b.id as id_produk
                                        FROM detail_transaksi a
                                        JOIN produk b
                                        ON a.id_produk=b.id
                                        WHERE id_transaksi = :id_transaksi"); // Menyiapkan query untuk memperoleh detail transaksi, termasuk kuantitas dan harga produk, dengan melakukan join antara tabel 'detail_transaksi' dan 'produk'
        $detail_transaksi->bindParam(':id_transaksi', $data['id']); // Mengikat parameter ':id_transaksi' dengan nilai 'id' dari hasil query transaksi sebelumnya
        $detail_transaksi->execute(); // Menjalankan query
        $detailData = $detail_transaksi->fetchAll(PDO::FETCH_ASSOC); // Mengambil hasil query sebagai array asosiatif

        $total = $_POST['total']; // Mengambil nilai 'total' dari input POST
        foreach ($detailData as $row) { // Melakukan iterasi untuk setiap data detail transaksi
            $updateStok = $db->prepare("UPDATE produk SET jumlah_produk_kg = jumlah_produk_kg - :kuantitas WHERE id = :id_produk"); // Menyiapkan query untuk mengupdate stok produk
            $updateStok->bindParam(':kuantitas', $row['kuantitas']); // Mengikat parameter ':kuantitas' dengan nilai 'kuantitas' dari data detail transaksi
            $updateStok->bindParam(':id_produk', $row['id_produk']); // Mengikat parameter ':id_produk' dengan nilai 'id_produk' dari data detail transaksi
            $updateStok->execute(); // Menjalankan query untuk mengupdate stok produk
        }

        $updateTransaksi = $db->prepare("UPDATE transaksi SET total = :total, is_done = 1 WHERE id = :id_transaksi"); // Menyiapkan query untuk mengupdate transaksi dengan 'total' dan 'is_done'
        $updateTransaksi->bindParam(':total', $total); // Mengikat parameter ':total' dengan nilai 'total'
        $updateTransaksi->bindParam(':id_transaksi', $data['id']); // Mengikat parameter ':id_transaksi' dengan nilai 'id' dari hasil query transaksi sebelumnya
        $updateTransaksi->execute(); // Menjalankan query untuk mengupdate transaksi

        if ($updateTransaksi->rowCount() > 0) { // Jika ada perubahan data pada transaksi
            $response['status'] = 'success'; // Menetapkan status 'success' pada respon
            $response['message'] = 'Data berhasil di proses'; // Menetapkan pesan sukses
            $response['title'] = 'Berhasil'; // Menetapkan judul sukses
            $response['id_transaksi'] = $data['id'];
        } else {
            $response['status'] = 'error'; // Menetapkan status 'error' pada respon
            $response['message'] = 'Tidak ada perubahan data'; // Menetapkan pesan kesalahan
            $response['title'] = 'Gagal'; // Menetapkan judul kesalahan
        }

        header('Content-Type: application/json'); // Mengatur header respon yang tepat

        echo json_encode($response); // Mengembalikan respon dalam format JSON
    } catch (PDOException $e) {
        echo "Error occurred: " . $e->getMessage(); // Menampilkan pesan kesalahan jika terjadi exception
    }
} elseif ($category == 'searchProduct') {
    try {
        $keyword = $_POST['keyword']; // Mengambil nilai 'keyword' dari input POST

        if ($keyword == '') { // Jika 'keyword' kosong
            $stmt = $db->prepare("SELECT * FROM produk WHERE jumlah_produk_kg > 0 "); // Menyiapkan query untuk memilih semua produk yang memiliki jumlah stok lebih dari 0
        } else {
            $stmt = $db->prepare("SELECT * FROM produk WHERE nama_produk LIKE CONCAT('%', :keyword, '%') AND jumlah_produk_kg > 0"); // Menyiapkan query untuk memilih produk berdasarkan 'nama_produk' yang mengandung 'keyword' dan memiliki jumlah stok lebih dari 0
            $stmt->bindParam(':keyword', $keyword); // Mengikat parameter ':keyword' dengan nilai 'keyword'
        }

        $stmt->execute(); // Menjalankan query

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC); // Mengambil hasil query sebagai array asosiatif

        if ($data) { // Jika data produk ditemukan
            header('Content-Type: application/json'); // Mengatur header respon yang tepat
            echo json_encode([
                'data' => $data,
                'message' => 'success'
            ]); // Mengembalikan respon dalam format JSON dengan data produk dan pesan sukses
        } else {
            echo json_encode(array('message' => 'Data tidak ada')); // Mengembalikan respon dalam format JSON dengan pesan bahwa data tidak ditemukan
        }
    } catch (PDOException $e) {
        echo "Error occurred: " . $e->getMessage(); // Menampilkan pesan kesalahan jika terjadi exception
    }
} elseif ($category == 'updateCart') {
    try {
        $kuantitas = $_POST['kuantitas']; // Mengambil nilai 'kuantitas' dari input POST
        $id_detail_transaksi = $_POST['id_detail_transaksi']; // Mengambil nilai 'id_detail_transaksi' dari input POST
        // get total kuantitas
        $query = "SELECT SUM(b.kuantitas) as total
                    FROM transaksi a 
                    JOIN detail_transaksi b ON a.id = b.id_transaksi
                    WHERE a.id_pengguna = :id_pengguna AND a.is_done = 0";
        $q = $db->prepare($query);
        $q->bindParam(':id_pengguna', $_SESSION['id_pengguna']);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        if (($data['total'] - $_POST['kuantitasCart'] + $kuantitas) > ($_SESSION['luas_tanah'] / 2)) {
            $response['status'] = 'info'; // Menetapkan status 'error' pada respon
            $response['message'] = 'Kuantitas melebihi batas belanja bulanan sebesar' . ($_SESSION['luas_tanah'] / 2) . ' kg'; // Menetapkan pesan kesalahan
            $response['title'] = 'Gagal'; // Menetapkan judul kesalahan
        } else {
            $stmt = $db->prepare("UPDATE detail_transaksi SET kuantitas = :kuantitas WHERE id = :id"); // Menyiapkan query untuk mengupdate 'kuantitas' pada 'detail_transaksi' berdasarkan 'id_detail_transaksi'
            $stmt->bindParam(':id', $id_detail_transaksi); // Mengikat parameter ':id' dengan nilai 'id_detail_transaksi'
            $stmt->bindParam(':kuantitas', $kuantitas); // Mengikat parameter ':kuantitas' dengan nilai 'kuantitas'
            $stmt->execute(); // Menjalankan query

            if ($stmt->rowCount() > 0) { // Jika ada perubahan data pada 'detail_transaksi'
                $response['status'] = 'success'; // Menetapkan status 'success' pada respon
                $response['message'] = 'Data berhasil di proses'; // Menetapkan pesan sukses
                $response['title'] = 'Berhasil'; // Menetapkan judul sukses
            } else {
                $response['status'] = 'error'; // Menetapkan status 'error' pada respon
                $response['message'] = 'Tidak ada perubahan data'; // Menetapkan pesan kesalahan
                $response['title'] = 'Gagal'; // Menetapkan judul kesalahan
            }
        }


        header('Content-Type: application/json'); // Mengatur header respon yang tepat

        echo json_encode($response); // Mengembalikan respon dalam format JSON
        exit; // Menghentikan eksekusi kode selanjutnya
    } catch (PDOException $e) {
        echo "Error occurred: " . $e->getMessage(); // Menampilkan pesan kesalahan jika terjadi exception
        exit; // Menghentikan eksekusi kode selanjutnya
    }
} elseif ($category == 'getDataQty') {
    try {
        $query = "SELECT SUM(b.kuantitas) as total
                    FROM transaksi a 
                    JOIN detail_transaksi b ON a.id = b.id_transaksi
                    WHERE a.id_pengguna = :id_pengguna AND a.is_done = 0";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id_pengguna', $_SESSION['id_pengguna']);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC); // Mengambil hasil query sebagai array asosiatif

        $batas = 0;
        if ($data['total'] == null) {
            $q = "SELECT SUM(b.kuantitas) as total
                    FROM transaksi a 
                    JOIN detail_transaksi b ON a.id = b.id_transaksi
                    WHERE a.id_pengguna = :id_pengguna AND a.is_done = 1
                    AND MONTH(a.tanggal_transaksi) = MONTH(NOW())
                    AND YEAR(a.tanggal_transaksi) = YEAR(NOW())";
            $exec = $db->prepare($q);
            $exec->bindParam(':id_pengguna', $_SESSION['id_pengguna']);
            $exec->execute();

            $column = $exec->fetch(PDO::FETCH_ASSOC);
            $batas = (int)$column['total'];
        }

        header('Content-Type: application/json'); // Mengatur header respon yang tepat
        echo json_encode([
            'total' => (int)$data['total'],
            'batas' => $batas
        ]);
    } catch (PDOException $e) {
        echo "Error occurred: " . $e->getMessage(); // Menampilkan pesan kesalahan jika terjadi exception
    }
}
