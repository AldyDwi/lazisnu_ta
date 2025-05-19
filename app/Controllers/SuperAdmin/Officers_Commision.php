<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Officers_Commision extends BaseController
{
    protected $folder_directory = 'super_admin\\page\\officers_commision\\view\\';
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
        $data['page_title'] = 'Komisi Petugas | Super-Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        // Ambil input filter tanggal dari request
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

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
                'a.is_deleted' => false,
                'DATE(a.created_at) >=' => $start_date,
                'DATE(a.created_at) <=' => $end_date,
                'a.program_id IS NULL OR a.program_id = ""' => null
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
                    'branch_name' => $data_table->branch_name,
                    'debit' => 0,
                    'credit' => 0,
                    'note' => $data_table->note,
                    'created_at' => $data_table->created_at,
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
            $formattedRow[] = $row['branch_name'];
            $formattedRow[] = 'Rp ' . number_format($row['debit'], 0, ',', '.');
            $formattedRow[] = 'Rp ' . number_format($row['credit'], 0, ',', '.');
    
            $data[] = $formattedRow;

        }

        return $this->response->setJSON(['data' => $data]);
    }

    public function get_income() {
        $currentMonth = date('m');
        $currentYear = date('Y');

        try {
            $user_id = session()->get('user_id');

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

            return $this->response->setJSON(['status' => true, 'data' => $data]);
        } catch (\Exception $e) {
            log_message('error', 'Error retrieving citizens data: ' . $e->getMessage());
            return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function save()
    {
        $data_post = $this->request->getPost();
        $currentMonth = $this->request->getPost('month');
        $currentYear = $this->request->getPost('year');

        $validation = [
            'month' => [
                'label' => 'Bulan', 
                'rules' => 'required'
            ],
            'year' => [
                'label' => 'Tahun',
                'rules' => 'required'
            ],
        ];

        validate($data_post, $validation);

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Cek apakah ada data komisi dalam bulan dan tahun tersebut
            $existing_commission = $db->query("SELECT COUNT(*) as total FROM transactions 
                WHERE type = 'field_officer_commision' 
                    AND is_deleted = false
                    AND MONTH(created_at) = ? 
                    AND YEAR(created_at) = ?", [$currentMonth, $currentYear])->getRow();

            if ($existing_commission->total > 0) {
                // Jika ada, soft delete data komisi lama di bulan dan tahun yang sama
                DatabaseModel::updateData(
                    'transactions',
                    [
                        'type' => 'field_officer_commision',
                        'MONTH(created_at)' => $currentMonth,
                        'YEAR(created_at)' => $currentYear
                    ],
                    [
                        'is_deleted' => true,
                        'deleted_at' => date('Y-m-d H:i:s')
                    ]
                );
            }

            // Ambil daftar petugas yang memiliki donasi dalam bulan dan tahun yang dipilih
            $data_transactions = $db->query("SELECT c.id as user_id, d.id as rw_id, SUM(a.debit) as total_donations
                FROM transactions a
                LEFT JOIN users c ON c.id = a.user_id
                LEFT JOIN rws d ON d.id = a.rw_id
                WHERE a.type = 'donations' 
                    AND a.is_deleted = false 
                    AND MONTH(a.created_at) = ?
                    AND YEAR(a.created_at) = ?
                GROUP BY c.id, d.id", [$currentMonth, $currentYear])->getResult();

            if (empty($data_transactions)) {
                return $this->response->setJSON(['status' => false, 'message' => 'Tidak ada donasi dalam bulan dan tahun ini.']);
            }

            // Simpan data komisi baru berdasarkan bulan dan tahun
            foreach ($data_transactions as $transaction) {
                $commission_amount = $transaction->total_donations * 0.10; // 10% komisi

                $array_insert = [
                    'user_id' => $transaction->user_id,
                    'rw_id' => $transaction->rw_id,
                    'type' => 'field_officer_commision',
                    'credit' => $commission_amount,
                    'status' => 'open',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                DatabaseModel::insertData('transactions', $array_insert);
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return $this->response->setJSON(['status' => false, 'message' => 'Gagal menyimpan data komisi.']);
            }

            return $this->response->setJSON(['status' => true, 'message' => 'Komisi berhasil ditambahkan.']);
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}