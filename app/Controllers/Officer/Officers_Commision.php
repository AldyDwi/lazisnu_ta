<?php

namespace App\Controllers\Officer;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Officers_Commision extends BaseController
{
    protected $folder_directory = 'admin\\page\\officers_commision\\view\\';
    protected $js = ['index'];

    protected $db;

    public function __construct()
    {
        $this->db = new DatabaseModel();
    }
    public function index()
    {
        // Ambil bulan dan tahun saat ini
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Ambil transaksi berdasarkan bulan dan tahun saat ini
        $data_transactions = $this->db->get([
            'select' => 'a.*, b.name as citizen_name, c.name as user_name, d.name as rw_name, e.name as program_name',
            'from' => 'transactions a',
            'where' => [
                'a.is_deleted' => false,
                'MONTH(a.created_at)' => $currentMonth,
                'YEAR(a.created_at)' => $currentYear
            ],
            'join' => [
                'citizens b, b.id = a.citizen_id, left',
                'users c, c.id = a.user_id, left',
                'rws d, d.id = a.rw_id, left',
                'programs e, e.id = a.program_id, left'
            ]
        ])->getResultObject();

        // Ambil status terbaru dari transaksi bulan ini
        $status = count($data_transactions) > 0 ? $data_transactions[0]->status : 'open';

        $data['data_transactions'] = $data_transactions;
        $data['status'] = $status;
        $data['page_title'] = 'Komisi Petugas | Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        // Ambil input filter tanggal dari request
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $branch_id =  session()->get('branch_id');

        // Jika tidak ada filter tanggal, gunakan bulan dan tahun saat ini
        if (!$start_date || !$end_date) {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
        }

        // Query data transaksi dengan filter tanggal
        $data_transactions = DatabaseModel::get([
            'select' => 'a.*, b.name as citizen_name, c.name as user_name, d.name as rw_name, e.name as branch_name',
            'from' => 'transactions a',
            'where' => [
                'e.id' => $branch_id,
                'a.is_deleted' => false,
                'DATE(a.created_at) >=' => $start_date,
                'DATE(a.created_at) <=' => $end_date,
                'a.program_id IS NULL OR a.program_id = ""' => null,
            ],
            'join' => [
                'citizens b, b.id = a.citizen_id, left',
                'users c, c.id = a.user_id, left',
                'rws d, d.id = a.rw_id, left',
                'branches e, e.id = d.branch_id, left'
            ],
            'order' => ['a.created_at' => 'ASC']
        ])->getResultObject();

        $groupedData = [];
        foreach ($data_transactions as $data_table) {
            $user_name = $data_table->user_name;

            if (!isset($groupedData[$user_name])) {
                $groupedData[$user_name] = [
                    'id' => $data_table->id,
                    'user_name' => $user_name,
                    'rw_name' => $data_table->rw_name,
                    'debit' => 0,
                    'credit' => 0,
                    'note' => $data_table->note,
                    'created_at' => $data_table->created_at,
                    'branch_name' => $data_table->branch_name,
                ];
            }

            // Tambahkan debit dan kredit ke transaksi yang sudah ada
            $groupedData[$user_name]['debit'] += $data_table->debit ? $data_table->debit : 0;
            $groupedData[$user_name]['credit'] += $data_table->credit ? $data_table->credit : 0;
        }

        // Urutkan transaksi berdasarkan created_at
        usort($groupedData, function ($a, $b) {
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });

        $data = [];
        foreach ($groupedData as $row) {
            $formattedRow = [];
            $formattedRow[] = $row['id'];
            $formattedRow[] = $row['user_name'];
            $formattedRow[] = $row['rw_name'];
            $formattedRow[] = 'Rp ' . number_format($row['debit'], 0, ',', '.');
            $formattedRow[] = 'Rp ' . number_format($row['credit'], 0, ',', '.');
            $formattedRow[] = $row['branch_name'];
    
            $data[] = $formattedRow;

        }

        return $this->response->setJSON(['data' => $data]);
    }

    public function get_income() {
        $currentMonth = $this->request->getPost('month') ?? date('m');
        $currentYear = $this->request->getPost('year') ?? date('Y');
        $user_id = $this->request->getPost('user_id');
    
        try {
            // $user_id = session()->get('user_id');
    
            $data = DatabaseModel::get([
                'select' => 'ANY_VALUE(user_id) AS user_id, SUM(debit) AS amount, SUM(debit) * 0.1 AS comission',
                'from'   => 'transactions',
                'where'  => [
                    'is_deleted' => false,
                    'user_id'    => $user_id,
                    'MONTH(created_at)' => $currentMonth,
                    'YEAR(created_at)' => $currentYear
                ],
                'groupBy' => ['user_id']
            ])->getResult();
    
            // Jika data kosong atau berisi NULL, kembalikan pesan "No data found"
            if (empty($data) || (isset($data[0]) && is_null($data[0]->user_id))) {
                return $this->response->setJSON(['status' => false, 'message' => 'Tidak ada data pada bulan dan tahun yang dipilih.']);
            }
    
            return $this->response->setJSON(['status' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function list_commision()
    {
        $currentYear = $this->request->getPost('year') ?? date('Y');
        $user_id = $this->request->getPost('user_id');

        $db = \Config\Database::connect();
        $db->query("SET SESSION sql_mode = ''");

        try {
            // Ambil data komisi petugas berdasarkan bulan dan tahun
            $data_comission = DatabaseModel::get([
                'select' => 'MONTH(a.created_at) AS month_number, 
                            COALESCE(SUM(a.debit), 0) AS amount, 
                            COALESCE(SUM(a.debit) * 0.1, 0) AS commission, 
                            DATE_FORMAT(MAX(a.created_at), "%d-%m-%Y") AS last_transaction,
                            b.name as user_name',
                'from'   => 'transactions a',
                'where'  => [
                    'a.is_deleted' => false,
                    'a.user_id'    => $user_id,
                    'YEAR(a.created_at)' => $currentYear,
                    'MONTH(a.created_at)' => date('n'),
                ],
                'join' => [
                    'users b, b.id = a.user_id, left',
                ],
                'groupBy' => ['MONTH(a.created_at)'],
                'orderBy' => ['MONTH(a.created_at)' => 'ASC']
            ])->getResult();

            // Mapping nama bulan ke bahasa Indonesia
            $bulanIndo = [
                1 => "Januari", 2 => "Februari", 3 => "Maret",
                4 => "April", 5 => "Mei", 6 => "Juni",
                7 => "Juli", 8 => "Agustus", 9 => "September",
                10 => "Oktober", 11 => "November", 12 => "Desember"
            ];
            
            $data = [];
            foreach ($data_comission as $row) {
                // Cek apakah data kosong atau hanya hasil query default (Rp 0)
                if ($row->amount == 0 && is_null($row->last_transaction)) {
                    continue; // Lewati data kosong
                }

                // Format ulang data menjadi objek dengan key-value
                $data[] = [
                    "user_name" => $row->user_name,
                    "month"     => $bulanIndo[$row->month_number] ?? "Tidak Diketahui",
                    "amount"    => 'Rp ' . number_format((float)$row->amount, 0, ',', '.'),
                    "commision" => 'Rp ' . number_format((float)$row->commission, 0, ',', '.'),
                    "date"      => $row->last_transaction
                ];
            }

            // Pastikan kembali, jika setelah filtering tetap kosong, kembalikan response kosong
            if (empty($data)) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data komisi untuk tahun ' . $currentYear . ' tidak ditemukan.'
                ]);
            }

            return $this->response->setJSON(['status' => true, 'data' => $data]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

}