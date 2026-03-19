<?php
// global_modal.php - Reusable global modal component
// This modal is used for displaying profile forms and other modals across the system
?>
<!-- Global Modal -->
<div class="modal fade" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="globalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="globalModalLabel">Modal Title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="globalModalBody">
                <!-- Dynamic content will be loaded here -->
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                    <p class="mt-2">Loading...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="globalModalSave">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
function openGlobalModal(title, url) {
    $('#globalModalLabel').text(title);
    $('#globalModalBody').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-2">Loading...</p></div>');
    $('#globalModal').modal('show');
    
    $.ajax({
        url: url,
        success: function(response) {
            $('#globalModalBody').html(response);
        },
        error: function() {
            $('#globalModalBody').html('<div class="alert alert-danger">Failed to load content</div>');
        }
    });
}

function closeGlobalModal() {
    $('#globalModal').modal('hide');
}
</script>
