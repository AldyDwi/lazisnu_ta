<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Beneficaries extends BaseController
{
    protected $folder_directory = 'super_admin\\page\\beneficaries\\view\\';
    protected $js = ['index'];

    protected $db;

    public function __construct() {
        $this->db = new DatabaseModel();
    }

    public function index()
    {
        $data_beneficaries = $this->db->get([
        'select' => 'a.*, b.name as branch_name',
        'from' => 'beneficaries a',
        'where' => ['a.is_deleted' => false],
        'join' => [
            'branches b, b.id = a.branches_id, left',
        ]
        ])->getResultObject();

        $data['data_beneficaries'] = $data_beneficaries;
        $data['page_title'] = 'Penerima Manfaat | Super-Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        // is_ajax();

        $data_beneficaries = $this->db->get([
            'select' => 'a.*, b.name as branch_name, c.name as type_name',
            'from' => 'beneficaries a',
            'where' => ['a.is_deleted' => false],
            'join' => [
            'branches b, b.id = a.branches_id, left',
            'beneficaries_type c, c.id = a.type_id, left',
            ]
        ])->getResultObject();

        $data = [];
        foreach ($data_beneficaries as $data_table) {
        $encrypted_id = encrypt_data($data_table->id);

        $row = [];
        $row[] = $data_table->id;
        $row[] = $data_table->name;
        $row[] = $data_table->type_name;
        $row[] = $data_table->branch_name;
        $row[] = '
                    <button id="btnEdit" class="btn btn-warning text-white fw-semibold" 
                        data-id="' . $encrypted_id . '" 
                        data-name="' . $data_table->name . '"
                        data-type_id="' . $data_table->type_id . '"
                        data-type_name="' . $data_table->type_name . '"
                        data-branch_id="' . $data_table->branches_id . '"
                        data-branch_name="' . $data_table->branch_name . '">
                        Ubah 
                    </button>
                    <button id="btnDelete" class="btn btn-danger text-white fw-semibold" 
                        data-id="' . $encrypted_id . '">
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
        // is_ajax();
        $data_post = $this->request->getPost();

        $validation = [
            'name' => [
                'label' => 'Nama RW', 
                'rules' => 'required'
                ],
            'branch_id' => [
                'label' => 'Cabang', 
                'rules' => 'required|valid_reference[branches,id,Cabang tidak valid atau tidak ditemukan]'
                ],
            'type_id' => [
                    'label' => 'Tipe', 
                    'rules' => 'required|valid_reference[beneficaries_type,id,Tipe Penerima tidak valid atau tidak ditemukan]'
                ],
        ];

        validate($data_post, $validation);

        $array_insert = [
        'name' => $data_post['name'],
        'type_id' => $data_post['type_id'],
        'branches_id' => $data_post['branch_id'],
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
        $lastId = DatabaseModel::insertData('beneficaries', $array_insert);
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
        // is_ajax();
        $data_post = $this->request->getPost();

        $validation = [
        'id' => [
            'label' => 'ID Penerima Manfaat', 
            'rules' => 'required'
        ],
        'name' => [
            'label' => 'Nama Penerima', 
            'rules' => 'required'
        ],
        'branch_id' => [
            'label' => 'Ranting', 
            'rules' => 'required|valid_reference[branches,id,Ranting tidak valid atau tidak ditemukan]'
        ],
        'type_id' => [
            'label' => 'Tipe', 
            'rules' => 'required|valid_reference[beneficaries_type,id,Tipe Penerima tidak valid atau tidak ditemukan]'
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
        'type_id' => $data_post['type_id'],
        'branches_id' => $data_post['branch_id'],
        'updated_at' => date('Y-m-d H:i:s')
        ];

        // Where condition
        $where_condition = ['id' => $decrypted_id];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
        // Use the correct parameter order for updateData
        $result = DatabaseModel::updateData('beneficaries', $where_condition, $array_update);

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
        // is_ajax();
        $data_post = $this->request->getPost();

        // Check if ID exists in the request
        if (!isset($data_post['id']) || empty($data_post['id'])) {
        return response()->setJSON([
            'status' => false,
            'inputerror' => ['id'],
            'error_string' => ['The ID Beneficaries field is required.']
        ]);
        }

        // Validate the ID field
        $validation = [
        'id' => [
            'label' => 'ID Penerima Manfaat', 
            'rules' => 'required'
        ]
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
        
        $is_used_in_detail_fund = DatabaseModel::get([
            'select' => 'COUNT(*) as count',
            'from' => 'detail_fund',
            'where' => [
            'beneficaries_id' => $decrypted_id,
            'is_deleted' => false
            ]
        ])->getRow()->count > 0;
        
        if ($is_used_in_detail_fund) {
            return response()->setJSON([
            'status' => false, 
            'message' => 'Data penerima manfaat tidak dapat dihapus karena masih digunakan di data detail dana.'
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
        $record = $db->table('beneficaries')
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
            'beneficaries',
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
}
