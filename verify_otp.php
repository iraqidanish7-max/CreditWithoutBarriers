<?php
// verify_otp.php - step 4: plain version with text messages
session_start();
require __DIR__ . '/connection.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'borrower') {
    die('Unauthorized access. You must be logged in as a borrower.');
}

$userId = (int) $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method.');
}

$offerId   = isset($_POST['offer_id']) ? (int)$_POST['offer_id'] : 0;
$otpCode   = isset($_POST['otp_code']) ? trim($_POST['otp_code']) : '';
$requestId = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;

if ($offerId <= 0 || $otpCode === '') {
    die('Please provide both offer_id and otp_code.');
}

// 1) Fetch the latest OTP for this user + offer
$sql = "
    SELECT id, otp_code, expires_at, is_used
    FROM offer_otps
    WHERE offer_id = ? AND user_id = ?
    ORDER BY id DESC
    LIMIT 1
";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $offerId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('No OTP found for this offer and user. Generate a new OTP first.');
}

$row = $result->fetch_assoc();

// 2) Check if already used
if ((int)$row['is_used'] === 1) {
    die('This OTP has already been used. Please request a new OTP.');
}

// 3) Check expiry
$currentTime = new DateTime();
$expiresAt   = new DateTime($row['expires_at']);

if ($currentTime > $expiresAt) {
    die('This OTP has expired. Please request a new OTP.');
}

// 4) Check code match
if ($row['otp_code'] !== $otpCode) {
    die('Invalid OTP. Please try again.');
}

// 5) Mark OTP as used
$upd = $conn->prepare("UPDATE offer_otps SET is_used = 1 WHERE id = ?");
$upd->bind_param('i', $row['id']);
$upd->execute();

// 6) Set session flag that this offer is now OTP-verified
$_SESSION['otp_verified_offer'] = $offerId;

// 7) Redirect to accept_offer.php with same parameters as before
$redirectUrl = 'accept_offer.php?offer_id=' . $offerId;
if ($requestId > 0) {
    $redirectUrl .= '&request_id=' . $requestId;
}

header('Location: ' . $redirectUrl);
exit;