<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Distribution_Page extends BaseController
{
    protected $folder_directory = 'super_admin\\page\\distribution_page\\view\\';
    protected $js = ['index'];
    protected $db;

    public function __construct() {
        $this->db = new DatabaseModel();
    }

    public function index()
    {
        $data_distribution_page = $this->db->get([
            'select' => 'a.*, b.name as program_name',
            'from' => 'distribution_page a',
            'where' => ['a.is_deleted' => false],
            'join' => [
            'programs b, b.id = a.program_id, left',
            ]
        ])->getResultObject();

        $data['data_distribution_page'] = $data_distribution_page;
        $data['page_title'] = 'Berita Penyaluran | Super-Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        is_ajax();

        $data_distribution_page = $this->db->get([
            'select' => 'a.*, b.name as program_name, c.name as branch_name',
            'from' => 'distribution_page a',
            'where' => ['a.is_deleted' => false],
            'join' => [
            'programs b, b.id = a.program_id, left',
            'branches c, c.id = b.branches_id, left'
            ]
        ])->getResultObject();

        $data = [];
        foreach ($data_distribution_page as $data_table) {

            $id = $data_table->id;

            // Ambil data gambar dari distribution_image berdasarkan distribution_page_id
            $distribution_image = $this->db->get([
                'select' => 'e.id as distribution_image_id, e.image as distribution_image_name',
                'from' => 'distribution_image e',
                'where' => [
                    'e.distribution_page_id' => $id, 
                    'e.is_deleted' => false
                ]
            ])->getResultObject();

            // Simpan distribution_image dalam format JSON untuk data-btnEdit
            $distribution_image_list = [];
            foreach ($distribution_image as $image) {
                $distribution_image_list[] = [
                    'id' => $image->distribution_image_id,
                    'image' => $image->distribution_image_name
                ];
            }

            $distribution_image_json = htmlspecialchars(json_encode($distribution_image_list), ENT_QUOTES, 'UTF-8');

            $encrypted_id = encrypt_data($data_table->id);

            $row = [];
            $row[] = $data_table->id;
            $row[] = $data_table->program_name;
            $row[] = $data_table->branch_name;
            $row[] = date('d-m-Y', strtotime($data_table->date));
            $row[] = $data_table->description;
            $row[] = '
                        <button id="btnDetail" class="btn btn-primary text-white fw-semibold my-1" 
                            data-id="'.$encrypted_id.'">
                            Detail
                        </button>
                        <button id="btnEdit" class="btn btn-warning text-white fw-semibold my-1" 
                            data-id="' . $encrypted_id . '" 
                            data-program_id="' . $data_table->program_id . '"
                            data-program_name="' . $data_table->program_name . '"
                            data-date="' . $data_table->date . '"
                            data-description="' . htmlspecialchars($data_table->description, ENT_QUOTES, 'UTF-8') . '"
                            data-image=\'' . $distribution_image_json . '\'>
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

    public function get_detail()
    {
        is_ajax();
        $id = decrypt_data($this->request->getGet('id'));

        $data_distribution_page = $this->db->get([
            'select' => 'a.*, b.name as program_name, c.name as branch_name',
            'from' => 'distribution_page a',
            'where' => ['a.id' => $id],
            'join' => [
            'programs b, b.id = a.program_id, left',
            'branches c, c.id = b.branches_id, left'
            ]
        ])->getRowObject();

        // Query daftar distribution_image
        $distribution_image = $this->db->get([
            'select' => 'image',
            'from' => 'distribution_image',
            'where' => [
                'distribution_page_id' => $id,
                'is_deleted' => false
            ],
        ])->getResultObject();

        // Format hasil distribution_image menjadi array nama
        $data_distribution_image = array_map(fn($b) => base_url('assets/themes/super_admin/uploads/' . $b->image), $distribution_image);

        // Kirim data sebagai JSON
        return $this->response->setJSON([
            'status' => true,
            'distribution_page' => $data_distribution_page,
            'distribution_image' => $data_distribution_image
        ]);
    }

    public function save()
    {
        $data_post = $this->request->getPost();
        $files = $this->request->getFiles();

        // Validasi teks (tanpa gambar dulu)
        $validation = [
            'program_id' => [
                'label' => 'Program',
                'rules' => 'required|valid_reference[programs,id,Program tidak valid atau tidak ditemukan]'
            ],
            'description' => [
                'label' => 'Deskripsi',
                'rules' => 'required'
            ],
            'date' => [
                'label' => 'Tanggal',
                'rules' => 'required'
            ],
        ];

        // Jalankan validasi teks
        if (!validate($data_post, $validation)) {
            return response()->setJSON([
                'status' => false,
                'error_string' => \Config\Services::validation()->getErrors(),
                'inputerror' => array_keys(\Config\Services::validation()->getErrors())
            ]);
        }

        if (empty($files['images'])) {
            return response()->setJSON([
                'status' => false,
                'message' => 'No images uploaded.',
            ]);
        }

        $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];
        foreach ($files['images'] as $image) {
            if (!$image->isValid()) {
                return response()->setJSON(['status' => false, 'message' => 'Invalid image uploaded.']);
            }
            if (!in_array($image->getMimeType(), $allowedTypes)) {
                return response()->setJSON(['status' => false, 'message' => 'Invalid file type. Only PNG, JPG, JPEG allowed.']);
            }
        }

        $db = \Config\Database::connect();

        $program = $db->table('programs')
              ->select('name')
              ->where('id', $data_post['program_id'])
              ->get()
              ->getRow();

        if (!$program) {
            return response()->setJSON(['status' => false, 'message' => 'Program tidak ditemukan.']);
        }

        helper('text');
        $baseSlug = url_title($program->name, '-', true);
        $slug = $baseSlug . '-' . time();

        // Simpan data distribution_page
        $array_insert = [
            'description'   => $data_post['description'],
            'program_id'    => $data_post['program_id'],
            'slug'          => $slug,
            'date'          => $data_post['date'],
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s')
        ];

        $db->transStart();

        try {
            // Insert ke tabel distribution_page
            $lastId = DatabaseModel::insertData('distribution_page', $array_insert);

            // Simpan gambar-gambar yang diunggah
            foreach ($files['images'] as $image) {
                if ($image->isValid() && !$image->hasMoved()) {
                    $imageName = $image->getRandomName();
                    $image->move('assets/themes/super_admin/uploads/', $imageName);

                    $imageData = [
                        'distribution_page_id' => $lastId,
                        'image' => $imageName,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    DatabaseModel::insertData('distribution_image', $imageData);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return response()->setJSON(['status' => false, 'message' => 'Failed to save data']);
            }

            $encrypted_id = encrypt_data((string)$lastId);

        } catch (\Exception $e) {
            $db->transRollback();
            return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }

        return response()->setJSON(['status' => true, 'message' => 'Data berhasil disimpan', 'id' => $encrypted_id]);
    }

    public function update()
    {
        $data_post = $this->request->getPost();
        $files = $this->request->getFiles();
        $id = decrypt_data($data_post['id']);

        // Validasi teks (tanpa gambar dulu)
        $validation = [
            'program_id' => [
                'label' => 'Program',
                'rules' => 'required|valid_reference[programs,id,Program tidak valid atau tidak ditemukan]'
            ],
            'description' => [
                'label' => 'Deskripsi',
                'rules' => 'required'
            ],
            'date' => [
                'label' => 'Tanggal',
                'rules' => 'required'
            ],
        ];

        // Jalankan validasi
        if (!validate($data_post, $validation)) {
            return response()->setJSON([
                'status' => false,
                'error_string' => \Config\Services::validation()->getErrors(),
                'inputerror' => array_keys(\Config\Services::validation()->getErrors())
            ]);
        }

        $db = \Config\Database::connect();

        $program = $db->table('programs')
        ->select('name')
        ->where('id', $data_post['program_id'])
        ->get()
        ->getRow();

        if (!$program) {
            return response()->setJSON(['status' => false, 'message' => 'Program tidak ditemukan.']);
        }

        helper('text');
        $baseSlug = url_title($program->name, '-', true);
        $slug = $baseSlug . '-' . time();

        $db->transStart();

        try {
            // Update data distribution_page
            $update_data = [
                'description'   => $data_post['description'],
                'program_id'    => $data_post['program_id'],
                'slug'          => $slug,
                'date'          => $data_post['date'],
                'updated_at'    => date('Y-m-d H:i:s')
            ];

            DatabaseModel::updateData('distribution_page', ['id' => $id], $update_data);

            if (empty($files) || !isset($files['images'])) {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'File tidak ditemukan dalam request.'
                ]);
            }

            // Ambil gambar lama dari database sebelum soft delete
            $oldImages = $db->table('distribution_image')
                ->where('distribution_page_id', $id)
                ->where('is_deleted', false)
                ->get()
                ->getResultArray();

            // Konversi ke array jika masih objek
            if (!is_array($oldImages)) {
                $oldImages = json_decode(json_encode($oldImages), true);
            }

            // Hapus file gambar lama dari folder
            foreach ($oldImages as $image) {
                if (isset($image['image'])) { // Pastikan key 'image' ada
                    $filePath = 'assets/themes/super_admin/uploads/' . $image['image'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }

            // Soft delete gambar lama di database
            $db->table('distribution_image')
            ->where('distribution_page_id', $id)
            ->update([
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s')
            ]);
            
            // Jika 'images' adalah array (multiple file upload)
            if (is_array($files['images'])) {
                foreach ($files['images'] as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        $file->move('assets/themes/super_admin/uploads/', $newName);
            
                        // Simpan ke database
                        DatabaseModel::insertData('distribution_image', [
                            'distribution_page_id' => $id,
                            'image' => $newName,
                            'is_deleted' => false,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            } else {
                return $this->response->setJSON([
                    'status' => false,
                    'message' => 'Format file tidak sesuai.'
                ]);
            }

            // Jika ada gambar baru, lakukan soft delete + hapus file gambar lama
            $db->transComplete();

            if ($db->transStatus() === false) {
                return response()->setJSON(['status' => false, 'message' => 'Failed to update data']);
            }

        } catch (\Exception $e) {
            $db->transRollback();
            return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }

        return response()->setJSON(['status' => true, 'message' => 'Data berhasil diperbarui']);
    }

    public function delete()
    {
        $data_post = $this->request->getPost();
        $id = decrypt_data($data_post['id']);

        if (!$id) {
            return $this->response->setJSON(['status' => false, 'message' => 'ID tidak valid']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Cek apakah distribution_page masih ada
            $distribution = DatabaseModel::getData('distribution_page', ['id' => $id, 'is_deleted' => false]);
            if (!$distribution) {
                return $this->response->setJSON(['status' => false, 'message' => 'Data tidak ditemukan']);
            }

            $images = $db->table('distribution_image')
                ->where('distribution_page_id', $id)
                ->where('is_deleted', false)
                ->get()
                ->getResultArray();

            // Konversi ke array jika masih berbentuk objek
            if (!is_array($images)) {
                $images = json_decode(json_encode($images), true);
            }

            // Hapus file gambar dari folder
            foreach ($images as $image) {
                if (!empty($image['image'])) {
                    $filePath = 'assets/themes/super_admin/uploads/' . $image['image'];
                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
            }

            // Soft delete data di database
            DatabaseModel::updateData(
                'distribution_page',
                ['id' => $id],
                ['is_deleted' => true, 'deleted_at' => date('Y-m-d H:i:s')],
            );

            DatabaseModel::updateData(
                'distribution_image',
                ['distribution_page_id' => $id],
                ['is_deleted' => true, 'deleted_at' => date('Y-m-d H:i:s')],
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['status' => false, 'message' => 'Gagal menghapus data']);
            }

            return $this->response->setJSON(['status' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Delete Error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
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
