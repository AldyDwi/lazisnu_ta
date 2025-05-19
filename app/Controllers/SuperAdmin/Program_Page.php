<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Program_Page extends BaseController
{
    protected $folder_directory = 'super_admin\\page\\program_page\\view\\';
    protected $js = ['index'];
    protected $db;

    public function __construct() {
        $this->db = new DatabaseModel();
    }

  public function index()
    {
        $data_program_page = $this->db->get([
            'select' => 'a.*, b.name as program_name',
            'from' => 'program_page a',
            'where' => ['a.is_deleted' => false],
            'join' => [
            'programs b, b.id = a.program_id, left',
            ]
        ])->getResultObject();

        $data['data_program_page'] = $data_program_page;
        $data['page_title'] = 'Program Page | Super-Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        is_ajax();

        $data_program_page = $this->db->get([
            'select' => 'a.*, b.name as program_name, c.name as branch_name',
            'from' => 'program_page a',
            'where' => ['a.is_deleted' => false],
            'join' => [
            'programs b, b.id = a.program_id, left',
            'branches c, c.id = b.branches_id, left'
            ]
        ])->getResultObject();

        $data = [];
        foreach ($data_program_page as $data_table) {
        $encrypted_id = encrypt_data($data_table->id);

        // Cek jika ada gambar atau tidak
        $image_path = base_url('assets/themes/super_admin/uploads/' . $data_table->image);

        $row = [];
        $row[] = $data_table->id;
        $row[] = $data_table->program_name;
        $row[] = $data_table->branch_name;
        $row[] = '<a href="' . $image_path . '" class="glightbox">
                    <img src="' . $image_path . '" alt="Program Image" class="img-thumbnail" style="width: 80px; height: 80px;">
                  </a>';
        $row[] = '
                    <button id="btnEdit" class="btn btn-warning text-white fw-semibold" 
                        data-id="' . $encrypted_id . '" 
                        data-program_id="' . $data_table->program_id . '"
                        data-program_name="' . $data_table->program_name . '"
                        data-image="' . $data_table->image . '">
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
            'image' => [
                'label' => 'Gambar', 
                'rules' => 'uploaded[image]|is_image[image]|max_size[image,2048]|mime_in[image,image/png,image/jpeg,image/jpg]'
            ],
            'program_id' => [
                'label' => 'Program', 
                'rules' => 'required|valid_reference[programs,id, Program tidak valid atau tidak ditemukan]'
            ],
        ];

        validate($data_post, $validation);

        $image = $this->request->getFile('image');
        $imageName = $image->getRandomName();
        $image->move('assets/themes/super_admin/uploads/', $imageName);

        $array_insert = [
        'image' => $imageName,
        'program_id' => $data_post['program_id'],
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
        $lastId = DatabaseModel::insertData('program_page', $array_insert);
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
        $file = $this->request->getFile('image');

        $validation = [
            'program_id' => [
                'label' => 'Program',
                'rules' => 'required|valid_reference[programs,id, Program tidak valid atau tidak ditemukan]'
            ],
        ];
    
        if ($file && $file->isValid()) {
            $validation['image'] = [
                'label' => 'Gambar',
                'rules' => 'is_image[image]|max_size[image,2048]|mime_in[image,image/png,image/jpeg,image/jpg]'
            ];
        }

        validate($data_post, $validation);

        // Decrypt ID
        $decrypted_id = $this->decrypt($data_post['id']);

        if (!$decrypted_id) {
            return response()->setJSON(['status' => false, 'message' => 'Invalid ID']);
        }

        // Ambil gambar lama
        $db = \Config\Database::connect();
        $existingData = $db->table('program_page')->where('id', $decrypted_id)->get()->getRow();

        $imageName = $existingData->image;

        if ($file && $file->isValid()) {
            if (!empty($existingData->image)) {
                $oldFilePath = 'assets/themes/super_admin/uploads/' . $existingData->image;
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
    
            $imageName = $file->getRandomName();
            $file->move('assets/themes/super_admin/uploads/', $imageName);
        }

        $array_update = [
        'image' => $imageName,
        'program_id' => $data_post['program_id'],
        'updated_at' => date('Y-m-d H:i:s')
        ];

        // Where condition
        $where_condition = ['id' => $decrypted_id];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
        // Use the correct parameter order for updateData
        $result = DatabaseModel::updateData('program_page', $where_condition, $array_update);

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
            $record = $db->table('program_page')
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

            if (!empty($record->image)) {
                $image_path = 'assets/themes/super_admin/uploads/' . $record->image;
                
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            // Update with soft delete
            DatabaseModel::updateData(
                'program_page',
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
            'status' => true,
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
