<?php

namespace App\Controllers\LandingPage;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseController
{
    protected $folder_directory = 'landing_page/page/home/view/';
    protected $js = ['index'];
    protected $db;

    public function __construct() {
        $this->db = new DatabaseModel();
    }

    public function index()
    {
        $data['page_title'] = 'Lazisnu';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function get_data()
    {
        is_ajax();
        
        try {
            $requestType = $this->request->getGet('type'); // Menentukan data yang diambil dari parameter URL
            $db = \Config\Database::connect();

            switch ($requestType) {
                case 'citizens':
                    $citizens = $db->table('citizens')
                        ->select('name')
                        ->where('is_deleted', false)
                        ->get()
                        ->getResultArray();
                    
                    // Mengambil nama depan dari setiap citizen
                    foreach ($citizens as &$citizen) {
                        $citizen['name'] = explode(' ', $citizen['name'])[0];
                    }
                    unset($citizen);

                    return $this->response->setJSON(['success' => true, 'data' => $citizens]);

                case 'statistics':
                    $currentMonth = date('m');
                    $currentYear = date('Y');

                    $beneficaries_monthly = $db->table('detail_fund')
                        ->select('COUNT(DISTINCT beneficaries_id) as total')
                        ->where('is_deleted', false)
                        ->where('MONTH(created_at)', $currentMonth)
                        ->where('YEAR(created_at)', $currentYear)
                        ->get()
                        ->getRowObject();

                    $beneficaries_total = $db->table('beneficaries a')
                        ->select('COUNT(a.id) as total')
                        ->where('a.is_deleted', false)
                        ->join('branches b', 'b.id = a.branches_id', 'left')
                        ->get()
                        ->getRowObject();

                    $citizens_total = $db->table('citizens a')
                        ->select('COUNT(a.id) as total')
                        ->where('a.is_deleted', false)
                        ->join('rws b', 'b.id = a.rw_id', 'left')
                        ->join('branches c', 'c.id = b.branch_id', 'left')
                        ->get()
                        ->getRowObject();

                    $saldo_total = $db->table('transactions a')
                        ->select('SUM(a.debit) - SUM(a.credit) as total')
                        ->where('a.is_deleted', false)
                        ->join('citizens b', 'b.id = a.citizen_id', 'left')
                        ->join('users c', 'c.id = a.user_id', 'left')
                        ->join('rws d', 'd.id = b.rw_id', 'left')
                        ->join('programs e', 'e.id = a.program_id', 'left')
                        ->join('branches f', 'f.id = d.branch_id', 'left')
                        ->join('branches g', 'g.id = e.branches_id', 'left')
                        ->join('rws h', 'h.id = c.rw_id', 'left')
                        ->join('branches i', 'i.id = h.branch_id', 'left')
                        ->get()
                        ->getRowObject();

                    $data = [
                        'citizens_total' => $citizens_total->total,
                        'beneficaries_monthly' => $beneficaries_monthly->total,
                        'beneficaries_total' => $beneficaries_total->total,
                        'saldo_total' => $saldo_total->total ? 'Rp ' . number_format($saldo_total->total, 0, ',', '.') : '-',
                    ];

                    return $this->response->setJSON(['success' => true, 'data' => $data]);

                case 'programs':
                    $programs = $db->table('program_page a')
                        ->select('a.image, b.name as program_name')
                        ->join('programs b', 'b.id = a.program_id', 'left')
                        ->where('a.is_deleted', false)
                        ->get()
                        ->getResultArray();

                    foreach ($programs as &$program) {
                        $program['image'] = base_url('assets/themes/super_admin/uploads/' . $program['image']);
                    }

                    return $this->response->setJSON(['success' => true, 'data' => $programs]);

                default:
                    return $this->response->setJSON(['error' => 'Invalid request type'], 400);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => $e->getMessage()], 500);
        }
    }

    public function detail_list()
    {
        is_ajax();

        $db = \Config\Database::connect();

        $data_distribution_page = $db->table('distribution_page a')
            ->select('a.id, a.description, a.slug, a.date, b.name as program_name, c.name as branch_name')
            ->join('programs b', 'b.id = a.program_id', 'left')
            ->join('branches c', 'c.id = b.branches_id', 'left')
            ->where('a.is_deleted', false)
            ->orderBy('a.id', 'DESC')
            ->get()
            ->getResult();

        $data = [];
        foreach ($data_distribution_page as $item) {
            $id = $item->id;

            // Ambil satu gambar saja
            $distribution_image = $db->table('distribution_image')
                ->select('image')
                ->where('distribution_page_id', $id)
                ->where('is_deleted', false)
                ->orderBy('id', 'ASC')
                ->limit(1)
                ->get()
                ->getRow();

            $image_url = $distribution_image ? base_url('assets/themes/super_admin/uploads/' . $distribution_image->image) : base_url('assets/themes/super_admin/uploads/default.png');

            $data[] = [
                'id' => encrypt_data($id),
                'slug' => $item->slug,
                'date' => $item->date,
                'program_name' => $item->program_name,
                'image' => $image_url
            ];
        }

        return $this->response->setJSON(['data' => $data]);
    }
}
