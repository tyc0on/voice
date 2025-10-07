<?php
declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo 'Method Not Allowed';
    exit;
}

ini_set('session.cookie_lifetime', (string) (60 * 60 * 24 * 365));
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /sign-in.php');
    exit;
}

require_once __DIR__ . '/../core/stripe/service.php';

try {
    $db = ensure_database_connection();
    $accountId = (int) ($_SESSION['id'] ?? 0);
    if ($accountId <= 0) {
        throw new RuntimeException('Invalid account context.');
    }

    $account = stripe_fetch_account($db, $accountId);
    if ($account === null) {
        throw new RuntimeException('Account not found.');
    }

    $customer = stripe_ensure_customer($db, $accountId, $account);
    $returnUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/billing.php';

    $session = stripe_client()->billingPortal->sessions->create([
        'customer' => $customer['stripe_customer_id'],
        'return_url' => $returnUrl,
    ]);

    header('Location: ' . $session->url);
    exit;
} catch (Throwable $e) {
    error_log('Failed to create billing portal session: ' . $e->getMessage());
    $_SESSION['billing_error'] = 'We were unable to open the billing portal. Please try again or contact support.';
    header('Location: /billing.php');
    exit;
}
