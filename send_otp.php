<?php
// send_otp.php (STEP 2 - basic version, no email yet)
session_start();
header('Content-Type: application/json');

require __DIR__ . '/connection.php';

// 1) Check: user logged-in & borrower hai ya nahi
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'borrower') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$userId = (int) $_SESSION['user_id'];

// 2) Check: POST request & offer_id mila ya nahi
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['offer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$offerId = (int) $_POST['offer_id'];

// 3) Check: ye offer iss borrower ke loan request ka hi hai?
// 3) Check: this offer exists at all
$sql = "SELECT id FROM loan_offers WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $offerId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Offer not found']);
    exit;
}
// 4) 6-digit OTP generate karo
$otpCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT); // e.g. 035982
$expiresAt = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');

// Optional: pehle ke unused OTP ko used mark karo
$upd = $conn->prepare("UPDATE offer_otps SET is_used = 1 WHERE offer_id = ? AND is_used = 0");
$upd->bind_param('i', $offerId);
$upd->execute();

// 5) Naya OTP record insert karo
$ins = $conn->prepare("INSERT INTO offer_otps (offer_id, user_id, otp_code, expires_at) VALUES (?, ?, ?, ?)");
$ins->bind_param('iiss', $offerId, $userId, $otpCode, $expiresAt);
$ins->execute();

// 6) Abhi ke liye sirf testing ke liye OTP JSON me bhej rahe hain
echo json_encode([
    'success' => true,
    'message' => 'OTP sent successfully ).',
    'otp'     => $otpCode   
]);