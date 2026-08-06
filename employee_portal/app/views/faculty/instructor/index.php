<?php
include __DIR__ . '/../partials/header.php';
include __DIR__ . '/../partials/sidebar.php';

$instructors = $instructors ?? [];

$totalInstructors = count($instructors);
$activeCount = 0;
$departments = [];

foreach ($instructors as $instructor) {
    if (strtolower($instructor['employment_status'] ?? '') === 'active') {
        $activeCount++;
    }

    if (!empty($instructor['department'])) {
        $departments[] = $instructor['department'];
    }
}

$departmentCount = count(array_unique($departments));
?>

<main class="main-content bg-light pb-5">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h5 class="fw-bold mb-0">Manage Instructors</h5>
            <small class="text-muted">View and manage instructor information</small>
        </div>
    </div>

    <!-- Summary -->
    <div class="row g-2 mb-2">

        <?php
        $cards = [
            ['Total Instructors', $totalInstructors, 'fa-chalkboard-user', 'primary'],
            ['Active', $activeCount, 'fa-user-check', 'success'],
            ['Departments', $departmentCount, 'fa-building', 'secondary']
        ];
        ?>

        <?php foreach ($cards as [$label, $value, $icon, $color]): ?>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center">

                            <div class="rounded-circle bg-<?= $color ?>-subtle text-<?= $color ?>
                                        d-flex align-items-center justify-content-center me-2"
                                style="width:32px;height:32px;">
                                <i class="fa-solid <?= $icon ?>"></i>
                            </div>

                            <div>
                                <small class="text-muted"><?= $label ?></small>
                                <div class="fw-bold"><?= $value ?></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <!-- Instructor List -->
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 px-3 py-2">
            <div class="d-flex justify-content-between align-items-center gap-2">

                <div>
                    <div class="fw-bold small">Instructor List</div>
                    <small class="text-muted">
                        <?= $totalInstructors ?> instructor(s)
                    </small>
                </div>

                <div class="input-group input-group-sm" style="max-width:220px;">
                    <span class="input-group-text bg-white">
                        <i class="fa-solid fa-magnifying-glass text-muted"></i>
                    </span>

                    <input
                        type="text"
                        id="instructorSearch"
                        class="form-control"
                        placeholder="Search instructor...">
                </div>

            </div>
        </div>

        <div class="table-responsive">

            <table class="table table-hover table-sm align-middle mb-0"
                id="instructorTable">

                <thead class="table-light">
                    <tr class="small">
                        <th class="px-3">#</th>
                        <th>Employee</th>
                        <th>Employee No.</th>
                        <th>Username</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Date Hired</th>
                        <th class="text-end px-3">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if ($instructors): ?>

                        <?php foreach ($instructors as $i => $row): ?>

                            <?php
                            $status = strtolower($row['employment_status'] ?? 'inactive');
                            $active = $status === 'active';
                            ?>

                            <tr class="small">

                                <td class="px-3 text-muted">
                                    <?= $i + 1 ?>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center">

                                        <div class="rounded-circle bg-primary text-white
                                                d-flex align-items-center justify-content-center me-2"
                                            style="width:28px;height:28px;font-size:11px;">
                                            <?= strtoupper(substr($row['full_name'] ?? 'N', 0, 1)) ?>
                                        </div>

                                        <div class="text-nowrap">
                                            <div class="fw-semibold">
                                                <?= htmlspecialchars($row['full_name'] ?? 'N/A') ?>
                                            </div>

                                            <small class="text-muted">
                                                <?= htmlspecialchars($row['position_title'] ?? 'Instructor') ?>
                                            </small>
                                        </div>

                                    </div>
                                </td>

                                <td class="fw-semibold">
                                    <?= htmlspecialchars($row['employee_no'] ?? 'N/A') ?>
                                </td>

                                <td class="text-muted">
                                    <?= htmlspecialchars($row['username'] ?? 'N/A') ?>
                                </td>

                                <td>
                                    <?= !empty($row['department'])
                                        ? htmlspecialchars($row['department'])
                                        : '<span class="text-muted">Not assigned</span>' ?>
                                </td>

                                <td>
                                    <span class="badge bg-<?= $active ? 'success' : 'secondary' ?>-subtle
                                             text-<?= $active ? 'success' : 'secondary' ?>">
                                        <?= $active ? 'Active' : htmlspecialchars(ucfirst($status)) ?>
                                    </span>
                                </td>

                                <td class="text-nowrap">
                                    <?= !empty($row['date_hired'])
                                        ? date('M d, Y', strtotime($row['date_hired']))
                                        : '<span class="text-muted">N/A</span>' ?>
                                </td>

                                <td class="text-end px-3 text-nowrap">

                                    <button class="btn btn-sm btn-light py-1 px-2" title="View">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>

                                    <button class="btn btn-sm btn-light py-1 px-2" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fa-solid fa-user-slash fs-4 mb-2"></i>

                                <div class="fw-semibold">
                                    No instructors found
                                </div>

                                <small>No instructor records available.</small>
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>

        </div>

        <div class="card-footer bg-white border-0 px-3 py-2">
            <small class="text-muted">
                Showing <?= $totalInstructors ?> instructor(s)
            </small>
        </div>

    </div>

</main>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.14.1/dist/gsap.min.js"></script>
<script>
    const tl = gsap.timeline();

    tl.from(["#card-1", "#card-2", "#card-3", '#card-4', '#card-5', '#card-6'], {
        y: -10,
        opacity: 0,
        duration: 1,
        stagger: 0.4,
        ease: "power2.out"
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/driver.js@latest/dist/driver.js.iife.js"></script>
<script src="<?php echo BASE_URL ?>/js/driver.js"></script>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.20/index.global.min.js'></script>
<script src="<?php echo BASE_URL ?>/js/home.js"></script>
<?php include  __DIR__ . '/../partials/footer.php'; ?>