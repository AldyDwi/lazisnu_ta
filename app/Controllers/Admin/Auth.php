<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DatabaseModel;
use CodeIgniter\Email\Email;

class Auth extends BaseController
{
    protected $folder_directory = 'admin\\page\\auth\\view\\';
    protected $js = ['index'];

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $data['page_title'] = 'Login';
        $data['js'] = $this->js;
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'index', $data);
    }

    public function register_page()
    {
        $data['page_title'] = 'Register';
        $data['js'] = ['register'];
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'register', $data);
    }

    public function forgot_password_page()
    {
        $data['page_title'] = 'Lupa Password';
        $data['js'] = ['forgot_password'];
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'forgot_password', $data);
    }

    public function validate_otp_page()
    {
        $data['page_title'] = 'Validasi Kode OTP';
        $data['js'] = ['validate_otp'];
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'validate_otp', $data);
    }

    public function reset_password_page()
    {
        $data['page_title'] = 'Reset Password';
        $data['js'] = ['reset_password'];
        $data['folder_directory'] = $this->folder_directory;

        return view($this->folder_directory . 'reset_password', $data);
    }

    public function login()
    {
        // is_ajax();
        $data_post = $this->request->getPost();

        $validation = [
            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[4]|max_length[30]'
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[6]'
            ],
        ];

        validate($data_post, $validation);

        // Fetch user data from database with RW information
        $user = DatabaseModel::get([
            'select' => 'a.*, b.id as rw_id, b.name as rw_name, c.id as branch_id, c.name as branch_name', 
            'from' => 'users a',
            'where' => [
                'a.username' => $data_post['username'],
                'a.is_deleted' => false,
                // 'a.is_active' => true
            ],
            'join' => [
                'rws b, b.id = a.rw_id, left',
                'branches c, c.id = b.branch_id, left',
            ]
        ])->getRow();

        if (!$user) {
            return response()->setJSON([
                'status' => false,
                'message' => 'Username tidak ditemukan atau akun tidak aktif'
            ]);
        }

        if (!password_verify($data_post['password'], $user->password)) {
            return response()->setJSON([
                'status' => false,
                'message' => 'Password tidak valid'
            ]);
        }

        // // Update last login timestamp
        // DatabaseModel::updateData('users', [
        //     'last_login' => date('Y-m-d H:i:s')
        // ], ['id' => $user->id]);
        $token = $this->generateToken($user);
        // Set session data
        $this->session->set([
            'user_id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'role' => $user->role,
            'rw_id' => $user->rw_id,
            'branch_id' => $user->branch_id,
            'token' => $token,
            'isLoggedIn' => true,
        ]);

        if ($user->role === 'superadmin') {
            // Response for superadmin dashboard
            return response()->setJSON([
                'status' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'name' => $user->name,
                    'role' => $user->role,
                    'redirect' => base_url('/super-admin/dashboard'),
                    'token' => $token
                ]
            ]);
        } if ($user->role === 'admin') {
            // Response for admin dashboard
            return response()->setJSON([
                'status' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'name' => $user->name,
                    'role' => $user->role,
                    'branch_id' => $user->branch_id,
                    'branch_name' => $user->branch_name,
                    'redirect' => base_url('/dashboard'),
                    'token' => $token
                ]
            ]);
        } else {
            // Response for officer/petugas dashboard
            return response()->setJSON([
                'status' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'name' => $user->name,
                    'role' => $user->role,
                    'branch_id' => $user->branch_id,
                    'branch_name' => $user->branch_name,
                    'rw_id' => $user->rw_id,
                    'redirect' => base_url('/officer/dashboard'),
                    'token' => $token
                ]
            ]);
        }
    }   

    public function logout()
    {   
        $this->session->destroy();
        
        return response()->setJSON([
            'status' => true,
            'message' => 'Logout berhasil',
            'redirect' => base_url('/')
        ]);
    }
    
    // Endpoint untuk refresh token
    public function refresh()
    {
        is_ajax();
        
        // Ambil refresh token dari request
        $refresh_token = $this->request->getHeaderLine('X-Refresh-Token');
        
        if (empty($refresh_token)) {
            return response()->setJSON([
                'status' => false,
                'message' => 'Refresh token diperlukan'
            ])->setStatusCode(401);
        }
        
        try {
            // Validasi refresh token
            $decoded = jwt_decode($refresh_token);
            
            // Pastikan ini adalah refresh token
            if (!isset($decoded->type) || $decoded->type !== 'refresh') {
                throw new \Exception('Token tidak valid');
            }
            
            // Pastikan token belum kadaluarsa
            if (time() > $decoded->exp) {
                throw new \Exception('Refresh token sudah kadaluarsa');
            }
            
            // Ambil data user
            $user = DatabaseModel::get([
                'select' => 'a.*, b.id as rw_id, b.name as rw_name', 
                'from' => 'users a',
                'where' => [
                    'a.id' => $decoded->sub,
                    'a.is_deleted' => false,
                    'a.is_active' => true
                ],
                'join' => [
                    'rws b, b.id = a.rw_id, left',
                ]
            ])->getRow();
            
            if (!$user) {
                throw new \Exception('User tidak ditemukan');
            }
            
            // Generate token baru
            return response()->setJSON([
                'status' => true,
                'message' => 'Token berhasil diperbarui',
                'data' => [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'rw_id' => $user->rw_id,
                    'rw_name' => $user->rw_name,
                    'token' => $this->generateToken($user),
                    'refresh_token' => $this->generateRefreshToken($user->id) // Optional: Perbarui refresh token juga
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->setJSON([
                'status' => false,
                'message' => 'Failed to refresh token: ' . $e->getMessage()
            ])->setStatusCode(401);
        }
    }

    public function register()
    {
        // is_ajax();
        $data_post = $this->request->getPost();

        $validation = [
            'username' => [
                'label' => 'Username',
                'rules' => 'required|min_length[4]|max_length[30]|alpha_numeric|is_unique[users.username]'
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|is_unique[users.email]'
            ],
            'password' => [
                'label' => 'Password',
                'rules' => 'required|min_length[6]'
            ],
            'confirm_password' => [
                'label' => 'Konfirmasi Password',
                'rules' => 'required|matches[password]'
            ],
            'name' => [
                'label' => 'Nama Lengkap',
                'rules' => 'required|min_length[3]|max_length[100]'
            ],
            'phone' => [
                'label' => 'Nomor Telepon',
                'rules' => 'required|numeric|min_length[10]|max_length[20]'
            ],
            'role' => [
                'label' => 'Role',
                'rules' => 'required|in_list[admin,petugas,superadmin]'
            ],
        ];

        // Tambahan validasi jika rolenya petugas
        if ($data_post['role'] === 'petugas') {
            $validation['rw_id'] = [
                'label' => 'RW',
                'rules' => 'required|valid_reference[rws,id,Data RW tidak valid atau tidak ditemukan]'
            ];
        }

        validate($data_post, $validation);

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $array_insert = [
                'username' => $data_post['username'],
                'email' => $data_post['email'],
                'password' => password_hash($data_post['password'], PASSWORD_BCRYPT),
                'name' => $data_post['name'],
                'phone' => $data_post['phone'],
                'role' => $data_post['role'],
                'rw_id' => $data_post['role'] === 'petugas' ? $data_post['rw_id'] : null,
                'is_active' => true,
                'is_deleted' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $lastId = DatabaseModel::insertData('users', $array_insert);
            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return response()->setJSON(['status' => false, 'message' => 'Gagal mendaftarkan pengguna baru']);
            }
            
            // Enkripsi ID
            $encrypted_id = encrypt_data($lastId);
            
        } catch (\Exception $e) {
            $db->transRollback();
            return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }

        return response()->setJSON([
            'status' => true,
            'message' => 'Registrasi berhasil',
            'id' => $encrypted_id
        ]);
    }

    public function forgotPassword()
    {
        $data_post = $this->request->getPost();
        $db = \Config\Database::connect();
        
        // Cari user berdasarkan username
        $user = $db->table('users')->where('username', $data_post['username'])->get()->getRow();

        if (!$user) {
            return $this->response->setJSON(['status' => false, 'message' => 'Username tidak terdaftar']);
        }

        $validation = [
            'username' => [
                'label' => 'Username',
                'rules' => 'required'
            ]
        ];

        validate($data_post, $validation);

        // Generate OTP
        $otp = random_int(100000, 999999);
        $expired_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $db->transStart();

        try {
            // Update token berdasarkan username
            $db->table('users')
            ->where('username', $data_post['username'])
            ->update([
                'reset_token' => $otp,
                'reset_token_expired_at' => $expired_at,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Kirim email menggunakan email dari hasil query user
            $this->sendResetEmail($user->email, $otp);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return $this->response->setJSON(['status' => false, 'message' => 'Gagal memproses permintaan reset password']);
            }

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }

        // Tetap mengirim email dalam response
        return $this->response->setJSON([
            'status' => true,
            'message' => 'Kode OTP telah dikirim ke email Anda.',
            'email' => $user->email
        ]);
    }


    public function validateOtp()
    {
        $data_post = $this->request->getPost();
        $db = \Config\Database::connect();

        $user = $db->table('users')
            ->select('id, email')
            ->where('reset_token', $data_post['otp'])
            ->where('reset_token_expired_at >=', date('Y-m-d H:i:s'))
            ->get()
            ->getRow();

        if (!$user) {
            return $this->response->setJSON(['status' => false, 'message' => 'OTP tidak valid atau sudah kedaluwarsa']);
        }

        // Hapus OTP setelah validasi berhasil
        $db->table('users')->where('id', $user->id)->update([
            'reset_token' => null,
            'reset_token_expired_at' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'OTP valid, silakan lanjutkan reset password',
            'email' => $user->email
        ]);
    }


    public function resetPassword()
    {
        $data_post = $this->request->getPost();
        $db = \Config\Database::connect();

        // Validasi input
        $validation = [
            'email' => [
                'label' => 'Email',
                'rules' => 'required'
            ],
            'password' => [
                'label' => 'Password Baru',
                'rules' => 'required|min_length[6]'
            ],
            'confirm_password' => [
                'label' => 'Konfirmasi Password',
                'rules' => 'required|matches[password]'
            ]
        ];

        validate($data_post, $validation);

        $db->transStart();

        try {
            // Hash password baru
            $hashed_password = password_hash($data_post['password'], PASSWORD_BCRYPT);

            // Update password dan hapus token reset
            $db->table('users')->where('email', $data_post['email'])->update([
                'password' => $hashed_password,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                return $this->response->setJSON(['status' => false, 'message' => 'Gagal mereset password']);
            }

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Password berhasil direset, silakan login dengan password baru'
        ]);
    }

    public function changePassword()
    {
        $data_post = $this->request->getPost();
        $userId = $this->session->get('user_id');

        // Validasi Input
        $validation = [
            'current_password' => [
                'label' => 'Password Saat Ini',
                'rules' => 'required'
            ],
            'new_password' => [
                'label' => 'Password Baru',
                'rules' => 'required|min_length[6]'
            ],
            'confirm_password' => [
                'label' => 'Konfirmasi Password',
                'rules' => 'required|matches[new_password]'
            ]
        ];

        validate($data_post, $validation);

        $db = \Config\Database::connect();
        $db->transStart(); // Mulai transaksi

        try {
            // Ambil password lama dari database
            $user = $db->table('users')->where('id', $userId)->get()->getRow();
            
            if (!$user) {
                return response()->setJSON(['status' => false, 'message' => 'User tidak ditemukan']);
            }

            // Cek apakah password saat ini cocok dengan database
            if (!password_verify($data_post['current_password'], $user->password)) {
                return response()->setJSON(['status' => false, 'message' => 'Password saat ini salah']);
            }

            // Hash password baru sebelum menyimpan
            $hashedPassword = password_hash($data_post['new_password'], PASSWORD_BCRYPT);

            // Update password
            $db->table('users')
                ->where('id', $userId)
                ->update([
                    'password' => $hashedPassword,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

            $db->transComplete(); // Selesaikan transaksi

            if ($db->transStatus() === false) {
                return response()->setJSON(['status' => false, 'message' => 'Gagal mengubah password']);
            }

            return response()->setJSON(['status' => true, 'message' => 'Password berhasil diubah']);
            
        } catch (\Exception $e) {
            $db->transRollback();
            return response()->setJSON(['status' => false, 'message' => $e->getMessage()]);
        }
    }


    private function generateToken($user)
    {
        $key = getenv('JWT_SECRET') ?: 'your-secret-key';
        $payload = [
            'iss' => base_url(),
            'aud' => base_url(),
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour expiry
            'sub' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'rw_id' => $user->rw_id,
            'rw_name' => $user->rw_name ?? null
        ];

        return jwt_encode($payload, $key);
    }
    
    private function generateRefreshToken($userId)
    {
        $key = getenv('JWT_SECRET') ?: 'your-secret-key';
        $expiry = 604800; // 7 hari
        
        $payload = [
            'iss' => base_url(),
            'iat' => time(),
            'exp' => time() + $expiry,
            'sub' => $userId,
            'type' => 'refresh' // Penanda bahwa ini refresh token
        ];
        
        return jwt_encode($payload, $key);
    }

    private function sendResetEmail($email, $token)
    {
        $email_service = \Config\Services::email();
        
        $email_service->setFrom('emailujicoba456@gmail.com', 'Admin Website');
        $email_service->setTo($email);
        $email_service->setSubject('Kode OTP Reset Password');
        
        // $reset_link = base_url('reset-password/' . $token);
        // $message = "Untuk mereset password Anda, silakan klik link berikut: {$reset_link}. Link akan kadaluarsa dalam 1 jam.";

        $message = "
            <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f9f9f9;'>
                <h3 style='color: #333; text-align: center;'>Kode OTP Anda</h3>
                <p style='font-size: 16px; color: #555; text-align: center;'>Gunakan kode OTP berikut untuk reset password:</p>
            
                <div style='text-align: center; background-color: #ffecec; border: 2px dashed #ff4d4d; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                    <h2 style='color: #ff4d4d; font-size: 24px; margin: 0;'>$token</h2>
                </div>

                <p style='font-size: 14px; color: #777; text-align: center;'>Kode ini berlaku selama <strong>10 menit</strong>.</p>
            
                <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
            
                <p style='font-size: 12px; color: #aaa; text-align: center;'>Jika Anda tidak meminta reset password, abaikan email ini.</p>
            </div>  
        ";
        
        $email_service->setMessage($message);
        
        // Uncomment to actually send email
        if ($email_service->send()) {
            return "Email berhasil dikirim.";
        } else {
            return "Gagal mengirim email: " . $email_service->printDebugger(['headers']);
        }
        
        // For development/testing purposes
        log_message('info', 'Reset password link: ' . $reset_link);
    }

    private function decrypt($encrypted_id)
    {
        $encrypter = \Config\Services::encrypter();
        try {
            return $encrypter->decrypt(base64_decode($encrypted_id));
        } catch (\Exception $e) {
            return $encrypted_id; // Fallback jika dekripsi gagal
        }
    }

    private function encrypt($id)
    {
        return encrypt_data($id);
    }
}