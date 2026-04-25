<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Credit Without Barriers — Home</title>

  <!-- Fonts & Bootstrap -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- App styles -->
  <link rel="stylesheet" href="style.css" />
</head>

<body>

<!-- HEADER -->
<?php
include 'header.php'; 
 ?>
<!-- HERO SPLIT -->
 <main class="main-content">
<section class="container hero-split my-4">
  <div class="row align-items-center g-4">

    <!-- LEFT -->
    <div class="col-lg-6 order-2 order-lg-1">
      <div class="card p-4 mb-3">
        <h1 class="display-6 mb-2">Microloans made simple — for everyone</h1>
        <p class="muted lead">Fast, transparent loans for people banks often miss. Community-verified, low-hassle, and secure.</p>

        <div class="row g-2 mt-3">
          <div class="col-sm-6">
            <div class="feature-card p-3">
              <h6 class="mb-1">Quick Verification</h6>
              <p class="small muted mb-0">Community validators speed up trust checks.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="feature-card p-3">
              <h6 class="mb-1">Flexible Loans</h6>
              <p class="small muted mb-0">Small, short-term loans that fit needs.</p>
            </div>
          </div>
        </div>

        <div class="mt-4 d-flex gap-2">
          <a class="btn btn-primary btn-lg" href="apply.php">Apply Now</a>
          <a class="btn btn-outline-primary btn-lg" href="services.php">How it works</a>
        </div>
      </div>

      <div class="card p-4">
        <h4 class="mb-2">Problem highlights</h4>
        <ul class="fa-list muted">
          <li><strong>Lack of Collateral:</strong> Many applicants can't pledge assets.</li>
          <li><strong>Complex Docs:</strong> Paperwork excludes deserving borrowers.</li>
          <li><strong>High Interest:</strong> Informal lenders often exploit.</li>
        </ul>
      </div>
    </div>

    <!-- RIGHT (CAROUSEL) -->
    <div class="col-lg-6 order-1 order-lg-2">
      <div class="carousel-container card p-0">
        <div id="splitCarousel" class="carousel slide h-100" data-bs-ride="carousel" data-bs-interval="4000">
          <div class="carousel-indicators">
            <button type="button" data-bs-target="#splitCarousel" data-bs-slide-to="0" class="active" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#splitCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#splitCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
          </div>

          <div class="carousel-inner h-100">
            <div class="carousel-item active h-100">
              <div class="carousel-bg" style="background-image:url('images/customer_new.webp')"></div>
              <div class="carousel-overlay"></div>
            </div>

            <div class="carousel-item h-100">
              <div class="carousel-bg" style="background-image:url('images/customer_new2.jpg')"></div>
              <div class="carousel-overlay"></div>
            </div>

            <div class="carousel-item h-100">
              <div class="carousel-bg" style="background-image:url('images/customer_new3.jpg')"></div>
              <div class="carousel-overlay"></div>
            </div>
          </div>

          <button class="carousel-control-prev" type="button" data-bs-target="#splitCarousel" data-bs-slide="prev" aria-label="Previous slide">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#splitCarousel" data-bs-slide="next" aria-label="Next slide">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
          </button>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- MAIN CONTENT -->
<main class="container my-5">
  <section class="mt-3">
    <h2 class="mb-3">How it works</h2>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="card min-h-140 p-3">
          <h5>Sign Up & Verify</h5>
          <p class="small muted">Quick registration & document upload. Community validator signs to confirm authenticity.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card min-h-140 p-3">
          <h5>Post Request</h5>
          <p class="small muted">Borrowers post amount, purpose & desired terms. Lenders browse and offer.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card min-h-140 p-3">
          <h5>Receive Funds & Repay</h5>
          <p class="small muted">Disbursement after acceptance. Simple repayment UI with notifications for dues.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="mt-5">
    <h2 class="mb-3">Key Features</h2>
    <div class="row g-3">
      <div class="col-md-6 col-lg-4">
        
        <div class="feature-card p-3">
          <h6>Automated Notifications</h6>
          <p class="small muted">Alerts for due dates & repayments.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="feature-card p-3">
          <h6>Dual Roles</h6>
          <p class="small muted">Switch between borrower and lender easily.</p>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="feature-card p-3">
          <h6>Secure Authentication</h6>
          <p class="small muted">OTP-based login options and verified profiles.</p>
        </div>
      </div>
    </div>
  </section>
</main>
</main>
<?php 
include 'footer.php';
?>
</body>
</html>