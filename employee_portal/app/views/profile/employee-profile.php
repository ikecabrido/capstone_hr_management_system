<div class="card shadow-sm border-0 rounded-4">

    <div class="card-header bg-primary text-white rounded-top-4 py-3">
        <h3 class="mb-0 fw-bold">
            <i class="fas fa-user me-2"></i>Employee Profile
        </h3>
        <small>
            Employee profile information
        </small>
    </div>

    <div class="card-body">

        <!-- Account Information -->

        <h5 class="fw-bold text-primary mb-3">
            Account Information
        </h5>

        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Username
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($user['username'] ?? '-') ?>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Email
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($user['email'] ?? '-') ?>
                </div>
            </div>

        </div>

        <hr>

        <!-- Personal Information -->

        <h5 class="fw-bold text-primary mb-3">
            Personal Information
        </h5>

        <div class="row">

            <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold text-muted">
                    First Name
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($employeeProfileInfo['first_name'] ?? '-') ?>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Middle Name
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($employeeProfileInfo['middle_name'] ?? '-') ?>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Last Name
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($employeeProfileInfo['last_name'] ?? '-') ?>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Suffix
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($employeeProfileInfo['suffix'] ?? '-') ?>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Gender
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($employeeProfileInfo['gender'] ?? '-') ?>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Birth Date
                </label>

                <div class="form-control bg-light">
                    <?= !empty($employeeProfileInfo['birth_date'])
                        ? htmlspecialchars(date('F d, Y', strtotime($employeeProfileInfo['birth_date'])))
                        : '-' ?>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Birth Place
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($employeeProfileInfo['birth_place'] ?? '-') ?>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Civil Status
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($employeeProfileInfo['civil_status'] ?? '-') ?>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Citizenship
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($employeeProfileInfo['citizenship'] ?? '-') ?>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Religion
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($employeeProfileInfo['religion'] ?? '-') ?>
                </div>
            </div>

        </div>

        <hr>

        <!-- Contact Information -->

        <h5 class="fw-bold text-primary mb-3">
            Contact Information
        </h5>

        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Mobile Number
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($employeeProfileInfo['mobile_no'] ?? '-') ?>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Telephone Number
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($employeeProfileInfo['phone_no'] ?? '-') ?>
                </div>
            </div>

        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold text-muted">
                Current Address
            </label>

            <div class="border rounded bg-light p-3" style="min-height:90px;">
                <?= nl2br(htmlspecialchars($employeeProfileInfo['current_address'] ?? '-')) ?>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold text-muted">
                Permanent Address
            </label>

            <div class="border rounded bg-light p-3" style="min-height:90px;">
                <?= nl2br(htmlspecialchars($employeeProfileInfo['permanent_address'] ?? '-')) ?>
            </div>
        </div>

        <hr>

        <!-- Educational Information -->

        <h5 class="fw-bold text-primary mb-3">
            Educational Information
        </h5>

        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Highest Educational Attainment / Credentials
                </label>

                <div class="border rounded bg-light p-3" style="min-height:90px;">
                    <?= nl2br(htmlspecialchars($employeeProfileInfo['credentials'] ?? '-')) ?>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-muted">
                    Graduate Level
                </label>

                <div class="form-control bg-light">
                    <?= htmlspecialchars($employeeProfileInfo['graduate_level'] ?? '-') ?>
                </div>
            </div>

        </div>

        <hr>

        <!-- Profile Photo -->

        <h5 class="fw-bold text-primary mb-3">
            Profile Photo
        </h5>

        <div class="text-center mb-4">
            <img
                src="<?= !empty($employeeProfileInfo['profile_image'])
                            ? '/capstone_hr_management_system/employee_portal/public/uploads/profile/' . htmlspecialchars($employeeProfileInfo['profile_image'])
                            : '/capstone_hr_management_system/employee_portal/public/assets/image/default_user_icon.webp'; ?>"
                class="rounded-circle border shadow"
                width="170"
                height="170"
                style="object-fit:cover;"
                alt="Profile Photo">

        </div>

    </div>

</div>