<?php
// accept_offer.php - borrower accepts ONE offer for a given loan request (now OTP-protected)

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: customerlogin.php');
    exit;
}

// Only borrowers allowed here (adjust if your role key is different)
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

$user_email = $_SESSION['user_email'] ?? '';
if ($user_email === '') {
    header('Location: customerlogin.php');
    exit;
}

include __DIR__ . '/connection.php';

// Get parameters
$offer_id   = isset($_GET['offer_id'])   ? (int)$_GET['offer_id']   : 0;
$request_id = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 0;

if ($offer_id <= 0 || $request_id <= 0) {
    header('Location: dashboard.php');
    exit;
}

// 🔐 OTP ENFORCEMENT: require successful OTP verification for this offer
if (
    !isset($_SESSION['otp_verified_offer']) ||
    (int)$_SESSION['otp_verified_offer'] !== $offer_id
) {
    // Either no OTP verified, or OTP was for some other offer
    $msg  = 'OTP verification missing or invalid. Please accept the offer through the View Offers page and complete OTP verification.';
    $type = 'danger';
    header("Location: view_offers.php?request_id={$request_id}&msg=" . urlencode($msg) . "&type={$type}");
    exit;
}

// OTP was valid for this offer; clear it so it cannot be reused
unset($_SESSION['otp_verified_offer']);

// 1. Verify that this offer exists AND belongs to a request owned by this borrower
$sql = "
    SELECT 
        o.id,
        o.request_id,
        o.status,
        o.admin_status,
        c.email AS borrower_email,
        c.loanreq
    FROM loan_offers o
    INNER JOIN customerdetails c ON o.request_id = c.id
    WHERE o.id = ? AND o.request_id = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    $msg  = 'Database error: unable to load offer.';
    $type = 'danger';
    header("Location: view_offers.php?request_id={$request_id}&msg=" . urlencode($msg) . "&type={$type}");
    exit;
}

$stmt->bind_param('ii', $offer_id, $request_id);
$stmt->execute();
$result = $stmt->get_result();
$offerRow = $result->fetch_assoc();
$stmt->close();

if (!$offerRow || $offerRow['borrower_email'] !== $user_email) {
    // Either offer doesn't exist, or it isn't for this borrower's request
    $msg  = 'You are not allowed to accept this offer.';
    $type = 'danger';
    header("Location: view_offers.php?request_id={$request_id}&msg=" . urlencode($msg) . "&type={$type}");
    exit;
}

// 2. If the offer is already accepted, just inform user and return
if ($offerRow['status'] === 'accepted') {
    $msg  = 'This offer is already accepted.';
    $type = 'info';
    header("Location: view_offers.php?request_id={$request_id}&msg=" . urlencode($msg) . "&type={$type}");
    exit;
}
// New: ensure admin has approved this offer
if (!isset($offerRow['admin_status']) || $offerRow['admin_status'] !== 'approved') {
    $msg  = 'This offer is not yet approved by admin and cannot be accepted.';
    $type = 'warning';
    header("Location: view_offers.php?request_id={$request_id}&msg=" . urlencode($msg) . "&type={$type}");
    exit;
}
$conn->begin_transaction();

try {
    // 3. First, reject ALL other offers for this request (pending or accepted)
    //    We keep withdrawn as it is, but you can include it if you want.
    $rejectSql = "
        UPDATE loan_offers
        SET status = 'rejected'
        WHERE request_id = ? 
          AND id <> ?
          AND status IN ('pending', 'accepted')
    ";
    $stmt = $conn->prepare($rejectSql);
    if (!$stmt) {
        throw new Exception('Failed to prepare reject query: ' . $conn->error);
    }
    $stmt->bind_param('ii', $request_id, $offer_id);
    $stmt->execute();
    $stmt->close();

    // 4. Now, mark THIS offer as accepted
    $acceptSql = "
        UPDATE loan_offers
        SET status = 'accepted'
        WHERE id = ? AND request_id = ?
    ";
    $stmt = $conn->prepare($acceptSql);
    if (!$stmt) {
        throw new Exception('Failed to prepare accept query: ' . $conn->error);
    }
    $stmt->bind_param('ii', $offer_id, $request_id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected <= 0) {
        throw new Exception('Offer could not be accepted. It may have been updated already.');
    }

    // Everything OK
    $conn->commit();

    $msg  = 'Offer accepted successfully. Other offers for this request have been marked as rejected.';
    $type = 'success';

} catch (Exception $e) {
    $conn->rollback();
    $msg  = 'Error accepting offer: ' . $e->getMessage();
    $type = 'danger';
}

// Redirect back to the offers page for this request
header("Location: view_offers.php?request_id={$request_id}&msg=" . urlencode($msg) . "&type={$type}");
exit;