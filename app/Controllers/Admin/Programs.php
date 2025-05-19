<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Programs extends BaseController
{
    protected $folder_directory = 'admin\\page\\programs\\view\\';
    protected $js = ['index'];

    public function __construct() {}

    public function index()
    {
        $data_programs = DatabaseModel::get([
            'select' => 'a.*, b.name as branch_name',
            'from' => 'programs a',
            'where' => ['a.is_deleted' => false],
            'join' => [
                'branches b, b.id = a.branches_id, left',
            ]
        ])->getResultObject();

        $data['data_programs'] = $data_programs;
        $data['page_title'] = 'Program | Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        is_ajax();
        $branch_id =  session()->get('branch_id');

        $data_programs = DatabaseModel::get([
            'select' => 'a.*, b.name as branch_name, CONCAT(b.name, ", ", c.name) AS branch_region',
            'from' => 'programs a',
            'where' => [
                'a.is_deleted' => false,
                'a.branches_id' => $branch_id
            ],
            'join' => [
                'branches b, b.id = a.branches_id, left',
                'region c, c.id = b.region_id, left'
            ]
        ])->getResultObject();

        $data = [];
        foreach ($data_programs as $data_table) {
            $encrypted_id = encrypt_data($data_table->id);
            
            $row = [];
            $row[] = $data_table->id;
            $row[] = $data_table->name;
            $row[] = $data_table->branch_name;
            $row[] = $data_table->percentage . '%';
            $row[] = $data_table->type;
            $row[] = '
                <button id="btnEdit" class="btn btn-warning text-white fw-semibold" 
                    data-id="'.$encrypted_id.'" 
                    data-name="'.$data_table->name.'"
                    data-percentage="'.$data_table->percentage.'"
                    data-type="'.$data_table->type.'">
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
        $branch_id =  session()->get('branch_id');

        $validation = [
            'name' => [
                'label' => 'Nama Program', 
                'rules' => 'required|is_unique_name[programs]'
            ],
            'percentage' => [
                'label' => 'Persentase', 
                'rules' => 'required|valid_percentage'
            ],
            'type' => [
                'label' => 'Tipe', 
                'rules' => 'required|valid_transaction_type[routine,incidental]'
            ],
        ];

        validate($data_post, $validation);

        $array_insert = [
            'name' => $data_post['name'],
            'branches_id' => $branch_id,
            'percentage' => $data_post['percentage'],
            'type' => $data_post['type'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $lastId = DatabaseModel::insertData('programs', $array_insert);
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
        $branch_id =  session()->get('branch_id');

        $validation = [
            'id' => [
                'label' => 'ID Program', 
                'rules' => 'required'
            ],
            'name' => [
                'label' => 'Nama Program', 
                'rules' => 'required'
            ],
            'percentage' => [
                'label' => 'Persentase', 
                'rules' => 'required|valid_percentage'
            ],
            'type' => [
                'label' => 'Tipe', 
                'rules' => 'required|valid_transaction_type[routine,incidental]'
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
            'branches_id' => $branch_id,
            'percentage' => $data_post['percentage'],
            'type' => $data_post['type'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Where condition
        $where_condition = ['id' => $decrypted_id];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Use the correct parameter order for updateData
            $result = DatabaseModel::updateData('programs', $where_condition, $array_update);

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
                'error_string' => ['The ID Program field is required.']
            ]);
        }

        // Validate the ID field
        $validation = [
            'id' => [
                'label' => 'ID Program', 
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
            
            $is_used_in_transactions = DatabaseModel::get([
                'select' => 'COUNT(*) as count',
                'from' => 'transactions',
                'where' => [
                    'program_id' => $decrypted_id,
                    'is_deleted' => false
                ]
            ])->getRow()->count > 0;
            
            if ($is_used_in_transactions) {
                return response()->setJSON([
                    'status' => false, 
                    'message' => 'Program ini tidak dapat dihapus karena sudah digunakan dalam transaksi'
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
            $record = $db->table('programs')
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
                'programs',
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