<?php
function navItem($page, $icon, $label, $currentPage) {
    $active = $currentPage === $page ? 'active' : '';
    return "<li class=\"nav-item\"><a href=\"{$page}\" class=\"nav-link {$active}\"><i class=\"nav-icon fas fa-{$icon}\"></i><p>{$label}</p></a></li>";
}

function renderSidebar($userName, $currentPage) {
    return <<<HTML
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="index.php" class="brand-link">
    <img src="../../assets/pics/bcpLogo.png" alt="Logo" class="brand-image elevation-3" style="opacity: .8" />
    <span class="brand-text font-weight-light">BCP Employee</span>
  </a>
  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="info"><a href="#" class="d-block">{$userName}</a></div>
    </div>
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        " . navItem('index.php', 'tachometer-alt', 'Dashboard', $currentPage) . "
        " . navItem('survey.php', 'poll', 'Survey', $currentPage) . "
        " . navItem('social.php', 'users', 'Social', $currentPage) . "
        " . navItem('grievance.php', 'exclamation-triangle', 'Grievances', $currentPage) . "
        " . navItem('../../logout.php', 'sign-out-alt', 'Logout', $currentPage) . "
      </ul>
    </nav>
  </div>
</aside>
HTML;
}
