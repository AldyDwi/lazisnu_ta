<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Program_Allocation extends BaseController
{
    protected $folder_directory = 'admin\\page\\program_allocation\\view\\';
    protected $js = ['index'];

    public function __construct() {}

    public function index()
    {
        $data_allocations = DatabaseModel::get([
            'select' => 'a.*, b.name as program_name',
            'from' => 'program_allocation a',
            'where' => ['a.is_deleted' => false],
            'join' => [
                'programs b, b.id = a.program_id, left',
            ]
        ])->getResultObject();

        $data['data_allocations'] = $data_allocations;
        $data['page_title'] = 'Alokasi Dana Program | Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        is_ajax();

        $data_allocations = DatabaseModel::get([
            'select' => 'a.*, b.name as program_name',
            'from' => 'program_allocation a',
            'where' => ['a.is_deleted' => false],
            'join' => [
                'programs b, b.id = a.program_id, left',
            ]
        ])->getResultObject();

        $data = [];
        foreach ($data_allocations as $data_table) {
            $encrypted_id = encrypt_data($data_table->id);
            
            $row = [];
            $row[] = $data_table->id;
            $row[] = $data_table->program_name;
            $row[] = 'Rp ' . number_format($data_table->amount, 0, ',', '.');
            $row[] = date('d-m-Y', strtotime($data_table->date));
            $row[] = '
                <button id="btnEdit" class="btn btn-warning text-white fw-semibold" 
                    data-id="'.$encrypted_id.'" 
                    data-program_id="'.$data_table->program_id.'"
                    data-program_name="'.$data_table->program_name.'"
                    data-amount="'.$data_table->amount.'"
                    data-date="'.$data_table->date.'">
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

        if (isset($data_post['amount'])) {
            $data_post['amount'] = preg_replace('/[^0-9,]/', '', $data_post['amount']);
            $data_post['amount'] = str_replace(',', '.', $data_post['amount']);
        }

        $validation = [
            'program_id' => [
                'label' => 'Program', 
                'rules' => 'required|valid_reference[programs,id,Program tidak valid atau tidak ditemukan]'
            ],
            'amount' => [
                'label' => 'Jumlah', 
                'rules' => 'required|valid_decimal'
            ],
            'date' => [
                'label' => 'Tanggal', 
                'rules' => 'required|valid_date'
            ],
        ];

        validate($data_post, $validation);

        // Konversi format angka
        // if (strpos($data_post['amount'], ',') !== false || strpos($data_post['amount'], '.') !== false) {
        //     $data_post['amount'] = str_replace('.', '', $data_post['amount']);
        //     $data_post['amount'] = str_replace(',', '.', $data_post['amount']);
        // }

        $array_insert = [
            'program_id' => $data_post['program_id'],
            'amount' => $data_post['amount'],
            'date' => $data_post['date'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $lastId = DatabaseModel::insertData('program_allocation', $array_insert);
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

        if (isset($data_post['amount'])) {
            $data_post['amount'] = preg_replace('/[^0-9,]/', '', $data_post['amount']);
            $data_post['amount'] = str_replace(',', '.', $data_post['amount']);
        }

        $validation = [
            'id' => [
                'label' => 'ID Alokasi', 
                'rules' => 'required'
            ],
            'program_id' => [
                'label' => 'Program', 
                'rules' => 'required|valid_reference[programs,id,Program tidak valid atau tidak ditemukan]'
            ],
            'amount' => [
                'label' => 'Jumlah', 
                'rules' => 'required|valid_decimal'
            ],
            'date' => [
                'label' => 'Tanggal', 
                'rules' => 'required|valid_date'
            ],
        ];

        validate($data_post, $validation);

        // Decrypt ID
        $decrypted_id = $this->decrypt($data_post['id']);

        if (!$decrypted_id) {
            return response()->setJSON(['status' => false, 'message' => 'Invalid ID']);
        }

        // // Konversi format angka
        // if (strpos($data_post['amount'], ',') !== false || strpos($data_post['amount'], '.') !== false) {
        //     $data_post['amount'] = str_replace('.', '', $data_post['amount']);
        //     $data_post['amount'] = str_replace(',', '.', $data_post['amount']);
        // }

        $array_update = [
            'program_id' => $data_post['program_id'],
            'amount' => $data_post['amount'],
            'date' => $data_post['date'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Where condition
        $where_condition = ['id' => $decrypted_id];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Use the correct parameter order for updateData
            $result = DatabaseModel::updateData('program_allocation', $where_condition, $array_update);

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
                'error_string' => ['The ID Alokasi field is required.']
            ]);
        }

        // Validate the ID field
        $validation = [
            'id' => [
                'label' => 'ID Alokasi', 
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
            
            // // Periksa apakah alokasi ini sudah digunakan dalam transaksi
            // $used_in_transactions = DatabaseModel::get([
            //     'from' => 'transactions',
            //     'where' => [
            //         'allocation_id' => $decrypted_id,
            //         'is_deleted' => false
            //     ]
            // ])->getNumRows();
            
            // if ($used_in_transactions > 0) {
            //     return response()->setJSON([
            //         'status' => false, 
            //         'message' => 'Alokasi ini tidak dapat dihapus karena sudah digunakan dalam transaksi'
            //     ]);
            // }
            
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
            $record = $db->table('program_allocation')
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
                'program_allocation',
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