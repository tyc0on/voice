<?php
declare(strict_types=1);

use Stripe\Event;
use Stripe\Webhook;

require_once __DIR__ . '/../core/stripe/service.php';

$payload = file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

if ($payload === false) {
    http_response_code(400);
    echo 'Invalid payload.';
    exit;
}

$webhookSecret = stripe_webhook_secret();
if ($webhookSecret === '') {
    http_response_code(500);
    echo 'Webhook secret is not configured.';
    exit;
}

try {
    $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
} catch (UnexpectedValueException $e) {
    http_response_code(400);
    echo 'Invalid payload: ' . $e->getMessage();
    exit;
} catch (Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    echo 'Invalid signature: ' . $e->getMessage();
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Failed to parse webhook: ' . $e->getMessage();
    exit;
}

try {
    $db = ensure_database_connection();
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Database unavailable.';
    error_log('Stripe webhook database connection failed: ' . $e->getMessage());
    exit;
}

try {
    stripe_log_event($db, $event->id, $event->type, json_encode($event->toArray(), JSON_THROW_ON_ERROR));
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Failed to log event.';
    error_log('Stripe webhook logging failed: ' . $e->getMessage());
    exit;
}

try {
    stripe_handle_event($db, $event);
    stripe_mark_event_status($db, $event->id, 'processed');
    http_response_code(200);
    echo 'Processed';
} catch (Throwable $e) {
    stripe_mark_event_status($db, $event->id, 'failed', $e->getMessage());
    error_log('Stripe webhook handling failed: ' . $e->getMessage());
    http_response_code(500);
    echo 'Failed to process event.';
}
