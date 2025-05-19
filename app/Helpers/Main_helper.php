<?php

use App\Models\DatabaseModel;
use CodeIgniter\I18n\Time;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * Check if the request is an AJAX request or not.
 *
 * @return boolean
 * @throws \RuntimeException
 */
function is_ajax()
{
    $request = \Config\Services::request();
    if (!$request->isAjax()) {
        echo json_encode('No direct script access allowed');
        exit();
    }
}

/**
 * @code
 * $rules = [
 *           'username' => [
 *              'label' => 'Nama Pengguna',
 *                'rules' => 'required'
 *            ],
 *            'password' => [
 *                'label' => 'Kata Sandi',
 *                'rules' => 'required'
 *            ],
 *        ]
 */
function validate(array $data, array $rules, $isApi = false)
{
    $validation = \Config\Services::validation();
    $validation->setRules($rules);

    if (!$validation->run($data)) {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['error_message'] = [];
        $data['status'] = FALSE;
        $errors = $validation->getErrors();

        foreach ($errors as $key => $value) {
            $data['inputerror'][] = $key;
            $data['error_string'][] = $value;
            $data['error_message'][$key] = $value;
        }

        if ($isApi) {
            return ['status' => false, 'message' => $data['error_message']];
        }

        unset($data['error_message']);
        $response = \Config\Services::response();
        $response->setJSON($data)->send();
        exit;
    }

    return true;
}

/**
 * Encrypt the given data.
 *
 * @param string $data_encrypt The data to be encrypted
 *
 * @return string The encrypted data
 */
function encrypt_data($data)
{
    // Convert any non-string data to string before encryption
    if (!is_string($data)) {
        $data = (string) $data;
    }

    $encrypter = \Config\Services::encrypter();
    return bin2hex($encrypter->encrypt($data));
}

function decrypt_data($data)
{
    if (empty($data)) {
        return null;
    }

    try {
        $encrypter = \Config\Services::encrypter();
        $decrypted = $encrypter->decrypt(hex2bin($data));

        // If the original data was numeric, convert it back
        if (is_numeric($decrypted)) {
            if (strpos($decrypted, '.') !== false) {
                return (float) $decrypted;
            } else {
                return (int) $decrypted;
            }
        }

        return $decrypted;
    } catch (\Exception $e) {
        log_message('error', 'Decryption error: ' . $e->getMessage());
        return null;
    }
}


function get_uniq_name()
{
    return Time::now()->toDateString() . '-' . uniqid();
}

function upload_file(string $field_name, string $upload_path, bool $compress_webp = true, array $allowed_types = ['*'], int $maxSize = 0, $file_name = '')
{
    $request = service('request');
    $file = $request->getFile($field_name);

    // Cek jika file ada
    if (!$file->isValid()) {
        return [
            'error' => true,
            'msg' => 'File tidak valid atau tidak ditemukan.'
        ];
    }

    // Validasi jenis file
    if (!empty($allowed_types) && !in_array($file->getClientMimeType(), $allowed_types)) {
        return [
            'error' => true,
            'msg' => 'Jenis file tidak diizinkan.'
        ];
    }

    // Validasi ukuran file
    if ($maxSize > 0 && $file->getSizeByUnit('kb') > $maxSize) {
        return [
            'error' => true,
            'msg' => 'Ukuran file melebihi batas.'
        ];
    }

    // Tentukan nama file baru untuk menghindari duplikasi
    $file_ext = $file->getExtension();
    $new_filename =  get_uniq_name() . '.' . $file_ext;

    if ($file_name) {
        $new_filename = $file_name;
    }

    $file->move($upload_path, $new_filename);

    if ($file_ext !== 'webp') {
        // Check if file is not webp then compress
        if ($compress_webp) {
            $new_filename = webp_image($upload_path . '/' . $new_filename, 50);
        }

        // Pindahkan file ke folder tujuan
    }

    return $new_filename;
}

function webp_image($source, $quality = 100, $removeOld = false)
{
    $dir = pathinfo($source, PATHINFO_DIRNAME);
    $name = pathinfo($source, PATHINFO_FILENAME);
    $file_name = $name . '.webp';
    $destination = $dir . DIRECTORY_SEPARATOR . $file_name;
    $info = getimagesize($source);
    $isAlpha = false;
    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source);
    elseif ($isAlpha = $info['mime'] == 'image/gif') {
        $image = imagecreatefromgif($source);
    } elseif ($isAlpha = $info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
    } else {
        return $source;
    }
    if ($isAlpha) {
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    }
    imagewebp($image, $destination, $quality);

    if ($removeOld)
        unlink($source);

    return $file_name;
}

function check_image($image, $location, $default = 'default.webp', $with_path = FALSE)
{
    $image = empty($image) ? $default : (file_exists(APPPATH . '../public/' . $location . $image) ? $image : $default);
    if ($with_path) {
        $image = base_url($location) . $image;
    }
    return $image;
}

/**
 * ===============================
 * JWT Helper Functions
 * ===============================
 */

/**
 * JWT Encode Helper
 *
 * @param array $payload Data yang akan disertakan dalam token
 * @param string|null $key Secret key untuk signing (opsional)
 * @param string $alg Algoritma yang digunakan (default: HS256)
 * @return string Token JWT yang sudah di-encode
 */
if (!function_exists('jwt_encode')) {
    function jwt_encode($payload, $key = null, $alg = 'HS256')
    {
        if ($key === null) {
            $key = getenv('JWT_SECRET') ?: 'your-fallback-secret-key';
        }

        return JWT::encode($payload, $key, $alg);
    }
}

/**
 * JWT Decode Helper
 *
 * @param string $token Token JWT yang akan di-decode
 * @param string|null $key Secret key untuk verifikasi (opsional)
 * @param array|string $alg Algoritma yang digunakan (default: HS256)
 * @return object Data yang telah di-decode dari token
 */
if (!function_exists('jwt_decode')) {
    function jwt_decode($token, $key = null, $alg = 'HS256')
    {
        if ($key === null) {
            $key = getenv('JWT_SECRET') ?: 'your-fallback-secret-key';
        }

        return JWT::decode($token, new Key($key, $alg));
    }
}

/**
 * Generate Standard JWT Payload
 *
 * @param array $userData Data user yang akan disertakan dalam token
 * @param int $expiry Waktu kedaluwarsa dalam detik (default: 3600)
 * @return array Payload JWT
 */
if (!function_exists('jwt_payload')) {
    function jwt_payload($userData, $expiry = 3600)
    {
        $time = time();

        return [
            'iss' => base_url(),           // Issuer
            'aud' => base_url(),           // Audience
            'sub' => $userData['id'] ?? 0, // Subject (biasanya ID user)
            'iat' => $time,                // Issued at time
            'exp' => $time + $expiry,      // Expiration time
            // Data user tambahan
            'user' => $userData,
        ];
    }
}

/**
 * Validate JWT Token
 *
 * @param string $token Token JWT yang akan divalidasi
 * @return object|false Data yang telah di-decode atau false jika tidak valid
 */
if (!function_exists('jwt_validate')) {
    function jwt_validate($token)
    {
        try {
            $decoded = jwt_decode($token);

            // Cek waktu kedaluwarsa
            if (isset($decoded->exp) && time() > $decoded->exp) {
                return false;
            }

            return $decoded;
        } catch (Exception $e) {
            log_message('error', 'JWT Validation Error: ' . $e->getMessage());
            return false;
        }
    }
}

/**
 * Extract JWT Token from Authorization Header
 *
 * @param string $header Authorization header
 * @return string|null Token JWT atau null jika tidak ditemukan
 */
if (!function_exists('jwt_extract_token')) {
    function jwt_extract_token($header)
    {
        if (empty($header)) {
            return null;
        }

        if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }
}

/**
 * Generate Refresh Token
 *
 * @param int $userId ID user
 * @param int $expiry Waktu kedaluwarsa dalam detik (default: 7 hari)
 * @return string Refresh token
 */
if (!function_exists('jwt_refresh_token')) {
    function jwt_refresh_token($userId, $expiry = 604800)
    {
        $time = time();

        $payload = [
            'iss' => base_url(),
            'sub' => $userId,
            'iat' => $time,
            'exp' => $time + $expiry,
            'type' => 'refresh'
        ];

        return jwt_encode($payload);
    }
}
