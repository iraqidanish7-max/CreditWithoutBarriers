<?php
// admin_update_offer_status.php
// Admin-only endpoint to approve/reject lender offers (compliance / moderation)

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: adminlogin.php');
    exit;
}

$role = $_SESSION['role'] ?? '';
if ($role !== 'admin') {
    if ($role === 'borrower') {
        header('Location: dashboard.php');
    } elseif ($role === 'lender') {
        header('Location: lender_dashboard.php');
    } else {
        header('Location: adminlogin.php');
    }
    exit;
}

require __DIR__ . '/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $msg  = 'Invalid request method.';
    $type = 'danger';
    header('Location: admin_dashboard.php?msg=' . urlencode($msg) . '&type=' . $type);
    exit;
}

$offer_id = isset($_POST['offer_id']) ? (int)$_POST['offer_id'] : 0;
$action   = isset($_POST['action']) ? trim($_POST['action']) : '';

if ($offer_id <= 0 || ($action !== 'approve' && $action !== 'reject')) {
    $msg  = 'Invalid offer data.';
    $type = 'danger';
    header('Location: admin_dashboard.php?msg=' . urlencode($msg) . '&type=' . $type);
    exit;
}

// Map action -> admin_status
$admin_status = ($action === 'approve') ? 'approved' : 'rejected';

$stmt = $conn->prepare("UPDATE loan_offers SET admin_status = ? WHERE id = ?");
if (!$stmt) {
    $msg  = 'Database error: ' . $conn->error;
    $type = 'danger';
    header('Location: admin_dashboard.php?msg=' . urlencode($msg) . '&type=' . $type);
    exit;
}

$stmt->bind_param('si', $admin_status, $offer_id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($affected <= 0) {
    $msg  = 'No changes made. The offer may not exist.';
    $type = 'warning';
} else {
    $msg  = ($admin_status === 'approved')
        ? 'Offer marked as APPROVED by admin.'
        : 'Offer marked as REJECTED by admin.';
    $type = 'success';
}

header('Location: admin_dashboard.php?msg=' . urlencode($msg) . '&type=' . $type);
exit;