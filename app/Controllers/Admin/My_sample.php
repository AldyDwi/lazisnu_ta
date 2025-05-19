<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\HTTP\ResponseInterface;

class My_sample extends BaseController
{
    protected $folder_directory = 'admin\\page\\example\\view\\';
    protected $js = ['index'];
    public function __construct() {}

    # Rule Controller
    # 1. Penulisan nama controller harus diawali dengan huruf kapital dan menggunakan 
    #    underscore untuk pemisah kata
    # 2. Namespace controller harus sesuai dengan path file controller
    # 3. Impor kelas yang wajib ditambahkan adalah BaseController, dan DatabaseModel
    # 4. Nama method dan property menggunakan snake_case
    # 5. Untuk property yang wajib diisi adalah folder_directory dan js
    # 6. Property folder_directory digunakan untuk menyimpan path folder view dan file js 
    # 7. Property js digunakan untuk menyimpan nama file js yang akan digunakan pada view
    # 8. Untuk penamaan method CRUD menggunakan save, edit, update, dan delete
    # 9. Untuk method yang diakses dengan AJAX gunakan fungsi is_ajax() pada awal method

    # Rule Method
    # 1. Di method yang menghasilkan return view harus ditambahkan variabel js dan folder_directory 
    #    digunakan untuk mendapatkan lokasi file view dan js, yang nanti ditambahkan ke parameter view
    # 2. Untuk data yang dibutuhkan di awal load / awal akses halaman view, bisa ditambahkan di 
    #    dalam method dengan melakukan query ke database 
    # 3. Untuk method yang diakses dengan AJAX harus menambahkan fungsi is_ajax() pada awal method
    # 4. Untuk return method yang berupa data gunakan response json Ex. return response()->setJSON($response); 
    # 5. Untuk response code bisa ditambahkan manual atau dibiarkan default
    # 6. Untuk struktur response data menggunakan Ex. 
    #   $response = [
    #      'status' => true, // jika tidak ada error, false jika ada error
    #      'message' => 'Data berhasil disimpan', // pesan yang akan ditampilkan
    #      'data' => $data,
    #   ]; 
    # 7. Gunakan try catch untuk menangkap error, dan jika terjadi error kirim response error
    # 8. Gunakan transaksi database untuk insert, update, dan delete data, agar rollback data jika terjadi error 

    # Rule Database 
    # 1. Query database menggunakan DatabaseModel dengan melakukan static method call 
    #    Ex. DatabaseModel::getData
    # 2. Query database menggunakan method getData(mengambil data berdasarkan kolom tertentu), 
    #    insertData, updateData, deleteData, dan get (mengambil data berdasarkan query tertentu)
    # 3. Jika menggunakan method get untuk bagian select ambil data yang dibutuhkan saja jangan pakai *,
    #    dan jika menggunakan join gunakan left, right, atau inner, untuk nama tabel gunakan alias
    #    yang singkat Ex. tb_region a tidak perlu seperti tb_region as region atau tb_region as a 
    # 4. Untuk join gunakan array dengan key join, berisi list tabel yang dijoin. Ex. 
    #    'join' => [
    #        'region b, b.id = a.region_id, left', // format nama tabel alias, kondisi join, dan jenis join
    #        'city c, c.id = a.city_id, left'
    #    ]
    # 5. Untuk result data pakai result berbentuk object Ex. getResultObject() 

    # Rule Helper
    # 1. Helper digunakan untuk mempercepat pengerjaan dan berupa function yang berisi fungsi untuk validasi, encrypt dan lainnya 
    # 2. Gunakan helper untuk validasi input, ex. 
    #    validate($data_post, $validation);
    #    $validation = [
    #     'name' => ['label' => 'Nama Cabang', 'rules' => 'required'], // key name dari data_post, label untuk pesan error, rules untuk validasi
    #    ];
    #    return dari validasi dalam bentuk response json. Ex.
    # 3. Untuk response error validasi gunakan format response berikut Ex. 
    #   {
    #       "error_string": [
    #           "Bidang Judul Materi diperlukan.",
    #       ],
    #       "inputerror": [
    #           "title_session",
    #       ],
    #       "status": false
    #    }

    public function index()
    {
        $data_branches = DatabaseModel::get([
            'select' => 'a.*, b.name as region_name',
            'from' => 'branches a',
            'where' => ['a.is_deleted' => false],
            'join' => [
                'region b, b.id = a.region_id, left',
            ]
        ])->getResultObject();

        $data['data_branches'] = $data_branches;
        $data['page_title'] = 'Sample';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function list_data()
    {
        is_ajax();

        $data_branches = DatabaseModel::get([
            'select' => 'a.*, b.name as region_name',
            'from' => 'branches a',
            'where' => ['a.is_deleted' => false],
            'join' => [
                'region b, b.id = a.region_id, left',
            ]
        ])->getResultObject();

        $i = 0;
        $data = [];
        foreach ($data_branches as $data_table) {
            $row = [];
            $row[] = ++$i;
            $row[] = $data_table->name;
            $row[] = $data_table->region_name;
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
            'name' => ['label' => 'Nama Cabang', 'rules' => 'required'],
            'date_start' => ['label' => 'Tanggal Mulai', 'rules' => 'required'],
            'date_end' => ['label' => 'Tanggal Akhir', 'rules' => 'required|valid_date_range[date_start,date_end]'],
        ];

        validate($data_post, $validation);

        $array_insert = [
            'name' => $data_post['name']
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $lastId = DatabaseModel::insertData('branches', $array_insert);
            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return response()->setJSON(['status' => false, 'message' => 'Failed to save data']);
            }
        } catch (\Exception $e) {
            $db->transRollback();
            return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }

        return response()->setJSON(['status' => TRUE, 'message' => 'Data berhasil disimpan']);
    }
}
