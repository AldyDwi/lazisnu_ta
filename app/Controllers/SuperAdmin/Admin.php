<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Admin extends BaseController
{
    protected $folder_directory = 'super_admin\\page\\admin\\view\\';
    protected $js = ['index'];

    protected $db;

    public function __construct() {
        $this->db = new DatabaseModel();
    }

    public function index()
    {
        $data_users = $this->db->get([
            'select' => 'a.*, b.name as rw_name, c.name as branch_name, d.name as region_name',
            'from' => 'users a',
            'where' => [
                'a.is_deleted' => false,
                'a.role' => 'admin',
            ],
            'join' => [
                'rws b, b.id = a.rw_id, left',
                'branches c, c.id = b.branch_id, left',
                'region d, d.id = c.region_id, left'
            ]
        ])->getResultObject();

        $data['data_users'] = $data_users;
        $data['page_title'] = 'Admin | Super-Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        is_ajax();

        $data_users = $this->db->get([
            'select' => 'a.*, b.name as rw_name, c.name as branch_name, d.name as region_name',
            'from' => 'users a',
            'where' => [
                'a.is_deleted' => false,
                'a.role' => 'admin',
            ],
            'join' => [
                'rws b, b.id = a.rw_id, left',
                'branches c, c.id = b.branch_id, left',
                'region d, d.id = c.region_id, left'
            ]
        ])->getResultObject();

        $data = [];
        foreach ($data_users as $data_table) {
            $encrypted_id = encrypt_data($data_table->id);

            $row = [];
            $row[] = $data_table->id;
            $row[] = $data_table->name;
            $row[] = $data_table->username;
            $row[] = $data_table->region_name;
            $row[] = $data_table->branch_name;
            $row[] = $data_table->gender;
            $row[] = $data_table->phone;
            $row[] = $data_table->email;
            $row[] = '
                <button id="btnEdit" class="btn btn-warning text-white fw-semibold my-1" 
                    data-id="' . $encrypted_id . '" 
                    data-name="' . $data_table->name . '"
                    data-username="' . $data_table->username . '"
                    data-rw_id="' . $data_table->rw_id . '"
                    data-rw_name="' . $data_table->rw_name . '"
                    data-region_name="' . $data_table->region_name . '"
                    data-branch_name="' . $data_table->branch_name . '"
                    data-gender="' . $data_table->gender . '"
                    data-phone="' . $data_table->phone . '"
                    data-email="' . $data_table->email . '">
                    Ubah 
                </button>
                <button id="btnDelete" class="btn btn-danger text-white fw-semibold my-1" 
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
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email'
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[6]'
            ],
            'confirm_password' => [
                'label' => 'Konfirmasi Password',
                'rules' => 'required|matches[password]'
            ],
            'name' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|min_length[3]|max_length[100]'
            ],
            'phone' => [
                'label' => 'Nomor Telepon',
                'rules' => 'required|numeric|min_length[10]|max_length[20]'
            ],
            'gender' => [
                'label' => 'Jenis Kelamin',
                'rules' => 'required|in_list[male,female]'
            ],
            'rw_id' => [
                'label' => 'RW',
                'rules' => 'required|valid_reference[rws,id,Data RW tidak valid atau tidak ditemukan]'
            ],
        ];

        validate($data_post, $validation);

        $db = \Config\Database::connect();
        $db->transStart();

        $username = strtolower(str_replace(' ', '', explode(' ', $data_post['name'])[0])) . rand(1000, 9999);

        try {
            $array_insert = [
                'username' => $username,
                'email' => $data_post['email'],
                'password' => password_hash($data_post['password'], PASSWORD_BCRYPT),
                'name' => $data_post['name'],
                'phone' => $data_post['phone'],
                'gender' => $data_post['gender'],
                'role' => 'admin',
                'rw_id' => $data_post['rw_id'],
                'is_active' => true,
                'is_deleted' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $lastId = DatabaseModel::insertData('users', $array_insert);
            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return response()->setJSON(['status' => false, 'message' => 'Gagal mendaftarkan petugas baru']);
            }
            
            // Enkripsi ID
            $encrypted_id = encrypt_data($lastId);
            
        } catch (\Exception $e) {
            $db->transRollback();
            return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }

        return response()->setJSON([
            'status' => true,
            'message' => 'Tambah petugas berhasil',
            'id' => $encrypted_id
        ]);
    }

    public function update()
    {
        is_ajax();
        $data_post = $this->request->getPost();

        $validation = [
            'id' => [
                'label' => 'ID Pengguna', 
                'rules' => 'required'
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email'
            ],
            'name' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|min_length[3]|max_length[100]'
            ],
            'phone' => [
                'label' => 'Nomor Telepon',
                'rules' => 'required|numeric|min_length[10]|max_length[20]'
            ],
            'gender' => [
                'label' => 'Jenis Kelamin',
                'rules' => 'required|in_list[male,female]'
            ],
            'rw_id' => [
                'label' => 'RW',
                'rules' => 'required|valid_reference[rws,id,Data RW tidak valid atau tidak ditemukan]'
            ],
        ];

        validate($data_post, $validation);

        // Decrypt ID
        $decrypted_id = decrypt_data($data_post['id']);
        
        if (!$decrypted_id) {
            return response()->setJSON(['status' => false, 'message' => 'Invalid ID']);
        }

        $username = strtolower(str_replace(' ', '', explode(' ', $data_post['name'])[0])) . rand(1000, 9999);

        $array_update = [
            'email' => $data_post['email'],
            'name' => $data_post['name'],
            'username' => $username,
            'phone' => $data_post['phone'],
            'gender' => $data_post['gender'],
            'rw_id' => $data_post['rw_id'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!empty($data_post['password'])) {
            $array_update['password'] = password_hash($data_post['password'], PASSWORD_BCRYPT);
        }
        
        // Where condition
        $where_condition = ['id' => $decrypted_id];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update data pengguna
            $result = DatabaseModel::updateData('users', $where_condition, $array_update);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return response()->setJSON(['status' => false, 'message' => 'Gagal memperbarui data petugas']);
            }
        } catch (\Exception $e) {
            $db->transRollback();
            return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }

        return response()->setJSON(['status' => true, 'message' => 'Data petugas berhasil diperbarui']);
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
                'error_string' => ['The ID Petugas field is required.']
            ]);
        }

        // Validate the ID field
        $validation = [
            'id' => [
                'label' => 'ID Petugas', 
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
                    'user_id' => $decrypted_id,
                    'is_deleted' => false
                ]
            ])->getRow()->count > 0;
            
            if ($is_used_in_transactions) {
                return response()->setJSON([
                    'status' => false, 
                    'message' => 'Petugas ini tidak dapat dihapus karena sudah digunakan dalam transaksi'
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
            $record = $db->table('users')
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
                'users',
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
