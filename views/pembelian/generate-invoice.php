<?php
session_start();
require_once '../../vendor/dompdf/vendor/autoload.php';
require_once('../../config/config.php');

use Dompdf\Dompdf;

$db = databaseConnection();

$query = "SELECT c.harga_produk, b.kuantitas, c.nama_produk
            FROM transaksi a
            JOIN detail_transaksi b 
            ON a.id = b.id_transaksi
            JOIN produk c
            ON b.id_produk = c.id
            WHERE a.id = " . $_GET['id_transaksi'];

$stmt = $db->query($query); // Menjalankan query
$results = $stmt->fetchAll(); // Mengambil hasil query sebagai array

$imagePath = $baseUrl . '/templates/assets/images/logo.png'; // Adjust the path
$imageData = base64_encode(file_get_contents($imagePath));
$imageDataUrl = 'data:image/png;base64,' . $imageData;


$html = '
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Belanja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .receipt {
            width: 30rem;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .receipt-logo {
            width: 70px;
            height: auto;
        }

        .receipt-title {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .receipt-details {
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ddd;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .item-desc {
            flex: 2;
        }

        .item-total {
            flex: 1;
        }

        .item-qty,
        .item-price {
            flex: 1;
        }

        .total {
            margin-top: 20px;
            text-align: right;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
        }

        .table {
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <img src='.$imageDataUrl.' alt="Receipt Logo" class="receipt-logo">
            <div class="receipt-title">Subak Desa Bongan</div>
            <div>Jl. Raya Tabanan</div>
            <div>Kec Tabanan, Kota Tabanan, 80223</div>
            <hr>
            <div>Tanggal: '. date('d.m.Y-h:i:s') . '</div>
            <hr>
        </div>
        <div class="receipt-details">
            <table class="table">';
                $total = 0;
                foreach($results as $key => $value) {
                $total += $value['harga_produk'] * $value['kuantitas'];
                $html .= '<tr>
                    <td>'.$value["nama_produk"].'</td>
                    <td>'.$value["kuantitas"].'</td>
                    <td>Rp'.number_format($value["harga_produk"], 0, '.', '.').'</td>
                    <td>Rp'.number_format(($value["harga_produk"]*$value["kuantitas"]), 0, '.', '.').'</td>
                </tr>';
                }
                $html .= '<tr>
                    <td></td>
                    <td colspan="3"><hr></td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="2">Total :</td>
                    <td>Rp'. number_format($total, 0, '.', '.') .'</td>
                </tr>
                <tr>
                    <td></td>
                    <td colspan="3"><hr></td>
                </tr>
            </table>
        </div>
        <div class="footer">
            Terima kasih telah berbelanja!
        </div>
    </div>
</body>
</html>';

// Create a DOMPDF instance
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
// $paperSize = array(0, 0, 100, 100); // Width and height in millimeters
// $dompdf->setPaper($paperSize);
$dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to the browser
$dompdf->stream('invoice-'.time().'.pdf', ['Attachment' => true]);
