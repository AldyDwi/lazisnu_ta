<?php

namespace App\Controllers\Officer;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Rws extends BaseController
{
  protected $folder_directory = 'super_admin\\page\\rws\\view\\';
  protected $js = ['index'];

  protected $db;

  public function __construct() {
    $this->db = new DatabaseModel();
  }

  public function index()
  {
    $data_rws = DatabaseModel::get([
      'select' => 'a.*, b.name as branch_name',
      'from' => 'rws a',
      'where' => ['a.is_deleted' => false],
      'join' => [
        'branches b, b.id = a.branch_id, left',
      ]
    ])->getResultObject();

    $data['data_rws'] = $data_rws;
    $data['page_title'] = 'RW | Super-Admin';
    $data['js'] = $this->js;
    $data['folder_directory'] = $this->folder_directory;

    return view($this->folder_directory . 'index', $data);
  }

  public function list_data()
  {
    // is_ajax();

    $data_rws = DatabaseModel::get([
      'select' => 'a.*, b.name as branch_name, c.name as region_name, CONCAT(a.name, ", ", c.name) AS rw_region',
      'from' => 'rws a',
      'where' => ['a.is_deleted' => false],
      'join' => [
        'branches b, b.id = a.branch_id, left',
        'region c, c.id = b.region_id, left'
      ]
    ])->getResultObject();

    $data = [];
    foreach ($data_rws as $data_table) {
      $encrypted_id = encrypt_data($data_table->id);

      $row = [];
      $row[] = $data_table->id;
      $row[] = $data_table->rw_region;
      $row[] = $data_table->name;
      $row[] = $data_table->region_name;
      $row[] = $data_table->branch_name;
      $row[] = '
                <button id="btnEdit" class="btn btn-warning text-white fw-semibold" 
                    data-id="' . $encrypted_id . '" 
                    data-name="' . $data_table->name . '"
                    data-branch_id="' . $data_table->branch_id . '"
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

  public function dropdown_admin()
  {
      $data_branches = DatabaseModel::get([
          'select' => 'a.id as rw_id, b.id as branch_id, b.name as branch_name',
          'from' => 'rws a',
          'where' => ['a.is_deleted' => false],
          'join' => [
              'branches b, b.id = a.branch_id, left',
          ],
          'order' => 'a.id ASC'
      ])->getResultObject();

      $filtered_branches = [];
      foreach ($data_branches as $branch) {
          if (!isset($filtered_branches[$branch->branch_id])) {
              $filtered_branches[$branch->branch_id] = [
                  'rw_id' => $branch->rw_id,
                  'branch_name' => $branch->branch_name,
              ];
          }
      }

      // Ubah menjadi array numerik untuk JSON response
      $data = [];
      foreach ($filtered_branches as $branch) {
          $data[] = [$branch['rw_id'], $branch['branch_name']];
      }

      return response()->setJSON(['data' => $data]);
  }

  public function get_data()
    {
        try {
            $rws = $this->db->get([
                'select' => 'rws.*, branches.name as branch_name',
                'from' => 'rws',
                'where' => [
                  'rws.is_deleted' => false,
                ],
                'join_custom' => [
                    'branches' => 'rws.branch_id = branches.id, left'
                ]
            ])->getResult();
            log_message('info', 'Rws data retrieved successfully: ' . json_encode($rws));
            return $this->response->setJSON(['status' => ResponseInterface::HTTP_OK, 'data' => $rws]);
        } catch (\Exception $e) {
            log_message('error', 'Error retrieving rws data: ' . $e->getMessage());
            return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['status' => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Error retrieving rws data']);
        }
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
    ];

    validate($data_post, $validation);

    $array_insert = [
      'name' => $data_post['name'],
      'branch_id' => $data_post['branch_id'],
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s')
    ];

    $db = \Config\Database::connect();
    $db->transStart();

    try {
      $lastId = DatabaseModel::insertData('rws', $array_insert);
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
          'label' => 'ID RW', 
          'rules' => 'required'
      ],
      'name' => [
          'label' => 'Nama RW', 
          'rules' => 'required'
      ],
      'branch_id' => [
          'label' => 'Cabang', 
          'rules' => 'required|valid_reference[branches,id,Cabang tidak valid atau tidak ditemukan]'
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
      'branch_id' => $data_post['branch_id'],
      'updated_at' => date('Y-m-d H:i:s')
    ];

    // Where condition
    $where_condition = ['id' => $decrypted_id];

    $db = \Config\Database::connect();
    $db->transStart();

    try {
      // Use the correct parameter order for updateData
      $result = DatabaseModel::updateData('rws', $where_condition, $array_update);

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
        'error_string' => ['The ID RW field is required.']
      ]);
    }

    // Validate the ID field
    $validation = [
      'id' => [
          'label' => 'ID RW', 
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
      
      // Check if RW is used in other tables
      $is_used_in_citizens = DatabaseModel::get([
        'select' => 'COUNT(*) as count',
        'from' => 'citizens',
        'where' => [
          'rw_id' => $decrypted_id,
          'is_deleted' => false
        ]
      ])->getRow()->count > 0;
      
      if ($is_used_in_citizens) {
        return response()->setJSON([
          'status' => false, 
          'message' => 'RW ini tidak dapat dihapus karena masih memiliki warga terdaftar'
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
      $record = $db->table('rws')
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
        'rws',
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