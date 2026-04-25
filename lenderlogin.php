<?php
// lenderlogin.php — same login form UI, but labeled as "Lender Login"
$prefill = isset($_GET['prefill']) ? trim($_GET['prefill']) : '';
$just_registered = isset($_GET['just_registered']) ? true : false;
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Lender Login — Credit Without Barriers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .note { font-size: .95rem; color: #374151; }
  </style>
</head>
<body>
<?php include 'header.php'; ?>

<main class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card p-4">
        <h3 class="mb-3">Lender Login</h3>

        <?php if ($just_registered): ?>
          <div class="alert alert-success">
            Lender account created successfully. Please log in below.
          </div>
        <?php endif; ?>

        <?php if ($error): ?>
          <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
          </div>
        <?php endif; ?>

        <!-- Same auth handler -->
        <form method="post" action="customer_auth.php" novalidate>
          <div class="mb-3">
            <label class="form-label">Email or Username</label>
            <input class="form-control" name="user" value="<?php echo htmlspecialchars($prefill); ?>" required>
          </div>

          <div class="mb-3">
            <label class="form-label" for="pwd">Password</label>
            <div class="pwd-wrap">
              <input id="pwd" class="form-control" type="password" name="password" required>

              <button type="button"
                      class="pwd-toggle-btn"
                      data-target="#pwd"
                      aria-label="Show password">

                <!-- Eye open -->
                <svg class="eye-open" viewBox="0 0 24 24" fill="none">
                  <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" 
                        stroke="currentColor" stroke-width="1.4" />
                  <circle cx="12" cy="12" r="3" 
                          stroke="currentColor" stroke-width="1.4"/>
                </svg>

                <!-- Eye closed -->
                <svg class="eye-closed" viewBox="0 0 24 24" fill="none" style="display:none;">
                  <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7 
                          .9-1.6 2.1-3 3.6-4.2"
                        stroke="currentColor" stroke-width="1.4"/>
                  <path d="M1 1l22 22"
                        stroke="currentColor" stroke-width="1.4"/>
                </svg>

              </button>
            </div>
          </div>

          <button class="btn btn-primary w-100">Login</button>
        </form>

        <p class="mt-3 small-muted note">
          New here? <a href="register.php">Create an account</a>.
        </p>
      </div>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>

<script>
  // (optional) you can reuse your password toggle JS here if you want
</script>
</body>
</html>