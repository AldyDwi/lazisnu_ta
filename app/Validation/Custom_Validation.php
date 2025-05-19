<?php

namespace App\Validation;

use App\Models\DatabaseModel;

class Custom_Validation
{
    /**
     * Validasi required dengan pesan kustom
     */
    public function is_required(string $value, string $error_message = null, array $data, string &$error = null): bool
    {
        $value = trim($value);
        if (empty($value)) {
            $error = $error_message ?: '{field} harus diisi';
            return false;
        }

        return true;
    }

    /**
     * Validasi format tanggal yang valid
     */
    public function valid_date(string $value, string $params = null, array $data, string &$error = null): bool
    {
        // Jika nilai kosong, langsung kembalikan true
        if (empty($value) || $value === null) {
            return true;
        }
        
        // Default format adalah Y-m-d, tapi bisa diganti dengan parameter
        $format = $params ?: 'Y-m-d';
        
        $d = \DateTime::createFromFormat($format, $value);
        
        // Periksa apakah format tanggal valid dan tanggal itu sendiri valid
        if (!$d || $d->format($format) !== $value) {
            $error = $error_message ?? 'Format {field} tidak valid. Format yang benar: ' . $format;
            return false;
        }
        
        return true;
    }

    /**
     * Validasi format tanggal yang valid dan wajib diisi
     */
    public function required_date(string $value, string $params = null, array $data, string &$error = null): bool
    {
        if (empty($value) || $value === null) {
            $error = '{field} harus diisi';
            return false;
        }
        
        // Default format adalah Y-m-d, tapi bisa diganti dengan parameter
        $format = $params ?: 'Y-m-d';
        
        $d = \DateTime::createFromFormat($format, $value);
        
        // Periksa apakah format tanggal valid dan tanggal itu sendiri valid
        if (!$d || $d->format($format) !== $value) {
            $error = $error_message ?? 'Format {field} tidak valid. Format yang benar: ' . $format;
            return false;
        }
        
        return true;
    }

    /**
     * Validasi nilai numerik yang memperbolehkan nilai kosong
     */
    public function numeric_permit_empty(string $value, string $error_message = null, array $data, string &$error = null): bool
    {
        // Jika nilai kosong, langsung kembalikan true
        if (empty($value) || $value === null) {
            return true;
        }
        
        // Jika nilai tidak kosong, pastikan numerik
        if (!is_numeric($value)) {
            $error = $error_message ?: '{field} harus berupa angka';
            return false;
        }
        
        return true;
    }

    /**
     * Validasi format angka dengan ribuan yang memperbolehkan nilai kosong
     */
    public function decimal_permit_empty(string $value, string $error_message = null, array $data, string &$error = null): bool
    {
        // Jika nilai kosong, langsung kembalikan true
        if (empty($value) || $value === null) {
            return true;
        }
        
        // Hapus format ribuan (titik) dan ganti koma dengan titik
        $clean_value = str_replace('.', '', $value);
        $clean_value = str_replace(',', '.', $clean_value);
        
        // Periksa apakah hasil konversi adalah numerik
        if (!is_numeric($clean_value)) {
            $error = $error_message ?: '{field} harus berupa angka';
            return false;
        }
        
        return true;
    }

    /**
     * Validasi rentang tanggal
     */
    public function valid_date_range(string $text, string $fields, array $data, string &$error = null): bool
    {
        list($startField, $endField) = explode(',', $fields);
        $startDate = strtotime($data[$startField]);
        $endDate = strtotime($data[$endField]);

        if ($endDate < $startDate) {
            $error = 'Tanggal akhir harus lebih besar atau sama dengan tanggal mulai';
            return false;
        }

        return true;
    }

    /**
     * Validasi nama duplikat dalam tabel
     */
    public function is_unique_name(string $name, string $params, array $data, string &$error = null): bool
    {
        // params format: "table,id_field,id_value"
        // contoh: "provinces,id,5" - untuk update, jika ada id
        // contoh: "provinces" - untuk insert, tanpa id

        $params_array = explode(',', $params);
        $table = $params_array[0];

        $where = ['name' => $name, 'is_deleted' => false];

        // Jika ini adalah operasi update, kecualikan ID saat ini
        if (count($params_array) > 2 && isset($data[$params_array[1]])) {
            $where[$params_array[1] . ' !='] = $data[$params_array[1]];
        }

        $count = DatabaseModel::get([
            'from' => $table,
            'where' => $where
        ])->getNumRows();

        if ($count > 0) {
            $error = 'Nama ini sudah digunakan';
            return false;
        }

        return true;
    }

    /**
     * Validasi jika record masih digunakan oleh tabel lain
     */
    public function is_not_used_in(string $id, string $params, array $data, string &$error = null): bool
    {
        // params format: "table,foreign_key_field,error_message"
        // contoh: "cities,province_id,Provinsi ini masih digunakan di data kota"
        list($table, $foreign_key, $error_message) = explode(',', $params);

        $count = DatabaseModel::get([
            'from' => $table,
            'where' => [$foreign_key => $id, 'is_deleted' => false]
        ])->getNumRows();

        if ($count > 0) {
            $error = $error_message;
            return false;
        }

        return true;
    }

    /**
     * Validasi apakah ID referensi ada di tabel lain
     */
    public function valid_reference(string $id, string $params, array $data, string &$error = null): bool
    {
        // params format: "table,id_field,error_message"
        // contoh: "branches,id,ID Cabang tidak valid"
        list($table, $id_field, $error_message) = explode(',', $params);

        // Jika ID kosong, anggap valid (untuk field opsional)
        if (empty($id)) {
            return true;
        }

        $count = DatabaseModel::get([
            'from' => $table,
            'where' => [$id_field => $id, 'is_deleted' => false]
        ])->getNumRows();

        if ($count == 0) {
            $error = $error_message;
            return false;
        }

        return true;
    }

    /**
     * Validasi status transaksi (apakah sudah ditutup)
     */
    public function transaction_not_closed(string $id, string $error_message = null, array $data, string &$error = null): bool
    {
        $transaction = DatabaseModel::get([
            'from' => 'transactions',
            'where' => ['id' => $id]
        ])->getRow();

        if (!$transaction) {
            $error = 'Transaksi tidak ditemukan';
            return false;
        }

        if ($transaction->status === 'close') {
            $error = $error_message ?: 'Transaksi sudah ditutup dan tidak dapat dimodifikasi';
            return false;
        }

        return true;
    }

    /**
     * Validasi format numerik dengan konversi
     */
    public function valid_decimal(string $value, string $error_message = null, array $data = [], string &$error = null): bool
    {
        // Menghapus format ribuan dan konversi ke floating point
        $value = str_replace('.', '', $value);
        $value = str_replace(',', '.', $value);

        if (!is_numeric($value)) {
            $error = $error_message ?: 'Nilai harus berupa angka';
            return false;
        }

        return true;
    }

    /**
     * Validasi apakah nilai dalam range persentase (0-100)
     */
    public function valid_percentage(string $value, string &$error = null, array $data = [], string $error_message = null): bool
    {
        $value = (float) $value;

        if ($value < 0 || $value > 100) {
            $error = $error_message ?: 'Persentase harus di antara 0 dan 100';
            return false;
        }

        return true;
    }

    /**
     * Validasi tipe transaksi
     */
    public function valid_transaction_type(string $type, string $allowed_types, array $data, string &$error = null): bool
    {
        $allowed = explode(',', $allowed_types);

        if (!in_array($type, $allowed)) {
            $error = 'Tipe transaksi tidak valid. Tipe yang diizinkan: ' . $allowed_types;
            return false;
        }

        return true;
    }

    /**
     * Validasi bulan
     */
    public function valid_month(string $month, string $error_message = null, array $data = [], string &$error = null): bool
    {
        $month = (int) $month;

        if ($month < 1 || $month > 12) {
            $error = $error_message ?: 'Bulan harus di antara 1 dan 12';
            return false;
        }

        return true;
    }

    /**
     * Validasi tahun
     */
    public function valid_year(string $year, string $error_message = null, array $data = [], string &$error = null): bool
    {
        $year = (int) $year;
        $current_year = (int) date('Y');

        if ($year < 2000 || $year > $current_year + 5) {
            $error = $error_message ?: 'Tahun tidak valid';
            return false;
        }

        return true;
    }
}