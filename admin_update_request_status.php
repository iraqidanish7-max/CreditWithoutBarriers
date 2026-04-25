<?php
// admin_update_request_status.php
// Admin-only endpoint to approve/reject borrower loan requests

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: adminlogin.php');
    exit;
}

$role = $_SESSION['role'] ?? '';
if ($role !== 'admin') {
    // Non-admins get kicked out
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

$request_id = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;
$action     = isset($_POST['action']) ? trim($_POST['action']) : '';

if ($request_id <= 0 || ($action !== 'approve' && $action !== 'reject')) {
    $msg  = 'Invalid request data.';
    $type = 'danger';
    header('Location: admin_dashboard.php?msg=' . urlencode($msg) . '&type=' . $type);
    exit;
}

// Map action -> status value in DB
$status = ($action === 'approve') ? 'approved' : 'rejected';

$stmt = $conn->prepare("UPDATE customerdetails SET status = ? WHERE id = ?");
if (!$stmt) {
    $msg  = 'Database error: ' . $conn->error;
    $type = 'danger';
    header('Location: admin_dashboard.php?msg=' . urlencode($msg) . '&type=' . $type);
    exit;
}

$stmt->bind_param('si', $status, $request_id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($affected <= 0) {
    $msg  = 'No changes made. The request may not exist.';
    $type = 'warning';
} else {
    $msg  = ($status === 'approved')
        ? 'Loan request approved successfully.'
        : 'Loan request rejected successfully.';
    $type = 'success';
}

header('Location: admin_dashboard.php?msg=' . urlencode($msg) . '&type=' . $type);
exit;