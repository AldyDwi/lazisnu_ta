<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Branches extends BaseController
{
    protected $folder_directory = 'super_admin\\page\\branches\\view\\';
    protected $js = ['index'];

    public function __construct() {}

    public function index()
    {
        $data_branches = DatabaseModel::get([
            'select' => 'a.*, b.name as region_name',
            'from' => 'branches a',
            'where' => ['a.is_deleted' => false],
            'join' => [
                'region b, b.id = a.region_id, left',
            ]
        ])->getResultObject();

        $data['data_branches'] = $data_branches;
        $data['page_title'] = 'Ranting | Super-Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        is_ajax();

        $data_branches = DatabaseModel::get([
            'select' => 'a.*, b.name as region_name, CONCAT(a.name, ", ", b.name) AS dropdown',
            'from' => 'branches a',
            'where' => ['a.is_deleted' => false],
            'join' => [
                'region b, b.id = a.region_id, left',
            ]
        ])->getResultObject();

        $data = [];
        foreach ($data_branches as $data_table) {
            $encrypted_id = encrypt_data($data_table->id);

            $row = [];
            $row[] = $data_table->id;
            $row[] = $data_table->dropdown;
            $row[] = $data_table->name;
            $row[] = $data_table->region_name;
            $row[] = $data_table->address;
            $row[] = '
                <button id="btnEdit" class="btn btn-warning text-white fw-semibold" 
                    data-id="' . $encrypted_id . '" 
                    data-name="' . $data_table->name . '"
                    data-address="' . $data_table->address . '"
                    data-region_id="' . $data_table->region_id . '"
                    data-region_name="' . $data_table->region_name . '">
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
        is_ajax();
        $data_post = $this->request->getPost();

        $validation = [
            'name' => [
                'label' => 'Nama Cabang',
                'rules' => 'required|is_unique_name[branches]'
            ],
            'region_id' => [
                'label' => 'Region',
                'rules' => 'required|valid_reference[region,id,ID Region tidak valid]'
            ],
            'address' => [
                'label' => 'Alamat Lengkap',
                'rules' => 'required'
            ],
        ];

        validate($data_post, $validation);

        $array_insert = [
            'name' => $data_post['name'],
            'region_id' => $data_post['region_id'],
            'address' => $data_post['address'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $lastId = DatabaseModel::insertData('branches', $array_insert);
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
                'label' => 'ID Cabang',
                'rules' => 'required'
            ],
            'name' => [
                'label' => 'Nama Cabang',
                'rules' => 'required'
            ],
            'region_id' => [
                'label' => 'Region',
                'rules' => 'required|valid_reference[region,id,ID Region tidak valid]'
            ],
            'address' => [
                'label' => 'Alamat Lengkap',
                'rules' => 'required'
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
            'region_id' => $data_post['region_id'],
            'address' => $data_post['address'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Tambahkan date_start dan date_end jika ada di data_post
        if (isset($data_post['date_start'])) {
            $array_update['date_start'] = $data_post['date_start'];
        }

        if (isset($data_post['date_end'])) {
            $array_update['date_end'] = $data_post['date_end'];
        }

        // Where condition
        $where_condition = ['id' => $decrypted_id];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Use the correct parameter order for updateData
            $result = DatabaseModel::updateData('branches', $where_condition, $array_update);

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

        // Log incoming data for debugging
        log_message('info', 'Delete request data: ' . json_encode($data_post));

        // Check if ID exists in the request
        if (!isset($data_post['id']) || empty($data_post['id'])) {
            return response()->setJSON([
                'status' => false,
                'inputerror' => ['id'],
                'error_string' => ['The ID Cabang field is required.']
            ]);
        }

        // Validate the ID field
        $validation = [
            'id' => [
                'label' => 'ID Cabang',
                'rules' => 'required'
            ],
        ];

        // Use try-catch to handle validation errors
        try {
            validate($data_post, $validation);

            // Jika perlu validasi ketika cabang digunakan di tabel lain
            $is_used_in_users = DatabaseModel::get([
                'select' => 'COUNT(*) as count',
                'from' => 'rws',
                'where' => [
                    'branch_id' => $this->decrypt($data_post['id']),
                    'is_deleted' => false
                ]
            ])->getRow()->count > 0;

            if ($is_used_in_users) {
                return response()->setJSON([
                    'status' => false,
                    'message' => 'Cabang ini tidak dapat dihapus karena masih digunakan oleh beberapa pengguna'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Validation error: ' . $e->getMessage());
            return response()->setJSON([
                'status' => false,
                'inputerror' => ['id'],
                'error_string' => [$e->getMessage()]
            ]);
        }

        // Decrypt ID
        $decrypted_id = $this->decrypt($data_post['id']);
        log_message('info', 'Decrypted ID for delete: ' . $decrypted_id);

        if (!$decrypted_id) {
            return response()->setJSON([
                'status' => false,
                'message' => 'Invalid ID',
                'inputerror' => ['id'],
                'error_string' => ['ID tidak valid atau tidak dapat didekripsi.']
            ]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // First, verify the record exists
            $record = $db->table('branches')
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
                'branches',
                ['id' => $decrypted_id],
                [
                    'is_deleted' => true,
                    'deleted_at' => date('Y-m-d H:i:s')
                ]
            );

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                log_message('error', 'Transaction failed in delete operation');
                return response()->setJSON([
                    'status' => false,
                    'message' => 'Failed to delete data'
                ]);
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Delete error: ' . $e->getMessage());
            return response()->setJSON([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }

        log_message('info', 'Delete successful for ID: ' . $decrypted_id);
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
