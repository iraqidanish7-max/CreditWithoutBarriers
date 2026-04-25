<?php
// lender_offers.php - view all offers made by the logged-in lender

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: lenderlogin.php');
    exit;
}

// Only lenders allowed
$role = $_SESSION['role'] ?? '';
if ($role !== 'lender') {
    if ($role === 'borrower') {
        header('Location: dashboard.php');
    } elseif ($role === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: customerlogin.php');
    }
    exit;
}

include __DIR__ . '/connection.php';

$lender_name  = $_SESSION['user_name']  ?? '';
$lender_email = $_SESSION['user_email'] ?? '';

// Fetch all offers by this lender, join with request details
$offers = [];
$stmt = $conn->prepare("
    SELECT 
        o.id,
        o.offer_amount,
        o.interest_rate,
        o.tenure_months,
        o.status,
        o.created_at,
        c.id AS request_id,
        c.name AS borrower_name,
        c.email AS borrower_email,
        c.mobile AS borrower_mobile,
        c.loanreq AS borrower_loanreq
    FROM loan_offers o
    INNER JOIN customerdetails c ON o.request_id = c.id
    WHERE o.lender_email = ?
    ORDER BY o.created_at DESC
");
if ($stmt) {
    $stmt->bind_param('s', $lender_email);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $offers[] = $row;
    }
    $stmt->close();
} else {
    die('Database error: ' . htmlspecialchars($conn->error));
}

// Simple stats
$total_offers   = count($offers);
$pending_count  = count(array_filter($offers, fn($o) => $o['status'] === 'pending'));
$accepted_count = count(array_filter($offers, fn($o) => $o['status'] === 'accepted'));
$rejected_count = count(array_filter($offers, fn($o) => $o['status'] === 'rejected'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Offers - Lender Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>

<div class="page-bg">
  <main class="container py-5 lender-offers-page">

    <!-- Hero card (same family as lender dashboard / borrower hero) -->
    <section class="lender-offers-hero-card mb-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-1 gap-3">
        <div>
          <p class="welcome-chip mb-1">My Offers</p>
          <h2 class="mb-1 hero-heading">
            Hello, <?php echo htmlspecialchars($lender_name ?: $lender_email); ?> 👋
          </h2>
          <p class="hero-subtitle mb-0">
            Here’s a snapshot of every offer you’ve made to borrowers, along with its current status.
          </p>
        </div>

        <div class="hero-stats">
          <div class="stat-card">
            <span class="stat-label">Total Offers</span>
            <span class="stat-value"><?php echo $total_offers; ?></span>
          </div>
          <div class="stat-card">
            <span class="stat-label">Pending</span>
            <span class="stat-value"><?php echo $pending_count; ?></span>
          </div>
          <div class="stat-card">
            <span class="stat-label">Accepted</span>
            <span class="stat-value"><?php echo $accepted_count; ?></span>
          </div>
        </div>
      </div>
    </section>

    <!-- Card: My Offers table -->
    <section class="card lender-offers-card p-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
        <div>
          <h4 class="mb-1">Offers You’ve Made</h4>
          <p class="text-muted mb-0 small">
            Each row is one offer linked to a borrower’s loan request.
          </p>
        </div>
        <div class="d-flex gap-2">
          <a href="lender_dashboard.php" class="btn btn-outline-primary btn-sm">
            ← Back to Lender Dashboard
          </a>
        </div>
      </div>

      <?php if (empty($offers)): ?>
        <div class="empty-state mt-2">
          <p>You haven’t made any offers yet.</p>
          <p class="empty-hint">
            Go to the Lender Dashboard to view borrower requests and start making offers.
          </p>
        </div>
      <?php else: ?>
        <div class="table-responsive lender-offers-table-wrapper mt-2">
          <table class="table align-middle lender-offers-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Borrower</th>
                <th>Loan Required (₹)</th>
                <th>Your Offer (₹)</th>
                <th>Interest</th>
                <th>Tenure</th>
                <th>Offered On</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($offers as $index => $o): ?>
              <?php
                // Map status to our custom badge classes
                $status_raw = strtolower($o['status'] ?? '');
                $status_label = strtoupper($status_raw ?: 'PENDING');
                $status_class = 'badge-status no-offers';

                if ($status_raw === 'pending') {
                    $status_class = 'badge-status has-offers';
                    $status_label = 'PENDING';
                } elseif ($status_raw === 'accepted') {
                    $status_class = 'badge-status accepted';
                    $status_label = 'ACCEPTED';
                } elseif ($status_raw === 'rejected') {
                    $status_class = 'badge-status no-offers';
                    $status_label = 'REJECTED';
                } elseif ($status_raw === 'withdrawn') {
                    $status_class = 'badge-status no-offers';
                    $status_label = 'WITHDRAWN';
                }
              ?>
              <tr>
                <td><?php echo $index + 1; ?></td>
                <td>
                  <div class="fw-semibold">
                    <?php echo htmlspecialchars($o['borrower_name']); ?>
                  </div>
                  <div class="small text-muted">
                    <?php echo htmlspecialchars($o['borrower_email']); ?><br>
                    📞 <?php echo htmlspecialchars($o['borrower_mobile']); ?>
                  </div>
                </td>
                <td>
                  ₹<?php echo number_format((float)$o['borrower_loanreq']); ?>
                </td>
                <td class="fw-semibold text-primary">
                  ₹<?php echo number_format((float)$o['offer_amount'], 2); ?>
                </td>
                <td>
                  <?php echo htmlspecialchars($o['interest_rate']); ?>%
                </td>
                <td>
                  <?php echo (int)$o['tenure_months']; ?> months
                </td>
                <td class="small text-muted">
                  <?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($o['created_at']))); ?>
                </td>
                <td>
                  <span class="<?php echo $status_class; ?>">
                    <?php echo $status_label; ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

  </main>
</div>

<?php include 'footer.php'; ?>
</body>
</html>