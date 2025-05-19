<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Citizen extends BaseController
{
    protected $folder_directory = 'admin\\page\\citizen\\view\\';
    protected $js = ['index'];

    protected $db;

    public function __construct() {
        $this->db = new DatabaseModel();
    }

    public function index()
    {
        $data_citizens = DatabaseModel::get([
            'select' => 'a.*, b.name as rw_name, c.name as region_name',
            'from' => 'citizens a',
            'where' => ['a.is_deleted' => false],
            'join' => [
                'rws b, b.id = a.rw_id, left',
                'branches d, d.id = b.branch_id, left',
                'region c, c.id = d.region_id, left'
            ]
        ])->getResultObject();

        $data['data_citizens'] = $data_citizens;
        $data['page_title'] = 'Warga | Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        // is_ajax();
        $branch_id =  session()->get('branch_id');

        $data_citizens = DatabaseModel::get([
            'select' => 'a.*, b.name as rw_name, c.name as region_name, d.name as branch_name',
            'from' => 'citizens a',
            'where' => [
                'a.is_deleted' => false,
                'a.rw_id IS NOT NULL' => null,
                'd.id' => $branch_id
            ],
            'join' => [
                'rws b, b.id = a.rw_id, left',
                'branches d, d.id = b.branch_id, left',
                'region c, c.id = d.region_id, left'
            ]
        ])->getResultObject();

        $data = [];
        foreach ($data_citizens as $data_table) {
            $encrypted_id = encrypt_data($data_table->id);
            
            $row = [];
            $row[] = $data_table->id;
            $row[] = $data_table->name;
            $row[] = $data_table->phone;
            $row[] = $data_table->rw_name;
            $row[] = $data_table->region_name;
            $row[] = $data_table->branch_name;
            $row[] = $data_table->status;
            $row[] = '
                <button id="btnEdit" class="btn btn-warning text-white fw-semibold my-1" 
                    data-id="'.$encrypted_id.'" 
                    data-name="'.$data_table->name.'"
                    data-phone="'.$data_table->phone.'"
                    data-rw_id="'.$data_table->rw_id.'"
                    data-rw_name="'.$data_table->rw_name.'">
                    Ubah 
                </button>
                <button id="btnDelete" class="btn btn-danger text-white fw-semibold my-1" 
                    data-id="'.$encrypted_id.'"
                    data-rw_id="'.$data_table->rw_id.'">
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

    public function list_data_not_rw()
    {
        // is_ajax();

        $data_citizens = DatabaseModel::get([
            'select' => 'a.*, b.name as rw_name, c.name as region_name, d.name as branch_name',
            'from' => 'citizens a',
            'where' => [
                'a.is_deleted' => false,
                'a.rw_id IS NULL' => null
            ],
            'join' => [
                'rws b, b.id = a.rw_id, left',
                'branches d, d.id = b.branch_id, left',
                'region c, c.id = d.region_id, left'
            ]
        ])->getResultObject();

        $data = [];
        foreach ($data_citizens as $data_table) {
            $encrypted_id = encrypt_data($data_table->id);
            
            $row = [];
            $row[] = $data_table->id;
            $row[] = $data_table->name;
            $row[] = $data_table->phone;
            $row[] = $data_table->address;
            $row[] = $data_table->status;
            $row[] = '
                <button id="btnEdit" class="btn btn-warning text-white fw-semibold my-1" 
                    data-id="'.$encrypted_id.'" 
                    data-name="'.$data_table->name.'"
                    data-phone="'.$data_table->phone.'"
                    data-address="'.$data_table->address.'">
                    Ubah 
                </button>
                <button id="btnDelete" class="btn btn-danger text-white fw-semibold my-1" 
                    data-id="'.$encrypted_id.'"
                    data-rw_id="'.$data_table->rw_id.'">
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

    public function get_data()
    {
        try {
            $citizens = $this->db->get([
                'select' => 'a.*, b.name as rw_name',
                'from' => 'citizens a',
                'join' => [
                    'rws b, b.id = a.rw_id, left'
                ],
                'where' => ['a.is_deleted' => 0], // Only get non-deleted records
            ])->getResult();
            log_message('info', 'Citizens data retrieved successfully: ' . json_encode($citizens));
            return $this->response->setJSON(['status' => ResponseInterface::HTTP_OK, 'data' => $citizens]);
        } catch (\Exception $e) {
            log_message('error', 'Error retrieving citizens data: ' . $e->getMessage());
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['status' => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Error retrieving citizens data']);
        }
    }

    public function dropdown()
    {
        // is_ajax();
        $branch_id =  session()->get('branch_id');

        $data_rws = DatabaseModel::get([
        'select' => 'a.*',
        'from' => 'rws a',
        'where' => [
            'a.is_deleted' => false,
            'a.branch_id' => $branch_id
        ],
        ])->getResultObject();

        $data = [];
        foreach ($data_rws as $data_table) {
        $encrypted_id = encrypt_data($data_table->id);

        $row = [];
        $row[] = $data_table->id;
        $row[] = $data_table->name;

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
                'label' => 'Nama Warga', 
                'rules' => 'required|is_unique_name[citizens]'
            ],
            'rw_id' => [
                'label' => 'RW', 
                'rules' => 'permit_empty|valid_reference[rws,id,RW yang dipilih tidak valid]'
            ],
            'phone' => [
                'label' => 'Nomor Telepon', 
                'rules' => 'required|numeric'
            ],
            'address' => [
                'label' => 'Alamat', 
                'rules' => 'permit_empty'
            ],
        ];

        validate($data_post, $validation);

        $array_insert = [
            'name' => $data_post['name'],
            'rw_id' => isset($data_post['rw_id']) ? $data_post['rw_id'] : null,
            'phone' => isset($data_post['phone']) ? $data_post['phone'] : null,
            'address' => isset($data_post['address']) ? $data_post['address'] : null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $lastId = DatabaseModel::insertData('citizens', $array_insert);
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

        return response()->setJSON(['status' => TRUE, 'message' => 'Data berhasil disimpan', 'id' => $lastId]);
    }

    public function update()
    {
        // is_ajax();
        $data_post = $this->request->getPost();

        $validation = [
            'id' => [
                'label' => 'ID Warga', 
                'rules' => 'required'
            ],
            'name' => [
                'label' => 'Nama Warga', 
                'rules' => 'required'
            ],
            'rw_id' => [
                'label' => 'RW', 
                'rules' => 'permit_empty|valid_reference[rws,id,RW yang dipilih tidak valid]'
            ],
            'phone' => [
                'label' => 'Nomor Telepon', 
                'rules' => 'required|numeric'
            ],
            'address' => [
                'label' => 'Alamat', 
                'rules' => 'permit_empty'
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
            'rw_id' => isset($data_post['rw_id']) ? $data_post['rw_id'] : null,
            'phone' => isset($data_post['phone']) ? $data_post['phone'] : null,
            'address' => isset($data_post['address']) ? $data_post['address'] : null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Where condition
        $where_condition = ['id' => $decrypted_id];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Use the correct parameter order for updateData
            $result = DatabaseModel::updateData('citizens', $where_condition, $array_update);

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

        // Log incoming data for debugging
        log_message('info', 'Delete request data: ' . json_encode($data_post));

        // Check if ID exists in the request
        if (!isset($data_post['id']) || empty($data_post['id'])) {
            return response()->setJSON([
                'status' => false,
                'inputerror' => ['id'],
                'error_string' => ['The ID Warga field is required.']
            ]);
        }

        // Validate the ID field
        $validation = [
            'id' => [
                'label' => 'ID Warga', 
                'rules' => 'required'
            ],
        ];

        // Use try-catch to handle validation errors
        try {
            validate($data_post, $validation);
            
            // Validasi jika warga digunakan di tabel lain
            $decrypted_id = $this->decrypt($data_post['id']);
            
            $is_used_in_donations = DatabaseModel::get([
                'select' => 'COUNT(*) as count',
                'from' => 'transactions',
                'where' => [
                    'citizen_id' => $decrypted_id,
                    'is_deleted' => false
                ]
            ])->getRow()->count > 0;
            
            if ($is_used_in_donations) {
                return response()->setJSON([
                    'status' => false, 
                    'message' => 'Warga ini tidak dapat dihapus karena masih memiliki data donasi'
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
            $record = $db->table('citizens')
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
                'citizens',
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