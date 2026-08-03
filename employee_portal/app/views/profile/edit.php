<div
    class="modal fade"
    id="employeeInfoModal"
    tabindex="-1"
    aria-labelledby="employeeInfoModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content rounded-4 border-0 shadow">

            <div class="modal-header bg-primary text-white">

                <div>
                    <h5
                        class="modal-title fw-bold"
                        id="employeeInfoModalLabel">

                        <i class="fas fa-user-plus me-2"></i>
                        Employee Profile

                    </h5>

                    <small>
                        This is your existing Profile information
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body">

                <form action="index.php?url=employee-profile-update" method="POST" enctype="multipart/form-data">
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
                                value="<?= htmlspecialchars($employeeProfileInfo['first_name'] ?? '') ?>"
                                required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Middle Name</label>

                            <input
                                type="text"
                                name="middle_name"
                                class="form-control"
                                value="<?= htmlspecialchars($employeeProfileInfo['middle_name'] ?? '') ?>">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                Last Name <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="last_name"
                                class="form-control"
                                value="<?= htmlspecialchars($employeeProfileInfo['last_name'] ?? '') ?>"
                                required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Suffix</label>

                            <input
                                type="text"
                                name="suffix"
                                class="form-control"
                                placeholder="Jr., Sr., III"
                                value="<?= htmlspecialchars($employeeProfileInfo['suffix'] ?? '') ?>">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                Gender <span class="text-danger">*</span>
                            </label>

                            <select
                                name="gender"
                                class="form-select"
                                required>

                                <option value="">Select Gender</option>
                                <option value="Male" <?= ($employeeProfileInfo['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>
                                    Male
                                </option>
                                <option value="Female" <?= ($employeeProfileInfo['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>
                                    Female
                                </option>
                                <option value="Other" <?= ($employeeProfileInfo['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>
                                    Other
                                </option>

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
                                value="<?= htmlspecialchars($employeeProfileInfo['birth_date'] ?? '') ?>"
                                required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Birth Place</label>

                            <input
                                type="text"
                                name="birth_place"
                                class="form-control"
                                value="<?= htmlspecialchars($employeeProfileInfo['birth_place'] ?? '') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Civil Status</label>

                            <select
                                name="civil_status"
                                class="form-select">

                                <option value="">Select</option>
                                <option value="Single" <?= ($employeeProfileInfo['civil_status'] ?? '') === 'Single' ? 'selected' : '' ?>>
                                    Single
                                </option>
                                <option value="Married" <?= ($employeeProfileInfo['civil_status'] ?? '') === 'Married' ? 'selected' : '' ?>>
                                    Married
                                </option>
                                <option value="Separated" <?= ($employeeProfileInfo['civil_status'] ?? '') === 'Separated' ? 'selected' : '' ?>>
                                    Separated
                                </option>
                                <option value="Widowed" <?= ($employeeProfileInfo['civil_status'] ?? '') === 'Widowed' ? 'selected' : '' ?>>
                                    Widowed
                                </option>

                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Citizenship</label>

                            <input
                                type="text"
                                name="citizenship"
                                class="form-control"
                                value="<?= htmlspecialchars($employeeProfileInfo['citizenship'] ?? 'Filipino') ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Religion</label>

                            <select
                                name="religion"
                                class="form-select">

                                <option value="">Select Religion</option>

                                <option value="Roman Catholic" <?= ($employeeProfileInfo['religion'] ?? '') === 'Roman Catholic' ? 'selected' : '' ?>>
                                    Roman Catholic
                                </option>

                                <option value="Islam" <?= ($employeeProfileInfo['religion'] ?? '') === 'Islam' ? 'selected' : '' ?>>
                                    Islam
                                </option>

                                <option value="Iglesia ni Cristo" <?= ($employeeProfileInfo['religion'] ?? '') === 'Iglesia ni Cristo' ? 'selected' : '' ?>>
                                    Iglesia ni Cristo
                                </option>

                                <option value="Aglipayan" <?= ($employeeProfileInfo['religion'] ?? '') === 'Aglipayan' ? 'selected' : '' ?>>
                                    Aglipayan (Philippine Independent Church)
                                </option>

                                <option value="Seventh-day Adventist" <?= ($employeeProfileInfo['religion'] ?? '') === 'Seventh-day Adventist' ? 'selected' : '' ?>>
                                    Seventh-day Adventist
                                </option>

                                <option value="Jehovah's Witnesses" <?= ($employeeProfileInfo['religion'] ?? '') === "Jehovah's Witnesses" ? 'selected' : '' ?>>
                                    Jehovah's Witnesses
                                </option>

                                <option value="Baptist" <?= ($employeeProfileInfo['religion'] ?? '') === 'Baptist' ? 'selected' : '' ?>>
                                    Baptist
                                </option>

                                <option value="Methodist" <?= ($employeeProfileInfo['religion'] ?? '') === 'Methodist' ? 'selected' : '' ?>>
                                    Methodist
                                </option>

                                <option value="Pentecostal" <?= ($employeeProfileInfo['religion'] ?? '') === 'Pentecostal' ? 'selected' : '' ?>>
                                    Pentecostal
                                </option>

                                <option value="Born Again Christian" <?= ($employeeProfileInfo['religion'] ?? '') === 'Born Again Christian' ? 'selected' : '' ?>>
                                    Born Again Christian
                                </option>

                                <option value="Christian" <?= ($employeeProfileInfo['religion'] ?? '') === 'Christian' ? 'selected' : '' ?>>
                                    Christian
                                </option>

                                <option value="Buddhism" <?= ($employeeProfileInfo['religion'] ?? '') === 'Buddhism' ? 'selected' : '' ?>>
                                    Buddhism
                                </option>

                                <option value="Hinduism" <?= ($employeeProfileInfo['religion'] ?? '') === 'Hinduism' ? 'selected' : '' ?>>
                                    Hinduism
                                </option>

                                <option value="Judaism" <?= ($employeeProfileInfo['religion'] ?? '') === 'Judaism' ? 'selected' : '' ?>>
                                    Judaism
                                </option>

                                <option value="None" <?= ($employeeProfileInfo['religion'] ?? '') === 'None' ? 'selected' : '' ?>>
                                    None
                                </option>

                                <option value="Others" <?= ($employeeProfileInfo['religion'] ?? '') === 'Others' ? 'selected' : '' ?>>
                                    Others
                                </option>

                            </select>
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

                            <div class="input-group">
                                <span class="input-group-text">+63</span>

                                <input
                                    type="tel"
                                    name="mobile_no"
                                    class="form-control"
                                    placeholder="9123456789"
                                    maxlength="10"
                                    pattern="[0-9]{10}"
                                    value="<?= htmlspecialchars($employeeProfileInfo['mobile_no'] ?? '') ?>">
                            </div>

                            <small class="text-muted">
                                Example: +63 9123456789
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telephone Number</label>

                            <input
                                type="text"
                                name="phone_no"
                                class="form-control"
                                placeholder="(044) 123-4567"
                                value="<?= htmlspecialchars($employeeProfileInfo['phone_no'] ?? '') ?>">
                        </div>

                    </div>

                    <div class="mb-3">
                        <label class="form-label">Current Address</label>

                        <textarea
                            name="current_address"
                            rows="3"
                            class="form-control"><?= htmlspecialchars($employeeProfileInfo['current_address'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Permanent Address</label>

                        <textarea
                            name="permanent_address"
                            rows="3"
                            class="form-control"><?= htmlspecialchars($employeeProfileInfo['permanent_address'] ?? '') ?></textarea>
                    </div>

                    <hr>

                    <!-- Educational Information -->

                    <h5 class="fw-bold text-primary mb-3">
                        Educational Information
                    </h5>
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Highest Educational Attainment / Credentials
                            </label>

                            <textarea
                                name="credentials"
                                rows="3"
                                class="form-control"><?= htmlspecialchars($employeeProfileInfo['credentials'] ?? '') ?></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Graduate Level
                            </label>

                            <select
                                name="graduate_level"
                                class="form-select">

                                <option value="">Select</option>

                                <option value="None"
                                    <?= ($employeeProfileInfo['graduate_level'] ?? '') === 'None' ? 'selected' : '' ?>>
                                    None
                                </option>

                                <option value="Bachelor's"
                                    <?= ($employeeProfileInfo['graduate_level'] ?? '') === "Bachelor's" ? 'selected' : '' ?>>
                                    Bachelor's
                                </option>

                                <option value="Masteral"
                                    <?= ($employeeProfileInfo['graduate_level'] ?? '') === 'Masteral' ? 'selected' : '' ?>>
                                    Masteral
                                </option>

                                <option value="Doctorate"
                                    <?= ($employeeProfileInfo['graduate_level'] ?? '') === 'Doctorate' ? 'selected' : '' ?>>
                                    Doctorate
                                </option>

                            </select>
                        </div>

                    </div>

                    <hr>

                    <!-- Profile Photo -->

                    <h5 class="fw-bold text-primary mb-3">
                        Profile Photo
                    </h5>

                    <div class="mb-4">

                        <?php if (!empty($employeeProfileInfo['profile_image'])): ?>
                            <div class="mb-3 text-center">
                                <img
                                    src="uploads/profile/<?= htmlspecialchars($employeeProfileInfo['profile_image']); ?>"
                                    alt="Profile Photo"
                                    class="rounded-circle border shadow"
                                    width="170"
                                    height="170"
                                    style="object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div class="mb-3 text-center">
                                <img
                                    src="assets/image/default_user_icon.webp"
                                    alt="Default Profile Photo"
                                    class="rounded-circle border shadow"
                                    width="170"
                                    height="170"
                                    style="object-fit: cover;">
                            </div>
                        <?php endif; ?>

                        <label class="form-label">Profile Photo</label>

                        <input
                            type="file"
                            name="profile_image"
                            class="form-control"
                            accept="image/*">

                        <small class="text-muted">
                            Leave this blank to keep the current profile photo.
                        </small>

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