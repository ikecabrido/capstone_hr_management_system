<div class="modal fade" id="leaveRequestModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title text-3xl">Request Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="leaveRequestForm" method="POST" action="index.php?url=admin-leave-request-store">
                <div class="modal-body px-4 py-3">
                    <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?? '' ?>">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Leave Type</label>
                        <select name="leave_type_id" class="form-select shadow-sm" required>
                            <option value="" disabled selected>Choose leave type...</option>
                            <?php foreach ($allLeaveTypes as $type): ?>
                                <option value="<?= $type['leave_type_id'] ?>"><?= htmlspecialchars($type['leave_type_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Reason for Leave</label>
                        <textarea
                            name="reason"
                            class="form-control shadow-sm"
                            rows="4"
                            placeholder="Provide a brief explanation..."
                            required></textarea>
                        <small class="text-muted">Be clear and concise.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Start Date</label>
                            <input type="date" id="start_date" name="start_date" class="form-control shadow-sm" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">End Date</label>
                            <input type="date" id="end_date" name="end_date" class="form-control shadow-sm" required>
                        </div>
                    </div>

                </div>

                <div class="modal-footer px-4 py-3 border-top-0">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" id="submitLeaveBtn" class="btn btn-success px-4 shadow-sm">
                        Submit Request
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const startDate = document.getElementById("start_date");
        const endDate = document.getElementById("end_date");

        const today = new Date();

        const formatDate = (date) => {
            return date.toISOString().split("T")[0];
        };

        // Today's date
        const todayString = formatDate(today);

        // Tomorrow's date
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);
        const tomorrowString = formatDate(tomorrow);

        // Set default values
        startDate.value = todayString;
        endDate.value = tomorrowString;

        // Prevent selecting dates before today
        startDate.min = todayString;
        endDate.min = todayString;

        // When start date changes
        startDate.addEventListener("change", function() {

            // End date cannot be earlier than start date
            endDate.min = this.value;

            // If current end date is before new start date,
            // automatically set it to the next day.
            if (endDate.value < this.value) {

                let nextDay = new Date(this.value);
                nextDay.setDate(nextDay.getDate() + 1);

                endDate.value = formatDate(nextDay);
            }
        });

        // Extra validation
        endDate.addEventListener("change", function() {

            if (this.value < startDate.value) {

                alert("End date cannot be earlier than the start date.");

                let nextDay = new Date(startDate.value);
                nextDay.setDate(nextDay.getDate() + 1);

                this.value = formatDate(nextDay);
            }
        });

    });

    document.getElementById("leaveRequestForm").addEventListener("submit", function(e) {

        const start = new Date(startDate.value);
        const end = new Date(endDate.value);

        if (end < start) {
            e.preventDefault();
            alert("End date cannot be earlier than the start date.");
        }

    });
</script>