<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .page-border {
            border: 2px solid black;
            padding: 15px;
            margin: 15px;
        }
        .header {
            font-family: 'Times New Roman', serif;
            text-align: center;
            margin-bottom: 10px;
            position: relative;
        }
        .header hr {
            border: 1px solid black;
            margin-top: 5px;
        }
        .header p {
            font-style: italic;
            font-size: 14px;
            font-weight: bold;
        }

        h2, h3, h4 {
            margin: 3px 0;
            font-size: 16px;
        }
        .table-container {
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            border: 1px solid black;
            padding: 6px;
            text-align: right;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-left {
            text-align: left;
        }
        .text-center {
            text-align: center;
        }
        .table-container {
            font-size: 14px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 16px;
            margin-top: 15px;
            font-style: italic;
            text-decoration: underline;
        }
        .hadis {
            text-align: center;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="page-border">
    <!-- Header -->
    <div class="header">
        <table width="100%" style="border: none; border-collapse: collapse;">
            <tr style="border: none;">
                <td width="30%" style="border: none; vertical-align: top;">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/2/29/Logo_NU_CARE-LAZISNU.jpg" width="150">
                </td>
                <td style="border: none; text-align: left;">
                    <h2 style="margin: 0;">NU CARE LAZISNU</h2>
                    <h3 style="margin: 0;">PENGURUS RANTING NAHDLATUL ULAMA</h3>
                    <h4 style="margin: 0;">KEL. <?= strtoupper($region_name) ?> KEC. <?= strtoupper($district_name) ?> KOTA KEDIRI</h4>
                    <p style="margin: 5px 0;"><strong><?= $address ?></strong></p>
                </td>
            </tr>
        </table>
        <hr>
    </div>

    <!-- Judul Laporan -->
    <h3 style="text-align: center;">LAPORAN KEUANGAN PENGGIAT KOIN NU</h3>
    <p style="text-align: center; font-weight: bold;font-size: 14px;">UPZIS - NU CARE LAZISNU RANTING <?= strtoupper($region_name) ?> - <?= strtoupper($district_name) ?> - KEDIRI</p>
    <p style="text-align: center; font-weight: bold;font-size: 14px;">BULAN <?= strtoupper($bulan) ?> <?= strtoupper($year) ?></p>

    <!-- Tabel Laporan -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th class="text-center">No</th>
                    <th class="text-left">URAIAN</th>
                    <th>DEBIT</th>
                    <th>KREDIT</th>
                    <th>SALDO</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                $totalDebit = 0;
                $totalCredit = 0;
                ?>
                <?php foreach ($transactions as $trx): ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-left"><?= $trx['uraian']; ?></td>
                        <td><?= $trx['debit'] ? number_format($trx['debit'], 0, ',', '.') : '-'; ?></td>
                        <td><?= $trx['credit'] ? number_format($trx['credit'], 0, ',', '.') : '-'; ?></td>
                        <td><?= number_format($trx['saldo'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php 
                    $totalDebit += $trx['debit'];
                    $totalCredit += $trx['credit'];
                    ?>
                <?php endforeach; ?>
                <tr>
                    <th colspan="2" class="text-right">JUMLAH</th>
                    <th><?= number_format($totalDebit, 0, ',', '.'); ?></th>
                    <th><?= number_format($totalCredit, 0, ',', '.'); ?></th>
                    <th><?= number_format($transactions[count($transactions) - 1]['saldo'], 0, ',', '.'); ?></th>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pesan Terima Kasih -->
    <p class="footer">
        TERIMA KASIH ATAS DONASINYA. SEMOGA ALLAH SWT MELIPATGANDAKANNYA
    </p>

    <!-- Hadis -->
    <p class="hadis">
        "Tidak ada suatu hari pun ketika seorang hamba melewati paginya kecuali akan turun (datang) dua malaikat kepadanya, lalu salah satunya berdoa: 
        'Ya Allah, berikanlah pengganti bagi siapa yang menafkahkan hartanya. Sedangkan yang satunya lagi berdoa: 
        Ya Allah, berikanlah kehancuran (kebinasaan) kepada orang yang menahan hartanya.'" <br>
        (HR Bukhari)
    </p>
</div>

</body>
</html>