<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Dompdf\Dompdf;
use Dompdf\Options;

class Export_Transaction extends BaseController
{
    public function excel()
    {
        $start_date = $this->request->getGet('start_excel');
        $end_date = $this->request->getGet('end_excel');

        $db = \Config\Database::connect();

        $data_transactions = $db->table('transactions a')
            ->select('a.*, b.name as citizen_name, c.name as user_name, d.name as rw_name, e.name as program_name, 
                    COALESCE(f.name, g.name, i.name) AS branch_name')
            ->join('citizens b', 'b.id = a.citizen_id', 'left')
            ->join('users c', 'c.id = a.user_id', 'left')
            ->join('rws d', 'd.id = b.rw_id', 'left')
            ->join('programs e', 'e.id = a.program_id', 'left')
            ->join('branches f', 'f.id = d.branch_id', 'left')
            ->join('branches g', 'g.id = e.branches_id', 'left')
            ->join('rws h', 'h.id = c.rw_id', 'left')
            ->join('branches i', 'i.id = h.branch_id', 'left')
            ->where('a.is_deleted', false)
            ->orderBy('a.created_at', 'ESC');
        
        if ($start_date && $end_date) {
            $data_transactions->where('DATE(a.created_at) >=', $start_date)
                              ->where('DATE(a.created_at) <=', $end_date);
        }

        $transaction = $data_transactions->get()->getResultArray();

        // Jika tidak ada data, kembalikan JSON respons dengan status 404
        if (empty($transaction)) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['message' => 'Tidak ada data pada tanggal yang dipilih']);
        }

        // Buat Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Tanggal');
        $sheet->setCellValue('C1', 'Tipe');
        $sheet->setCellValue('D1', 'Nama Warga');
        $sheet->setCellValue('E1', 'Nama Petugas');
        $sheet->setCellValue('F1', 'RW');
        $sheet->setCellValue('G1', 'Ranting');
        $sheet->setCellValue('H1', 'Program');
        $sheet->setCellValue('I1', 'Debit');
        $sheet->setCellValue('J1', 'Kredit');
        $sheet->setCellValue('K1', 'Saldo');

        $typeMapping = [
            'donations' => 'Donasi',
            'fund_distribution' => 'Distribusi Dana',
            'field_officer_commision' => 'Komisi Petugas'
        ];

        // Isi data
        $row = 2;
        $no = 1;
        $total = 0;
        foreach ($transaction as $t) {
            $debit = isset($t['debit']) ? $t['debit'] : 0;
            $credit = isset($t['credit']) ? $t['credit'] : 0;

            // Hitung total saldo
            $total += $debit - $credit;

            $type = $typeMapping[$t['type']] ?? '-';

            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, isset($t['created_at']) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($t['created_at'])) : '');
            $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode('DD-MM-YYYY');
            $sheet->setCellValue('C' . $row, $type);
            $sheet->setCellValue('D' . $row, $t['citizen_name'] ?: '-');
            $sheet->setCellValue('E' . $row, $t['user_name'] ?: '-');
            $sheet->setCellValue('F' . $row, $t['rw_name'] ?: '-');
            $sheet->setCellValue('G' . $row, $t['branch_name'] ?: '-');
            $sheet->setCellValue('H' . $row, $t['program_name'] ?: '-');
            $sheet->setCellValue('I' . $row, $t['debit'] ?: '-');
            $sheet->getStyle('I' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('J' . $row, $t['credit'] ?: '-');
            $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('K' . $row, $total);
            $sheet->getStyle('K' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        // Simpan file Excel ke dalam output buffer
        $writer = new Xlsx($spreadsheet);
        $fileName = 'data_export.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        // Set response headers
        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Content-Length', strlen($excelOutput))
            ->setBody($excelOutput);
    }

    public function pdf() {
        $currentMonth = $this->request->getPost('month_pdf');
        $currentYear = $this->request->getPost('year_pdf');

        $branch_id = $this->request->getPost('branch_id');

        $db = \Config\Database::connect();

        try {
            $start_date = sprintf('%04d-%02d-01', $currentYear, $currentMonth);
            $end_date = date('Y-m-t', strtotime($start_date));
            
            // Hitung saldo bulan lalu
            $lastMonth = $currentMonth - 1;
            $lastYear = $currentYear;

            // Jika Januari, maka mundur ke Desember tahun sebelumnya
            if ($currentMonth == 1) {
                $lastMonth = 12;
                $lastYear -= 1;
            }

            $last_start_date = sprintf('%04d-%02d-01', $lastYear, $lastMonth);
            $last_end_date = date('Y-m-t', strtotime($last_start_date));

            // Konversi angka bulan ke nama bulan
            $bulanName = date('F', mktime(0, 0, 0, $currentMonth, 1));

            // Jika ingin dalam bahasa Indonesia
            $bulanIndonesia = [
                'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
            ];

            $bulan = $bulanIndonesia[$bulanName] ?? $bulanName;

            $whereCondition = "
                a.is_deleted = false 
                AND DATE(a.created_at) < '$start_date'
            ";

            $select = "
                COALESCE(
                    SUM(CASE 
                        WHEN f.id = '$branch_id' AND a.rw_id IS NOT NULL 
                        THEN a.debit 
                        ELSE 0 
                    END) 
                    - 
                    SUM(CASE 
                        WHEN 
                            (g.id = '$branch_id' AND a.rw_id IS NULL AND a.user_id IS NULL)
                            OR 
                            (i.id = '$branch_id' AND a.program_id IS NULL)
                        THEN a.credit 
                        ELSE 0 
                    END),
                    0
                ) AS saldo
            ";

            $join = [
                'citizens b, b.id = a.citizen_id, left',
                'users c, c.id = a.user_id, left',
                'rws d, d.id = b.rw_id, left',
                'programs e, e.id = a.program_id, left',
                'branches f, f.id = d.branch_id, left',
                'branches g, g.id = e.branches_id, left',
                'rws h, h.id = c.rw_id, left',
                'branches i, i.id = h.branch_id, left'
            ];

            $saldo_bulan_lalu = DatabaseModel::get([
                'select' => $select,
                'from' => 'transactions a',
                'join' => $join,
                'where' => $whereCondition
            ])->getRowArray();

            $saldo_bulan_lalu = $saldo_bulan_lalu['saldo'] ?? 0;

            // Tambahkan saldo bulan lalu ke dalam hasil transaksi
            $saldo_awal_row = [
                'uraian' => 'Saldo Bulan Lalu',
                'debit' => 0,
                'credit' => 0,
                'saldo' => $saldo_bulan_lalu
            ];

            // **Perolehan RW (rw_donations)**
            $rw_donations = $db->table('transactions a')
                ->select("CONCAT('Perolehan ', d.name) AS uraian, SUM(a.debit) AS debit, 0 AS credit")
                ->join('citizens b', 'b.id = a.citizen_id', 'left')
                ->join('rws d', 'd.id = b.rw_id', 'left')
                ->join('branches f', 'f.id = d.branch_id', 'left')
                ->where('a.is_deleted', false)
                ->where('DATE(a.created_at) >=', $start_date)
                ->where('DATE(a.created_at) <=', $end_date)
                ->where('f.id', $branch_id)
                ->where('a.rw_id IS NOT NULL')
                ->groupBy('d.id')
                ->getCompiledSelect(false); 

            // **Total Operasional Koin (operasional)**
            $operasional = $db->table('transactions a')
                ->select("'Operasional Koin Bulan $bulan' AS uraian, 0 AS debit, SUM(a.credit) AS credit")
                ->join('users c', 'c.id = a.user_id', 'left')
                ->join('rws h', 'h.id = c.rw_id', 'left')
                ->join('branches i', 'i.id = h.branch_id', 'left')
                ->where('a.is_deleted', false)
                ->where('a.type', 'field_officer_commision')
                ->where('DATE(a.created_at) >=', $start_date)
                ->where('DATE(a.created_at) <=', $end_date)
                ->where('i.id', $branch_id)
                ->getCompiledSelect(false); 

            // **Distribusi Dana Program (fund_distribution)**
            $fund_distribution = $db->table('transactions a')
                ->select("e.name AS uraian, 0 AS debit, SUM(a.credit) AS credit")
                ->join('programs e', 'e.id = a.program_id', 'left')
                ->join('branches g', 'g.id = e.branches_id', 'left')
                ->where('a.is_deleted', false)
                ->where('DATE(a.created_at) >=', $start_date)
                ->where('DATE(a.created_at) <=', $end_date)
                ->where('g.id', $branch_id)
                ->where('a.rw_id IS NULL')
                ->where('a.user_id IS NULL')
                ->groupBy('e.name')
                ->getCompiledSelect(false); 

            // **Gabungkan semua hasil query dengan UNION ALL**
            $query = $db->query("$rw_donations UNION ALL $operasional UNION ALL $fund_distribution");
            $transactions = $query->getResultArray();

            if ($transactions == null) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data transaksi tidak ditemukan untuk bulan dan tahun yang dipilih.'
                ]);
            }

            // Mulai saldo awal dengan saldo bulan lalu
            $saldo_sekarang = $saldo_bulan_lalu;

            // Looping transaksi untuk menambahkan saldo
            foreach ($transactions as &$transaction) {
                $saldo_sekarang += $transaction['debit'];
                $saldo_sekarang -= $transaction['credit'];
                $transaction['saldo'] = $saldo_sekarang;
            }

            // Tambahkan saldo awal di atas transaksi
            array_unshift($transactions, $saldo_awal_row);

            // **Pengecekan jika transaksi hanya berisi saldo awal dan total operasional**
            if (count($transactions) <= 2) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Tidak ada data transaksi yang ditemukan untuk bulan dan tahun yang dipilih.'
                ]);
            }

            $regionInfo = $db->table('branches f')
                ->select("f.address, j.name AS region_name, k.name AS district_name, YEAR(NOW()) AS year")
                ->join('region j', 'j.id = f.region_id', 'left')
                ->join('districts k', 'k.id = j.district_id', 'left')
                ->where('f.id', $branch_id)
                ->get()
                ->getRowArray();

            // Set default jika tidak ada data
            $region_name = $regionInfo['region_name'] ?? 'Tidak Ada Data';
            $district_name = $regionInfo['district_name'] ?? 'Tidak Ada Data';
            $year = $regionInfo['year'] ?? date('Y');
            $address = $regionInfo['address'] ?? 'Tidak Ada Data';

            // **Load View PDF**
            $html = view('admin/page/transaction/view/_partials/pdf_template', [
                'transactions' => $transactions,
                'region_name' => $region_name, 
                'district_name' => $district_name,
                'address' => $address,
                'year' => $year,
                'bulan' => $bulan
            ]);

            // **Konfigurasi DomPDF**
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();
        
            return $this->response
                ->setStatusCode(200)
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="data_bulan_ini.pdf"')
                ->setBody($output);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memproses PDF: ' . $e->getMessage()
            ]);
        }
    }
}
