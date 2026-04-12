<div class="modal fade" id="editProgramModal" tabindex="-1" aria-labelledby="editProgramModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="post" id="editProgramForm" novalidate enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProgramModalLabel">Edit Program</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="modal-program-id">
                    <div class="row g-1">
                        <div class="col-12 mb-1"><input id="modal-title" class="form-control" name="title" placeholder="Program title"></div>
                        <div class="col-md-6"><input id="modal-trainer" class="form-control" name="trainer" placeholder="Trainer name (optional)"></div>
                        <div class="col-md-6">
                            <input id="modal-cover" class="form-control" name="cover_photo" type="file" accept="image/*" placeholder="Cover Photo">
                            <small class="text-muted">JPG, PNG, GIF, or WebP (max 2MB). Leave empty to keep current</small>
                        </div>
                        <div class="col-md-3"><input id="modal-date" class="form-control" name="date" placeholder="YYYY-MM-DD (optional)"></div>
                        <div class="col-md-3"><input id="modal-capacity" class="form-control" name="capacity" placeholder="Capacity" type="number"></div>
                        <div class="col-md-3"><input id="modal-sessions" class="form-control" name="sessions" placeholder="Sessions" type="number" min="1"></div>
                        <div class="col-md-3">
                            <select id="modal-status" name="status" class="form-select">
                                <option value="Active">Active</option>
                                <option value="Upcoming">Upcoming</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6 mt-2"><input id="modal-location" class="form-control" name="location" placeholder="Location (optional)"></div>
                        <div class="col-md-6 mt-2"><textarea id="modal-description" class="form-control" name="description" placeholder="Short description"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>