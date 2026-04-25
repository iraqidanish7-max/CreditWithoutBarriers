<?php
// agreement.php — Generate Loan Agreement PDF for an accepted offer

ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require __DIR__ . '/connection.php';
require __DIR__ . '/fpdf/fpdf.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: customerlogin.php');
    exit;
}

$user_id    = (int)($_SESSION['user_id'] ?? 0);
$user_role  = $_SESSION['role'] ?? 'borrower';
$user_email = $_SESSION['user_email'] ?? '';

// Get offer_id from query
$offer_id = isset($_GET['offer_id']) ? (int)$_GET['offer_id'] : 0;
if ($offer_id <= 0) {
    die('Invalid offer ID.');
}

// Fetch offer + borrower info
$sql = "
    SELECT 
        o.id            AS offer_id,
        o.request_id,
        o.lender_name,
        o.lender_email,
        o.offer_amount,
        o.interest_rate,
        o.tenure_months,
        o.status,
        o.admin_status,
        o.created_at,
        c.name          AS borrower_name,
        c.email         AS borrower_email,
        c.mobile        AS borrower_mobile,
        c.aadhar        AS borrower_aadhar,
        c.loanreq       AS borrower_loanreq,
        c.created_at    AS request_created_at
    FROM loan_offers o
    INNER JOIN customerdetails c ON o.request_id = c.id
    WHERE o.id = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die('DB error: ' . $conn->error);
}
$stmt->bind_param('i', $offer_id);
$stmt->execute();
$res   = $stmt->get_result();
$offer = $res->fetch_assoc();
$stmt->close();

if (!$offer) {
    die('Offer not found.');
}

// Security: who can download?
// - Borrower who owns this request (email matches)
// - OR admin
if ($user_role !== 'admin') {
    if (!isset($offer['borrower_email']) || $offer['borrower_email'] !== $user_email) {
        die('You are not allowed to view this agreement.');
    }
}

// Only for accepted offers (extra safety)
if (strtolower($offer['status']) !== 'accepted') {
    die('Agreement can only be generated for ACCEPTED offers.');
}

// Prepare human-readable values
$offerId          = $offer['offer_id'];
$requestId        = $offer['request_id'];
$borrowerName     = $offer['borrower_name'];
$borrowerEmail    = $offer['borrower_email'];
$borrowerMobile   = $offer['borrower_mobile'];
$borrowerAadhar   = $offer['borrower_aadhar'];
$loanRequested    = number_format((float)$offer['borrower_loanreq'], 2);
$lenderName       = $offer['lender_name'];
$lenderEmail      = $offer['lender_email'];
$offerAmount      = number_format((float)$offer['offer_amount'], 2);
$interestRate     = $offer['interest_rate'];
$tenureMonths     = (int)$offer['tenure_months'];
$offerCreatedAt   = date('d M Y, h:i A', strtotime($offer['created_at']));
$requestCreatedAt = date('d M Y, h:i A', strtotime($offer['request_created_at']));
$today            = date('d M Y');

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetTitle('Loan Agreement - Offer #' . $offerId);

// ====== LOGO AT TOP (if file exists) ======

// ====== TEXT LOGO AT TOP ======
$pdf->SetFont('Arial', 'B', 20);
$pdf->SetTextColor(90, 60, 200); // purple tone
$pdf->Cell(0, 12, 'CreditWithoutBarriers', 0, 1, 'C');
$pdf->SetTextColor(0, 0, 0); // reset
$pdf->Ln(3);


// Header
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Loan Agreement', 0, 1, 'C');


// Agreement Meta
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Agreement Date: ' . $today, 0, 1);
$pdf->Cell(0, 6, 'Offer ID: ' . $offerId, 0, 1);
$pdf->Cell(0, 6, 'Loan Request ID: ' . $requestId, 0, 1);
$pdf->Ln(3);

// Borrower details
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'Borrower Details', 0, 1);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Name: ' . $borrowerName, 0, 1);
$pdf->Cell(0, 6, 'Email: ' . $borrowerEmail, 0, 1);
if (!empty($borrowerMobile)) {
    $pdf->Cell(0, 6, 'Mobile: ' . $borrowerMobile, 0, 1);
}
if (!empty($borrowerAadhar)) {
    $pdf->Cell(0, 6, 'Aadhar: ' . $borrowerAadhar, 0, 1);
}
$pdf->Ln(3);

// Lender details
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'Lender Details', 0, 1);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Name: ' . $lenderName, 0, 1);
$pdf->Cell(0, 6, 'Email: ' . $lenderEmail, 0, 1);
$pdf->Ln(3);

// Loan details
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'Loan Details', 0, 1);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Original Loan Requested: Rs. ' . $loanRequested, 0, 1);
$pdf->Cell(0, 6, 'Loan Amount Sanctioned: Rs. ' . $offerAmount, 0, 1);
$pdf->Cell(0, 6, 'Interest Rate: ' . $interestRate . '% per annum', 0, 1);
$pdf->Cell(0, 6, 'Tenure: ' . $tenureMonths . ' month(s)', 0, 1);
$pdf->Cell(0, 6, 'Request Posted On: ' . $requestCreatedAt, 0, 1);
$pdf->Cell(0, 6, 'Offer Accepted On: ' . $offerCreatedAt, 0, 1);
$pdf->Ln(4);

// Terms & Conditions
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'Key Terms & Conditions', 0, 1);

$pdf->SetFont('Arial', '', 10);
$terms = "
1. The Borrower agrees to repay the sanctioned loan amount to the Lender as per the agreed tenure and interest rate.
2. Repayments shall be made in equal instalments as mutually agreed between the Borrower and the Lender.
3. In case of delay or default in repayment, the Lender reserves the right to levy additional charges or take necessary legal action as per applicable laws.
4. Both parties confirm that the information provided in the loan request and offer is true and correct to the best of their knowledge.
5. This agreement is generated digitally via the Credit Without Barriers platform for record and reference purposes.
";
$pdf->MultiCell(0, 5, trim($terms));
$pdf->Ln(6);

// Signatures section
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'Signatures', 0, 1);

$pdf->SetFont('Arial', '', 11);
$pdf->Ln(4);
$pdf->Cell(90, 6, 'Borrower:', 0, 0);
$pdf->Cell(90, 6, 'Lender:', 0, 1);
$pdf->Ln(12);
$pdf->Cell(90, 6, '__________', 0, 0);
$pdf->Cell(90, 6, '__________', 0, 1);
$pdf->Cell(90, 6, $borrowerName, 0, 0);
$pdf->Cell(90, 6, $lenderName, 0, 1);

// ====== LIGHT WATERMARK TEXT AT BOTTOM ======
$pdf->SetTextColor(200, 200, 200); // light grey
$pdf->SetFont('Arial', 'B', 18);
$pdf->Ln(10);
$pdf->Cell(0, 10, 'CreditWithoutBarriers', 0, 1, 'C');


// Output PDF as download
$filename = 'loan_agreement_offer_' . $offerId . '.pdf';
$pdf->Output('D', $filename);
exit;