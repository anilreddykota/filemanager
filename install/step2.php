<?php include 'includes/header.php'; ?>



<h4>Step 2: Database Configuration</h4>
<p>Enter your database connection details below:</p>
<?php if (isset($_GET['error'])): ?>
  <div class="alert alert-danger" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i> <?php echo htmlspecialchars($_GET['error']); ?>
  </div>
<?php endif; ?>



<form action="step3.php" method="POST">
  <div class="mb-3">
    <label>DB Host</label>
    <input type="text" name="db_host" class="form-control" value="localhost" required>
  </div>
  <div class="mb-3">
    <label>DB Name</label>
    <input type="text" name="db_name" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>DB Username</label>
    <input type="text" name="db_user" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>DB Password</label>
    <input type="password" name="db_pass" class="form-control">
  </div>

  <div class="form-text mb-3">
    If you don't have a database, it will be created automatically.
  </div>

  <!-- insert user with pro access  -->
  <div class="mb-3">
    <label>Admin Username</label>
    <input type="text" name="admin_user" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Admin Email</label>
    <input type="email" name="admin_email" class="form-control" required>
  </div>

  <div class="mb-3">
    <label>Admin Password</label>
    <input type="password" name="admin_pass" class="form-control" required>
  </div>

  <button type="submit" class="btn btn-success">Install Now</button>
</form>

<?php include 'includes/footer.php'; ?>