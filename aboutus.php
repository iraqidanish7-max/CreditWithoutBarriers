<?php
// aboutus.php — Zigzag animated About page for CreditWithoutBarriers
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>About Us — CreditWithoutBarriers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="page-bg">
  <main class="container py-5 about-page">

    <!-- Hero section (keep full width) -->
    <section class="card p-4 mb-4 admin-hero-card">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
          <p class="welcome-chip mb-1">About</p>
          <h2 class="hero-heading mb-1">
            CreditWithoutBarriers
          </h2>
          <p class="hero-subtitle mb-0">
            A  lending platform that makes borrowing and lending
            <strong>simple, transparent, and responsible</strong>.
          </p>
        </div>
        <div class="text-md-end">
          <div class="small text-muted">Tagline</div>
          <div class="fw-semibold">Credit Without Barriers, Hope Without Limits.</div>
        </div>
      </div>
    </section>

    <!-- ZIGZAG Cards wrapper -->
    <section class="about-cards-wrapper">

      <!-- Card 1: Left -->
      <div class="gradient-card-wrapper mb-4 about-animate slide-left zig-left">
        <div class="inner-card about-card p-0">
          <div class="row g-0 align-items-center">
            <div class="col-md-5">
              <!-- Replace src with your real image path -->
              <img src="images/customer_new5.png" class="img-fluid rounded-start about-card-img" alt="people discussing finance">
            </div>
            <div class="col-md-7">
              <div class="card-body py-4 px-4">
                <h4 class="card-title mb-2">What is CreditWithoutBarriers?</h4>
                <p class="card-text text-muted mb-2">
                  CreditWithoutBarriers is a demo fintech platform that connects people
                  who need loans with lenders who are ready to support them, in a safe and
                  structured way.
                </p>
                <p class="card-text mb-0">
                  Borrowers can post their requirements, lenders respond with offers, and
                  admins ensure everything is fair and transparent.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 2: Right -->
      <div class="gradient-card-wrapper mb-4 about-animate slide-right zig-right">
        <div class="inner-card about-card p-0">
          <div class="row g-0 flex-md-row-reverse align-items-center">
            <div class="col-md-5">
              <!-- Replace src with your real image path -->
              <img src="images/customer_new6.webp" class="img-fluid rounded-start about-card-img" alt="Student facing financial challenges">
            </div>
            <div class="col-md-7">
              <div class="card-body py-4 px-4">
                <h4 class="card-title mb-2">Why did we build this platform?</h4>
                <p class="card-text text-muted mb-2">
                  Many people struggle with small but urgent expenses or personal emergencies.
                </p>
                <p class="card-text mb-0">
                  This project shows how technology can make access to credit more
                  organised, instead of informal/untracked borrowing.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 3: Left -->
      <div class="gradient-card-wrapper mb-4 about-animate slide-left zig-left">
        <div class="inner-card about-card p-0">
          <div class="row g-0 align-items-center">
            <div class="col-md-5">
              <!-- Replace src with your real image path -->
              <img src="images/customer_new7.webp" class="img-fluid rounded-start about-card-img" alt="Borrower creating loan request">
            </div>
            <div class="col-md-7">
              <div class="card-body py-4 px-4">
                <h4 class="card-title mb-2">How Borrowing Works</h4>
                <ul class="card-text text-muted mb-0 small">
                  <li>Create an account as a <strong>Borrower</strong></li>
                  <li>Post your loan requirement with basic details</li>
                  <li>Wait for admin to review and approve your request</li>
                  <li>Compare admin-approved offers from lenders</li>
                  <li>Accept the best offer using secure <strong>OTP verification</strong></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 4: Right -->
      <div class="gradient-card-wrapper mb-4 about-animate slide-right zig-right">
        <div class="inner-card about-card p-0">
          <div class="row g-0 flex-md-row-reverse align-items-center">
            <div class="col-md-5">
              <!-- Replace src with your real image path -->
              <img src="images/customer_new8.jpeg" class="img-fluid rounded-start about-card-img" alt="Lender reviewing loan requests">
            </div>
            <div class="col-md-7">
              <div class="card-body py-4 px-4">
                <h4 class="card-title mb-2">How Lending Works</h4>
                <ul class="card-text text-muted mb-0 small">
                  <li>Create an account as a <strong>Lender</strong></li>
                  <li>Browse admin-approved loan requests from people</li>
                  <li>Make offers with your amount, interest and tenure</li>
                  <li>Track which offers are pending, accepted or rejected</li>
                  <li>Once accepted, a <strong>PDF loan agreement</strong> is generated</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 5: Left -->
      <div class="gradient-card-wrapper mb-4 about-animate slide-left zig-left">
        <div class="inner-card about-card p-0">
          <div class="row g-0 align-items-center">
            <div class="col-md-5">
              <!-- Replace src with your real image path -->
              <img src="images/customer_new9.jpg" class="img-fluid rounded-start about-card-img" alt="Admin monitoring platform">
            </div>
            <div class="col-md-7">
              <div class="card-body py-4 px-4">
                <h4 class="card-title mb-2">Admin & Safety Controls</h4>
                <p class="card-text text-muted mb-2 small">
                  The Admin plays a key role in keeping the platform clean:
                </p>
                <ul class="card-text text-muted mb-0 small">
                  <li>Approves or rejects <strong>loan requests</strong></li>
                  <li>Reviews lender <strong>offers</strong> before borrowers see them</li>
                  <li>Can enable/disable user accounts</li>
                  <li>Monitors platform using <strong>analytics charts</strong></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 6: Right -->
      <div class="gradient-card-wrapper mb-4 about-animate slide-right zig-right">
        <div class="inner-card about-card p-0">
          <div class="row g-0 flex-md-row-reverse align-items-center">
            <div class="col-md-5">
              <!-- Replace src with your real image path -->
              <img src="images/customer_new10.png" class="img-fluid rounded-start about-card-img" alt="Fintech project features">
            </div>
            <div class="col-md-7">
              <div class="card-body py-4 px-4">
                <h4 class="card-title mb-2">What makes it different?</h4>
                <ul class="card-text text-muted mb-0 small">
                  <li>Realistic <strong>multi-role</strong> (Borrower / Lender / Admin) workflow</li>
                  <li><strong>OTP-based</strong> acceptance of offers</li>
                  <li>Downloadable <strong>PDF loan agreement</strong></li>
                  <li><strong>CSV export</strong> of accepted offers</li>
                  <li>Admin <strong>analytics dashboard</strong> with charts</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 7: Video (Left) -->
      <div class="gradient-card-wrapper mb-4 about-animate slide-left zig-left">
        <div class="inner-card about-card p-0">
          <div class="row g-0 align-items-center">
            <div class="col-md-6">
              <div class="card-body py-4 px-4">
                <h4 class="card-title mb-2">Watch: Borrowing & Lending Explained</h4>
                <p class="card-text text-muted small mb-2">
                  This video visually explains how a simple, 
                  safe lending platform can
                  support people and lenders together.
                </p>
                <p class="card-text small mb-0">
                  This video shows how a banking or financial instituiton work,
                  and help people,
                  and we also intend to do so by providing our services
                </p>
              </div>
            </div>
            <div class="col-md-6">
              <!-- Replace src with your actual YouTube embed link -->
              <div class="ratio ratio-16x9 about-video-wrapper">
                <iframe
                  src="https://www.youtube.com/embed/6rdwLA-gFgs?si=A-0u8LYqdWJoJSUJ"
                  title="Loan explainer video"
                  allowfullscreen
                ></iframe>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 8: Map (Right) -->
      <div class="gradient-card-wrapper mb-4 about-animate slide-right zig-right">
        <div class="inner-card about-card p-0">
          <div class="row g-0 flex-md-row-reverse align-items-center">
            <div class="col-md-6">
              <div class="card-body py-4 px-4">
                <h4 class="card-title mb-2">Where are we based?</h4>
                <p class="card-text text-muted small mb-2">
                  Since we are an online platform we believe that  our college is our inspiration to
                  build such Web Application.The map shows
                  the location of our institution.
                </p>
                
              </div>
            </div>
            <div class="col-md-6">
              <!-- Replace src with your college Google Maps embed link -->
              <div class="ratio ratio-4x3 about-map-wrapper">
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5547.534843146895!2d72.80039803595852!3d19.416511992825598!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7abe2f6aaaaab%3A0xfb2503d0748cb985!2sM.B%20HARRIS%20COLLEGE%20OF%20ARTS!5e0!3m2!1sen!2sin!4v1763758860979!5m2!1sen!2sin"
                  style="border:0;"
                  allowfullscreen=""
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
              </div>
            </div>
          </div>
        </div>
      </div>

    </section>

  </main>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scroll animation script -->
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const animatedCards = document.querySelectorAll('.about-animate');

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
        }
      });
    }, {
      threshold: 0.2
    });

    animatedCards.forEach(card => observer.observe(card));
  });
</script>
</body>
</html>