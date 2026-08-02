<div class="w-full mt-4">
    <div class="content-wrapper px-4 py-4">

        <?php require __DIR__ . '/../../partials/notif.php'; ?>

        <div class="card shadow-sm border-0 rounded-4">

            <div class="card-header bg-primary text-white rounded-top-4 py-3">
                <h3 class="mb-0 fw-bold">
                    <i class="fas fa-user-plus me-2"></i>
                    Create Employee Profile
                </h3>
                <small>
                    Complete the employee information linked to this user account.
                </small>
            </div>

            <div class="card-body">

                <form action="index.php?url=admin-employee-store" method="POST" enctype="multipart/form-data">
                    <input
                        type="hidden"
                        name="user_id"
                        value="<?= $user['id']; ?>">

                    <!-- Account Information -->

                    <h5 class="fw-bold text-primary mb-3">
                        Account Information
                    </h5>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input
                                type="text"
                                class="form-control"
                                value="<?= htmlspecialchars($user['username']); ?>"
                                readonly>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                value="<?= htmlspecialchars($user['email']); ?>"
                                readonly>
                        </div>

                    </div>

                    <hr>

                    <!-- Personal Information -->

                    <h5 class="fw-bold text-primary mb-3">
                        Personal Information
                    </h5>

                    <div class="row">

                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                First Name <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="first_name"
                                class="form-control"
                                required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Middle Name</label>
                            <input
                                type="text"
                                name="middle_name"
                                class="form-control">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                Last Name <span class="text-danger">*</span>
                            </label>
                            <input
                                type="text"
                                name="last_name"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Suffix</label>
                            <input
                                type="text"
                                name="suffix"
                                class="form-control"
                                placeholder="Jr., Sr., III">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                Gender <span class="text-danger">*</span>
                            </label>

                            <select name="gender" class="form-select" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>

                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                Birth Date <span class="text-danger">*</span>
                            </label>
                            <input
                                type="date"
                                name="birth_date"
                                class="form-control"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Birth Place</label>
                            <input
                                type="text"
                                name="birth_place"
                                class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Civil Status</label>

                            <select
                                name="civil_status"
                                class="form-select">

                                <option value="">Select</option>
                                <option>Single</option>
                                <option>Married</option>
                                <option>Separated</option>
                                <option>Widowed</option>

                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Citizenship</label>
                            <input
                                type="text"
                                name="citizenship"
                                class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Religion</label>
                            <input
                                type="text"
                                name="religion"
                                class="form-control">
                        </div>

                    </div>

                    <hr>

                    <!-- Contact Information -->

                    <h5 class="fw-bold text-primary mb-3">
                        Contact Information
                    </h5>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input
                                type="text"
                                name="mobile_no"
                                class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telephone Number</label>
                            <input
                                type="text"
                                name="phone_no"
                                class="form-control">
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Address</label>

                        <textarea
                            name="current_address"
                            rows="3"
                            class="form-control"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Permanent Address</label>

                        <textarea
                            name="permanent_address"
                            rows="3"
                            class="form-control"></textarea>
                    </div>

                    <hr>

                    <!-- Educational Information -->

                    <h5 class="fw-bold text-primary mb-3">
                        Educational Information
                    </h5>

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Highest Educational Attainment / Credentials</label>

                            <textarea
                                name="credentials"
                                rows="3"
                                class="form-control"></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Graduate Level</label>

                            <select
                                name="graduate_level"
                                class="form-select">

                                <option value="">Select</option>
                                <option>None</option>
                                <option>Bachelor's</option>
                                <option>Masteral</option>
                                <option>Doctorate</option>

                            </select>
                        </div>

                    </div>

                    <hr>

                    <!-- Profile Photo -->

                    <h5 class="fw-bold text-primary mb-3">
                        Profile Photo
                    </h5>

                    <div class="mb-4">
                        <input
                            type="file"
                            name="profile_image"
                            class="form-control"
                            accept="image/*">
                    </div>

                    <div class="d-flex justify-content-end">

                        <a
                            href="index.php?url=admin-manage-user"
                            class="btn btn-secondary me-2">

                            <i class="fas fa-arrow-left me-1"></i>
                            Back

                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="fas fa-save me-1"></i>
                            Save Employee Profile

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
</div>