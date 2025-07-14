<?php include 'includes/header.php';

$requirements = [
    'PHP >= 7.4' => version_compare(PHP_VERSION, '7.4', '>='),
    'PDO Extension' => extension_loaded('pdo'),
    'Writable: config.php' => is_writable('../config/config.php') || is_writable('../config'),
    'MySQLi Extension' => extension_loaded('mysqli'),
];

$allOk = !in_array(false, $requirements);
?>

<h4>Step 1: System Requirements</h4>
<ul class="list-group mb-3">
  <?php foreach ($requirements as $key => $ok): ?>
    <li class="list-group-item d-flex justify-content-between align-items-center">
      <?= $key ?>
      <span class="badge bg-<?= $ok ? 'success' : 'danger' ?>">
        <?= $ok ? 'OK' : 'Missing' ?>
      </span>
    </li>
  <?php endforeach; ?>
</ul>

<?php if ($allOk): ?>
  <a href="step2.php" class="btn btn-primary">Next →</a>
<?php else: ?>
  <div class="alert alert-danger">Fix the errors above before continuing.</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
