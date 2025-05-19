<div class="modal fade" id="modalEditPassword" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Edit Password</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseEditPassword"></button>
            </div>
            <div class="modal-body">
                <form id="formEditPassword">
                    <div class="mb-3 form-password-toggle">
                        <div class="d-flex justify-content-between">
                            <label class="form-label fw-semibold" for="password">Password Lama</label>
                        </div>
                        <div class="input-group input-group-merge">
                            <input
                            type="password"
                            id="current_password"
                            class="form-control"
                            name="current_password"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="current_password"
                            />
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                        </div>
                    </div>
                    <div class="mb-3 form-password-toggle">
                        <div class="d-flex justify-content-between">
                            <label class="form-label fw-semibold" for="password">Password Baru</label>
                        </div>
                        <div class="input-group input-group-merge">
                            <input
                            type="password"
                            id="new_password"
                            class="form-control"
                            name="new_password"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="new_password"
                            />
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                        </div>
                    </div>
                    <div class="mb-3 form-password-toggle">
                        <div class="d-flex justify-content-between">
                            <label class="form-label fw-semibold" for="confirm_password">Konfirmasi Password Baru</label>
                        </div>
                        <div class="input-group input-group-merge">
                            <input
                            type="password"
                            id="confirm_password"
                            class="form-control"
                            name="confirm_password"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="confirm_password"
                            />
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning fw-bold fs-6">Ubah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
