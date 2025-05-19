<?php

namespace App\Controllers\Officer;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Total extends BaseController
{
    protected $folder_directory = 'admin\\page\\citizen\\view\\';
    protected $js = ['index'];

    protected $db;

    public function __construct() {
        $this->db = new DatabaseModel();
    }
    public function citizen_total()
    {
        $rw_id = $this->request->getPost('rw_id');

        try {
            $citizens_total = $this->db->get([
                'select' => 'COUNT(a.id) as total',
                'from' => 'citizens a',
                'where' => [
                    'a.is_deleted' => false,
                    'a.rw_id'    => $rw_id,
                ],
            ])->getResult();

            return $this->response->setJSON(['status' => true, 'data' => $citizens_total]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function beneficaries_total()
    {
        $branch_id = $this->request->getPost('branch_id');

        try {
            $beneficaries_total = $this->db->get([
                'select' => 'COUNT(a.id) as total',
                'from' => 'beneficaries a',
                'where' => [
                    'a.is_deleted' => false,
                    'a.branches_id'    => $branch_id,
                ],
            ])->getResult();

            return $this->response->setJSON(['status' => true, 'data' => $beneficaries_total]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
