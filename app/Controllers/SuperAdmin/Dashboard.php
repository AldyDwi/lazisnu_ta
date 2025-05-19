<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;


class Dashboard extends BaseController
{
    protected $folder_directory = 'super_admin\\page\\dashboard\\view\\';
    protected $js = ['index'];
    protected $db;

    public function __construct() {
        $this->db = new DatabaseModel();
    }

    public function index()
    {
        $data['page_title'] = 'Dashboard | Super-Admin';
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

        $branch_id = $this->request->getGet('branch_id');

        $where_citizens = "a.is_deleted = false";
        $where_officers = ['a.is_deleted' => false, 'role' => 'petugas'];
        $where_beneficaries = ['a.is_deleted' => false];
        $where_saldo_monthly = [
            'a.is_deleted' => false,
            'type' => 'donations',
            'MONTH(a.created_at)' => $currentMonth,
            'YEAR(a.created_at)' => $currentYear
        ];
        $where_saldo_overall = ['a.is_deleted' => false, 'type' => 'donations'];

        if (!empty($branch_id)) {
            $where_citizens .= " AND (c.id = '$branch_id' OR a.rw_id IS NULL)";
            $where_officers['c.id'] = $branch_id;
            $where_beneficaries['branches_id'] = $branch_id;
            $where_saldo_monthly['c.id'] = $branch_id;
            $where_saldo_overall['c.id'] = $branch_id;
            // $where_saldo_total .= "
            //     AND (
            //         -- Prioritaskan branch_id dari rw_id jika tersedia
            //         (f.id = '$branch_id' AND a.rw_id IS NOT NULL) 
            //         OR 
            //         -- Jika rw_id dan user_id NULL, gunakan branch_id dari program_id
            //         (g.id = '$branch_id' AND a.rw_id IS NULL AND a.user_id IS NULL) 
            //         OR 
            //         -- Jika program_id NULL, gunakan branch_id dari user_id
            //         (i.id = '$branch_id' AND a.program_id IS NULL) 
            //     )
            // ";
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
                ) AS total
            ";
        } else {
            $select = "
            SUM(a.debit) - SUM(a.credit) as total
            ";
        }

        $citizens_total = $this->db->get([
            'select' => 'COUNT(a.id) as total',
            'from' => 'citizens a',
            'where' => $where_citizens,
            'join' => [
                'rws b, b.id = a.rw_id, left',
                'branches c, c.id = b.branch_id, left'
            ],
        ])->getRowObject();

        $officers_total = $this->db->get([
            'select' => 'COUNT(a.id) as total',
            'from' => 'users a',
            'where' => $where_officers,
            'join' => [
                'rws b, b.id = a.rw_id, left',
                'branches c, c.id = b.branch_id, left'
            ],
        ])->getRowObject();

        $admins_total = $this->db->get([
            'select' => 'COUNT(id) as total',
            'from' => 'users',
            'where' => [
                'is_deleted' => false,
                'role' => 'admin'
            ]
        ])->getRowObject();

        $beneficaries_total = $this->db->get([
            'select' => 'COUNT(a.id) as total',
            'from' => 'beneficaries a',
            'where' => $where_beneficaries,
            'join' => [
                'branches b, b.id = a.branches_id, left'
            ],
        ])->getRowObject();

        $donations_total_monthly = $this->db->get([
            'select' => 'SUM(a.debit) as total',
            'from' => 'transactions a',
            'where' => $where_saldo_monthly,
            'join' => [
                'rws b, b.id = a.rw_id, left',
                'branches c, c.id = b.branch_id, left'
            ],
        ])->getRowObject();

        $donations_total_overall = $this->db->get([
            'select' => 'SUM(a.debit) as total',
            'from' => 'transactions a',
            'where' => $where_saldo_overall,
            'join' => [
                'rws b, b.id = a.rw_id, left',
                'branches c, c.id = b.branch_id, left'
            ],
        ])->getRow();

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

        $saldo_total = $this->db->get([
            'select' => $select,
            'from' => 'transactions a',
            'where' => 'a.is_deleted = false',
            'join' => $join
        ])->getRowObject();

        $data = [
            'citizens_total' => $citizens_total->total,
            'officers_total' => $officers_total->total,
            'admins_total' => $admins_total->total,
            'beneficaries_total' => $beneficaries_total->total,
            'donations_total_monthly' => $donations_total_monthly->total ? 'Rp ' . number_format($donations_total_monthly->total, 0, ',', '.') : '-',
            'donations_total_overall' => $donations_total_overall->total ? 'Rp ' . number_format($donations_total_overall->total, 0, ',', '.') : '-',
            'saldo_total' => $saldo_total->total ? 'Rp ' . number_format($saldo_total->total, 0, ',', '.') : '-',
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

        $branch_id = $this->request->getGet('branch_id');

        $where_transactions = "YEAR(a.created_at) = ? AND a.is_deleted = false";
        $query_params = [$year];

        if (!empty($branch_id)) {
            $where_transactions .= " AND (
                (f.id = ? AND a.rw_id IS NOT NULL) 
                OR (g.id = ? AND a.rw_id IS NULL AND a.user_id IS NULL) 
                OR (i.id = ? AND a.program_id IS NULL)
            )";
            array_push($query_params, $branch_id, $branch_id, $branch_id);
        }

        $query = $db->query("
            SELECT 
                MONTH(a.created_at) AS month, 
                SUM(a.debit) AS total_debit,
                SUM(a.credit) AS total_credit
            FROM transactions a
            LEFT JOIN citizens b ON b.id = a.citizen_id
            LEFT JOIN users c ON c.id = a.user_id
            LEFT JOIN rws d ON d.id = b.rw_id
            LEFT JOIN programs e ON e.id = a.program_id
            LEFT JOIN branches f ON f.id = d.branch_id
            LEFT JOIN branches g ON g.id = e.branches_id
            LEFT JOIN rws h ON h.id = c.rw_id
            LEFT JOIN branches i ON i.id = h.branch_id
            WHERE $where_transactions
            GROUP BY MONTH(a.created_at)
            ORDER BY MONTH(a.created_at)
        ", $query_params);

        $results = $query->getResult();

        // Format data untuk chart
        $debit = array_fill(0, 12, 0); // Isi default 12 bulan
        $credit = array_fill(0, 12, 0);

        foreach ($results as $row) {
            $debit[$row->month - 1] = (int) $row->total_debit;
            $credit[$row->month - 1] = (int) $row->total_credit;
        }

        return $this->response->setJSON([
            'debit' => $debit,
            'credit' => $credit
        ]);
    }

    public function get_latest_transactions()
    {

        $branch_id = $this->request->getGet('branch_id');
        $db = \Config\Database::connect();

        $where_transactions = "a.is_deleted = false";

        if (!empty($branch_id)) {
            $where_transactions .= " AND (
                (f.id = ? AND a.rw_id IS NOT NULL) 
                OR (g.id = ? AND a.rw_id IS NULL AND a.user_id IS NULL) 
                OR (i.id = ? AND a.program_id IS NULL)
            )";
        }

        $query = "
            SELECT a.*, 
                b.name AS citizen_name, 
                c.name AS user_name, 
                d.name AS rw_name, 
                e.name AS program_name, 
                COALESCE(f.name, g.name, i.name) AS branch_name
            FROM transactions a
            LEFT JOIN citizens b ON b.id = a.citizen_id
            LEFT JOIN users c ON c.id = a.user_id
            LEFT JOIN rws d ON d.id = b.rw_id
            LEFT JOIN programs e ON e.id = a.program_id
            LEFT JOIN branches f ON f.id = d.branch_id
            LEFT JOIN branches g ON g.id = e.branches_id
            LEFT JOIN rws h ON h.id = c.rw_id
            LEFT JOIN branches i ON i.id = h.branch_id
            WHERE $where_transactions
            ORDER BY a.created_at DESC
            LIMIT 5
        ";

        $data_transactions = $db->query($query, [$branch_id, $branch_id, $branch_id])->getResultObject();

        $transactions = [];
        
        foreach ($data_transactions as $data) {
            if ($data->type == 'donations') {
                $name = $data->citizen_name;
                $amount = 'Rp ' . number_format($data->debit, 0, ',', '.');
                $type = 'Donasi';
            } elseif ($data->type == 'fund_distribution') {
                $name = $data->program_name;
                $amount = 'Rp ' . number_format($data->credit, 0, ',', '.');
                $type = 'Distribusi Dana';
            } elseif ($data->type == 'field_officer_commision') {
                $name = $data->user_name;
                $amount = 'Rp ' . number_format($data->credit, 0, ',', '.');
                $type = 'Komisi Petugas';
            } else {
                continue;
            }

            $transactions[] = [
                'name' => $name,
                'amount' => $amount,
                'date' => date('d-m-Y', strtotime($data->created_at)),
                'branch' => $data->branch_name,
                'type' => $type,
                'is_credit' => in_array($data->type, ['fund_distribution', 'field_officer_commision'])
            ];
        }

        return $this->response->setJSON(['transactions' => $transactions]);
    }

    public function get_branch()
    {
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT DISTINCT 
                COALESCE(f.id, g.id, i.id) AS branch_id, 
                COALESCE(f.name, g.name, i.name) AS branch_name
            FROM transactions a
            LEFT JOIN rws d ON d.id = a.rw_id
            LEFT JOIN programs e ON e.id = a.program_id
            LEFT JOIN branches f ON f.id = d.branch_id
            LEFT JOIN branches g ON g.id = e.branches_id
            LEFT JOIN users c ON c.id = a.user_id
            LEFT JOIN rws h ON h.id = c.rw_id
            LEFT JOIN branches i ON i.id = h.branch_id
            WHERE a.is_deleted = false
            ORDER BY branch_name ASC
        ");

        $branches = $query->getResult();

        return $this->response->setJSON($branches);
    }
}