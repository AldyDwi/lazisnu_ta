<?= $this->extend("super_admin/layout/view/index") ?>
<?= $this->section('content'); ?>

<div class="container mt-4">
    <div class="row">
        <!-- Statistik Total -->
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <h5 class="fw-semibold">Penerima Manfaat</h5>
                <h3 id="beneficaries_total" class="text-primary fw-semibold"></h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <h5 class="fw-semibold">Warga/Donatur</h5>
                <h3 id="citizens_total" class="text-primary fw-semibold"></h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <h5 class="fw-semibold">Jumlah Admin</h5>
                <h3 id="admins_total" class="text-warning fw-semibold"></h3>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <h5 class="fw-semibold">Jumlah Petugas</h5>
                <h3 id="officers_total" class="text-warning fw-semibold"></h3>
            </div>
        </div>
    </div>
    
    <!-- Row untuk Total Admin, Total Petugas, dan Nominal Saldo -->
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
                <div class="d-flex justify-content-center justify-content-md-between flex-column flex-md-row gap-2 align-items-center">
                    <div class="text-md-start text-center w-md-50 chart-name">
                        <h5 class="fw-semibold">Grafik Donasi & Pengeluaran</h5>
                    </div>
                    

                    <div class="d-flex flex-column flex-md-row gap-2 w-md-50 justify-content-end">
                        <select id="SelectYear" class="form-select select-dashboard">
                            <!-- Tahun akan diisi dengan AJAX -->
                        </select>
                        <select id="SelectBranch" class="form-select select-dashboard">
                            <!-- Cabang akan diisi dengan AJAX -->
                        </select>
                    </div>

                    <input type="hidden" id="selectedYear">
                    <input type="hidden" id="selectedBranch">
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
