<?php
// make_offer.php - create an offer for a specific loan request

session_start();
include __DIR__ . '/connection.php';

// 1. Get and validate request_id from URL
$request_id = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 0;
if ($request_id <= 0) {
    die('Invalid loan request ID.');
}

// 2. Fetch the loan request from customerdetails
$stmt = $conn->prepare("
    SELECT id, name, aadhar, mobile, email, loanreq, created_at
    FROM customerdetails
    WHERE id = ?
");
if (!$stmt) {
    die('Database error: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param('i', $request_id);
$stmt->execute();
$result  = $stmt->get_result();
$request = $result->fetch_assoc();
$stmt->close();

if (!$request) {
    die('Loan request not found.');
}

$borrower_email = $request['email'] ?? '';
$messages       = [];

// 3. Handle form submission (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_offer'])) {
    $lender_name   = trim($_POST['lender_name'] ?? '');
    $lender_email  = trim($_POST['lender_email'] ?? '');
    $offer_amount  = trim($_POST['offer_amount'] ?? '');
    $interest_rate = trim($_POST['interest_rate'] ?? '');
    $tenure_months = trim($_POST['tenure_months'] ?? '');

    // basic validation
    if ($lender_name === '' || $lender_email === '' ||
        $offer_amount === '' || $interest_rate === '' || $tenure_months === '') {
        $messages[] = ['type' => 'danger', 'text' => 'Please fill all fields.'];
    } elseif (!is_numeric($offer_amount) || !is_numeric($interest_rate) || !ctype_digit($tenure_months)) {
        $messages[] = ['type' => 'danger', 'text' => 'Amount, interest, and tenure must be numeric values.'];
    } elseif ($borrower_email !== '' && strcasecmp($borrower_email, $lender_email) === 0) {
        // SELF-LENDING BLOCK: same email as borrower
        $messages[] = [
            'type' => 'danger',
            'text' => 'You cannot make an offer on your own loan request (same email detected).'
        ];
    } else {
        // convert numeric fields
        $offer_amount_val  = (float)$offer_amount;
        $interest_rate_val = (float)$interest_rate;
        $tenure_months_val = (int)$tenure_months;

        // 3.1 Check if this lender already has an offer on this request (pending/accepted)
        $checkStmt = $conn->prepare("
            SELECT id, status 
            FROM loan_offers 
            WHERE request_id = ? 
              AND lender_email = ?
            LIMIT 1
        ");
        if ($checkStmt) {
            $checkStmt->bind_param('is', $request_id, $lender_email);
            $checkStmt->execute();
            $checkRes      = $checkStmt->get_result();
            $existingOffer = $checkRes->fetch_assoc();
            $checkStmt->close();
        } else {
            $existingOffer = null;
        }

        if ($existingOffer && in_array($existingOffer['status'], ['pending','accepted'], true)) {
            // Prevent duplicate active offer
            $messages[] = [
                'type' => 'warning',
                'text' => 'You already have an active offer (ID #'
                          . (int)$existingOffer['id']
                          . ') on this request. You cannot create another while it is '
                          . htmlspecialchars($existingOffer['status']) . '.'
            ];
        } else {
            // 3.2 Insert into loan_offers table (either no offer, or only rejected/withdrawn exists)
            $stmt = $conn->prepare("
                INSERT INTO loan_offers 
                    (request_id, lender_name, lender_email, offer_amount, interest_rate, tenure_months)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            if ($stmt) {
                $stmt->bind_param(
                    'issddi',
                    $request_id,
                    $lender_name,
                    $lender_email,
                    $offer_amount_val,
                    $interest_rate_val,
                    $tenure_months_val
                );
                if ($stmt->execute()) {
                    $messages[] = ['type' => 'success', 'text' => 'Offer saved successfully!'];
                } else {
                    $messages[] = ['type' => 'danger', 'text' => 'Could not save offer. Please try again.'];
                }
                $stmt->close();
            } else {
                $messages[] = ['type' => 'danger', 'text' => 'Database error: ' . htmlspecialchars($conn->error)];
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Make Offer - Lender Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'header.php'; ?>
<div class="page-bg">
  <main class="container py-5 make-offer-page">

    <!-- Hero card -->
    <section class="make-offer-hero-card mb-4">
      <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
          <p class="welcome-chip mb-1">Make Offer</p>
          <h2 class="mb-1 hero-heading">
            Offer for Request #<?php echo (int)$request['id']; ?>
          </h2>
          <p class="hero-subtitle mb-1">
            Borrower: <strong><?php echo htmlspecialchars($request['name']); ?></strong><br>
            Loan requested: <strong>₹<?php echo htmlspecialchars($request['loanreq']); ?></strong>
          </p>
          <p class="hero-subtitle mb-0">
            Posted on <?php echo htmlspecialchars(date('d M Y, h:i A', strtotime($request['created_at']))); ?>
          </p>
        </div>
        <div class="d-flex gap-2">
          <a href="lender_dashboard.php" class="btn btn-outline-primary btn-sm">
            ← Back to Lender Dashboard
          </a>
        </div>
      </div>
    </section>
    <!-- Messages -->
    <?php foreach ($messages as $msg): ?>
      <div class="alert alert-<?php echo $msg['type']; ?>">
        <?php echo htmlspecialchars($msg['text']); ?>
      </div>
    <?php endforeach; ?>

    <!-- Main card: borrower details + offer form -->
    <section class="card p-4 make-offer-card">
      <div class="row g-4">
        <!-- Borrower request details -->
        <div class="col-lg-5">
          <h5 class="mb-3">Borrower Request Details</h5>
          <ul class="list-unstyled make-offer-borrower">
            <li><span class="label">Name</span><span class="value"><?php echo htmlspecialchars($request['name']); ?></span></li>
            <li><span class="label">Aadhar</span><span class="value"><?php echo htmlspecialchars($request['aadhar']); ?></span></li>
            <li><span class="label">Mobile</span><span class="value"><?php echo htmlspecialchars($request['mobile']); ?></span></li>
            <li><span class="label">Email</span><span class="value"><?php echo htmlspecialchars($request['email']); ?></span></li>
            <li><span class="label">Requested Amount</span><span class="value">₹<?php echo htmlspecialchars($request['loanreq']); ?></span></li>
            <li><span class="label">Requested At</span><span class="value"><?php echo htmlspecialchars($request['created_at']); ?></span></li>
          </ul>
          <p class="small text-muted mt-2">
            Review the borrower’s request details carefully before finalizing your offer.
          </p>
        </div>

        <!-- Lender offer form -->
        <div class="col-lg-7">
          <h5 class="mb-3">Your Offer</h5>
          <form method="post" class="make-offer-form">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Lender Name</label>
                <input type="text" name="lender_name" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['lender_name'] ?? ($_SESSION['user_name'] ?? '')); ?>" required>
              </div>

              <div class="col-md-6">
                <label class="form-label">Lender Email</label>
                <input type="email" name="lender_email" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['lender_email'] ?? ($_SESSION['user_email'] ?? '')); ?>" required>
              </div>

              <div class="col-md-4">
                <label class="form-label">Offer Amount (₹)</label>
                <input type="number" step="0.01" name="offer_amount" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['offer_amount'] ?? ''); ?>" required>
              </div>

              <div class="col-md-4">
                <label class="form-label">Interest Rate (%)</label>
                <input type="number" step="0.01" name="interest_rate" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['interest_rate'] ?? ''); ?>" required>
              </div>

              <div class="col-md-4">
                <label class="form-label">Tenure (months)</label>
                <input type="number" name="tenure_months" class="form-control"
                       value="<?php echo htmlspecialchars($_POST['tenure_months'] ?? ''); ?>" required>
              </div>

              <div class="col-12 mt-2">
                <button type="submit" name="save_offer" class="btn btn-primary">
                  Save Offer
                </button>
                <a href="lender_dashboard.php" class="btn btn-outline-secondary ms-2">
                  Cancel
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>

  </main>
</div>

<?php include 'footer.php'; ?>
</body>
</html>