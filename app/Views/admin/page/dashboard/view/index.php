<?= $this->extend("admin/layout/view/index") ?>
<?= $this->section('content'); ?>

<div class="container mt-4">
    <div class="row">
        <!-- Baris Pertama: Penerima Manfaat, Warga/Donatur, dan Jumlah Petugas -->
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <h5 class="fw-semibold">Penerima Manfaat</h5>
                <h3 id="beneficaries_total" class="text-primary fw-semibold"></h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <h5 class="fw-semibold">Warga/Donatur</h5>
                <h3 id="citizens_total" class="text-primary fw-semibold"></h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <h5 class="fw-semibold">Jumlah Petugas</h5>
                <h3 id="officers_total" class="text-warning fw-semibold"></h3>
            </div>
        </div>
    </div>
    
    <!-- Baris Kedua: Donasi Bulan Ini, Donasi Keseluruhan, dan Nominal Saldo -->
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <h5 class="fw-semibold">Donasi Masuk Bulan Ini</h5>
                <h3 id="donations_total_monthly" class="text-success fw-semibold"></h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <h5 class="fw-semibold">Donasi Masuk Keseluruhan</h5>
                <h3 id="donations_total_overall" class="text-success fw-semibold"></h3>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <h5 class="fw-semibold">Saldo Terkini</h5>
                <h3 id="saldo_total" class="text-success fw-semibold"></h3>
            </div>
        </div>
    </div>

    <!-- Chart dan Riwayat -->
    <div class="row">
        <div class="col-md-8 mb-5">
            <div class="card shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-semibold mb-0">Grafik Donasi & Pengeluaran</h5>
                    <select id="SelectYear" class="form-select w-25">
                       <!-- Tahun akan diisi dengan AJAX -->
                    </select>
                    <input type="hidden" id="selectedYear">
                </div>
                <div id="chart" class="chart-container mt-3"></div>
            </div>
        </div>
        <div class="col-md-4 mb-5">
            <div class="card shadow-sm">
                <h5 class="text-center fw-semibold">Riwayat</h5>
                <div class="p-3" id="latest-transactions">
                    <!-- Riwayat akan diisi dengan AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('content'); ?>
