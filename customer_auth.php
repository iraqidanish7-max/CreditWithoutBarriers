<?php
// customer_auth.php — login handler that checks the users table and verifies hashed passwords
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include __DIR__ . '/connection.php';

// basic validation
$userInput = trim($_POST['user'] ?? '');
$password  = $_POST['password'] ?? '';

if ($userInput === '' || $password === '') {
    // nicer UX: redirect back to login with message (quick)
    header('Location: customerlogin.php?error=' . urlencode('Please enter username/email and password'));
    exit;
}

// prepare: allow login by email OR by name (so users can enter either)
// ✅ include role AND is_active now
$sql = "SELECT id, name, email, password_hash, role, is_active 
        FROM users 
        WHERE email = ? OR name = ? 
        LIMIT 1";

if (!isset($conn)) {
    die("Database connection unavailable.");
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    die("Server error (prepare).");
}

$stmt->bind_param('ss', $userInput, $userInput);
$stmt->execute();
$res = $stmt->get_result();

if ($res && $res->num_rows === 1) {
    $row  = $res->fetch_assoc();
    $hash = $row['password_hash'] ?? '';

    // ✅ treat missing is_active as active (for old rows)
    $is_active = isset($row['is_active']) ? (int)$row['is_active'] : 1;

    // ✅ block disabled accounts before password check
    if ($is_active !== 1) {
        header('Location: customerlogin.php?error=' . urlencode('Your account has been disabled by the admin.'));
        exit;
    }

    if (password_verify($password, $hash)) {
        // ✅ login success
        $_SESSION['user_id']    = $row['id'];
        $_SESSION['user_name']  = $row['name'];
        $_SESSION['user_email'] = $row['email'];
        $_SESSION['role']       = $row['role'] ?? 'borrower';

        // ✅ redirect based on role
        if ($row['role'] === 'lender') {
            header('Location: lender_dashboard.php');
        } elseif ($row['role'] === 'admin') {
            header('Location: admin_dashboard.php');
        } else { // borrower (default)
            header('Location: dashboard.php');
        }
        exit;
    } else {
        // bad password
        header('Location: customerlogin.php?error=' . urlencode('Invalid credentials'));
        exit;
    }
} else {
    // user not found
    header('Location: customerlogin.php?error=' . urlencode('User not found'));
    exit;
}