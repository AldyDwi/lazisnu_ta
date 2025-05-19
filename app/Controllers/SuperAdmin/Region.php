<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Region extends BaseController
{
    protected $folder_directory = 'super_admin\\page\\region\\view\\';
    protected $js = ['index'];

    public function __construct() {}

    public function index()
    {
        $data_regions = DatabaseModel::get([
            'select' => 'a.*, b.name as district_name',
            'from' => 'region a',
            'where' => ['a.is_deleted' => false],
            'join' => [
                'districts b, b.id = a.district_id, left',
            ]
        ])->getResultObject();

        $data['data_regions'] = $data_regions;
        $data['page_title'] = 'Kelurahan | Super-Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        is_ajax();

        $data_regions = DatabaseModel::get([
            'select' => 'a.*, b.name as district_name, CONCAT(a.name, ", ", b.name) AS dropdown',
            'from' => 'region a',
            'where' => ['a.is_deleted' => false],
            'join' => [
                'districts b, b.id = a.district_id, left',
            ]
        ])->getResultObject();

        $data = [];
        foreach ($data_regions as $data_table) {
            $encrypted_id = encrypt_data($data_table->id);
            
            $row = [];
            $row[] = $data_table->id;
            $row[] = $data_table->dropdown;
            $row[] = $data_table->name;
            $row[] = $data_table->district_name;
            $row[] = '
                <button id="btnEdit" class="btn btn-warning text-white fw-semibold" 
                    data-id="'.$encrypted_id.'" 
                    data-name="'.$data_table->name.'"
                    data-district_id="'.$data_table->district_id.'"
                    data-district_name="'.$data_table->district_name.'">
                    Ubah 
                </button>
                <button id="btnDelete" class="btn btn-danger text-white fw-semibold" 
                    data-id="'.$encrypted_id.'">
                    Hapus
                </button>
            ';
            $data[] = $row;
        }

        $response = [
            'data' => $data,
        ];

        return response()->setJSON($response);
    }

    public function save()
    {
        is_ajax();
        $data_post = $this->request->getPost();

        $validation = [
            'name' => [
                'label' => 'Nama Region', 
                'rules' => 'required|max_length[100]|is_unique_name[region]'
            ],
            'district_id' => [
                'label' => 'Distrik', 
                'rules' => 'required|valid_reference[districts,id,Distrik tidak valid atau tidak ditemukan]'
            ],
        ];

        validate($data_post, $validation);

        $array_insert = [
            'name' => $data_post['name'],
            'district_id' => $data_post['district_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $lastId = DatabaseModel::insertData('region', $array_insert);
            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return response()->setJSON(['status' => false, 'message' => 'Failed to save data']);
            }

            // Ensure the ID is converted to string before encryption
            $encrypted_id = encrypt_data((string)$lastId);
        } catch (\Exception $e) {
            $db->transRollback();
            return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }

        return response()->setJSON(['status' => TRUE, 'message' => 'Data berhasil disimpan', 'id' => $encrypted_id]);
    }

    public function update()
    {
        is_ajax();
        $data_post = $this->request->getPost();

        $validation = [
            'id' => [
                'label' => 'ID Region', 
                'rules' => 'required'
            ],
            'name' => [
                'label' => 'Nama Region', 
                'rules' => 'required|max_length[100]'
            ],
            'district_id' => [
                'label' => 'Distrik', 
                'rules' => 'required|valid_reference[districts,id,Distrik tidak valid atau tidak ditemukan]'
            ],
        ];

        validate($data_post, $validation);

        // Decrypt ID
        $decrypted_id = $this->decrypt($data_post['id']);

        if (!$decrypted_id) {
            return response()->setJSON(['status' => false, 'message' => 'Invalid ID']);
        }

        $array_update = [
            'name' => $data_post['name'],
            'district_id' => $data_post['district_id'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Where condition
        $where_condition = ['id' => $decrypted_id];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Use the correct parameter order for updateData
            $result = DatabaseModel::updateData('region', $where_condition, $array_update);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return response()->setJSON(['status' => false, 'message' => 'Failed to update data']);
            }
        } catch (\Exception $e) {
            $db->transRollback();
            return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }

        return response()->setJSON(['status' => TRUE, 'message' => 'Data berhasil diperbarui']);
    }

    public function delete()
    {
        is_ajax();
        $data_post = $this->request->getPost();

        // Check if ID exists in the request
        if (!isset($data_post['id']) || empty($data_post['id'])) {
            return response()->setJSON([
                'status' => false,
                'inputerror' => ['id'],
                'error_string' => ['The ID Region field is required.']
            ]);
        }

        // Validate the ID field
        $validation = [
            'id' => [
                'label' => 'ID Region', 
                'rules' => 'required'
            ],
        ];

        // Use try-catch to handle validation errors
        try {
            validate($data_post, $validation);
            
            // Decrypt ID
            $decrypted_id = $this->decrypt($data_post['id']);
            
            if (!$decrypted_id) {
                return response()->setJSON([
                    'status' => false,
                    'message' => 'Invalid ID',
                    'inputerror' => ['id'],
                    'error_string' => ['ID tidak valid atau tidak dapat didekripsi.']
                ]);
            }
            
            // Check if region is used in other tables
            $is_used_in_branches = DatabaseModel::get([
                'select' => 'COUNT(*) as count',
                'from' => 'branches',
                'where' => [
                    'region_id' => $decrypted_id,
                    'is_deleted' => false
                ]
            ])->getRow()->count > 0;
            
            if ($is_used_in_branches) {
                return response()->setJSON([
                    'status' => false, 
                    'message' => 'Region ini tidak dapat dihapus karena masih digunakan oleh data RW'
                ]);
            }
            
        } catch (\Exception $e) {
            return response()->setJSON([
                'status' => false,
                'inputerror' => ['id'],
                'error_string' => [$e->getMessage()]
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // First, verify the record exists
            $record = $db->table('region')
                ->where('id', $decrypted_id)
                ->where('is_deleted', false)
                ->get()
                ->getRow();

            if (!$record) {
                return response()->setJSON([
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                    'inputerror' => ['id'],
                    'error_string' => ['Data dengan ID tersebut tidak ditemukan.']
                ]);
            }

            // Update with soft delete
            DatabaseModel::updateData(
                'region',
                ['id' => $decrypted_id],
                [
                    'is_deleted' => true,
                    'deleted_at' => date('Y-m-d H:i:s')
                ]
            );
            
            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return response()->setJSON([
                    'status' => false,
                    'message' => 'Failed to delete data'
                ]);
            }
        } catch (\Exception $e) {
            $db->transRollback();
            return response()->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }

        return response()->setJSON([
            'status' => TRUE,
            'message' => 'Data berhasil dihapus'
        ]);
    }

    private function decrypt($encrypted_id)
    {
        if (empty($encrypted_id)) {
            return null;
        }

        $encrypter = \Config\Services::encrypter();
        try {
            // First try with hex2bin (assuming it was encrypted with bin2hex)
            return $encrypter->decrypt(hex2bin($encrypted_id));
        } catch (\Exception $e) {
            try {
                // If that fails, try with base64_decode
                return $encrypter->decrypt(base64_decode($encrypted_id));
            } catch (\Exception $e2) {
                // If all decryption fails
                return null;
            }
        }
    }

    private function encrypt($id)
    {
        if (!is_string($id)) {
            $id = (string)$id;
        }

        $encrypter = \Config\Services::encrypter();
        return bin2hex($encrypter->encrypt($id));
    }
}