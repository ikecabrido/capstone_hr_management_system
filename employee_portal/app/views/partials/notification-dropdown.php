<li class="nav-item position-relative" id="notificationDropdown">

    <a href="javascript:void(0)"
        id="notificationBell"
        class="nav-link position-relative">

        <i class="far fa-bell fa-lg"></i>

        <?php if ($notificationCount > 0): ?>
            <span style="
                position:absolute;
                top:6px;
                right:6px;
                width:10px;
                height:10px;
                background:#dc3545;
                border:2px solid #fff;
                border-radius:50%;
            "></span>
        <?php endif; ?>

    </a>
    <div id="notificationMenu"
        style="
        display:none;
        position:absolute;
        top:100%;
        right:0;
        transform:translateX(75%);
        width:360px;
        margin-top:10px;
        background:#fff;
        border-radius:14px;
        box-shadow:0 12px 30px rgba(0,0,0,.18);
        border:1px solid #e5e7eb;
        overflow:hidden;
        z-index:9999;
    ">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom">

            <h5 class="mb-0 fw-bold">
                Notifications
            </h5>

            <?php if ($notificationCount > 0): ?>
                <span class="badge badge-primary rounded-pill px-2 py-1">
                    <?= $notificationCount ?>
                </span>
            <?php endif; ?>

        </div>

        <!-- Body -->
        <div style="max-height:420px;overflow-y:auto;">

            <?php if (!empty($latestNotifications)): ?>

                <?php foreach ($latestNotifications as $notification): ?>

                    <div class="text-decoration-none text-dark">

                        <div class="notification-item d-flex align-items-start px-3 py-3">

                            <!-- Icon -->
                            <div class="notification-icon">

                                <i class="fas fa-bell text-primary"></i>

                            </div>

                            <!-- Content -->
                            <div class="flex-grow-1 ml-3">

                                <div class="font-weight-bold text-dark">

                                    <?= htmlspecialchars($notification['title']); ?>

                                </div>

                                <div class="text-muted small mt-1">

                                    <?= htmlspecialchars(mb_strimwidth($notification['message'], 0, 70, '...')); ?>

                                </div>

                                <div class="small text-primary mt-2">

                                    <?= date('M d, h:i A', strtotime($notification['created_at'])); ?>

                                </div>

                            </div>

                            <?php if (empty($notification['is_read'])): ?>

                                <div class="notification-dot"></div>

                            <?php endif; ?>

                        </div>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="text-center py-5">

                    <i class="far fa-bell-slash fa-2x text-muted mb-3"></i>

                    <div class="text-muted">

                        No notifications yet

                    </div>

                </div>

            <?php endif; ?>

        </div>

        <!-- Footer -->
        <div class="border-top bg-light">

            <a href="index.php?url=employee-notifications"
                class="d-block text-center py-3 font-weight-bold text-decoration-none">

                View All Notifications

            </a>

        </div>

    </div>

    <style>
        .notification-item {
            display: flex;
            align-items: flex-start;
            transition: .15s;
        }

        .notification-item:hover {
            background: #f0f2f5;
        }

        .notification-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e7f3ff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 18px;
        }

        .notification-dot {
            width: 10px;
            height: 10px;
            background: #1877f2;
            border-radius: 50%;
            margin-left: 10px;
            margin-top: 18px;
            flex-shrink: 0;
        }

        #notificationMenu::-webkit-scrollbar {
            width: 6px;
        }

        #notificationMenu::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }
    </style>

    <style>
        .notification-item {
            transition: .15s ease;
        }

        .notification-item:hover {
            background: #f2f4f7;
        }
    </style>

</li>