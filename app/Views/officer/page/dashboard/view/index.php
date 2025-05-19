<?= $this->extend("officer/layout/view/index") ?>
<?= $this->section('content'); ?>

<div class="container mt-4">
    <div class="row">
    <!-- Baris Kedua: Donasi Bulan Ini, Donasi Keseluruhan, dan Nominal Saldo -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card text-center shadow-sm">
                <h5 class="fw-semibold">Donasi Masuk Bulan Ini</h5>
                <h3 id="donations_total_monthly" class="text-success fw-semibold"></h3>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card text-center shadow-sm">
                <h5 class="fw-semibold">Donasi Masuk Keseluruhan</h5>
                <h3 id="donations_total_overall" class="text-success fw-semibold"></h3>
            </div>
        </div>
        <!-- <div class="col-md-4 mb-3">
            <div class="card text-center shadow-sm">
                <h5 class="fw-semibold">Komisi Bulan ini</h5>
                <h3 id="commision_total_monthly" class="text-success fw-semibold"></h3>
            </div>
        </div> -->
    </div>

    <!-- Chart dan Riwayat -->
    <div class="row">
        <div class="col-md-8 mb-5">
            <div class="card shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-semibold mb-0">Grafik Donasi</h5>
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
