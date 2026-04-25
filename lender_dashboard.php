<?php
// lender_dashboard.php — view all borrower requests + navigate to Make Offer

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: lenderlogin.php');
    exit;
}

// Role guard: only lenders allowed here
$role = $_SESSION['role'] ?? '';
if ($role !== 'lender') {
    if ($role === 'borrower') {
        header('Location: dashboard.php');
    } elseif ($role === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: lenderlogin.php');
    }
    exit;
}

include __DIR__ . '/connection.php';

$lender_name  = $_SESSION['user_name']  ?? '';
$lender_email = $_SESSION['user_email'] ?? '';

// Fetch all APPROVED loan requests + basic offer stats for each
$requests = [];

$sql = "
    SELECT 
        c.id,
        c.name,
        c.aadhar,
        c.mobile,
        c.email,
        c.loanreq,
        c.status,
        c.created_at,
        COUNT(lo.id) AS total_offers,
        SUM(CASE WHEN lo.status = 'accepted' THEN 1 ELSE 0 END) AS accepted_offers
    FROM customerdetails c
    LEFT JOIN loan_offers lo ON lo.request_id = c.id
    WHERE c.status = 'approved'
    GROUP BY 
        c.id, c.name, c.aadhar, c.mobile, c.email, c.loanreq, c.status, c.created_at
    ORDER BY c.created_at DESC
";

if ($stmt = $conn->prepare($sql)) {
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        // Normalize nulls
        $row['total_offers']    = (int)($row['total_offers'] ?? 0);
        $row['accepted_offers'] = (int)($row['accepted_offers'] ?? 0);
        $requests[] = $row;
    }
    $stmt->close();
}

// Stats for hero
$total_requests = count($requests);
$with_offers    = count(array_filter($requests, fn($r) => $r['total_offers'] > 0));
$accepted_count = count(array_filter($requests, fn($r) => $r['accepted_offers'] > 0));
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Lender Dashboard — Credit Without Barriers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="page-bg">
  <main class="container py-5 lender-dashboard">

    <!-- Hero card: same family as borrower dashboard -->
    <section class="lender-hero-card mb-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
          <p class="welcome-chip mb-1">Lender Dashboard</p>
          <h2 class="mb-1 hero-heading">
            Welcome, <?php echo htmlspecialchars($lender_name ?: $lender_email); ?> 👋
          </h2>
          <p class="hero-subtitle mb-0">
            Browse admin-approved borrower requests, see where offers already exist, and choose where you want to extend credit.
          </p>
        </div>

        <div class="hero-stats">
          <div class="stat-card">
            <span class="stat-label">Approved Requests</span>
            <span class="stat-value"><?php echo $total_requests; ?></span>
          </div>
          <div class="stat-card">
            <span class="stat-label">With Offers</span>
            <span class="stat-value"><?php echo $with_offers; ?></span>
          </div>
          <div class="stat-card">
            <span class="stat-label">Offers Accepted</span>
            <span class="stat-value"><?php echo $accepted_count; ?></span>
          </div>
        </div>
      </div>
    </section>

    <!-- Main card: Borrower requests table -->
    <div class="gradient-card-wrapper mb-4">
      <section class="inner-card lender-requests-card p-0">
        <div class="p-4 pb-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
          <div>
            <h5 class="mb-1">Borrower Loan Requests</h5>
            <p class="small text-muted mb-0">
              Only <strong>admin-approved</strong> requests are shown here. Click <strong>Make Offer</strong> to propose your amount, interest and tenure.
            </p>
          </div>
          <div class="d-flex gap-2">
            <a href="lender_offers.php" class="btn btn-outline-primary btn-sm">
              💼 My Offers
            </a>
          </div>
        </div>

        <?php if ($total_requests === 0): ?>
          <div class="empty-state">
            <p class="mb-1 fw-semibold">No approved borrower requests are available yet.</p>
            <p class="empty-hint mb-0">
              Once the admin approves borrower loan requests, they’ll appear here and you can begin making offers.
            </p>
          </div>
        <?php else: ?>
          <div class="lender-requests-table-wrapper">
            <table class="table align-middle lender-requests-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Borrower</th>
                  <th>Contact</th>
                  <th>Loan Required (₹)</th>
                  <th>Posted On</th>
                  <th>Offers</th>
                  <th>Status</th>
                  <th class="text-end">Action</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($requests as $index => $r): ?>
                <?php
                  // Derive simple status label & badge color based on offers
                  if ($r['accepted_offers'] > 0) {
                      $status_label = 'Offer accepted';
                      $status_class = 'badge-status accepted';
                  } elseif ($r['total_offers'] > 0) {
                      $status_label = 'Offers available';
                      $status_class = 'badge-status has-offers';
                  } else {
                      $status_label = 'No offers yet';
                      $status_class = 'badge-status no-offers';
                  }
                ?>
                <tr>
                  <td><?php echo $index + 1; ?></td>
                  <td>
                    <div class="fw-semibold">
                      <?php echo htmlspecialchars($r['name']); ?>
                    </div>
                    <div class="small text-muted">
                      <?php echo htmlspecialchars($r['email']); ?>
                    </div>
                    <div class="small text-success">
                      ✅ Approved by Admin
                    </div>
                  </td>
                  <td>
                    <div class="small text-muted">
                      📞 <?php echo htmlspecialchars($r['mobile']); ?>
                    </div>
                    <?php if (!empty($r['aadhar'])): ?>
                      <div class="small text-muted">
                        🪪 Aadhar: <?php echo htmlspecialchars($r['aadhar']); ?>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td class="fw-semibold text-primary">
                    ₹<?php echo number_format((float)$r['loanreq']); ?>
                  </td>
                  <td class="small text-muted">
                    <?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($r['created_at']))); ?>
                  </td>
                  <td>
                    <span class="badge bg-secondary-subtle text-dark small">
                      <?php echo $r['total_offers']; ?> offer(s)
                    </span>
                  </td>
                  <td>
                    <span class="<?php echo $status_class; ?>">
                      <?php echo $status_label; ?>
                    </span>
                  </td>
                  <td class="text-end">
                    <a href="make_offer.php?request_id=<?php echo (int)$r['id']; ?>"
                       class="btn btn-sm btn-primary">
                      💰 Make Offer
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </section>
    </div>
  </main>
</div>

<?php include 'footer.php'; ?>
</body>
</html>