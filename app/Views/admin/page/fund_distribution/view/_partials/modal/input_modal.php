<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Tambah Data</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseCreate"></button>
            </div>
            <div class="modal-body">
                <form id="formCreate">
                    <div class="mb-3">
                        <label for="CreateProgram" class="form-label fw-bold fs-6">Program</label>
                        <select id="CreateProgram" name="program_id" class="form-select dropdown">
                        </select>
                        <input type="hidden" id="selectedProgram">
                        <div id="errorCreateProgram" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="CreateCredit" class="form-label fw-bold fs-6">Kredit</label>
                        <input type="text" class="form-control" id="CreateCredit" name="credit" placeholder="Tambah jumlah dana">
                        <p id="errorCreateCredit" class="text-danger small"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold fs-6">Penerima Manfaat <span class="text-warning">(Boleh dikosongi jika tidak ada)</span></label>
                        <div class="border p-2 rounded beneficariesList" style="max-height: 200px; overflow-y: auto;">
                            <!-- Checkbox akan dimuat melalui AJAX -->
                        </div>
                        <p id="errorCreateBeneficaries" class="text-danger small"></p>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold fs-6">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>