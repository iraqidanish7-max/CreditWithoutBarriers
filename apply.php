<?php
// apply.php - central apply page (single source of truth for all Apply buttons)
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Apply for Loan — Credit Without Barriers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .hero-apply { background: linear-gradient(90deg, rgba(106,17,203,0.07), rgba(37,99,235,0.05)); border-radius:12px; }
    .cta-large { padding: .9rem 1.2rem; font-size:1.05rem; border-radius:10px; }
  </style>
</head>
<body>
<?php include 'header.php'; ?>

<main class="container my-5">
  <div class="row align-items-center g-4">
    <div class="col-lg-7">
      <div class="card p-4 hero-apply">
        <h2 class="mb-2">Apply for a Microloan</h2>
        <p class="muted">Fast, community-verified microloans. Small amounts, simple process. Choose what suits you:</p>

        <div class="row g-3 mt-3">
          <div class="col-md-6">
            <div class="p-3 card-sm">
              <h5>Create an account</h5>
              <p class="small-muted">New user? Register to start your application and get faster verification.</p>
              <a href="register.php" class="btn btn-primary cta-large w-100 mt-2">Create Account</a>
            </div>
          </div>

          <div class="col-md-6">
            <div class="p-3 card-sm">
              <h5>Already registered?</h5>
              <p class="small-muted">Sign in to continue your application or view your dashboard.</p>
              <a href="customerlogin.php" class="btn btn-outline-primary cta-large w-100 mt-2">Login</a>
            </div>
          </div>
        </div>

        <hr />

        <h6 class="mt-3">Quick start (optional)</h6>
        <p class="small-muted">If you prefer, begin with a quick application summary — we will ask you to register before final submission.</p>

        <form method="post" action="apply.php" class="row g-2">
          <div class="col-md-6">
            <input name="name" class="form-control" placeholder="Your full name" value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>">
          </div>
          <div class="col-md-6">
            <input name="email" class="form-control" placeholder="Email" value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>">
          </div>
          <div class="col-12">
            <input name="loanreq" class="form-control" placeholder="Loan amount & purpose (e.g. ₹20,000 - small business)">
          </div>
          <div class="col-12 d-flex gap-2">
            <?php if (!empty($_SESSION['user_id'])): ?>
              <button name="submit_quick" class="btn btn-primary">Submit Application</button>
              <a class="btn btn-outline-secondary" href="dashboard.php">Go to Dashboard</a>
            <?php else: ?>
              <!-- If not logged in, guide user to register -->
              <button type="button" onclick="location.href='register.php?prefill='+encodeURIComponent(document.querySelector('[name=email]').value||'')" class="btn btn-primary">Register & Continue</button>
              <a class="btn btn-outline-secondary" href="customerlogin.php">Already Registered? Login</a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card p-4 card-sm">
        <h5>How it works</h5>
        <ol class="small-muted">
          <li>Register your account and verify identity.</li>
          <li>Post a loan request with amount & purpose.</li>
          <li>Lenders browse requests and make offers.</li>
          <li>Accept an offer and get funds disbursed.</li>
        </ol>
        <hr>
        <div class="small-muted">
          <strong>Tip:</strong> Keep your contact & document ready (Aadhaar / PAN / proof of address) for faster verification.
        </div>
      </div>
    </div>
  </div>
</main>

<?php include 'footer.php'; ?>
</body>
</html>