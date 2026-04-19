<div class="modal fade" id="viewProgramModal" tabindex="-1" aria-labelledby="viewProgramModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewProgramModalLabel">Program Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        <img id="view-image" src="img/placeholder.gif" class="img-fluid mb-3 view-image-anim" style="max-height:300px;object-fit:cover;width:100%;border-radius:8px;" alt="">
        <h4 id="view-title"></h4>
        <p id="view-description" class="text-muted"></p>
        <p id="view-trainer" class="text-muted mb-2" style="font-size: 0.9rem;"></p>
        <div id="view-meta-grid" class="mb-2">
          <div class="d-flex justify-content-between gap-1">
            <div id="meta-location" class="meta-label"></div>
            <div id="meta-status" class="meta-label"></div>
            <div id="meta-date" class="meta-label"></div>
            <div id="meta-capacity" class="meta-label"></div>
            <div id="meta-remaining" class="meta-label"></div>
          </div>
        </div>
        <div id="view-enrolled" class="mt-3" style="display:none;"></div>
      </div>
      <div class="modal-footer">
        <div id="view-actions" class="ms-auto"></div>
      </div>
    </div>
  </div>
</div>