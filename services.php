<?php
// services.php
// Put this in the project root (same folder as header.php/footer.php)
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Services — Credit Without Barriers</title>

  <!-- Fonts & Bootstrap -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Your stylesheet -->
  <link rel="stylesheet" href="style.css" />
</head>

<body>

<?php
 include 'header.php';
  ?>
<div class="top-accent"></div>
<!-- Page background (kept from your original file) -->
<main class="page-bg">
  <!-- hero-ish intro -->
  <section class="py-5">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <div class="card p-4 reveal">
            <h1 class="display-5 mb-3">Services built for real people</h1>
            <p class="muted lead">We connect borrowers and lenders through community-verified microloans — low paperwork, clear terms, timely notifications, and secure payment flows.</p>

            <div class="mt-4 d-flex gap-2 flex-wrap">
              <a class="btn btn-primary btn-lg" href="apply.php">Apply for Loan</a>
              <a class="btn btn-outline-primary btn-lg" href="register.php">Register</a>
            </div>
          </div>

          <!-- quick bullets -->
          <div class="mt-3 reveal">
            <ul class="fa-list muted">
              <li><strong>Dual Roles:</strong> Borrow or lend from the same account — switch easily.</li>
              <li><strong>Community KYC:</strong> Validators confirm identity so lenders can trust borrowers.</li>
              <li><strong>Flexible Terms:</strong> Short-term microloans with clear EMIs and reminders.</li>
              <li><strong>Secure Payments:</strong> UPI/Razorpay + bank integrations for smooth disbursal & repayments.</li>
            </ul>
          </div>
        </div>

        <!-- visual / large illustrative card -->
        <div class="col-lg-5">
          <div class="card p-0 reveal" style="overflow:hidden;">
            <img src="images/customer_new4.jpg" alt="services hero" style="width:100%; height:360px; object-fit:cover; display:block;">
            <div class="p-3">
              <h5 class="mb-1">Trusted microfinance marketplace</h5>
              <p class="muted small mb-0">Transparent, local, and designed to help communities grow.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- feature tiles (improved visuals) -->
  <section class="pb-5">
    <div class="container">
      <h2 class="mb-4">Key features</h2>
      <div class="row g-3">
        <!-- feature 1 -->
        <div class="col-md-6 col-lg-4">
          <div class="feature-card p-4 reveal h-100">
            <div class="d-flex align-items-start gap-3 mb-2">
              <div style="width:56px;height:56px;border-radius:10px;background:linear-gradient(135deg,#6a11cb,#2575fc);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:20px;">
                💳
              </div>
              <div>
                <h6 class="mb-1">Quick Verification</h6>
                <p class="small muted mb-0">Fast KYC via community validators and document upload.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- feature 2 -->
        <div class="col-md-6 col-lg-4">
          <div class="feature-card p-4 reveal h-100">
            <div class="d-flex align-items-start gap-3 mb-2">
              <div style="width:56px;height:56px;border-radius:10px;background:linear-gradient(135deg,#10b981,#06b6d4);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:20px;">
                🔁
              </div>
              <div>
                <h6 class="mb-1">Flexible Repayments</h6>
                <p class="small muted mb-0">Choose durations and payment plans that match cash flow.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- feature 3 -->
        <div class="col-md-6 col-lg-4">
          <div class="feature-card p-4 reveal h-100">
            <div class="d-flex align-items-start gap-3 mb-2">
              <div style="width:56px;height:56px;border-radius:10px;background:linear-gradient(135deg,#ffd166,#ff7a7a);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:20px;">
                🔔
              </div>
              <div>
                <h6 class="mb-1">Automated Notifications</h6>
                <p class="small muted mb-0">SMS & email reminders for due dates and offers.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- feature 4 -->
        <div class="col-md-6 col-lg-4">
          <div class="feature-card p-4 reveal h-100">
            <div class="d-flex align-items-start gap-3 mb-2">
              <div style="width:56px;height:56px;border-radius:10px;background:linear-gradient(135deg,#6a11cb,#2575fc);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:20px;">
                🔐
              </div>
              <div>
                <h6 class="mb-1">Secure Authentication</h6>
                <p class="small muted mb-0">OTP login + verified profiles to keep funds safe.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- feature 5 -->
        <div class="col-md-6 col-lg-4">
          <div class="feature-card p-4 reveal h-100">
            <div class="d-flex align-items-start gap-3 mb-2">
              <div style="width:56px;height:56px;border-radius:10px;background:linear-gradient(135deg,#10b981,#06b6d4);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:20px;">
                📈
              </div>
              <div>
                <h6 class="mb-1">Lender Dashboard</h6>
                <p class="small muted mb-0">Track offers, returns, and borrower history in one place.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- feature 6 -->
        <div class="col-md-6 col-lg-4">
          <div class="feature-card p-4 reveal h-100">
            <div class="d-flex align-items-start gap-3 mb-2">
              <div style="width:56px;height:56px;border-radius:10px;background:linear-gradient(135deg,#ffd166,#ff7a7a);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:20px;">
                🧾
              </div>
              <div>
                <h6 class="mb-1">Clear Terms</h6>
                <p class="small muted mb-0">All charges and EMIs shown upfront — no surprises.</p>
              </div>
            </div>
          </div>
        </div>

      </div> <!-- .row -->
    </div> <!-- .container -->
  </section>

  <!-- CTA strip -->
  <section class="py-4">
    <div class="container">
      <div class="card p-3 reveal d-flex align-items-center justify-content-between flex-column flex-md-row gap-3">
        <div>
          <h5 class="mb-1">Ready to start?</h5>
          <p class="muted small mb-0">Create a free account and apply in minutes — our community validators will help speed things up.</p>
        </div>
        <div class="d-flex gap-2">
          <a class="btn btn-lg btn-primary" href="register.php">Create Account</a>
          <a class="btn btn-lg btn-outline-primary" href="lenderlogin.php">I want to lend</a>
        </div>
      </div>
    </div>
  </section>
</main>
<?php 
include 'footer.php';
 ?>

</body>
</html>