<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Tambah Data</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseCreate"></button>
            </div>
            <div class="modal-body">
                <form id="formCreate">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="CreateName" class="form-label fw-bold fs-6">Nama Petugas</label>
                                <input type="text" class="form-control" id="CreateName" name="name" placeholder="Tambah nama petugas">
                                <p id="errorCreateName" class="text-danger small"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="CreatePhone" class="form-label fw-bold fs-6">No. HP</label>
                                <input type="text" class="form-control" id="CreatePhone" name="phone" placeholder="Tambah nomor hp">
                                <p id="errorCreatePhone" class="text-danger small"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="CreateType" class="form-label fw-bold fs-6">Jenis Kelamin</label>
                                <select id="CreateType" name="gender" class="form-select">
                                    <option value="male">Laki-laki</option>
                                    <option value="female">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="CreateRW" class="form-label fw-bold fs-6">RW</label>
                                <select id="CreateRW" name="rw_id" class="form-select dropdown"></select>
                                <input type="hidden" id="selectedRW">
                                <div id="errorCreateRW" class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="CreateEmail" class="form-label fw-bold fs-6">Email</label>
                                <input type="text" class="form-control" id="CreateEmail" name="email" placeholder="Tambah email">
                                <p id="errorCreateEmail" class="text-danger small"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="CreatePassword" class="form-label fw-bold fs-6">Password</label>
                                <input type="password" class="form-control" id="CreatePassword" name="password" placeholder="Tambah password">
                                <p id="errorCreatePassword" class="text-danger small"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="CreatePasswordConfirmation" class="form-label fw-bold fs-6">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="CreatePasswordConfirmation" name="confirm_password" placeholder="Konfirmasi password">
                                <p id="errorCreatePasswordConfirmation" class="text-danger small"></p>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold fs-6">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
