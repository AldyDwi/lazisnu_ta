<?php

namespace App\Controllers\Officer;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;


class Dashboard extends BaseController
{
    protected $folder_directory = 'officer\\page\\dashboard\\view\\';
    protected $js = ['index'];
    protected $db;
    public function __construct() {
        $this->db = new DatabaseModel();
    }

    public function index()
    {
        $data['page_title'] = 'Dashboard | Petugas';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        $today = date('d');
        if ($today == '01') {
            $db = \Config\Database::connect();

            // Update status citizens dari 'already' menjadi 'not yet'
            $builder = $db->table('citizen');
            $builder->where('status', 'already');
            $builder->update(['status' => 'not yet']);
        }

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data() {
        // is_ajax();
        $currentMonth = date('m');
        $currentYear = date('Y');

        $user_id =  session()->get('user_id');

        $donations_total_monthly = $this->db->get([
            'select' => 'SUM(a.debit) as total',
            'from' => 'transactions a',
            'where' => [
                'a.is_deleted' => false,
                'type' => 'donations',
                'b.id' => $user_id,
                'MONTH(a.created_at)' => $currentMonth,
                'YEAR(a.created_at)' => $currentYear
            ],
            'join' => [
                'users b, b.id = a.user_id, left',
            ],
        ])->getRowObject();

        $donations_total_overall = $this->db->get([
            'select' => 'SUM(a.debit) as total',
            'from' => 'transactions a',
            'where' => [
                'a.is_deleted' => false,
                'type' => 'donations',
                'b.id' => $user_id,
            ],
            'join' => [
                'users b, b.id = a.user_id, left',
            ],
        ])->getRow();

        $commision_total_monthly = ($donations_total_monthly->total ?? 0) * 0.1;

        $data = [
            'donations_total_monthly' => $donations_total_monthly->total ? 'Rp ' . number_format($donations_total_monthly->total, 0, ',', '.') : '-',
            'donations_total_overall' => $donations_total_overall->total ? 'Rp ' . number_format($donations_total_overall->total, 0, ',', '.') : '-',
            'commision_total_monthly' => $commision_total_monthly ? 'Rp ' . number_format($commision_total_monthly, 0, ',', '.') : '-',
        ];

        $response = [
            'data' => $data,
        ];

        return response()->setJSON($response);
    }

    public function get_year()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT DISTINCT YEAR(created_at) AS year FROM transactions ORDER BY year DESC");
        $years = $query->getResult();

        return $this->response->setJSON($years);
    }

    public function get_chart_data($year)
    {
        $db = \Config\Database::connect();

        $user_id =  session()->get('user_id');

        // Query untuk mendapatkan total debit (Donasi) per bulan
        $query = $db->query("
            SELECT 
                MONTH(a.created_at) AS month,
                SUM(a.debit) AS total_debit
            FROM transactions a
            LEFT JOIN users c ON c.id = a.user_id
            WHERE YEAR(a.created_at) = ?
            AND a.is_deleted = false
            AND c.id = ?
            AND a.type = 'donations'
            GROUP BY MONTH(a.created_at)
            ORDER BY MONTH(a.created_at)
        ", [$year, $user_id]);

        $results = $query->getResult();

        // Format data untuk chart
        $debit = array_fill(0, 12, 0); 

        foreach ($results as $row) {
            $debit[$row->month - 1] = (int) $row->total_debit;
        }

        return $this->response->setJSON([
            'debit' => $debit,
        ]);
    }

    public function get_latest_transactions()
    {

        $user_id =  session()->get('user_id');
        $db = \Config\Database::connect();

        $query = "
            SELECT a.*, 
                b.name AS citizen_name, 
                c.name AS user_name, 
                d.name AS rw_name, 
                e.name AS branch_name
            FROM transactions a
            LEFT JOIN citizens b ON b.id = a.citizen_id
            LEFT JOIN users c ON c.id = a.user_id
            LEFT JOIN rws d ON d.id = a.rw_id
            LEFT JOIN branches e ON e.id = d.branch_id
            WHERE a.is_deleted = false
            AND c.id = ? AND a.rw_id IS NOT NULL
            AND a.type = 'donations'
            ORDER BY a.created_at DESC
            LIMIT 5
        ";

        $data_transactions = $db->query($query, [$user_id])->getResultObject();

        $transactions = [];
        
        foreach ($data_transactions as $data) {
            $amount = 'Rp ' . number_format($data->debit, 0, ',', '.');

            $transactions[] = [
                'name' => $data->citizen_name,
                'amount' => $amount,
                'date' => date('d-m-Y', strtotime($data->created_at)),
                'branch' => $data->branch_name,
                'type' => 'Donasi',
            ];
        }

        return $this->response->setJSON(['transactions' => $transactions]);
    }
}