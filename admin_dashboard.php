<?php
// admin_dashboard.php - Admin panel: overview of users, requests, offers, user management, analytics

session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: adminlogin.php');
    exit;
}

// Only admin allowed
$role = $_SESSION['role'] ?? '';
if ($role !== 'admin') {
    if ($role === 'borrower') {
        header('Location: dashboard.php');
    } elseif ($role === 'lender') {
        header('Location: lender_dashboard.php');
    } else {
        header('Location: customerlogin.php');
    }
    exit;
}

include __DIR__ . '/connection.php';

// 1. Summary stats
$counts = [
    'borrowers'       => 0,
    'lenders'         => 0,
    'admins'          => 0,
    'requests'        => 0,
    'offers'          => 0,
    'accepted_offers' => 0
];

$res = $conn->query("SELECT role, COUNT(*) AS c FROM users GROUP BY role");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        if ($row['role'] === 'borrower') $counts['borrowers'] = (int)$row['c'];
        if ($row['role'] === 'lender')   $counts['lenders']   = (int)$row['c'];
        if ($row['role'] === 'admin')    $counts['admins']    = (int)$row['c'];
    }
}

// total loan requests
$res = $conn->query("SELECT COUNT(*) AS c FROM customerdetails");
if ($res) {
    $row = $res->fetch_assoc();
    $counts['requests'] = (int)$row['c'];
}

// total offers
$res = $conn->query("SELECT COUNT(*) AS c FROM loan_offers");
if ($res) {
    $row = $res->fetch_assoc();
    $counts['offers'] = (int)$row['c'];
}

// accepted offers
$res = $conn->query("SELECT COUNT(*) AS c FROM loan_offers WHERE status = 'accepted'");
if ($res) {
    $row = $res->fetch_assoc();
    $counts['accepted_offers'] = (int)$row['c'];
}

// 2. Fetch all loan requests (including status)
$requests = [];
$res = $conn->query("
    SELECT id, name, email, mobile, aadhar, loanreq, status, created_at
    FROM customerdetails
    ORDER BY created_at DESC
");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $requests[] = $row;
    }
}

// 3. Fetch all loan offers joined with borrower info (including admin_status)
$offers = [];
$res = $conn->query("
    SELECT 
        o.id,
        o.request_id,
        o.lender_name,
        o.lender_email,
        o.offer_amount,
        o.interest_rate,
        o.tenure_months,
        o.status,
        o.admin_status,
        o.created_at,
        c.name    AS borrower_name,
        c.email   AS borrower_email,
        c.loanreq AS borrower_loanreq
    FROM loan_offers o
    INNER JOIN customerdetails c ON o.request_id = c.id
    ORDER BY o.created_at DESC
");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $offers[] = $row;
    }
}

// 4. Fetch all users for management
$users = [];
$res = $conn->query("
    SELECT id, name, email, role, is_active, created_at
    FROM users
    ORDER BY created_at DESC
");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $users[] = $row;
    }
}

// 5. Analytics datasets

// 5A. Role distribution (for pie chart)
$roleChart = [
    'labels' => ['Borrowers', 'Lenders', 'Admins'],
    'data'   => [
        (int)$counts['borrowers'],
        (int)$counts['lenders'],
        (int)$counts['admins']
    ]
];

// 5B. Loan requests per month (last 6 months)
$requestsPerMonth = [];
$res = $conn->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') AS ym,
        DATE_FORMAT(created_at, '%b %Y') AS label,
        COUNT(*) AS c
    FROM customerdetails
    GROUP BY ym, label
    ORDER BY ym DESC
    LIMIT 6
");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $requestsPerMonth[] = [
            'label' => $row['label'],
            'count' => (int)$row['c']
        ];
    }
    // reverse so oldest month first
    $requestsPerMonth = array_reverse($requestsPerMonth);
}

// 5C. Offers by status
$offerStatusCounts = [
    'pending'   => 0,
    'accepted'  => 0,
    'rejected'  => 0,
    'withdrawn' => 0
];
$res = $conn->query("SELECT status, COUNT(*) AS c FROM loan_offers GROUP BY status");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $key = strtolower($row['status'] ?? '');
        if (isset($offerStatusCounts[$key])) {
            $offerStatusCounts[$key] = (int)$row['c'];
        }
    }
}

// Simple flash message handling from all admin_update_* scripts
$flash_msg  = isset($_GET['msg'])  ? trim($_GET['msg'])  : '';
$flash_type = isset($_GET['type']) ? trim($_GET['type']) : '';
$allowed_types = ['success', 'danger', 'warning', 'info'];
if (!in_array($flash_type, $allowed_types, true)) {
    $flash_type = 'info';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Credit Without Barriers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include 'header.php'; ?>
<div class="page-bg">
  <main class="container py-5 admin-dashboard-page">

    <!-- Optional flash message -->
    <?php if ($flash_msg !== ''): ?>
      <div class="alert alert-<?php echo htmlspecialchars($flash_type); ?> alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($flash_msg); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <!-- Hero card: System overview + stats -->
    <section class="admin-hero-card mb-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
          <p class="welcome-chip mb-1">Admin Panel</p>
          <h2 class="mb-1 hero-heading">
            System Overview
          </h2>
          <p class="hero-subtitle mb-0">
            Monitor users, loan requests, offers, and platform health at a glance.
          </p>
        </div>
        <div class="text-md-end admin-hero-meta">
          <div>Logged in as:</div>
          <div class="fw-semibold">
            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>
          </div>
        </div>
      </div>

      <div class="hero-stats mt-3">
        <div class="stat-card">
          <span class="stat-label">Borrowers</span>
          <span class="stat-value"><?php echo (int)$counts['borrowers']; ?></span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Lenders</span>
          <span class="stat-value"><?php echo (int)$counts['lenders']; ?></span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Loan Requests</span>
          <span class="stat-value"><?php echo (int)$counts['requests']; ?></span>
        </div>
        <div class="stat-card">
          <span class="stat-label">Offers (Accepted)</span>
          <span class="stat-value">
            <?php echo (int)$counts['accepted_offers']; ?>
            / <?php echo (int)$counts['offers']; ?>
          </span>
        </div>
      </div>
    </section>

    <!-- Analytics charts section -->
    <section class="card p-4 admin-card mb-4">
      <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-3">
        <div>
          <h4 class="mb-1">Platform Analytics</h4>
          <p class="text-muted mb-0 small">
            Visual overview of users, loan requests, and offer activity.
          </p>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-md-4">
          <h6 class="small text-muted mb-2">User Roles</h6>
          <canvas id="rolesChart" height="200"></canvas>
        </div>
        <div class="col-md-4">
          <h6 class="small text-muted mb-2">Loan Requests per Month</h6>
          <canvas id="requestsChart" height="200"></canvas>
        </div>
        <div class="col-md-4">
          <h6 class="small text-muted mb-2">Offers by Status</h6>
          <canvas id="offersStatusChart" height="200"></canvas>
        </div>
      </div>
    </section>

    <!-- Loan Requests table (spread-wide) -->
    <section class="card p-4 admin-card admin-requests-card mb-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
        <div>
          <h4 class="mb-1">All Loan Requests</h4>
          <p class="text-muted mb-0 small">
            Every request submitted by borrowers on the platform.
          </p>
        </div>
      </div>

      <?php if (empty($requests)): ?>
        <div class="empty-state mt-2">
          <p>No loan requests found.</p>
        </div>
      <?php else: ?>
        <div class="table-responsive admin-table-wrapper mt-2">
          <table class="table align-middle admin-table">
            <thead>
              <tr>
                <th>#ID</th>
                <th>Borrower</th>
                <th>Contact</th>
                <th>Loan Request (₹)</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
                <th>Requested At</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($requests as $r): ?>
              <?php
                $req_status = strtolower($r['status'] ?? 'pending');
                $status_badge_class = 'badge bg-warning text-dark';
                $status_label = 'PENDING';
                if ($req_status === 'approved') {
                    $status_badge_class = 'badge bg-success';
                    $status_label = 'APPROVED';
                } elseif ($req_status === 'rejected') {
                    $status_badge_class = 'badge bg-danger';
                    $status_label = 'REJECTED';
                }
              ?>
              <tr>
                <td><?php echo (int)$r['id']; ?></td>
                <td>
                  <div class="fw-semibold">
                    <?php echo htmlspecialchars($r['name']); ?>
                  </div>
                  <div class="small text-muted">
                    <?php echo htmlspecialchars($r['email']); ?>
                  </div>
                </td>
                <td>
                  <div class="small text-muted">
                    📞 <?php echo htmlspecialchars($r['mobile']); ?>
                  </div>
                  <?php if (!empty($r['aadhar'])): ?>
                    <div class="small text-muted">
                      🪪 <?php echo htmlspecialchars($r['aadhar']); ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td>
                  ₹<?php echo htmlspecialchars($r['loanreq']); ?>
                </td>
                <td>
                  <span class="<?php echo $status_badge_class; ?>">
                    <?php echo $status_label; ?>
                  </span>
                </td>
                <td class="text-end">
                  <?php if ($req_status === 'pending'): ?>
                    <form method="post" action="admin_update_request_status.php" style="display:inline-block;">
                      <input type="hidden" name="request_id" value="<?php echo (int)$r['id']; ?>">
                      <input type="hidden" name="action" value="approve">
                      <button type="submit"
                              class="btn btn-sm btn-success"
                              onclick="return confirm('Approve this loan request?');">
                        Approve
                      </button>
                    </form>

                    <form method="post" action="admin_update_request_status.php" style="display:inline-block; margin-left:4px;">
                      <input type="hidden" name="request_id" value="<?php echo (int)$r['id']; ?>">
                      <input type="hidden" name="action" value="reject">
                      <button type="submit"
                              class="btn btn-sm btn-danger"
                              onclick="return confirm('Reject this loan request?');">
                        Reject
                      </button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted small">No actions</span>
                  <?php endif; ?>
                </td>
                <td class="small text-muted">
                  <?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($r['created_at']))); ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <!-- Offers table (spread-wide) -->
    <section class="card p-4 admin-card admin-offers-card mb-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
    <div>
      <h4 class="mb-1">All Offers (Borrowers & Lenders)</h4>
      <p class="text-muted mb-0 small">
        Complete list of offers with borrower, lender details and admin review.
      </p>
    </div>
    <div>
      <a href="export_accepted_offers_csv.php" class="btn btn-primary w-100 mt-2">
        ⬇ Download Accepted Offers CSV
      </a>
    </div>
  </div>
      <?php if (empty($offers)): ?>
        <div class="empty-state mt-2">
          <p>No offers found.</p>
        </div>
      <?php else: ?>
        <div class="table-responsive admin-table-wrapper mt-2">
          <table class="table align-middle admin-table">
            <thead>
              <tr>
                <th>#Offer</th>
                <th>Req ID</th>
                <th>Borrower</th>
                <th>Lender</th>
                <th>Amount (₹)</th>
                <th>Rate (%)</th>
                <th>Tenure</th>
                <th>Offer Status</th>
                <th>Admin Review</th>
                <th class="text-nowrap">Created</th>
                <th class="text-end text-nowrap">Admin Action</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($offers as $o): ?>
              <?php
                // Offer lifecycle status (from loan_offers.status)
                $status_raw = strtolower($o['status'] ?? '');
                $status_label = strtoupper($status_raw ?: 'PENDING');
                $badge_class = 'badge bg-warning text-dark';
                if ($status_raw === 'accepted') {
                    $badge_class = 'badge bg-success';
                } elseif ($status_raw === 'rejected') {
                    $badge_class = 'badge bg-danger';
                } elseif ($status_raw === 'withdrawn') {
                    $badge_class = 'badge bg-secondary';
                }

                // Admin review status (from loan_offers.admin_status)
                $admin_status_raw = strtolower($o['admin_status'] ?? 'pending');
                $admin_label = 'PENDING';
                $admin_badge_class = 'badge bg-warning text-dark';
                if ($admin_status_raw === 'approved') {
                    $admin_badge_class = 'badge bg-success';
                    $admin_label = 'APPROVED';
                } elseif ($admin_status_raw === 'rejected') {
                    $admin_badge_class = 'badge bg-danger';
                    $admin_label = 'REJECTED';
                }
              ?>
              <tr>
                <td><?php echo (int)$o['id']; ?></td>
                <td><?php echo (int)$o['request_id']; ?></td>
                <td>
                  <div class="fw-semibold">
                    <?php echo htmlspecialchars($o['borrower_name']); ?>
                  </div>
                  <div class="small text-muted">
                    <?php echo htmlspecialchars($o['borrower_email']); ?>
                  </div>
                </td>
                <td>
                  <div class="fw-semibold">
                    <?php echo htmlspecialchars($o['lender_name']); ?>
                  </div>
                  <div class="small text-muted">
                    <?php echo htmlspecialchars($o['lender_email']); ?>
                  </div>
                </td>
                <td>
                  ₹<?php echo number_format((float)$o['offer_amount'], 2); ?>
                </td>
                <td><?php echo htmlspecialchars($o['interest_rate']); ?></td>
                <td><?php echo (int)$o['tenure_months']; ?></td>
                <td>
                  <span class="<?php echo $badge_class; ?>">
                    <?php echo $status_label; ?>
                  </span>
                </td>
                <td>
                  <span class="<?php echo $admin_badge_class; ?>">
                    <?php echo $admin_label; ?>
                  </span>
                </td>
                <td class="small text-muted text-nowrap">
                  <?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($o['created_at']))); ?>
                </td>
                <td class="text-end text-nowrap">
                  <?php if ($admin_status_raw === 'pending'): ?>
                    <div class="btn-group btn-group-sm" role="group">
                      <form method="post" action="admin_update_offer_status.php" style="display:inline;">
                        <input type="hidden" name="offer_id" value="<?php echo (int)$o['id']; ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit"
                                class="btn btn-success"
                                onclick="return confirm('Approve this offer?');">
                          Approve
                        </button>
                      </form>
                      <form method="post" action="admin_update_offer_status.php" style="display:inline;">
                        <input type="hidden" name="offer_id" value="<?php echo (int)$o['id']; ?>">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit"
                                class="btn btn-danger"
                                onclick="return confirm('Reject this offer?');">
                          Reject
                        </button>
                      </form>
                    </div>
                  <?php else: ?>
                    <span class="text-muted small">No actions</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>

    <!-- User Management table (spread-wide via CSS .admin-users-card) -->
    <section class="card p-4 admin-card admin-users-card mb-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
        <div>
          <h4 class="mb-1">User Management</h4>
          <p class="text-muted mb-0 small">
            View all registered users and enable/disable accounts when needed.
          </p>
        </div>
      </div>

      <?php if (empty($users)): ?>
        <div class="empty-state mt-2">
          <p>No users found.</p>
        </div>
      <?php else: ?>
        <div class="table-responsive admin-table-wrapper mt-2">
          <table class="table align-middle admin-table">
            <thead>
              <tr>
                <th>#User</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th class="text-nowrap">Joined</th>
                <th class="text-end text-nowrap">Action</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $u): ?>
              <?php
                $is_active = (int)($u['is_active'] ?? 1);
                $status_label = $is_active ? 'Active' : 'Disabled';
                $status_class = $is_active ? 'badge bg-success' : 'badge bg-secondary';
                $is_self = ((int)$u['id'] === (int)($_SESSION['user_id'] ?? 0));
              ?>
              <tr>
                <td><?php echo (int)$u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['name'] ?: '-'); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td class="text-capitalize"><?php echo htmlspecialchars($u['role']); ?></td>
                <td>
                  <span class="<?php echo $status_class; ?>">
                    <?php echo $status_label; ?>
                  </span>
                </td>
                <td class="small text-muted text-nowrap">
                  <?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($u['created_at']))); ?>
                </td>
                <td class="text-end text-nowrap">
                  <?php if ($is_self): ?>
                    <span class="text-muted small">You</span>
                  <?php else: ?>
                    <?php if ($is_active): ?>
                      <form method="post" action="admin_update_user_status.php" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                        <input type="hidden" name="action" value="deactivate">
                        <button type="submit"
                                class="btn btn-sm btn-outline-danger"
                                onclick="return confirm('Disable this user account?');">
                          Disable
                        </button>
                      </form>
                    <?php else: ?>
                      <form method="post" action="admin_update_user_status.php" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
                        <input type="hidden" name="action" value="activate">
                        <button type="submit"
                                class="btn btn-sm btn-outline-success"
                                onclick="return confirm('Enable this user account?');">
                          Enable
                        </button>
                      </form>
                    <?php endif; ?>
                  <?php endif; ?>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ======== Chart.js Analytics Setup ========

// Data from PHP
const roleChartData = <?php echo json_encode($roleChart); ?>;
const requestsPerMonthData = <?php echo json_encode($requestsPerMonth); ?>;
const offerStatusCounts = <?php echo json_encode($offerStatusCounts); ?>;

// 1) Roles Pie Chart
const rolesCtx = document.getElementById('rolesChart')?.getContext('2d');
if (rolesCtx) {
  new Chart(rolesCtx, {
    type: 'pie',
    data: {
      labels: roleChartData.labels,
      datasets: [{
        data: roleChartData.data
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' },
        title: { display: false }
      }
    }
  });
}

// 2) Loan Requests per Month (Bar)
const reqLabels = requestsPerMonthData.map(item => item.label);
const reqCounts = requestsPerMonthData.map(item => item.count);
const requestsCtx = document.getElementById('requestsChart')?.getContext('2d');
if (requestsCtx) {
  new Chart(requestsCtx, {
    type: 'bar',
    data: {
      labels: reqLabels,
      datasets: [{
        label: 'Requests',
        data: reqCounts
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: { precision: 0 }
        }
      },
      plugins: {
        legend: { display: false }
      }
    }
  });
}

// 3) Offers by Status (Bar)
const offersCtx = document.getElementById('offersStatusChart')?.getContext('2d');
if (offersCtx) {
  const offerLabels = ['Pending', 'Accepted', 'Rejected', 'Withdrawn'];
  const offerData = [
    offerStatusCounts.pending || 0,
    offerStatusCounts.accepted || 0,
    offerStatusCounts.rejected || 0,
    offerStatusCounts.withdrawn || 0
  ];
  new Chart(offersCtx, {
    type: 'bar',
    data: {
      labels: offerLabels,
      datasets: [{
        label: 'Offers',
        data: offerData
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: { precision: 0 }
        }
      },
      plugins: {
        legend: { display: false }
      }
    }
  });
}
</script>

</body>
</html>