<div class="w-full ml-16">
    <div class="content-wrapper p-4 w-full">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">📊 Admin Dashboard</h4>
                <small class="text-muted">Manage employee documents and approvals</small>
            </div>
            <a href="index.php?url=employee-documents-create" class="btn btn-primary rounded-3">
                + New Document
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">Total Documents</small>
                            <h4 class="fw-bold mb-0 mt-1">128</h4>
                        </div>
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                            📄
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">Pending</small>
                            <h4 class="fw-bold text-warning mb-0 mt-1">32</h4>
                        </div>
                        <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                            ⏳
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">Approved</small>
                            <h4 class="fw-bold text-success mb-0 mt-1">76</h4>
                        </div>
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                            ✔
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 p-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <small class="text-muted">Rejected</small>
                            <h4 class="fw-bold text-danger mb-0 mt-1">20</h4>
                        </div>
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                            ✖
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h6 class="fw-bold mb-3">⚡ Quick Actions</h6>

            <div class="d-flex flex-wrap gap-2">
                <a href="index.php?url=employee-documents-create" class="btn btn-outline-primary rounded-3">
                    Upload Document
                </a>

                <a href="#" class="btn btn-outline-success rounded-3">
                    Approve Requests
                </a>

                <a href="#" class="btn btn-outline-danger rounded-3">
                    Review Rejected
                </a>

                <a href="#" class="btn btn-outline-secondary rounded-3">
                    View Reports
                </a>
            </div>
        </div>

        <!-- Filters / Controls -->
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h6 class="fw-bold mb-3">🔍 Filters</h6>

            <div class="row g-3">

                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <select class="form-select rounded-3">
                        <option>All Departments</option>
                        <option>HR</option>
                        <option>IT</option>
                        <option>Finance</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select class="form-select rounded-3">
                        <option>All Status</option>
                        <option>Pending</option>
                        <option>Approved</option>
                        <option>Rejected</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control rounded-3" placeholder="Search documents...">
                </div>

            </div>

        </div>

    </div>
</div>