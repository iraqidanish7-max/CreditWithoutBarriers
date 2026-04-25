<?php
// view_offers.php - borrower view of lender offers for one loan request

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: customerlogin.php');
    exit;
}

include __DIR__ . '/connection.php';

$user_email = $_SESSION['user_email'] ?? '';
$user_name  = $_SESSION['user_name']  ?? '';
if ($user_email === '') {
    die('No email in session. Please log in again.');
}

// optional success/error message from redirect
$flash_message = '';
$flash_type = 'success';
if (!empty($_GET['msg'])) {
    $flash_message = $_GET['msg'];
    $flash_type = $_GET['type'] ?? 'success';
}

// 1. Get request_id from URL
$request_id = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 0;
if ($request_id <= 0) {
    die('Invalid loan request ID.');
}

// 2. Fetch this loan request and verify it belongs to logged-in user
$stmt = $conn->prepare("
    SELECT id, name, aadhar, mobile, email, loanreq, created_at
    FROM customerdetails
    WHERE id = ? AND email = ?
");
if (!$stmt) {
    die('Database error: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param('is', $request_id, $user_email);
$stmt->execute();
$res = $stmt->get_result();
$request = $res->fetch_assoc();
$stmt->close();

if (!$request) {
    // Either request does not exist or does not belong to this user
    die('Loan request not found or you are not allowed to view it.');
}

// 3. Fetch all offers for this request
$offers = [];
$stmt = $conn->prepare("
    SELECT id, lender_name, lender_email, offer_amount, interest_rate, tenure_months, status,
    admin_status, created_at
    FROM loan_offers
    WHERE request_id = ? AND admin_status='approved'
    ORDER BY created_at DESC
");
if (!$stmt) {
    die('Database error: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param('i', $request_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $offers[] = $row;
}
$stmt->close();

// 4. Simple stats
$total_offers   = count($offers);
$pending_count  = count(array_filter($offers, fn($o) => $o['status'] === 'pending'));
$accepted_count = count(array_filter($offers, fn($o) => $o['status'] === 'accepted'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Offers - Credit Without Barriers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap + global styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
  .otp-modal {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.75);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
  }

  .otp-modal-content {
    background: rgba(10, 10, 30, 0.97);
    padding: 20px 24px;
    border-radius: 16px;
    max-width: 400px;
    width: 90%;
    color: #fff;
    box-shadow: 0 0 25px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.15);
  }

  .otp-message {
    font-size: 0.9rem;
    color: #ccc;
  }

  .otp-error {
    font-size: 0.85rem;
    color: #ff6b6b;
  }
</style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="page-bg">
  <main class="container py-5 borrower-offers-page">

    <!-- Hero card (same family as borrower dashboard hero) -->
    <section class="borrower-offers-hero-card mb-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
          <p class="welcome-chip mb-1">Loan Offers</p>
          <h2 class="mb-1 hero-heading">
            Offers for your request 👋
          </h2>
          <p class="hero-subtitle mb-1">
            Borrower: <strong><?php echo htmlspecialchars($request['name'] ?: $user_name ?: $user_email); ?></strong><br>
            Loan requested: <strong>₹<?php echo number_format((float)$request['loanreq']); ?></strong>
          </p>
          <p class="hero-subtitle mb-0">
            Posted on <?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($request['created_at']))); ?>
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

    <!-- Flash message (from accept_offer redirect) -->
    <?php if ($flash_message): ?>
      <div class="alert alert-<?php echo htmlspecialchars($flash_type); ?> soft-alert">
          <?php echo htmlspecialchars($flash_message); ?>
      </div>
    <?php endif; ?>

    <!-- Main offers card -->
    <div class="gradient-card-wrapper mb-5">
  <section class="inner-card borrower-offers-card p-0">
      <div class="p-4 pb-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
        <div>
          <h5 class="mb-1">Offers You’ve Received</h5>
          <p class="small-muted mb-0">
            Compare amount, interest rate and tenure before accepting one offer. Only one offer can be accepted per request.
          </p>
          <p class="small text-muted mb-0">
            Request #<?php echo (int)$request['id']; ?> • 
            ₹<?php echo number_format((float)$request['loanreq']); ?> • 
            <?php echo htmlspecialchars($request['mobile']); ?> • 
            <?php echo htmlspecialchars($request['email']); ?>
          </p>
        </div>
        <div>
          <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
            ← Back to Dashboard
          </a>
        </div>
      </div>

      <?php if (empty($offers)): ?>
        <div class="p-4 text-center">
          <p class="mb-1 fw-semibold">No offers yet for this request.</p>
          <p class="small-muted mb-0">
            Lenders are still reviewing or Lenders may have submitted offers that are still 
            under admi review. You can check back later from your dashboard.
          </p>
          
        </div>
      <?php else: ?>
        <div class="borrower-offers-table-wrapper">
          <table class="table table-hover align-middle mb-0 borrower-offers-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Lender</th>
                <th>Offer Amount (₹)</th>
                <th>Interest Rate (%)</th>
                <th>Tenure (months)</th>
                <th>Offered On</th>
                <th>Status</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($offers as $index => $o): ?>
              <?php
                $status_raw   = strtolower($o['status'] ?? '');
                $status_label = ucfirst($status_raw ?: 'pending');
                $status_class = 'badge-status no-offers';

                if ($status_raw === 'pending') {
                    $status_class = 'badge-status has-offers';
                } elseif ($status_raw === 'accepted') {
                    $status_class = 'badge-status accepted';
                } elseif ($status_raw === 'rejected' || $status_raw === 'withdrawn') {
                    $status_class = 'badge-status no-offers';
                }
              ?>
              <tr>
                <td><?php echo $index + 1; ?></td>
                <td>
                  <div class="fw-semibold">
                    <?php echo htmlspecialchars($o['lender_name'] ?: $o['lender_email']); ?>
                  </div>
                  <div class="small text-muted">
                    <?php echo htmlspecialchars($o['lender_email']); ?>
                  </div>
                </td>
                <td class="fw-semibold text-primary">
                  ₹<?php echo number_format((float)$o['offer_amount'], 2); ?>
                </td>
                <td><?php echo htmlspecialchars($o['interest_rate']); ?></td>
                <td><?php echo (int)$o['tenure_months']; ?></td>
                <td class="small text-muted">
                  <?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($o['created_at']))); ?>
                </td>
                <td>
                  <span class="<?php echo $status_class; ?>">
                    <?php echo htmlspecialchars(strtoupper($status_label)); ?>
                  </span>
                </td>
                <td class="text-end">
                  <?php if ($status_raw === 'pending'): ?>
                    <button type="button"class="btn btn-sm btn-success btn-accept-offer"
                    data-offer-id="<?php echo (int)$o['id']; ?>
                    "data-request-id="<?php echo (int)$request['id']; ?>">Accept</button>
                     <?php elseif ($status_raw === 'accepted'): ?>
                      <span class="text-success fw-bold me-2">Accepted</span>
                     <a href="agreement.php?offer_id=<?php echo (int)$o['id']; ?>"
                            class="btn btn-primary w-100 mt-2">
                           Download Agreement
                              </a>
                            <?php else: ?>
                    <span class="text-muted small">No action</span>
                  <?php endif; ?>
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
<!-- OTP Modal -->
<div id="otpModal" class="otp-modal" style="display:none;">
  <div class="otp-modal-content">
    <h3>Confirm Offer Acceptance</h3>
    <p class="otp-message">
      We have sent a 6-digit OTP for this offer. Please enter it below to confirm acceptance.
    </p>

    <form id="otpForm" method="post" action="verify_otp.php">
      <!-- This will be filled by JS when user clicks Accept -->
      <input type="hidden" name="offer_id" id="otp_offer_id">
      <input type="hidden" name="request_id" id="otp_request_id">
      <div class="mb-3">
        <input 
          type="text" 
          name="otp_code" 
          id="otp_code" 
          maxlength="6" 
          required 
          placeholder="Enter 6-digit OTP"
          class="form-control text-center"
        >
      </div>

      <div class="d-flex justify-content-between mt-2">
        <button type="submit" class="btn btn-primary">
          Verify OTP
        </button>
        <button type="button" class="btn btn-secondary" id="otpCancelBtn">
          Cancel
        </button>
      </div>

      <p id="otpError" class="otp-error mt-2"></p>
    </form>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const acceptButtons = document.querySelectorAll('.btn-accept-offer');
    const otpModal      = document.getElementById('otpModal');
    const otpOfferId    = document.getElementById('otp_offer_id');
    const otpRequestId  = document.getElementById('otp_request_id');
    const otpError      = document.getElementById('otpError');
    const otpInput      = document.getElementById('otp_code');
    const otpCancelBtn  = document.getElementById('otpCancelBtn');

    if (!otpModal || !otpOfferId ||!otpRequestId || !otpError || !otpInput) {
        console.warn('OTP modal elements not found on this page.');
        return;
    }

    function openModal() {
        otpModal.style.display = 'flex';
    }

    function closeModal() {
        otpModal.style.display = 'none';
        otpError.textContent = '';
        otpInput.value = '';
    }

    if (otpCancelBtn) {
        otpCancelBtn.addEventListener('click', function () {
            closeModal();
        });
    }

    // Close modal when clicking outside the box
    otpModal.addEventListener('click', function (e) {
        if (e.target === otpModal) {
            closeModal();
        }
    });

    // When user clicks green Accept button
    acceptButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
        const offerId   = this.getAttribute('data-offer-id');
        const requestId = this.getAttribute('data-request-id');

        if (!offerId) {
            console.error('No offer-id found on button.');
            return;
        }

        // Fill hidden inputs so verify_otp.php knows which offer & request
        otpOfferId.value   = offerId;
        otpRequestId.value = requestId || '';
            // Show a temporary message
            otpError.style.color = '#cccccc';
            otpError.textContent = 'Sending OTP, please wait...';

            // Call send_otp.php via AJAX
            fetch('send_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'offer_id=' + encodeURIComponent(offerId)
            })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                // Always open modal so user sees feedback
                openModal();

                if (data.success) {
                    otpError.style.color = '#00ffa0';
                    // For now, show OTP in message for testing
                    if (data.otp) {
                        otpError.textContent = 'OTP sent successfully.' +
                            ' ( Your OTP is: ' + data.otp + ')';
                    } else {
                        otpError.textContent = data.message || 'OTP sent successfully. Check your email.';
                    }
                } else {
                    otpError.style.color = '#ff6b6b';
                    if (data.otp) {
                        otpError.textContent = (data.message || 'Failed to send OTP.') +
                            ' (Your OTP is: ' + data.otp + ')';
                    } else {
                        otpError.textContent = data.message || 'Failed to send OTP.';
                    }
                }
            })
            .catch(function (error) {
                console.error('Error sending OTP:', error);
                openModal();
                otpError.style.color = '#ff6b6b';
                otpError.textContent = 'Something went wrong while sending OTP. Please try again.';
            });
        });
    });
});
</script>
<?php include 'footer.php'; ?>
</body>
</html>