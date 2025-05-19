<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Beneficaries_Type extends BaseController
{
    protected $folder_directory = 'super_admin\\page\\beneficaries_type\\view\\';
    protected $js = ['index'];

    public function __construct() {}

    public function index()
    {
        $data_beneficaries_type = DatabaseModel::get([
            'select' => '*',
            'from' => 'beneficaries_type',
            'where' => ['is_deleted' => false]
        ])->getResultObject();

        $data['data_beneficaries_type'] = $data_beneficaries_type;
        $data['page_title'] = 'Tipe Penerima | Super-Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        is_ajax();

        $data_beneficaries_type = DatabaseModel::get([
            'select' => '*',
            'from' => 'beneficaries_type',
            'where' => ['is_deleted' => false]
        ])->getResultObject();

        $data = [];
        foreach ($data_beneficaries_type as $data_table) {
            $encrypted_id = encrypt_data($data_table->id);
            
            $row = [];
            $row[] = $data_table->id;;
            $row[] = $data_table->name;
            $row[] = '
                <button id="btnEdit" class="btn btn-warning text-white fw-semibold" 
                    data-id="'.$encrypted_id.'" 
                    data-name="'.$data_table->name.'">
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
                'label' => 'Nama Tipe Penerima', 
                'rules' => 'required|is_unique_name[beneficaries_type]'
            ]
        ];

        validate($data_post, $validation);

        $array_insert = [
            'name' => $data_post['name'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $lastId = DatabaseModel::insertData('beneficaries_type', $array_insert);
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
                'label' => 'ID Tipe Penerima', 
                'rules' => 'required'
            ],
            'name' => [
                'label' => 'Nama Tipe Penerima', 
                'rules' => 'required'
            ]
        ];

        validate($data_post, $validation);

        // Decrypt ID
        $decrypted_id = $this->decrypt($data_post['id']);

        if (!$decrypted_id) {
            return response()->setJSON(['status' => false, 'message' => 'Invalid ID']);
        }

        $array_update = [
            'name' => $data_post['name'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Where condition
        $where_condition = ['id' => $decrypted_id];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Use the correct parameter order for updateData
            $result = DatabaseModel::updateData('beneficaries_type', $where_condition, $array_update);

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
                'error_string' => ['The ID Beneficaries Type field is required.']
            ]);
        }

        // Validate the ID field
        $validation = [
            'id' => [
                'label' => 'ID Tipe Penerima', 
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
            
            // Check if province is used in beneficaries table
            $is_used_in_beneficaries = DatabaseModel::get([
                'select' => 'COUNT(*) as count',
                'from' => 'beneficaries',
                'where' => [
                    'type_id' => $decrypted_id,
                    'is_deleted' => false
                ]
            ])->getRow()->count > 0;
            
            if ($is_used_in_beneficaries) {
                return response()->setJSON([
                    'status' => false, 
                    'message' => 'Tipe Penerima ini tidak dapat dihapus karena masih digunakan oleh data Penerima Manfaat'
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
            $record = $db->table('beneficaries_type')
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
                'beneficaries_type',
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
