<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Dompdf\Dompdf;
use Dompdf\Options;

class Export_Commision extends BaseController
{
    public function excel()
    {
        $selectedMonth = $this->request->getPost('month_excel');
        $selectedYear = $this->request->getPost('year_excel');
        $db = \Config\Database::connect();

        // Base query
        $builder = $db->table('transactions a')
            ->select('a.*, b.name as citizen_name, c.name as user_name, d.name as rw_name, e.name as branch_name, MONTHNAME(a.created_at) as month_name, MONTH(a.created_at) as month_num, YEAR(a.created_at) as year')
            ->join('citizens b', 'b.id = a.citizen_id', 'left')
            ->join('users c', 'c.id = a.user_id', 'left')
            ->join('rws d', 'd.id = a.rw_id', 'left')
            ->join('branches e', 'e.id = d.branch_id', 'left')
            ->where('a.is_deleted', false)
            ->groupStart()
                ->where('a.program_id IS NULL')
                ->orWhere('a.program_id', '') 
            ->groupEnd();

        // Tambahkan filter jika bulan/tahun dipilih
        if ($selectedMonth && $selectedYear) {
            $builder->where('MONTH(a.created_at)', $selectedMonth)
                    ->where('YEAR(a.created_at)', $selectedYear);
        } elseif ($selectedYear) {
            $builder->where('YEAR(a.created_at)', $selectedYear);
        }

        $builder->orderBy('a.created_at', 'ASC');
        $transactions = $builder->get()->getResultArray();

        if (!$transactions) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['message' => 'Tidak ada data yang ditemukan']);
        }

        // Konversi bulan ke Bahasa Indonesia
        $bulanIndo = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
            'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
            'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember',
        ];

        // Kelompokkan berdasarkan bulan, tahun, dan user
        $groupedData = [];
        foreach ($transactions as $data) {
            $user = $data['user_name'];
            $rw = $data['rw_name'];
            $branch = $data['branch_name'];
            $month = $data['month_name'];
            $year = $data['year'];

            $key = "{$user}_{$month}_{$year}";

            if (!isset($groupedData[$key])) {
                $groupedData[$key] = [
                    'user_name' => $user,
                    'rw_name' => $rw,
                    'branch_name' => $branch,
                    'debit' => 0,
                    'credit' => 0,
                    'month' => $bulanIndo[$month] ?? '-',
                    'year' => $year,
                ];
            }

            $groupedData[$key]['debit'] += $data['debit'] ?? 0;
            $groupedData[$key]['credit'] += ($data['debit'] * 0.1) ?? 0;
        }

        // Buat file Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Petugas');
        $sheet->setCellValue('C1', 'RW');
        $sheet->setCellValue('D1', 'Ranting');
        $sheet->setCellValue('E1', 'Perolehan (Rp)');
        $sheet->setCellValue('F1', 'Komisi (Rp)');
        $sheet->setCellValue('G1', 'Bulan');
        $sheet->setCellValue('H1', 'Tahun');

        $row = 2;
        $no = 1;
        foreach ($groupedData as $entry) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $entry['user_name'] ?: '-');
            $sheet->setCellValue('C' . $row, $entry['rw_name'] ?: '-');
            $sheet->setCellValue('D' . $row, $entry['branch_name'] ?: '-');
            $sheet->setCellValue('E' . $row, $entry['debit']);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('F' . $row, $entry['credit']);
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');
            $sheet->setCellValue('G' . $row, $entry['month'] ?: '-');
            $sheet->setCellValue('H' . $row, $entry['year'] ?: '-');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'komisi_petugas.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Content-Length', strlen($excelOutput))
            ->setBody($excelOutput);
    }

    public function pdf() {
        try {
            $currentMonth = $this->request->getPost('month_pdf');
            $currentYear = $this->request->getPost('year_pdf');
    
            $start_date = sprintf('%04d-%02d-01', $currentYear, $currentMonth);
            $end_date = date('Y-m-t', strtotime($start_date));
    
            $branch_id = $this->request->getPost('branch_id');
    
            if (!$branch_id) {
                return $this->response->setJSON(['status' => false, 'message' => 'Branch ID harus diisi']);
            }
    
            $db = \Config\Database::connect();
    
            // Query data transaksi
            $data_transactions = $db->table('transactions a')
                ->select('a.*, b.name as citizen_name, c.name as user_name, d.name as rw_name, e.name as branch_name, f.name as region_name, YEAR(a.created_at) as year, g.name as district_name')
                ->join('citizens b', 'b.id = a.citizen_id', 'left')
                ->join('users c', 'c.id = a.user_id', 'left')
                ->join('rws d', 'd.id = a.rw_id', 'left')
                ->join('branches e', 'e.id = d.branch_id', 'left')
                ->join('region f', 'f.id = e.region_id', 'left')
                ->join('districts g', 'g.id = f.district_id', 'left')
                ->where('e.id', $branch_id)
                ->where('a.is_deleted', false)
                ->where('DATE(a.created_at) >=', $start_date)
                ->where('DATE(a.created_at) <=', $end_date)
                ->groupStart()
                    ->where('a.program_id IS NULL')
                    ->orWhere('a.program_id', '') 
                ->groupEnd()
                ->orderBy('a.created_at', 'ASC')
                ->get()
                ->getResultArray();
    
            if (empty($data_transactions)) {
                return $this->response->setJSON(['status' => false, 'message' => 'Tidak ada data pada tanggal yang dipilih']);
            }
    
            // Proses data untuk PDF
            $groupedData = [];
            foreach ($data_transactions as $data_table) {
                $user_name = $data_table['user_name'];
    
                if (!isset($groupedData[$user_name])) {
                    $groupedData[$user_name] = [
                        'user_name' => $user_name,
                        'rw_name' => $data_table['rw_name'],
                        'branch_name' => $data_table['branch_name'],
                        'debit' => 0,
                        'credit' => 0,
                    ];
                }
    
                $groupedData[$user_name]['debit'] += $data_table['debit'] ?? 0;
                $groupedData[$user_name]['credit'] += ($data_table['debit'] * 0.1) ?? 0; // 10% dari debit
            }
    
            $region_name = $data_transactions[0]['region_name'] ?? 'Tidak Ada Data';
            $district_name = $data_transactions[0]['district_name'] ?? 'Tidak Ada Data';
            $year = $data_transactions[0]['year'] ?? 'Tidak Ada Data';
    
            // Render PDF
            $html = view('super_admin/page/officers_commision/view/_partials/pdf_template', [
                'groupedData' => $groupedData,
                'region_name' => $region_name,
                'district_name' => $district_name,
                'year' => $year,
            ]);
    
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
    
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
    
            $output = $dompdf->output();
    
            return $this->response
                ->setStatusCode(200)
                ->setHeader('Content-Type', 'application/pdf')
                ->setHeader('Content-Disposition', 'inline; filename="komisi_petugas.pdf"')
                ->setBody($output);
        } catch (\Exception $e) {
            log_message('error', 'Error export data: ' . $e->getMessage());
            return $this->response->setJSON(['status' => false, 'message' => 'Terjadi kesalahan saat membuat PDF: ' . $e->getMessage()]);
        }
    }
}
