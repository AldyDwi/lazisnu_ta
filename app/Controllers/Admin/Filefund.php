<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Filefund extends BaseController
{
    protected $folder_directory = 'admin\\page\\file_fund\\view\\';
    protected $js = ['index'];

    public function __construct() {}

    public function index()
    {
        $data_file_funds = DatabaseModel::get([
            'select' => 'a.*, b.name as transaction_name',
            'from' => 'file_fund a',
            'where' => ['a.is_deleted' => false],
            'join' => [
                'transactions b, b.id = a.transaction_id, left',
            ]
        ])->getResultObject();

        $data['data_file_funds'] = $data_file_funds;
        $data['page_title'] = 'File Funds';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        is_ajax();

        $data_file_funds = DatabaseModel::get([
            'select' => 'a.*, b.name as transaction_name',
            'from' => 'file_fund a',
            'where' => ['a.is_deleted' => false],
            'join' => [
                'transactions b, b.id = a.transaction_id, left',
            ]
        ])->getResultObject();
        
        $data = [];
        foreach ($data_file_funds as $data_table) {
            $encrypted_id = encrypt_data($data_table->id);
            
            // Tentukan folder berdasarkan tipe file
            $folder = 'uploads/';
            if (strpos($data_table->type, 'image') !== false) {
                $folder .= 'images/';
            } elseif (strpos($data_table->type, 'video') !== false) {
                $folder .= 'videos/';
            }
            
            // Cek dan tampilkan gambar jika tipe file adalah gambar
            $file_preview = '';
            if (strpos($data_table->type, 'image') !== false) {
                $image_url = check_image($data_table->filename, $folder, 'default.webp', true);
                $file_preview = '<img src="'.$image_url.'" class="img-thumbnail" style="max-width:100px" alt="Preview">';
            } else {
                $file_preview = '<span><i class="fas fa-file"></i> '.$data_table->filename.'</span>';
            }
            
            $row = [];
            $row[] = $data_table->id;
            $row[] = $data_table->transaction_name;
            $row[] = $file_preview;
            $row[] = $data_table->type;
            $row[] = '
                <button id="btnEdit" class="btn btn-warning text-white fw-semibold" 
                    data-id="'.$encrypted_id.'" 
                    data-transaction_id="'.$data_table->transaction_id.'"
                    data-filename="'.$data_table->filename.'">
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
            'transaction_id' => [
                'label' => 'Transaksi', 
                'rules' => 'required|valid_reference[transactions,id,Transaksi yang dipilih tidak valid]|transaction_not_closed[Transaksi sudah ditutup, tidak dapat menambahkan file]'
            ],
        ];

        validate($data_post, $validation);

        // Tentukan folder upload berdasarkan tipe file
        $file = $this->request->getFile('file');
        if (!$file->isValid()) {
            return response()->setJSON(['status' => false, 'message' => 'File wajib diunggah']);
        }

        $fileType = $file->getClientMimeType();
        $upload_path = '';

        // Determine the folder based on file type
        if (strpos($fileType, 'image') !== false) {
            $upload_path = WRITEPATH . 'uploads/images';
            $compress = true;
        } elseif (strpos($fileType, 'video') !== false) {
            $upload_path = WRITEPATH . 'uploads/videos';
            $compress = false;
        } else {
            return response()->setJSON(['status' => false, 'message' => 'Tipe file tidak diizinkan. Hanya file gambar dan video yang diperbolehkan']);
        }

        // Upload file menggunakan helper
        $file_result = upload_file('file', $upload_path, $compress);
        
        if (is_array($file_result) && isset($file_result['error'])) {
            return response()->setJSON(['status' => false, 'message' => $file_result['msg']]);
        }

        $array_insert = [
            'transaction_id' => $data_post['transaction_id'],
            'filename' => $file_result,
            'type' => $fileType,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $lastId = DatabaseModel::insertData('file_fund', $array_insert);
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
                'label' => 'ID File', 
                'rules' => 'required'
            ],
            'transaction_id' => [
                'label' => 'Transaksi', 
                'rules' => 'required|valid_reference[transactions,id,Transaksi yang dipilih tidak valid]|transaction_not_closed[Transaksi sudah ditutup, tidak dapat mengubah file]'
            ],
        ];

        validate($data_post, $validation);

        // Decrypt ID
        $decrypted_id = $this->decrypt($data_post['id']);
        
        if (!$decrypted_id) {
            return response()->setJSON(['status' => false, 'message' => 'Invalid ID']);
        }

        $array_update = [
            'transaction_id' => $data_post['transaction_id'],
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Where condition
        $where_condition = ['id' => $decrypted_id];

        // Handle file upload if present
        $file = $this->request->getFile('file');
        if ($file && $file->isValid()) {
            $fileType = $file->getClientMimeType();
            $upload_path = '';

            // Determine the folder based on file type
            if (strpos($fileType, 'image') !== false) {
                $upload_path = WRITEPATH . 'uploads/images';
                $compress = true;
            } elseif (strpos($fileType, 'video') !== false) {
                $upload_path = WRITEPATH . 'uploads/videos';
                $compress = false;
            } else {
                return response()->setJSON(['status' => false, 'message' => 'Tipe file tidak diizinkan. Hanya file gambar dan video yang diperbolehkan']);
            }

            // Ambil file lama sebelum update
            $old_file = DatabaseModel::get([
                'select' => 'filename, type',
                'from' => 'file_fund',
                'where' => ['id' => $decrypted_id]
            ])->getRow();

            // Upload file menggunakan helper
            $file_result = upload_file('file', $upload_path, $compress);
            
            if (is_array($file_result) && isset($file_result['error'])) {
                return response()->setJSON(['status' => false, 'message' => $file_result['msg']]);
            }

            $array_update['filename'] = $file_result;
            $array_update['type'] = $fileType;
            
            // Hapus file lama jika berhasil upload
            if ($old_file) {
                $old_path = '';
                if (strpos($old_file->type, 'image') !== false) {
                    $old_path = WRITEPATH . 'uploads/images/' . $old_file->filename;
                } elseif (strpos($old_file->type, 'video') !== false) {
                    $old_path = WRITEPATH . 'uploads/videos/' . $old_file->filename;
                }
                
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Use the correct parameter order for updateData
            $result = DatabaseModel::updateData('file_fund', $where_condition, $array_update);
            
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
                'error_string' => ['The ID File field is required.']
            ]);
        }

        // Validate the ID field
        $validation = [
            'id' => [
                'label' => 'ID File', 
                'rules' => 'required'
            ],
        ];

        // Use try-catch to handle validation errors
        try {
            validate($data_post, $validation);
            
            // Decrypt ID untuk validasi
            $decrypted_id = $this->decrypt($data_post['id']);
            
            // Periksa apakah transaksi masih terbuka
            $file_data = DatabaseModel::get([
                'select' => 'f.id, t.id as transaction_id, t.status',
                'from' => 'file_fund f',
                'where' => ['f.id' => $decrypted_id, 'f.is_deleted' => false],
                'join' => ['transactions t, t.id = f.transaction_id, left']
            ])->getRow();
            
            if ($file_data && $file_data->status === 'close') {
                return response()->setJSON([
                    'status' => false, 
                    'message' => 'Transaksi sudah ditutup, tidak dapat menghapus file'
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
            $record = $db->table('file_fund')
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
                'file_fund',
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