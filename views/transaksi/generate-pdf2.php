<?php
session_start();
require_once '../../vendor/dompdf/vendor/autoload.php';
require_once('../../config/config.php');

use Dompdf\Dompdf;

$db = databaseConnection();

$id_pengguna = $_SESSION['id_pengguna']; // Mengambil nilai 'id_pengguna' dari sesi saat ini
$role = $_SESSION['role']; // Mengambil nilai 'role' dari sesi saat ini

if ($role == 1) { // Jika 'role' adalah 1
    $colspan = 5; // Mengatur nilai 'colspan' menjadi 5
    $query = "SELECT a.*, b.username
                FROM transaksi a
                JOIN pengguna b
                ON a.id_pengguna=b.id
                WHERE is_done = 1"; // Membuat query untuk mengambil transaksi yang selesai bersama dengan informasi pengguna terkait
} else {
    $colspan = 4; // Mengatur nilai 'colspan' menjadi 4
    $query = "SELECT * FROM transaksi WHERE is_done = 1 AND id_pengguna = " . $id_pengguna; // Membuat query untuk mengambil transaksi yang selesai untuk pengguna dengan ID tertentu
}

$stmt = $db->query($query); // Menjalankan query
$results = $stmt->fetchAll(); // Mengambil hasil query sebagai array


// HTML content for generating the PDF
$html = '
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        h5 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        tfoot td {
            font-weight: bold;
        }

        tfoot .text-right {
            text-align: right;
        }

        .header {
            background-color: #f2f2f2;
            padding: 10px;
        }

        .header h1 {
            margin: 0;
        }

        .customer-name {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .transaction-table {
            margin-bottom: 20px;
        }

        .transaction-table th, .transaction-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .transaction-table th {
            background-color: #f2f2f2;
        }

        .transaction-table .text-right {
            text-align: right;
        }

        .transaction-table .font-weight-bold {
            font-weight: bold;
        }

        .transaction-table .text-center {
            text-align: center;
        }

        .transaction-table .text-right {
            text-align: right;
            font-weight: bold;
        }

        .grand-total {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Transaksi</h1>
        <h5>Dicetak oleh: ' . $_SESSION['username'] . '</h5>
    </div>
    <table class="transaction-table">
    <thead>
        <tr>
            <th>No</th>';
            if ($role == 1) {
                $html .= '<th>Nama Pelanggan</th>';
            }
            $html .= '<th>Tanggal Transaksi</th>
            <th colspan="5" class="text-center">Detail Transaksi</th>';
            if ($role == 1) {
                $html .= '<th>Total Bayar</th>
                <th>Keuntungan</th>';
            } else {
                $html .= '<th>Total</th>';
            }
        $html .= '</tr>
    </thead>
    <tbody>';
    $grantotal = 0;
    $totalKeuntungan = 0;
foreach ($results as $key => $result) {
    $grantotal += $result['total'];
    $html .= '<tr>
            <td>' . ($key+1) .'</td>';
            if ($role == 1) {
                $html .= '<td>'. $result['username'] .'</td>';
            }
            $html .= '<td>' . $result['tanggal_transaksi'] .'</td>
            <td colspan="5">
                <table>
                    <tbody>';
                    $q = "SELECT a.id_transaksi, b.nama_produk, b.harga_produk, a.kuantitas, ((b.harga_produk - b.harga_produk_asli) * a.kuantitas) as keuntungan
                            FROM detail_transaksi a
                            JOIN produk b
                            ON a.id_produk = b.id
                            WHERE a.id_transaksi = " . $result['id'];
                    $dt = $db->query($q);
                    $col = $dt->fetchAll();
                    foreach($col as $index => $row) {
                        $totalKeuntungan += $row['keuntungan'];
                        $html .='<tr>
                            <td>'. $row['nama_produk'] .'</td>
                            <td>Rp'. number_format($row['harga_produk'], 0,".",'.') .'</td>
                            <td>'. $row['kuantitas'] .'kg</td>
                            <td class="text-right">Rp'. number_format(($row['kuantitas'] * $row['harga_produk']), 0,".",'.') .'</td>
                        </tr>';
                    }   
                    $html .= '</tbody>
                </table>
            </td>
            <td>' . number_format($result['total'], 0,".",'.') .'</td>';
            if ($role == 1) {
                $html .= '<td class="text-right">'. $row['keuntungan'] .'</td>';
            }
        $html .= '</tr>';
}


$html .= '</tbody>
<tfoot>
    <tr>
        <td class="text-center" colspan="4">Grandtotal</td>
        <td class="text-right" colspan="'.$colspan.'">Rp'.number_format($grantotal, 0,".",'.').'</td>';
        if ($role == 1) {
            $html .= '<td>Rp'.number_format($totalKeuntungan, 0,".",'.').'</td>';
        } 
    '</tr>
</tfoot>
</table>
</body>
</html>';

// Create a DOMPDF instance
$dompdf = new Dompdf();
$dompdf->loadHtml($html);

// Set the paper size and orientation
if ($role == 1) {
    $dompdf->setPaper('A4', 'landscape');
} else {
    $dompdf->setPaper('A4', 'portrait');
}

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to the browser
$dompdf->stream('transaction_report.pdf', ['Attachment' => true]);
