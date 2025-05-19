<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Donations extends BaseController
{
    protected $folder_directory = 'admin\\page\\donations\\view\\';
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
        $data['status'] = $status; // Kirim status ke view
        $data['page_title'] = 'Donasi Warga | Admin';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        // Ambil input filter tanggal dari request
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        // Jika tidak ada filter tanggal, gunakan bulan dan tahun saat ini
        if (!$start_date || !$end_date) {
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
        }

        $branch_id = session()->get('branch_id');

        // Query data transaksi dengan filter tanggal
        $data_transactions = DatabaseModel::get([
            'select' => 'a.*, b.name as citizen_name, c.name as user_name, d.name as rw_name',
            'from' => 'transactions a',
            'where' => [
                'a.is_deleted' => false,
                'a.type' => 'donations',
                'd.branch_id' => $branch_id,
                'DATE(a.created_at) >=' => $start_date,
                'DATE(a.created_at) <=' => $end_date,
            ],
            'join' => [
                'citizens b, b.id = a.citizen_id, left',
                'users c, c.id = a.user_id, left',
                'rws d, d.id = a.rw_id, left',
            ],
            'order' => ['a.created_at' => 'DESC']
        ])->getResultObject();

        $data = [];
        foreach ($data_transactions as $data_table) {
            $encrypted_id = encrypt_data($data_table->id);
            
            $row = [];
            $row[] = $data_table->id;
            $row[] = $data_table->citizen_name;
            $row[] = $data_table->user_name;
            $row[] = $data_table->rw_name;
            $row[] = 'Rp ' . number_format($data_table->debit, 0, ',', '.');
            $row[] = date('d-m-Y', strtotime($data_table->created_at));
            $row[] = $data_table->status;
            $row[] = $data_table->note;
            $row[] = '
                    <button id="btnEdit" class="btn btn-warning text-white fw-semibold" 
                        data-id="' . $encrypted_id . '" 
                        data-citizen_id="' . $data_table->citizen_id . '"
                        data-citizen_name="' . $data_table->citizen_name . '"
                        data-user_id="' . $data_table->user_id . '"
                        data-user_name="' . $data_table->user_name . '"
                        data-rw_id="' . $data_table->rw_id . '"
                        data-rw_name="' . $data_table->rw_name . '"
                        data-debit="' . $data_table->debit . '"
                        data-note="' . $data_table->note . '">
                        Ubah 
                    </button>
                ';

            $data[] = $row;
        }

        return $this->response->setJSON(['data' => $data]);
    }

    public function citizen_dropdown()
    {
        // is_ajax();
        $branch_id =  session()->get('branch_id');

        $data_citizens = DatabaseModel::get([
            'select' => 'a.*, b.name as rw_name, c.name as region_name, d.name as branch_name',
            'from' => 'citizens a',
            'where' => [
                'a.is_deleted' => false,
                '(d.id = ' . $branch_id . ' OR a.rw_id IS NULL)' => null,
                'a.status' => 'not yet',
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

            $data[] = $row;
        }

        $response = [
            'data' => $data,
        ];

        return response()->setJSON($response);
    }

    private function convertToDecimalFormat($input)
    {
        if (empty($input)) {
        return null;
        }

        // Menghapus semua karakter kecuali angka dan koma/titik
        $cleanedInput = preg_replace('/[^\d.,]/', '', $input);
        // Menghapus pemisah ribuan
        $cleanedInput = str_replace('.', '', $cleanedInput);
        // Mengganti koma desimal dengan titik jika ada
        $cleanedInput = str_replace(',', '.', $cleanedInput);
        // Mengonversi ke format desimal
        return number_format((float)$cleanedInput, 2, '.', '');
    }

    public function update()
    {
        // is_ajax();
        $data_post = $this->request->getPost();

        $validation = [
            'id' => [
                'label' => 'ID Transaksi',
                'rules' => 'required'
            ],
            'citizen_id' => [
                'label' => 'Warga',
                'rules' => 'required|valid_reference[citizens,id,Data warga tidak valid atau tidak ditemukan]'
            ],
            'debit' => [
                'label' => 'Jumlah Donasi',
                'rules' => 'required',
            ],
        ];

        validate($data_post, $validation);

        // Decrypt ID
        $decrypted_id = $this->decrypt($data_post['id']);

        if (!$decrypted_id) {
        return response()->setJSON(['status' => false, 'message' => 'Invalid ID']);
        }

        // Periksa status transaksi
        $transaction = DatabaseModel::get([
        'from' => 'transactions',
        'where' => ['id' => $decrypted_id],
        ])->getRow();

        if (!$transaction) {
        return response()->setJSON(['status' => false, 'message' => 'Transaksi tidak ditemukan']);
        }

        if ($transaction->status === 'close') {
        return response()->setJSON(['status' => false, 'message' => 'Transaksi sudah ditutup dan tidak dapat diedit']);
        }

        // Konversi format angka jika ada
        if (isset($data_post['debit'])) {
        $data_post['debit'] = $this->convertToDecimalFormat($data_post['debit']);
        }

        $array_update = [
        'citizen_id' => $data_post['citizen_id'] ?? $transaction->citizen_id,
        'debit' => $data_post['debit'] ?? $transaction->debit,
        'note' => $data_post['note'] ?? $transaction->note,
        'updated_at' => date('Y-m-d H:i:s')
        ];

        // Where condition
        $where_condition = ['id' => $decrypted_id];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Use the correct parameter order for updateData
            DatabaseModel::updateData('transactions', $where_condition, $array_update);

            if ($transaction->citizen_id != $data_post['citizen_id']) {
                // ID citizen lama
                $old_citizen_id = $transaction->citizen_id;

                // Update status citizen lama menjadi 'not yet'
                $db->table('citizens')
                    ->where('id', $old_citizen_id)
                    ->update([
                        'status' => 'not yet',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $db->table('citizens')
                ->where('id', $data_post['citizen_id'])
                ->update([
                    'status' => 'already',
                    'updated_at' => date('Y-m-d H:i:s'),
            ]);

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
