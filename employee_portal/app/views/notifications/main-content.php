<div class="content-wrapper">

    <section class="content pt-3">

        <div class="container-fluid">

            <div class="card shadow-sm">

                <div class="card-header d-flex justify-content-between align-items-center">

                    <div>

                        <h3 class="card-title mb-0 fw-bold">
                            <i class="fas fa-bell text-primary mr-2"></i>
                            Employee Notifications
                        </h3>

                        <small class="text-muted">
                            View all notifications sent to you.
                        </small>

                    </div>

                    <a href="index.php?url=employee-notification-mark-all-read"
                        class="btn btn-primary btn-sm">

                        <i class="fas fa-check-double mr-1"></i>

                        Mark All as Read

                    </a>

                </div>

                <div class="card-body p-0">

                    <?php if (!empty($notifications)):  ?>

                        <?php foreach ($notifications as $notification): ?>

                            <div class="border-bottom p-3 d-flex justify-content-between align-items-start <?= empty($notification['is_read']) ? 'bg-light' : ''; ?>">

                                <div class="flex-grow-1">

                                    <div class="d-flex align-items-center mb-1">

                                        <?php if (empty($notification['is_read'])): ?>

                                            <span class="badge badge-primary mr-2">
                                                NEW
                                            </span>

                                        <?php endif; ?>

                                        <h5 class="mb-0">

                                            <?= htmlspecialchars($notification['title']); ?>

                                        </h5>

                                    </div>

                                    <p class="text-muted mb-2">

                                        <?= nl2br(htmlspecialchars($notification['message'])); ?>

                                    </p>

                                    <small class="text-muted">

                                        <i class="far fa-clock mr-1"></i>

                                        <?= date('F d, Y h:i A', strtotime($notification['created_at'])); ?>

                                    </small>

                                </div>

                                <div class="ml-3 text-right">

                                    <?php if (empty($notification['is_read'])): ?>

                                        <a href="index.php?url=employee-notification-mark-read&id=<?= $notification['notification_id']; ?>"
                                            class="btn btn-success btn-sm">

                                            <i class="fas fa-check mr-1"></i>

                                            Mark Read

                                        </a>

                                    <?php else: ?>

                                        <span class="badge badge-success">

                                            Read

                                        </span>

                                    <?php endif; ?>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="text-center py-5">

                            <i class="far fa-bell-slash fa-3x text-muted mb-3"></i>

                            <h5>No Notifications</h5>

                            <p class="text-muted">

                                You don't have any notifications yet.

                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </section>

</div>