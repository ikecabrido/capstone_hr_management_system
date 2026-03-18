<?php require_once __DIR__ . '/config.php';
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/header.php';
?>

<h2>Profile</h2>
<style>
  .profile-table { border-collapse: collapse; width: 100%; max-width: 640px; }
  .profile-table th, .profile-table td { padding: 8px 12px; text-align: left; vertical-align: top; }
  .profile-table th { border-bottom: 1px solid #ccc; font-weight: 600; }
  .profile-array { font-family: monospace; }
</style>

<table class="profile-table">
  <thead>
    <tr>
      <th>user</th>
      <th>role</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="profile-array">[ "<?php echo htmlspecialchars($_SESSION['username']); ?>" ]</td>
      <td class="profile-array">[ "<?php echo htmlspecialchars($_SESSION['role'] ?? ''); ?>" ]</td>
    </tr>
  </tbody>
</table>

<?php require_once __DIR__ . '/footer.php'; ?>