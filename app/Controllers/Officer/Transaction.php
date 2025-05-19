<?php

namespace App\Controllers\Officer;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Transaction extends BaseController
{
  protected $folder_directory = 'admin\\page\\transaction\\view\\';
  protected $js = ['index'];

  protected $db;

  public function __construct()
  {
    $this->db = new DatabaseModel();
  }

  public function index()
    {
      $currentMonth = date('m');
      $currentYear = date('Y');
  
      // $db = \Config\Database::connect();

      // $builder = $db->table('transactions');

      // // Pastikan semua transaksi di bulan sebelum bulan ini otomatis menjadi "close"
      // $builder->where('YEAR(created_at) <', $currentYear)
      //     ->orWhere('(YEAR(created_at) = ' . $currentYear . ' AND MONTH(created_at) < ' . $currentMonth . ')')
      //     ->update(['status' => 'close']);


        $data_transactions = $this->db->get([
            'select' => 'a.*, b.name as citizen_name, c.name as user_name, d.name as rw_name, e.name as program_name',
            'from' => 'transactions a',
            'where' => [
                'a.is_deleted' => false,
                'MONTH(a.created_at)' => $currentMonth,
                'YEAR(a.created_at)' => $currentYear
            ],
            'join' => [
                'citizens b, b.id = a.citizen_id, left',
                'users c, c.id = a.user_id, left',
                'rws d, d.id = a.rw_id, left',
                'programs e, e.id = a.program_id, left'
            ]
        ])->getResultObject();

        // Ambil status terbaru dari transaksi bulan ini
        $status = count($data_transactions) > 0 ? $data_transactions[0]->status : 'open';

        $data['data_transactions'] = $data_transactions;
        $data['status'] = $status;
        $data['page_title'] = 'Laporan Donasi | Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

  public function list_data()
  {
    // is_ajax();

    // Ambil input filter tanggal dari request
    $start_date = $this->request->getGet('start_date');
    $end_date = $this->request->getGet('end_date');

    $branch_id =  session()->get('branch_id');

    // Jika tidak ada filter tanggal, gunakan bulan dan tahun saat ini
    if (!$start_date || !$end_date) {
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
    }

    $whereCondition = "
      a.is_deleted = false 
      AND DATE(a.created_at) >= '$start_date' 
      AND DATE(a.created_at) <= '$end_date'
      AND (
        -- Prioritaskan branch_id dari rw_id jika tersedia
        (f.id = '$branch_id' AND a.rw_id IS NOT NULL) 
        OR 
        -- Jika rw_id dan user_id NULL, gunakan branch_id dari program_id
        (g.id = '$branch_id' AND a.rw_id IS NULL AND a.user_id IS NULL) 
        OR 
        -- Jika program_id NULL, gunakan branch_id dari user_id
        (i.id = '$branch_id' AND a.program_id IS NULL) 
    )
    ";

    $join = [
      'citizens b, b.id = a.citizen_id, left',
      'users c, c.id = a.user_id, left',
      'rws d, d.id = b.rw_id, left', // nyambung citizen
      'programs e, e.id = a.program_id, left',
      'branches f, f.id = d.branch_id, left', // nyambung rw
      'branches g, g.id = e.branches_id, left', // nyambung program
      'rws h, h.id = c.rw_id, left', // nyambung user
      'branches i, i.id = h.branch_id, left' // nyambung user
    ];

    // Query data transaksi dengan filter tanggal
    $data_transactions = DatabaseModel::get([
        'select' => 'a.*, b.name as citizen_name, c.name as user_name, d.name as rw_name, e.name as program_name, COALESCE(f.name, g.name, i.name) AS branch_name',
        'from' => 'transactions a',
        'where' => $whereCondition,
        'join' => $join,
        'orderBy' => ['a.created_at' => 'DESC']
    ])->getResultObject();

    $data = [];
    $total = 0;

    foreach ($data_transactions as $data_table) {
      
      $encrypted_id = encrypt_data($data_table->id);

      // Hitung debit dan credit
      $debit = $data_table->debit ? $data_table->debit : 0;
      $credit = $data_table->credit ? $data_table->credit : 0;

      // Hitung total saldo
      $total += $debit - $credit;

      $row = [];
      $row[] = $data_table->id;
      $row[] = $data_table->citizen_name;
      $row[] = $data_table->user_name;
      $row[] = $data_table->rw_name;
      $row[] = $data_table->program_name;
      $row[] = $data_table->debit ? 'Rp ' . number_format($data_table->debit, 0, ',', '.') : '-';
      $row[] = $data_table->credit ? 'Rp ' . number_format($data_table->credit, 0, ',', '.') : '-';
      $row[] = 'Rp ' . number_format($total, 0, ',', '.');
      $row[] = $data_table->note;
      $row[] = date('d-m-Y', strtotime($data_table->created_at));
      $row[] = $data_table->status;
      $row[] = $data_table->type;
      $row[] = $data_table->branch_name;
      
      $data[] = $row;
    }

    $response = [
      'data' => $data,
    ];

    return response()->setJSON($response);
  }

  public function get_income()
  {
      $currentMonth = date('m');
      $currentYear = date('Y');

      $branch_id =  session()->get('branch_id');

      $whereCondition = "
      a.is_deleted = false 
      AND MONTH(a.created_at) = '$currentMonth' 
      AND YEAR(a.created_at) = '$currentYear'
      AND (
        -- Prioritaskan branch_id dari rw_id jika tersedia
        (f.id = '$branch_id' AND a.rw_id IS NOT NULL) 
        OR 
        -- Jika rw_id dan user_id NULL, gunakan branch_id dari program_id
        (g.id = '$branch_id' AND a.rw_id IS NULL AND a.user_id IS NULL) 
        OR 
        -- Jika program_id NULL, gunakan branch_id dari user_id
        (i.id = '$branch_id' AND a.program_id IS NULL) 
      )
      ";

      $join = [
          'citizens b, b.id = a.citizen_id, left', // nyambung transaction
          'users c, c.id = a.user_id, left', // nyambung transaction
          'rws d, d.id = b.rw_id, left', // nyambung citizen
          'programs e, e.id = a.program_id, left', // nyambung transaction
          'branches f, f.id = d.branch_id, left', // nyambung rw
          'branches g, g.id = e.branches_id, left', // nyambung program
          'rws h, h.id = c.rw_id, left', // nyambung user
          'branches i, i.id = h.branch_id, left' // nyambung user
      ];

      // **Hitung total pendapatan bulanan**
      $total_monthly = $this->db->get([
          'select' => 'SUM(a.debit) as total_debit, SUM(a.credit) as total_credit',
          'from' => 'transactions a',
          'where' => $whereCondition,
          'join' => $join
      ])->getRow();

      $total_monthly_income = ($total_monthly->total_debit ?? 0) - ($total_monthly->total_credit ?? 0);

      // **Hitung total pendapatan keseluruhan**
      $total_overall = $this->db->get([
          'select' => 'SUM(a.debit) as total_debit, SUM(a.credit) as total_credit',
          'from' => 'transactions a',
          'where' => $whereCondition,
          'join' => $join
      ])->getRow();

      $total_overall_income = ($total_overall->total_debit ?? 0) - ($total_overall->total_credit ?? 0);

      return $this->response->setJSON([
          'total_monthly_income' => number_format($total_monthly_income, 0, ',', '.'),
          'total_overall_income' => number_format($total_overall_income, 0, ',', '.')
      ]);
  }

  public function update_status()
  {
      $month = $this->request->getPost('month');
      $year = $this->request->getPost('year');
      $status = $this->request->getPost('status');
      $branch_id = session()->get('branch_id');

      // Validasi input
      if (!$month || !$year || !$status || !$branch_id) {
          return $this->response->setJSON(['status' => false, 'message' => 'Data tidak lengkap']);
      }

      $db = \Config\Database::connect();
      $db->transStart();

      try {
          // **Subquery untuk mendapatkan ID transaksi yang akan diupdate**
          $subQuery = $db->table('transactions a')
              ->select('a.id')
              ->join('citizens b', 'b.id = a.citizen_id', 'left')
              ->join('users c', 'c.id = a.user_id', 'left')
              ->join('rws d', 'd.id = b.rw_id', 'left') // nyambung citizen
              ->join('programs e', 'e.id = a.program_id', 'left')
              ->join('branches f', 'f.id = d.branch_id', 'left') // nyambung rw
              ->join('branches g', 'g.id = e.branches_id', 'left') // nyambung program
              ->join('rws h', 'h.id = c.rw_id', 'left') // nyambung user
              ->join('branches i', 'i.id = h.branch_id', 'left') // nyambung user
              ->where('a.is_deleted', false)
              ->where('MONTH(a.created_at)', $month)
              ->where('YEAR(a.created_at)', $year)
              ->groupStart()
                  ->where('f.id', $branch_id)->where('a.rw_id IS NOT NULL') // Jika rw_id tersedia
                  ->orGroupStart()
                      ->where('g.id', $branch_id)->where('a.rw_id IS NULL')->where('a.user_id IS NULL') // Jika rw_id dan user_id NULL, gunakan branch_id dari program_id
                  ->groupEnd()
                  ->orGroupStart()
                      ->where('i.id', $branch_id)->where('a.program_id IS NULL') // Jika program_id NULL, gunakan branch_id dari user_id
                  ->groupEnd()
              ->groupEnd();

          $subQueryResult = $subQuery->get()->getResult();

          if (empty($subQueryResult)) {
              return $this->response->setJSON(['status' => false, 'message' => 'Tidak ada data yang ditemukan berdasarkan filter!']);
          }

          // **Eksekusi update berdasarkan subquery**
          $builder = $db->table('transactions');
          $affectedRows = $builder->whereIn('id', array_column($subQueryResult, 'id'))->update(['status' => $status]);

          if ($affectedRows === 0) {
              return $this->response->setJSON(['status' => false, 'message' => 'Tidak ada baris yang diperbarui. Periksa kembali data yang dikirim!']);
          }

          $db->transComplete();

          if ($db->transStatus() === FALSE) {
              return $this->response->setJSON(['status' => false, 'message' => 'Status gagal diperbarui!']);
          }

      } catch (\Exception $e) {
          $db->transRollback();
          return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
      }

      return $this->response->setJSON(['status' => true, 'message' => 'Status berhasil diperbarui!']);
  }


  public function get_data()
  {
      try {
          // Ambil ID user dari session
          $session = session();
          $user_id = $session->get('user_id');

          if (!$user_id) {
              return $this->response->setJSON(['status' => ResponseInterface::HTTP_UNAUTHORIZED, 'message' => 'User tidak terautentikasi']);
          }

          $query = [
              'select' => 'transactions.id, transactions.citizen_id, transactions.user_id, transactions.rw_id, transactions.program_id, transactions.debit, transactions.credit, transactions.note, transactions.total_collected, transactions.created_at, transactions.updated_at, citizens.name as citizen_name, users.name as user_name, rws.name as rw_name, programs.name as program_name',
              'from' => 'transactions',
              'join' => [
                  'citizens, citizens.id = transactions.citizen_id, left',
                  'users, users.id = transactions.user_id, left',
                  'rws, rws.id = transactions.rw_id, left',
                  'programs, programs.id = transactions.program_id, left'
              ],
              'where' => [
                  'transactions.is_deleted' => 0,
                  'transactions.type' => 'donations',
                  'transactions.user_id' => $user_id, // Filter berdasarkan user yang login
              ], // Only get non-deleted records
              'order_by' => 'transactions.created_at DESC' // Order by created_at in descending order
          ];

          // Eksekusi kueri
          $transactions = $this->db->get($query)->getResult();
          log_message('info', 'Transactions data: ' . json_encode($transactions));

          return $this->response->setJSON(['status' => ResponseInterface::HTTP_OK, 'data' => $transactions]);
      } catch (\Exception $e) {
          log_message('error', 'Error retrieving transactions data: ' . $e->getMessage());
          return $this->response->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(['status' => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR, 'message' => 'Error retrieving transactions data']);
      }
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
        'error_string' => ['The ID Transaksi field is required.']
      ]);
    }

    // Validate the ID field
    $validation = [
      'id' => [
        'label' => 'ID Transaksi',
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

      // Check if transaction status is closed
      $transaction = DatabaseModel::get([
        'from' => 'transactions',
        'where' => ['id' => $decrypted_id]
      ])->getRow();

      if ($transaction && $transaction->status === 'close') {
        return response()->setJSON([
          'status' => false,
          'message' => 'Transaksi sudah ditutup dan tidak dapat dihapus'
        ]);
      }

      // Check if transaction is used in file_fund
      $is_used_in_file_fund = DatabaseModel::get([
        'select' => 'COUNT(*) as count',
        'from' => 'file_fund',
        'where' => [
          'transaction_id' => $decrypted_id,
          'is_deleted' => false
        ]
      ])->getRow()->count > 0;

      if ($is_used_in_file_fund) {
        return response()->setJSON([
          'status' => false,
          'message' => 'Transaksi ini tidak dapat dihapus karena masih terkait dengan file pendanaan'
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
      $record = $db->table('transactions')
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
        'transactions',
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

  public function reportInfaqPerRW()
  {
    // is_ajax();

    try {
      $report = DatabaseModel::get([
        'select' => 'd.name as rw_name, SUM(a.debit) as total_infaq',
        'from' => 'transactions a',
        'where' => ['a.is_deleted' => false, 'a.type' => 'donations'],
        'join' => [
          'rws d, d.id = a.rw_id, left'
        ],
        'group_by' => 'd.id'
      ])->getResultObject();

      return response()->setJSON(['status' => TRUE, 'data' => $report]);
    } catch (\Exception $e) {
      return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
    }
  }

  public function closeTransactions()
  {
    // is_ajax();
    $data_post = $this->request->getPost();

    $validation = [
      'month' => [
        'label' => 'Bulan',
        'rules' => 'required|valid_month'
      ],
      'year' => [
        'label' => 'Tahun',
        'rules' => 'required|valid_year'
      ],
    ];

    validate($data_post, $validation);

    $db = \Config\Database::connect();
    $db->transStart();

    try {
      $db->query(
        "UPDATE transactions SET status = 'close', updated_at = NOW() 
                        WHERE MONTH(created_at) = ? AND YEAR(created_at) = ? AND is_deleted = false",
        [$data_post['month'], $data_post['year']]
      );

      $db->transComplete();

      if ($db->transStatus() === FALSE) {
        return response()->setJSON(['status' => false, 'message' => 'Failed to close transactions']);
      }
    } catch (\Exception $e) {
      $db->transRollback();
      return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
    }

    return response()->setJSON(['status' => TRUE, 'message' => 'Transactions closed successfully']);
  }

  public function openTransactions()
  {
    // is_ajax();
    $data_post = $this->request->getPost();

    $validation = [
      'month' => [
        'label' => 'Bulan',
        'rules' => 'required|valid_month'
      ],
      'year' => [
        'label' => 'Tahun',
        'rules' => 'required|valid_year'
      ],
    ];

    validate($data_post, $validation);

    $db = \Config\Database::connect();
    $db->transStart();

    try {
      $db->query(
        "UPDATE transactions SET status = 'open', updated_at = NOW() 
                        WHERE MONTH(created_at) = ? AND YEAR(created_at) = ? AND is_deleted = false",
        [$data_post['month'], $data_post['year']]
      );

      $db->transComplete();

      if ($db->transStatus() === FALSE) {
        return response()->setJSON(['status' => false, 'message' => 'Failed to open transactions']);
      }
    } catch (\Exception $e) {
      $db->transRollback();
      return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
    }

    return response()->setJSON(['status' => TRUE, 'message' => 'Transactions opened successfully']);
  }

  public function reportInfaqPerMonthForUser()
  {
    // is_ajax();
    $data_post = $this->request->getPost();

    $validation = [
      'user_id' => [
        'label' => 'Petugas',
        'rules' => 'required|valid_reference[users,id,Data petugas tidak valid atau tidak ditemukan]'
      ],
      'month' => [
        'label' => 'Bulan',
        'rules' => 'required|valid_month'
      ],
      'year' => [
        'label' => 'Tahun',
        'rules' => 'required|valid_year'
      ],
    ];

    validate($data_post, $validation);

    try {
      $report = DatabaseModel::get([
        'select' => 'SUM(a.debit) as total_infaq',
        'from' => 'transactions a',
        'where' => [
          'a.is_deleted' => false,
          'a.type' => 'donations',
          'a.user_id' => $data_post['user_id'],
          'MONTH(a.created_at)' => $data_post['month'],
          'YEAR(a.created_at)' => $data_post['year']
        ]
      ])->getRow();

      return response()->setJSON(['status' => TRUE, 'data' => $report]);
    } catch (\Exception $e) {
      return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
    }
  }

  public function reportCommissionPerMonthForUser()
  {
    // is_ajax();
    $data_post = $this->request->getPost();

    $validation = [
      'user_id' => [
        'label' => 'Petugas',
        'rules' => 'required|valid_reference[users,id,Data petugas tidak valid atau tidak ditemukan]'
      ],
      'month' => [
        'label' => 'Bulan',
        'rules' => 'required|valid_month'
      ],
      'year' => [
        'label' => 'Tahun',
        'rules' => 'required|valid_year'
      ],
    ];

    validate($data_post, $validation);

    try {
      $report = DatabaseModel::get([
        'select' => 'SUM(a.credit) as total_commission',
        'from' => 'transactions a',
        'where' => [
          'a.is_deleted' => false,
          'a.type' => 'field_officer_commision',
          'a.user_id' => $data_post['user_id'],
          'MONTH(a.created_at)' => $data_post['month'],
          'YEAR(a.created_at)' => $data_post['year']
        ]
      ])->getRow();

      return response()->setJSON(['status' => TRUE, 'data' => $report]);
    } catch (\Exception $e) {
      return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
    }
  }

  public function previewCommissionPerMonthForUser()
  {
    // is_ajax();
    $data_post = $this->request->getPost();

    $validation = [
      'user_id' => [
        'label' => 'Petugas',
        'rules' => 'required|valid_reference[users,id,Data petugas tidak valid atau tidak ditemukan]'
      ],
      'month' => [
        'label' => 'Bulan',
        'rules' => 'required|valid_month'
      ],
      'year' => [
        'label' => 'Tahun',
        'rules' => 'required|valid_year'
      ],
    ];

    validate($data_post, $validation);

    try {
      // Hitung total donasi yang dilakukan oleh petugas tertentu dalam bulan dan tahun tertentu
      $result = DatabaseModel::get([
        'select' => 'SUM(a.debit) as total_donations',
        'from' => 'transactions a',
        'where' => [
          'a.is_deleted' => false,
          'a.type' => 'donations',
          'a.user_id' => $data_post['user_id'],
          'MONTH(a.created_at)' => $data_post['month'],
          'YEAR(a.created_at)' => $data_post['year']
        ]
      ])->getRow();

      $totalDonations = $result->total_donations ?? 0;
      $commission = $totalDonations * 0.10; // 10% dari total donasi

      return response()->setJSON([
        'status' => TRUE,
        'data' => [
          'total_donations' => $totalDonations,
          'commission' => $commission
        ]
      ]);
    } catch (\Exception $e) {
      return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
    }
  }

  private function validateReferences($references)
  {
    foreach ($references as $field => $id) {
      if (empty($id)) continue;

      $table = '';
      switch ($field) {
        case 'citizen_id':
          $table = 'citizens';
          break;
        case 'user_id':
          $table = 'users';
          break;
        case 'rw_id':
          $table = 'rws';
          break;
        case 'program_id':
          $table = 'programs';
          break;
      }

      if (!empty($table)) {
        $data = DatabaseModel::get([
          'from' => $table,
          'where' => ['id' => $id, 'is_deleted' => false],
        ])->getRow();

        if (!$data) {
          throw new \Exception('ID ' . $field . ' tidak valid');
        }
      }
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


  private function encrypt($id)
  {
    if (!is_string($id)) {
      $id = (string)$id;
    }

    $encrypter = \Config\Services::encrypter();
    return bin2hex($encrypter->encrypt($id));
  }

  public function get_by_rw()
  {
    // is_ajax();
    $data_post = $this->request->getGet();

    $validation = [
      'rw_id' => [
        'label' => 'RW ID',
        'rules' => 'required|valid_reference[rws,id,RW tidak valid atau tidak ditemukan]'
      ],
    ];

    try {
      validate($data_post, $validation);

      // First, get the RW officers information
      $rwOfficers = DatabaseModel::get([
        'select' => 'u.id, u.name as officer_name',
        'from' => 'users u',
        'where' => [
          'u.rw_id' => $data_post['rw_id'],
          'u.is_deleted' => false,
        ]
      ])->getResultObject();

      // Create a list of officers for this RW
      $officers = [];
      foreach ($rwOfficers as $officer) {
        $encrypted_id = encrypt_data($officer->id);
        $officers[] = [
          'id' => $encrypted_id,
          'name' => $officer->officer_name
        ];
      }

      // Get citizens for this RW
      $citizens = DatabaseModel::get([
        'select' => 'a.*, b.name as rw_name, c.name as region_name',
        'from' => 'citizens a',
        'where' => [
          'a.is_deleted' => false,
          'a.rw_id' => $data_post['rw_id']
        ],
        'join' => [
          'rws b, b.id = a.rw_id, left',
          'branches d, d.id = b.branch_id, left',
          'region c, c.id = d.region_id, left'
        ]
      ])->getResultObject();

      $citizenData = [];
      foreach ($citizens as $citizen) {
        $encrypted_id = encrypt_data($citizen->id);
        $citizenData[] = [
          'id' => $citizen->id,
          'name' => $citizen->name,
          'rw_name' => $citizen->rw_name,
          'region_name' => $citizen->region_name
        ];
      }

      // Get RW information
      $rw = DatabaseModel::get([
        'select' => 'name',
        'from' => 'rws',
        'where' => ['id' => $data_post['rw_id']]
      ])->getRow();

      return response()->setJSON([
        'status' => true,
        'rw_name' => $rw ? $rw->name : '',
        'officers' => $officers,
        'citizens' => $citizenData
      ]);
    } catch (\Exception $e) {
      return response()->setJSON([
        'status' => false,
        'message' => $e->getMessage()
      ]);
    }
  }

  public function get_no_rw()
  {
      try {
          // Ambil data warga yang tidak memiliki rw_id (NULL)
          $citizens = DatabaseModel::get([
              'select' => 'a.*',
              'from' => 'citizens a',
              'where' => [
                  'a.is_deleted' => false,
                  'a.rw_id IS NULL' => null
              ],
              
          ])->getResultObject();

          $citizenData = [];
          foreach ($citizens as $citizen) {
              $encrypted_id = encrypt_data($citizen->id);
              $citizenData[] = [
                  'id' => $citizen->id,
                  'name' => $citizen->name,
                  'phone' => $citizen->phone,
                  'address' => $citizen->address
              ];
          }

          return response()->setJSON([
              'status' => true,
              'citizens' => $citizenData
          ]);
      } catch (\Exception $e) {
          return response()->setJSON([
              'status' => false,
              'message' => $e->getMessage()
          ]);
      }
  }

}
