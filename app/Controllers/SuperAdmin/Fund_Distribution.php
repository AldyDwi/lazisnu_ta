<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Fund_Distribution extends BaseController
{
    protected $folder_directory = 'super_admin\\page\\fund_distribution\\view\\';
    protected $js = ['index'];

    protected $db;

    public function __construct()
    {
        $this->db = new DatabaseModel();
    }
    public function index()
    {
        // Ambil bulan dan tahun saat ini
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Ambil transaksi berdasarkan bulan dan tahun saat ini
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
        $data['page_title'] = 'Distribusi Dana | Super-Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        is_ajax();
        // Ambil input filter tanggal dari request
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        // Jika tidak ada filter tanggal, gunakan bulan dan tahun saat ini
        if (!$start_date || !$end_date) {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
        }

        // Query data transaksi dengan filter tanggal
        $data_transactions = DatabaseModel::get([
            'select' => 'a.*, b.name as program_name, c.name as branch_name, b.percentage, c.id as branch_id',
            'from' => 'transactions a',
            'where' => [
                'a.is_deleted' => false,
                'a.type' => 'fund_distribution',
                'DATE(a.created_at) >=' => $start_date,
                'DATE(a.created_at) <=' => $end_date,
            ],
            'join' => [
                'programs b, b.id = a.program_id, left',
                'branches c, c.id = b.branches_id, left',
            ],
            'order' => ['a.created_at' => 'DESC']
        ])->getResultObject();

        $data = [];
        foreach ($data_transactions as $data_table) {

            $id = $data_table->id; // Ambil ID transaksi

            // Query daftar beneficiaries berdasarkan ID transaksi
            $beneficiaries = $this->db->get([
                'select' => 'e.id as beneficaries_id, e.name as beneficaries_name',
                'from' => 'detail_fund d',
                'where' => [
                    'd.transaction_id' => $id,
                    'd.is_deleted' => false
                ],
                'join' => [
                    'beneficaries e, e.id = d.beneficaries_id, left'
                ]
            ])->getResultObject();

            // Simpan beneficiaries dalam format JSON untuk data-btnEdit
            $beneficiaries_list = [];
            foreach ($beneficiaries as $beneficiary) {
                $beneficiaries_list[] = [
                    'id' => $beneficiary->beneficaries_id,
                    'name' => $beneficiary->beneficaries_name
                ];
            }

            $beneficiaries_json = htmlspecialchars(json_encode($beneficiaries_list), ENT_QUOTES, 'UTF-8');

            $encrypted_id = encrypt_data($data_table->id);
            
            $row = [];
            $row[] = $id;
            $row[] = $data_table->program_name;
            $row[] = 'Rp ' . number_format($data_table->credit, 0, ',', '.');
            $row[] = $data_table->branch_name;
            $row[] = date('d-m-Y', strtotime($data_table->created_at));
            $row[] = '
                    <button id="btnDetail" class="btn btn-primary text-white fw-semibold" 
                        data-id="'.$encrypted_id.'">
                        Detail
                    </button>
                    <button id="btnEdit" class="btn btn-warning text-white fw-semibold" 
                        data-id="' . $encrypted_id . '" 
                        data-program_id="' . $data_table->program_id . '"
                        data-program_name="' . $data_table->program_name . '"
                        data-branch_id="' . $data_table->branch_id . '"
                        data-percentage="' . $data_table->percentage . '"
                        data-credit="' . $data_table->credit . '"
                        data-beneficiaries="' . $beneficiaries_json . '">
                        Ubah 
                    </button>
                    <button id="btnDelete" class="btn btn-danger text-white fw-semibold" 
                        data-id="'.$encrypted_id.'">
                        Hapus
                    </button>
                ';
            // $row[] = $data_table->beneficaries_list;

            $data[] = $row;
        }

        return $this->response->setJSON(['data' => $data]);
    }

    public function dropdown()
    {
        is_ajax();

        $data_programs = DatabaseModel::get([
            'select' => 'a.*, b.name as branch_name, CONCAT(a.name, ", ", b.name) AS program_branch',
            'from' => 'programs a',
            'where' => ['a.is_deleted' => false],
            'join' => [
                'branches b, b.id = a.branches_id, left',
                'region c, c.id = b.region_id, left'
            ]
        ])->getResultObject();

        $data = [];
        foreach ($data_programs as $data_table) {
            $encrypted_id = encrypt_data($data_table->id);

            $row = [];
            $row[] = $data_table->id;
            $row[] = $data_table->program_branch;
            $row[] = $data_table->percentage;
            $row[] = $data_table->branches_id;

            $data[] = $row;
        }

        $response = [
        'data' => $data,
        ];

        return response()->setJSON($response);
    }


    public function get_income()
    {
        is_ajax();
        $currentMonth = date('m');
        $currentYear = date('Y');

        $branch_id = $this->request->getGet('branch_id');

        // $branch_id =  session()->get('branch_id');

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
            'citizens b, b.id = a.citizen_id, left',
            'users c, c.id = a.user_id, left',
            'rws d, d.id = b.rw_id, left',
            'programs e, e.id = a.program_id, left',
            'branches f, f.id = d.branch_id, left',
            'branches g, g.id = e.branches_id, left',
            'rws h, h.id = c.rw_id, left',
            'branches i, i.id = h.branch_id, left'
        ];

        // **Hitung total pendapatan bulanan**
        $total_monthly = $this->db->get([
            'select' => 'SUM(a.debit) as total_debit, SUM(a.credit) as total_credit',
            'from' => 'transactions a',
            'where' => $whereCondition,
            'join' => $join
        ])->getRow();

        $total_monthly_income = ($total_monthly->total_debit ?? 0) - ($total_monthly->total_credit ?? 0);

        return $this->response->setJSON([
            'total_monthly_income' => number_format($total_monthly_income, 0, ',', '.'),
        ]);
    }

    public function get_detail()
    {
        is_ajax();
        $id = decrypt_data($this->request->getGet('id'));

        // Query data transaksi
        $data_transaction = $this->db->get([
            'select' => "a.*, b.name as program_name, c.name as branch_name",
            'from' => 'transactions a',
            'where' => ['a.id' => $id],
            'join' => [
                'programs b, b.id = a.program_id, left',
                'branches c, c.id = b.branches_id, left'
            ]
        ])->getRowObject();

        // Query daftar beneficiaries
        $beneficiaries = $this->db->get([
            'select' => 'e.name as beneficaries_name',
            'from' => 'detail_fund d',
            'where' => [
                'd.transaction_id' => $id,
                'd.is_deleted' => false
            ],
            'join' => [
                'beneficaries e, e.id = d.beneficaries_id, left'
            ]
        ])->getResultObject();

        // Format hasil beneficiaries menjadi array nama
        $beneficiaries_names = array_map(fn($b) => $b->beneficaries_name, $beneficiaries);

        // Kirim data sebagai JSON
        return $this->response->setJSON([
            'status' => true,
            'transaction' => $data_transaction,
            'beneficiaries' => $beneficiaries_names
        ]);
    }


    public function save()
    {
        is_ajax();
        try {
            $request = $this->request->getPost();
            
            $program_id = $request['program_id'] ?? null;
            $beneficaries = $this->request->getPost('beneficaries') ?? [];

            if (isset($request['credit'])) {
                $request['credit'] = preg_replace('/[^0-9,]/', '', $request['credit']);
                $request['credit'] = str_replace(',', '.', $request['credit']);
            }

            // Validasi input
            $validation = [
                'program_id' => [
                    'label' => 'Program', 
                    'rules' => 'required'
                ],
                'credit' => [
                    'label' => 'Kredit', 
                    'rules' => 'required|numeric'
                ],
            ];
    
            validate($request, $validation);

            $db = \Config\Database::connect();

            // Mulai transaksi database
            $db->transStart();

            // Simpan transaksi ke tabel utama
            $transactionData = [
                'type' => 'fund_distribution',
                'program_id' => $program_id,
                'credit' => $request['credit'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $db->table('transactions')->insert($transactionData);
            // $lastId = DatabaseModel::insertData('programs', $transactionData);
            $transaction_id = $db->insertID();

            // Simpan beneficaries yang dipilih
            foreach ($beneficaries as $beneficaries_id) {
                $db->table('detail_fund')->insert([
                    'transaction_id' => $transaction_id,
                    'beneficaries_id' => $beneficaries_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            $db->transComplete();

            // Commit transaksi jika semua berhasil
            if ($db->transStatus() === false) {
                return response()->setJSON(['status' => false, 'message' => 'Gagal menyimpan data.']);
            }

            return response()->setJSON(['status' => true, 'message' => 'Data berhasil disimpan']);
        
        } catch (\Exception $e) {
            $db->transRollback();
            return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update()
    {
        is_ajax();
        try {
            $request = $this->request->getPost();
            
            $id = $request['id'] ?? null;
            $program_id = $request['program_id'] ?? null;
            $beneficaries = $this->request->getPost('beneficaries') ?? [];

            $decrypted_id = decrypt_data($id);

            if (!$decrypted_id) {
                return response()->setJSON(['status' => false, 'message' => 'ID transaksi tidak ditemukan.']);
            }

            if (isset($request['credit'])) {
                $request['credit'] = preg_replace('/[^0-9,]/', '', $request['credit']);
                $request['credit'] = str_replace(',', '.', $request['credit']);
            }

            // Validasi input
            $validation = [
                'program_id' => [
                    'label' => 'Program', 
                    'rules' => 'required'
                ],
                'credit' => [
                    'label' => 'Kredit', 
                    'rules' => 'required|numeric'
                ],
            ];

            validate($request, $validation);

            $db = \Config\Database::connect();

            $db->transStart();

            // Update transaksi di tabel utama
            $transactionData = [
                'program_id' => $program_id,
                'credit' => $request['credit'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $db->table('transactions')->where('id', $decrypted_id)->update($transactionData);

            // Hapus semua data lama dari detail_fund berdasarkan transaction_id
            $db->table('detail_fund')
            ->where('transaction_id', $decrypted_id)
            ->update([
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s')
            ]);

            // Simpan daftar beneficaries baru
            foreach ($beneficaries as $beneficaries_id) {
                $db->table('detail_fund')->insert([
                    'transaction_id' => $decrypted_id,
                    'beneficaries_id' => $beneficaries_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            $db->transComplete();

            // Commit transaksi jika semua berhasil
            if ($db->transStatus() === false) {
                return response()->setJSON(['status' => false, 'message' => 'Gagal memperbarui data.']);
            }

            return response()->setJSON(['status' => true, 'message' => 'Data berhasil diperbarui']);

        } catch (\Exception $e) {
            $db->transRollback();
            return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
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
            'error_string' => ['The ID Fund Distribution field is required.']
        ]);
        }

        // Validate the ID field
        $validation = [
        'id' => [
            'label' => 'ID Distribusi Dana', 
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

            // Update transactions with soft delete
            DatabaseModel::updateData(
                'transactions',
                ['id' => $decrypted_id],
                [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s')
                ]
            );

            // Update detail_fund with soft delete
            DatabaseModel::updateData(
                'detail_fund',
                ['transaction_id' => $decrypted_id],
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
