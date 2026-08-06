<?php

$totalComplaints = count($complaints);

$pendingCount = 0;
$resolvedCount = 0;
$rejectedCount = 0;

foreach ($complaints as $complaint) {

    switch ($complaint['status']) {

        case 'submitted':
        case 'under_review':
        case 'investigation':
            $pendingCount++;
            break;

        case 'resolved':
            $resolvedCount++;
            break;

        case 'rejected':
            $rejectedCount++;
            break;
    }
}
