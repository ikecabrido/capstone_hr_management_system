<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3" style="display: flex; gap: 1rem; align-items: flex-end;">
            <input type="hidden" name="route" value="training">
            <div class="col-md-4">
                <label class="form-label">Search Programs</label>
                <input type="text" name="search" class="form-control" placeholder="Search by name, description..."
                    value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Active" <?php echo $statusFilter === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $statusFilter === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
            <div class="col-md-2">
                <a href="index.php?url=training-program-index" class="btn btn-secondary w-100">
                    Clear
                </a>
            </div>
        </form>
    </div>
</div>