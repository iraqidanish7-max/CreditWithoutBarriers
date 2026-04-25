<?php
// export_accepted_offers_csv.php
// Admin-only: export all accepted offers as CSV

ini_set('display_errors', 1);
error_reporting(E_ALL);

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
        header('Location: customerlogin.php');
    }
    exit;
}

require __DIR__ . '/connection.php';

$sql = "
    SELECT 
        o.id                    AS offer_id,
        o.request_id,
        o.lender_name,
        o.lender_email,
        o.offer_amount,
        o.interest_rate,
        o.tenure_months,
        o.status,
        o.admin_status,
        o.created_at,
        c.name                  AS borrower_name,
        c.email                 AS borrower_email,
        c.loanreq               AS borrower_loanreq
    FROM loan_offers o
    INNER JOIN customerdetails c ON o.request_id = c.id
    WHERE o.status = 'accepted'
    ORDER BY o.created_at DESC
";

$result = $conn->query($sql);
if (!$result) {
    die('Query failed: ' . $conn->error);
}

if ($result->num_rows === 0) {
    echo "<!doctype html><html><head><meta charset='utf-8'><title>No Accepted Offers</title></head><body>";
    echo "<p style='font-family: system-ui, sans-serif; padding: 16px;'>
            There are currently <strong>no accepted offers</strong> to export.
            Please approve and accept at least one offer, then try again.
          </p>";
    echo "</body></html>";
    exit;
}

// IMPORTANT: no output before this point

$filename = 'accepted_offers_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

fputcsv($output, [
    'Offer ID',
    'Request ID',
    'Borrower Name',
    'Borrower Email',
    'Loan Requested (₹)',
    'Lender Name',
    'Lender Email',
    'Offer Amount (₹)',
    'Interest Rate (%)',
    'Tenure (months)',
    'Offer Status',
    'Admin Review',
    'Offer Created At'
]);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['offer_id'],
        $row['request_id'],
        $row['borrower_name'],
        $row['borrower_email'],
        $row['borrower_loanreq'],
        $row['lender_name'],
        $row['lender_email'],
        $row['offer_amount'],
        $row['interest_rate'],
        $row['tenure_months'],
        $row['status'],
        $row['admin_status'],
        $row['created_at'],
    ]);
}

fclose($output);
exit;