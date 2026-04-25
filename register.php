<?php
// register.php - safer registration handler that uses your connection.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Unified DB connection
include __DIR__ . '/connection.php';

// helper: show form
function show_form($errors = [], $old = []) {
    ?>
    <!doctype html>
    <html>
    <head>
      <meta charset="utf-8">
      <title>Register</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
      <link rel="stylesheet" href="style.css">
    </head>
    <body>
    <?php include 'header.php'; ?>
    <main class="container my-5">
      <div class="row justify-content-center">
        <div class="col-md-7">
          <div class="card p-4">
            <h3 class="mb-3">Create Account</h3>

            <?php if($errors): ?>
              <div class="alert alert-danger">
                <?php foreach($errors as $e) echo '<div>' . htmlentities($e) . '</div>'; ?>
              </div>
            <?php endif; ?>

            <form method="post" action="register.php" novalidate>
              <div class="mb-2">
                <label class="form-label">Full name</label>
                <input class="form-control" name="name" value="<?php echo htmlentities($old['name'] ?? '') ?>">
              </div>
              <div class="mb-2">
                <label class="form-label">Email</label>
                <input class="form-control" name="email" type="email" value="<?php echo htmlentities($old['email'] ?? '') ?>">
              </div>
              <div class="mb-2">
                <label class="form-label">Phone</label>
                <input class="form-control" name="phone" value="<?php echo htmlentities($old['phone'] ?? '') ?>">
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

              <div class="mb-3">
                <label class="form-label">I want to</label>
                <select name="role" class="form-select" required>
                    <option value="borrower" selected>Borrow money (Borrower)</option>
                    <option value="lender">Lend money (Lender)</option>
                    <!-- For now we won’t expose admin here -->
                </select>
              </div>

              <div class="d-flex gap-2 mt-3">
                <button class="btn btn-primary">Register</button>
                <a class="btn btn-outline-secondary" href="index.php">Cancel</a>
              </div>
            </form>

            <?php if (!isset($GLOBALS['conn'])): ?>
              <div class="mt-3 alert alert-warning">
                Warning: database connection not found. Registration will not save until DB is configured.
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
    <?php include 'footer.php'; ?>
    </body>
    </html>
    <?php
}

// If not POST, show form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    show_form();
    exit;
}

// Validate input
$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$pass  = $_POST['password'] ?? '';
$role  = $_POST['role'] ?? 'borrower';

$allowed_roles = ['borrower', 'lender', 'admin'];
if (!in_array($role, $allowed_roles, true)) {
    $role = 'borrower';
}

$errors = [];
if ($name === '') $errors[] = 'Name is required.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
if ($phone === '') $errors[] = 'Phone is required.';
if (strlen($pass) < 6) $errors[] = 'Password must be at least 6 characters.';

if ($errors) {
    show_form($errors, $_POST);
    exit;
}

// Ensure DB connection is present
if (!isset($conn)) {
    $errors[] = 'Database connection not found. Please ensure connection.php defines $conn.';
    show_form($errors, $_POST);
    exit;
}

// Create users table if it doesn't exist (safe, now includes role)
$tableSql = "CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  email VARCHAR(200) NOT NULL UNIQUE,
  phone VARCHAR(50),
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('borrower','lender','admin') NOT NULL DEFAULT 'borrower',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

try {
    mysqli_query($conn, $tableSql);
} catch (Exception $ex) {
    error_log("Could not create users table: " . $ex->getMessage());
}

// Insert user with prepared statement
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $hash = password_hash($pass, PASSWORD_DEFAULT);

    // ✅ 5 columns, 5 placeholders, 5 param types
    $stmt = $conn->prepare("
        INSERT INTO users (name, email, phone, password_hash, role) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $name, $email, $phone, $hash, $role);
    $stmt->execute();
    $stmt->close();

    // redirect user to appropriate login page after registering
$targetLogin = ($role === 'lender') ? 'lenderlogin.php' : 'customerlogin.php';

header("Location: " . $targetLogin . "?prefill=" . urlencode($email) . "&just_registered=1");
exit;
} catch (mysqli_sql_exception $e) {
    // Unique email? or other DB error
    $msg = $e->getMessage();
    error_log("Register DB error: " . $msg);
    if (stripos($msg, 'Duplicate') !== false) {
        $errors[] = 'An account with this email already exists.';
    } else {
        $errors[] = 'Could not create account right now. Please try later.';
    }
    show_form($errors, $_POST);
    exit;
} catch (Exception $ex) {
    error_log("Register error: " . $ex->getMessage());
    $errors[] = 'Unexpected error occurred. Please try again later.';
    show_form($errors, $_POST);
    exit;
}