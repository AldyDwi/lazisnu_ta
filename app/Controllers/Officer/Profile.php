<?php

namespace App\Controllers\Officer;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Profile extends BaseController
{
    protected $folder_directory = 'officer\\page\\profile\\view\\';
    protected $js = ['index'];

    protected $db;

    public function __construct() {
        $this->db = new DatabaseModel();
    }

    public function index()
    {
        $data['page_title'] = 'Profil | Officer';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        is_ajax();

        $user_id = session()->get('user_id');

        $data_users = $this->db->get([
            'select' => 'a.*, b.id as rw_id, b.name as rw_name, c.name as branch_name',
            'from' => 'users a',
            'where' => [
                'a.is_deleted' => false,
                'a.id' => $user_id,
            ],
            'join' => [
                'rws b, b.id = a.rw_id, left',
                'branches c, c.id = b.branch_id, left'
            ]
        ])->getResultObject();

        $data = [];
        foreach ($data_users as $data_table) {
            $encrypted_id = encrypt_data($data_table->id);

            $row = [];
            $row[] = $data_table->id;
            $row[] = $data_table->name;
            $row[] = $data_table->username;
            $row[] = $data_table->gender;
            $row[] = $data_table->phone;
            $row[] = $data_table->email;
            $row[] = $data_table->role;
            $row[] = $data_table->branch_name;
            $row[] = $data_table->rw_name;
            $row[] = '
                
                    <button id="btnEdit" class="text-white btn btn-primary fw-bold" 
                        data-id="' . $encrypted_id . '" 
                        data-name="' . $data_table->name . '"
                        data-username="' . $data_table->username . '"
                        data-gender="' . $data_table->gender . '"
                        data-phone="' . $data_table->phone . '"
                        data-email="' . $data_table->email . '">
                        Edit Profil 
                    </button>
                    <button id="btnEditPassword" class="btn btn-warning fw-bold"
                    data-id="' . $encrypted_id . '" >Edit Password</button>
                
            ';

            $data[] = $row;
        }

        $response = [
            'data' => $data,
        ];

        return response()->setJSON($response);
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
                return response()->setJSON(['status' => false, 'message' => 'Gagal memperbarui data admin']);
            }
        } catch (\Exception $e) {
            $db->transRollback();
            return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }

        return response()->setJSON(['status' => true, 'message' => 'Data admin berhasil diperbarui']);
    }
}
