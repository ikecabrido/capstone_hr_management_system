<div class="content-wrapper">

    <section class="content pt-4">

        <div class="container-fluid px-4">

            <div class="row justify-content-center">

                <div class="col-12">

                    <div class="card shadow-lg border-0">

                        <div class="card-header bg-white py-4">

                            <div class="d-flex justify-content-between align-items-center flex-wrap">

                                <div>

                                    <h2 class="font-weight-bold text-dark mb-1">
                                        <i class="fas fa-bell text-primary mr-2"></i>
                                        Employee Notifications
                                    </h2>

                                    <p class="text-secondary mb-0 p-2">
                                        Stay updated with announcements, reminders, payroll, meetings, and other important employee activities.
                                    </p>

                                </div>

                                <?php if (!empty($employeeNotifications)): ?>

                                    <a href="index.php?url=employee-notification-mark-all-read"
                                        class="btn btn-primary d-flex align-items-center justify-content-center">

                                        <span>Mark All as Read</span>

                                    </a>
                                <?php endif; ?>

                            </div>

                        </div>

                        <div class="card-body p-0">

                            <?php if (!empty($employeeNotifications)): ?>

                                <?php foreach ($employeeNotifications as $notification): ?>

                                    <?php

                                    switch ($notification['type']) {

                                        case 'announcement':
                                            $icon = 'fas fa-bullhorn text-primary';
                                            break;

                                        case 'payroll':
                                            $icon = 'fas fa-money-check-alt text-success';
                                            break;

                                        case 'leave':
                                            $icon = 'fas fa-calendar-check text-warning';
                                            break;

                                        case 'training':
                                            $icon = 'fas fa-graduation-cap text-info';
                                            break;

                                        case 'performance':
                                            $icon = 'fas fa-chart-line text-success';
                                            break;

                                        case 'meeting':
                                            $icon = 'fas fa-users text-purple';
                                            break;

                                        case 'document':
                                            $icon = 'fas fa-file-alt text-secondary';
                                            break;

                                        case 'compliance':
                                            $icon = 'fas fa-shield-alt text-danger';
                                            break;

                                        default:
                                            $icon = 'fas fa-bell text-primary';
                                    }

                                    switch ($notification['priority']) {

                                        case 'urgent':
                                            $priorityStyle = 'background-color:#b02a37;color:#fff;';
                                            break;

                                        case 'important':
                                            $priorityStyle = 'background-color:#a66a00;color:#fff;';
                                            break;

                                        default:
                                            $priorityStyle = 'background-color:#495057;color:#fff;';
                                            break;
                                    }

                                    ?>

                                    <div class="border-bottom p-4 <?= empty($notification['is_read']) ? 'border-left border-primary border-4' : ''; ?>">

                                        <div class="d-flex">

                                            <div class="mr-4">

                                                <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                                    style="width:70px;height:70px;background-color:#dee2e6;">

                                                    <i class="<?= $icon ?> fa-lg"></i>

                                                </div>

                                            </div>

                                            <div class="flex-grow-1">

                                                <div class="d-flex justify-content-between align-items-start flex-wrap">

                                                    <div>

                                                        <h4 class="font-weight-bold text-dark mb-2">

                                                            <?= htmlspecialchars($notification['title']); ?>

                                                        </h4>

                                                        <div class="mb-3">

                                                            <?php if (empty($notification['is_read'])): ?>

                                                                <span class="badge badge-primary px-3 py-2 mr-2">
                                                                    NEW
                                                                </span>

                                                            <?php endif; ?>

                                                            <span class="badge px-3 py-2 mr-2"
                                                                style="<?= $priorityStyle; ?>">
                                                                <?= ucfirst($notification['priority']); ?>
                                                            </span>

                                                            <span class="badge badge-light border text-dark px-3 py-2">
                                                                <?= ucfirst($notification['type']); ?>
                                                            </span>

                                                        </div>

                                                    </div>

                                                    <div class="text-right">

                                                        <small class="text-muted d-block">

                                                            <i class="far fa-clock mr-1"></i>

                                                            <?= date('F d, Y h:i A', strtotime($notification['created_at'])); ?>

                                                        </small>

                                                    </div>

                                                </div>

                                                <p class="text-dark mb-4" style="font-size:15px; line-height:1.7;">

                                                    <?= nl2br(htmlspecialchars($notification['message'])); ?>

                                                </p>

                                                <div class="d-flex align-items-center flex-wrap">

                                                    <?php if (!empty($notification['target_url'])): ?>

                                                        <a href="index.php?url=<?= htmlspecialchars($notification['target_url']); ?>"
                                                            class="btn btn-outline-primary btn-sm mr-2">

                                                            <i class="fas fa-external-link-alt mr-1"></i>
                                                            Open

                                                        </a>

                                                    <?php endif; ?>

                                                    <?php if (empty($notification['is_read'])): ?>

                                                        <a href="index.php?url=employee-notification-mark-read&id=<?= $notification['notification_id']; ?>"
                                                            class="btn btn-success btn-sm">

                                                            <i class="fas fa-check mr-1"></i>
                                                            Mark as Read

                                                        </a>

                                                    <?php else: ?>

                                                        <span class="badge d-flex align-items-center justify-content-center px-3 p-[20px]"
                                                            style="background:#198754;color:#fff;height:31px;">

                                                            <i class="fas fa-check-circle mr-1"></i>
                                                            Already Read

                                                        </span>

                                                    <?php endif; ?>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <div class="text-center py-5">

                                    <i class="far fa-bell-slash text-secondary mb-4"
                                        style="font-size:70px;"></i>

                                    <h3 class="font-weight-bold text-dark">

                                        No Notifications

                                    </h3>

                                    <p class="text-muted">

                                        You're all caught up! New notifications will appear here.

                                    </p>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>