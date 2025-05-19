<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Edit Profil</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseEdit"></button>
            </div>
            <div class="modal-body">
            <form id="formEdit">
                    <input type="hidden" id="EditId" name="id">
                    <div class="mb-3">
                        <label for="EditName" class="form-label fw-bold fs-6">Nama</label>
                        <input type="text" class="form-control" id="EditName" name="name" placeholder="Tambah nama">
                        <p id="errorEditName" class="text-danger small"></p>
                    </div>
                    <div class="mb-3">
                        <label for="EditPhone" class="form-label fw-bold fs-6">No. HP</label>
                        <input type="text" class="form-control" id="EditPhone" name="phone" placeholder="Tambah nomor hp">
                        <p id="errorEditPhone" class="text-danger small"></p>
                    </div>
                    <div class="mb-3">
                        <label for="EditType" class="form-label fw-bold fs-6">Jenis Kelamin</label>
                        <select id="EditType" name="gender" class="form-select">
                            <option value="male">Laki-laki</option>
                            <option value="female">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="EditEmail" class="form-label fw-bold fs-6">Email</label>
                        <input type="text" class="form-control" id="EditEmail" name="email" placeholder="Tambah email">
                        <p id="errorEditEmail" class="text-danger small"></p>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning fw-bold fs-6">Ubah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
