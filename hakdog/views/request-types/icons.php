<?php
$icons = [
    "fa-calendar-days",
    "fa-file",
    "fa-file-lines",
    "fa-user",
    "fa-users",
    "fa-briefcase",
    "fa-clock",
    "fa-money-bill",
    "fa-building",
    "fa-envelope",
    "fa-paper-plane",
    "fa-check",
    "fa-circle-check",
    "fa-xmark",
    "fa-triangle-exclamation",
    "fa-gear",
    "fa-screwdriver-wrench",
    "fa-hospital",
    "fa-stethoscope",
    "fa-graduation-cap",
    "fa-laptop",
    "fa-key",
    "fa-id-card",
    "fa-chart-line",
    "fa-house",
    "fa-user-tie",
    "fa-address-book",
    "fa-bell",
    "fa-calendar-check",
    "fa-clipboard",
    "fa-folder",
    "fa-folder-open",
    "fa-note-sticky",
    "fa-pen",
    "fa-print",
    "fa-receipt",
    "fa-shield-halved",
    "fa-star",
    "fa-tag",
    "fa-thumbs-up",
    "fa-truck",
    "fa-upload",
    "fa-download",
    "fa-wrench"
];

foreach ($icons as $icon):
?>
    <button type="button"
        class="btn btn-light icon-select"
        data-icon="<?= $icon ?>">
        <i class="fa-solid <?= $icon ?> fa-lg"></i>
    </button>
<?php endforeach; ?>