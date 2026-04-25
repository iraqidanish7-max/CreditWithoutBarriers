<?php
// dashboard.php - Borrower dashboard (post loan request + view my requests)
session_start();

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: customerlogin.php');
    exit;
}

// Role guard: only borrowers allowed here
$role = $_SESSION['role'] ?? 'borrower';
if ($role !== 'borrower') {
    if ($role === 'lender') {
        header('Location: lender_dashboard.php');
    } elseif ($role === 'admin') {
        header('Location: admin_dashboard.php');
    } else {
        header('Location: customerlogin.php');
    }
    exit;
}

include __DIR__ . '/connection.php';

// user data from session
$user_id    = $_SESSION['user_id'];
$user_name  = $_SESSION['user_name']  ?? '';
$user_email = $_SESSION['user_email'] ?? '';

$messages = [];

// handle form submission for creating a loan request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_loan'])) {
    $name    = trim($_POST['name']   ?? $user_name);
    $aadhar  = trim($_POST['aadhar'] ?? '');
    $mobile  = trim($_POST['mobile'] ?? '');
    $email   = trim($_POST['email']  ?? $user_email);
    $loanreq = trim($_POST['loanreq'] ?? '');

    // basic validation
    if ($name === '' || $loanreq === '') {
        $messages[] = ['type' => 'danger', 'text' => 'Please enter your name and loan amount required.'];
    } else {
        // insert into customerdetails table (existing structure)
        $stmt = $conn->prepare("
            INSERT INTO customerdetails (name, aadhar, mobile, email, loanreq)
            VALUES (?, ?, ?, ?, ?)
        ");
        if ($stmt) {
            $stmt->bind_param('sssss', $name, $aadhar, $mobile, $email, $loanreq);
            if ($stmt->execute()) {
                $messages[] = ['type' => 'success', 'text' => 'Loan request posted successfully.'];
            } else {
                $messages[] = ['type' => 'danger', 'text' => 'Could not save request — please try again.'];
            }
            $stmt->close();
        } else {
            $messages[] = [
                'type' => 'danger',
                'text' => 'Database error: ' . htmlspecialchars($conn->error)
            ];
        }
    }
}

// fetch this borrower's loan requests + offer stats (by email)
$requests = [];
$total_requests      = 0;
$total_offers        = 0;
$total_accepted      = 0;
$last_request_date   = null;

if (!empty($user_email)) {
    $sql = "
        SELECT 
            c.id,
            c.name,
            c.aadhar,
            c.mobile,
            c.email,
            c.loanreq,
            c.created_at,
            COUNT(lo.id) AS total_offers,
            SUM(CASE WHEN lo.status = 'accepted' THEN 1 ELSE 0 END) AS accepted_offers
        FROM customerdetails c
        LEFT JOIN loan_offers lo 
            ON lo.request_id = c.id
           AND lo.admin_status = 'approved'  -- only admin-approved offers counted
        WHERE c.email = ?
        GROUP BY 
            c.id, c.name, c.aadhar, c.mobile, 
            c.email, c.loanreq, c.created_at
        ORDER BY c.created_at DESC
    ";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $user_email);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) {
            $r['total_offers']    = (int)($r['total_offers']    ?? 0);
            $r['accepted_offers'] = (int)($r['accepted_offers'] ?? 0);

            // derive a simple status label & CSS class (only cares about admin-approved offers now)
            if ($r['accepted_offers'] > 0) {
                $r['status_label'] = 'Offer accepted';
                $r['status_class'] = 'badge-status accepted';
            } elseif ($r['total_offers'] > 0) {
                $r['status_label'] = 'Offers available';
                $r['status_class'] = 'badge-status has-offers';
            } else {
                $r['status_label'] = 'No offers yet';
                $r['status_class'] = 'badge-status no-offers';
            }

            $requests[] = $r;
        }
        $stmt->close();
    }

    $total_requests = count($requests);
}
    // borrower-wide offer stats
    $offerStatsSql = "
        SELECT 
            COUNT(*) AS total_offers,
            SUM(CASE WHEN lo.status = 'accepted' THEN 1 ELSE 0 END) AS accepted_offers
        FROM loan_offers lo
        INNER JOIN customerdetails c ON lo.request_id = c.id
        WHERE c.email = ?
    ";
    if ($stmt = $conn->prepare($offerStatsSql)) {
        $stmt->bind_param('s', $user_email);
        $stmt->execute();
        $stmt->bind_result($total_offers, $total_accepted);
        $stmt->fetch();
        $stmt->close();
        $total_offers   = (int)($total_offers   ?? 0);
        $total_accepted = (int)($total_accepted ?? 0);
    }

    if ($total_requests > 0 && isset($requests[0]['created_at'])) {
        $last_request_date = $requests[0]['created_at'];
    }


?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Borrower Dashboard — Credit Without Barriers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="page-bg">
  <main class="container py-5 borrower-dashboard">

    <!-- Hero card: welcome + stats (light style like your lender pages) -->
    <section class="borrower-hero-card mb-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
          <p class="welcome-chip mb-1">Borrower Dashboard</p>
          <h2 class="mb-1 hero-heading">
            Welcome, <?php echo htmlspecialchars($user_name ?: $user_email); ?> 👋
          </h2>
          <p class="hero-subtitle mb-0">
            Post loan requests, track responses from lenders, and see which offers have been accepted.
          </p>
        </div>

        <div class="hero-stats">
          <div class="stat-card">
            <span class="stat-label">Loan Requests</span>
            <span class="stat-value"><?php echo $total_requests; ?></span>
          </div>
          <div class="stat-card">
            <span class="stat-label">Offers Received</span>
            <span class="stat-value"><?php echo (int)$total_offers; ?></span>
          </div>
          <div class="stat-card">
            <span class="stat-label">Offers Accepted</span>
            <span class="stat-value"><?php echo (int)$total_accepted; ?></span>
          </div>
        </div>
      </div>
    </section>

    <!-- Flash messages -->
    <?php foreach ($messages as $m): ?>
      <div class="alert alert-<?php echo $m['type']; ?> soft-alert">
        <?php echo htmlspecialchars($m['text']); ?>
      </div>
    <?php endforeach; ?>

    <div class="row g-4 mt-1">
      <!-- Left card: Post a loan request -->
      <div class="col-lg-5">
        <div class="gradient-card-wrapper h-100">
          <section class="inner-card borrower-form-card p-4 h-100">
            <h4 class="mb-2">Post a Loan Request</h4>
            <p class="small-muted mb-3">
              Share how much you need and why. Clear, honest details help lenders feel confident to make you better offers.
            </p>

            <form method="post" action="dashboard.php" class="row g-3">
              <input type="hidden" name="post_loan" value="1">

              <div class="col-12">
                <label class="form-label">Full name</label>
                <input class="form-control" name="name"
                       value="<?php echo htmlspecialchars($user_name); ?>"
                       placeholder="Your full name">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Aadhar (optional)</label>
                <input class="form-control" name="aadhar" placeholder="XXXX-XXXX-XXXX">
              </div>

              <div class="col-12 col-md-6">
                <label class="form-label">Mobile</label>
                <input class="form-control" name="mobile" placeholder="10-digit mobile number">
              </div>

              <div class="col-12">
                <label class="form-label">Email (login email)</label>
                <input class="form-control" name="email"
                       value="<?php echo htmlspecialchars($user_email); ?>"
                       readonly>
                <div class="form-text">
                  This is your login email and will be used to match offers from lenders.
                </div>
              </div>

              <div class="col-12">
                <label class="form-label">Loan amount required / notes</label>
                <input class="form-control" name="loanreq"
                       placeholder="e.g. 20000 for semester fees or small business">
              </div>

              <div class="col-12 d-flex justify-content-end">
                <button class="btn btn-primary">
                  Post Request
                </button>
              </div>
            </form>
          </section>
        </div>
      </div>

      <!-- Right card: My loan requests + table -->
      <div class="col-lg-7">
        <div class="gradient-card-wrapper h-100">
          <section class="inner-card borrower-requests-card h-100">
            <div class="p-4 pb-3 border-bottom">
              <h5 class="mb-1">My Loan Requests</h5>
              <p class="small-muted mb-0">
                See which requests have offers and jump into details to compare them.
              </p>
            </div>

            <?php if (count($requests) === 0): ?>
              <div class="p-4 text-center">
                <p class="mb-1 fw-semibold">You haven’t posted any loan requests yet.</p>
                <p class="small-muted mb-0">
                  Use the card on the left to post your first request and start receiving offers.
                </p>
              </div>
            <?php else: ?>
              <div class="borrower-requests-table-wrapper">
                <table class="table align-middle mb-0 borrower-requests-table">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Loan Request</th>
                      <th>Posted On</th>
                      <th>Offers</th>
                      <th>Status</th>
                      <th class="text-end">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($requests as $index => $r): ?>
                    <tr>
                      <td><?php echo $index + 1; ?></td>
                      <td>
                        <div class="fw-semibold">
                          <?php echo htmlspecialchars($r['loanreq']); ?>
                        </div>
                        <div class="small-muted">
                          <?php echo htmlspecialchars($r['name']); ?>
                          <?php if (!empty($r['mobile'])): ?>
                            &nbsp;•&nbsp;📞 <?php echo htmlspecialchars($r['mobile']); ?>
                          <?php endif; ?>
                        </div>
                      </td>
                      <td class="small-muted">
                        <?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($r['created_at']))); ?>
                      </td>
                      <td>
                        <span class="badge bg-secondary-subtle text-dark small">
                          <?php echo (int)$r['total_offers']; ?> offer(s)
                        </span>
                      </td>
                      <td>
                        <span class="<?php echo $r['status_class']; ?>">
                          <?php echo htmlspecialchars($r['status_label']); ?>
                        </span>
                      </td>
                      <td class="text-end">
                        <a href="view_offers.php?request_id=<?php echo (int)$r['id']; ?>"
                           class="btn btn-sm btn-outline-primary">
                          View Offers
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
      </div>
    </div>

  </main>
</div>

<?php include 'footer.php'; ?>
</body>
</html>