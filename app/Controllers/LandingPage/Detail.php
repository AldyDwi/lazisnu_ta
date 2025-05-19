<?php

namespace App\Controllers\LandingPage;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Detail extends BaseController
{

    protected $folder_directory = 'landing_page/page/detail/view/';
    protected $js = ['index'];
    protected $db;

    public function __construct() {
        $this->db = new DatabaseModel();
    }

    public function index($slug = null)
    {
        $data['page_title'] = 'Detail Penyaluran Saldo';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;
        $data['slug'] = $slug;

        return view($this->folder_directory . 'index', $data);
    }

    public function get_detail()
    {
        try {
            is_ajax();
            $slug = $this->request->getGet('slug');

            $db = \Config\Database::connect();

            $data_distribution_page = $db->table('distribution_page a')
                ->select('a.*, b.name as program_name, c.name as branch_name')
                ->join('programs b', 'b.id = a.program_id', 'left')
                ->join('branches c', 'c.id = b.branches_id', 'left')
                ->where('a.slug', $slug)
                ->get()
                ->getRow();

            if (!$data_distribution_page) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Data tidak ditemukan!'
                ]);
            }

            $distribution_image = $db->table('distribution_image')
                ->select('image')
                ->where('distribution_page_id', $data_distribution_page->id)
                ->where('is_deleted', false)
                ->get()
                ->getResult();

            $data_distribution_image = array_map(
                fn($b) => base_url('assets/themes/super_admin/uploads/' . $b->image),
                $distribution_image
            );

            return $this->response->setJSON([
                'status' => true,
                'distribution_page' => $data_distribution_page,
                'distribution_image' => $data_distribution_image
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
