<?php
// admin_update_user_status.php
// Admin-only endpoint to enable/disable user accounts

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

$user_id   = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$action    = isset($_POST['action']) ? trim($_POST['action']) : '';
$current_admin_id = (int)($_SESSION['user_id'] ?? 0);

if ($user_id <= 0 || ($action !== 'activate' && $action !== 'deactivate')) {
    $msg  = 'Invalid user data.';
    $type = 'danger';
    header('Location: admin_dashboard.php?msg=' . urlencode($msg) . '&type=' . $type);
    exit;
}

// Don’t let admin disable their own account
if ($user_id === $current_admin_id && $action === 'deactivate') {
    $msg  = 'You cannot disable your own admin account.';
    $type = 'warning';
    header('Location: admin_dashboard.php?msg=' . urlencode($msg) . '&type=' . $type);
    exit;
}

$is_active = ($action === 'activate') ? 1 : 0;

$stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE id = ?");
if (!$stmt) {
    $msg  = 'Database error: ' . $conn->error;
    $type = 'danger';
    header('Location: admin_dashboard.php?msg=' . urlencode($msg) . '&type=' . $type);
    exit;
}

$stmt->bind_param('ii', $is_active, $user_id);
$stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if ($affected <= 0) {
    $msg  = 'No changes made. The user may not exist.';
    $type = 'warning';
} else {
    if ($is_active) {
        $msg  = 'User account has been ACTIVATED.';
        $type = 'success';
    } else {
        $msg  = 'User account has been DEACTIVATED.';
        $type = 'success';
    }
}

header('Location: admin_dashboard.php?msg=' . urlencode($msg) . '&type=' . $type);
exit;