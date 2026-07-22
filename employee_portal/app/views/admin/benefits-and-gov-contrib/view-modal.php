<div class="modal fade" id="viewBenefitModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>
                    Benefit Record Details
                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <table class="table table-bordered">

                    <tr>
                        <th width="30%">Employee</th>
                        <td id="viewEmployee"></td>
                    </tr>

                    <tr>
                        <th>Record Type</th>
                        <td>
                            <span class="badge bg-primary" id="viewRecordType"></span>
                        </td>
                    </tr>

                    <tr>
                        <th>Period</th>
                        <td id="viewPeriod"></td>
                    </tr>

                    <tr>
                        <th>Description</th>
                        <td id="viewDescription"></td>
                    </tr>

                    <tr>
                        <th>Uploaded By</th>
                        <td id="viewUploadedBy"></td>
                    </tr>

                    <tr>
                        <th>Uploaded At</th>
                        <td id="viewUploadedAt"></td>
                    </tr>

                    <tr>
                        <th>Attachment</th>
                        <td>
                            <a id="viewFile"
                                href="#"
                                target="_blank"
                                class="btn btn-info btn-sm">
                                <i class="fas fa-file-pdf"></i>
                                View File
                            </a>
                        </td>
                    </tr>

                </table>

            </div>

            <div class="modal-footer">
                <button
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>