<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export PDF</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 14px;
            margin: 40px;
            text-align: center;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 3px solid black;
        }
        .header h3, .header h4, .header h5, .header p {
            margin: 2px 0;
        }
        .header h3 {
            font-size: 18px;
            font-weight: bold;
        }
        .header h4 {
            font-size: 16px;
            font-weight: bold;
        }
        .header h5 {
            font-size: 14px;
            font-weight: bold;
        }
        .header p {
            font-size: 16px;
        }
        .header2 {
            text-align: center;
            margin-top: 20px;
        }
        .header2 p {
            font-size: 12px;
            font-weight: bold;
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #ddd;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }


        .ttd-table {
            width: 100%;
            margin-top: 40px;
        }
        .ttd-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            border: none;
        }
        .signature {
            display: block;
            margin-top: 70px;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <table width="100%" style="border: none; border-collapse: collapse;">
            <tr style="border: none;">
                <td width="30%" style="border: none; vertical-align: top;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/29/Logo_NU_CARE-LAZISNU.jpg" width="150">
                </td>
                <td style="border: none; text-align: left;">
                    <h3 style="margin: 0;">PENGURUS RANTING NAHDLATUL ULAMA</h3>
                    <h4 style="margin: 0;"><?= strtoupper($region_name) ?></h4>
                    <h5 style="margin: 0;">UNIT PENGELOLA ZAKAT, INFAQ & SHADAQAH (UP ZIS)</h5>
                    <p style="margin: 5px 0;">Email: <strong><?= strtolower($region_name) ?>.nucare@gmail.com</strong></p>
                </td>
            </tr>
        </table>
    </div>
    <div class="header2">
        <p>TANDA TERIMA BAGIAN PETUGAS LAPANGAN PENGGIAT KOIN NU</p>
        <p>NU CARE LAZISNU</p>
        <p>PENGURUS RANTING NAHDLATUL ULAMA <?= strtoupper($region_name) ?> KEC. <?= strtoupper($district_name) ?> KOTA KEDIRI</p>
        <p><?= strtoupper($year) ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Uraian</th>
                <th>RW</th>
                <th>Perolehan</th>
                <th>10%</th>
                <th>TTD</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $total_perolehan = 0; 
                $total_potongan = 0;
                $no = 1;
                foreach ($groupedData as $trx): 
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $trx['user_name'] ?></td>
                <td><?= $trx['rw_name'] ?></td>
                <td>Rp <?= number_format($trx['debit'], 0, ',', '.') ?></td>
                <td>Rp <?= number_format($trx['credit'], 0, ',', '.') ?></td>
                <td></td>
            </tr>
            <?php 
                $total_perolehan += $trx['debit']; 
                $total_potongan += $trx['credit'];
                endforeach;
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><?= $no++; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td><?= $no++; ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr class="total-row">
                <td colspan="3">JUMLAH TOTAL</td>
                <td>Rp <?= number_format($total_perolehan, 0, ',', '.') ?></td>
                <td>Rp <?= number_format($total_potongan, 0, ',', '.') ?></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table class="ttd-table">
        <tr>
            <td>
                <p style="color: white;">-</p>
                <p><strong>Ketua</strong></p>
                <div class="signature">(..........................................)</div>
            </td>
            <td>
                <p>Kediri,..................................<?= strtoupper($year) ?></p>
                <p><strong>Sekretaris</strong></p>
                <div class="signature">(..........................................)</div>
            </td>
        </tr>
    </table>
</body>
</html>
